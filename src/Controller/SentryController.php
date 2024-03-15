<?php

namespace App\Controller;

use App\Bridge\OneSignal\Client;
use App\Bridge\OneSignal\Entity\Notification;
use App\Entity\Device;
use App\Entity\Enum\ExceptionLevel;
use App\Entity\SentryException;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class SentryController extends AbstractController
{
    public function __construct(
        private readonly string $appUrl,
        private readonly Client $client,
        private readonly EntityManagerInterface $em
    ) {
    }

    public function webhookAction(Request $request, string $user): Response
    {
        $user = $this->em->getRepository(User::class)->find($user);

        if (null === $user) {
            throw new NotFoundHttpException();
        }

        $data = $request->toArray();
        $event = $data['data']['event'];

        $error = new SentryException();
        $error->eventId = $event['event_id'];
        $error->projectId = $event['project'];
        $error->platform = $event['platform'];
        $error->message = $event['message'];
        $error->release = $event['release'];
        $error->title = $event['title'];
        $error->level = ExceptionLevel::tryFrom($event['level']);
        $error->location = $event['location'];
        $error->exception = $event['exception'];

        $error->request = $event['request'];

        $error->url = $event['web_url'];

        $error->owner = $user;

        $this->em->persist($error);
        $this->em->flush();

        if ($user->slackWebhookUrl) {
            $this->sendSlackMessage($error, $user);
        }

        $devices = $this->em->getRepository(Device::class)
            ->findBy([
                'owner' => $user,
            ]);

        $notification = new Notification();
        $notification->web_url = $this->appUrl;
        $notification->type = Notification::TYPE_DAILY_REMINDER;

        foreach ($devices as $device) {
            if ($device->isExpired()) {
                continue;
            }

            $notification->addDevice($device);
        }

        $this->client->sendNotification($notification);

        return new Response('OK');
    }

    private function sendSlackMessage(SentryException $exception, User $user): void
    {
        $client = HttpClient::create();

        $client->request('POST', $user->slackWebhookUrl, [
           'json' => [
               'text' => "Erreur Sentry : {$exception->title} (Level {$exception->level->name}). Please check the following link for more details: {$exception->url}",
           ],
        ]);
    }
}
