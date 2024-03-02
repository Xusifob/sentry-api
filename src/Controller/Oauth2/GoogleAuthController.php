<?php

namespace App\Controller\Oauth2;

use App\Auth\GoogleAuthService;
use App\Services\AuthService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class GoogleAuthController extends AbstractController
{
    public function __construct(
        private readonly AuthService $authService,
        private readonly GoogleAuthService $service,
    ) {
    }

    public function redirectAction(): RedirectResponse
    {
        $url = $this->service->getAuthUrl();

        return new RedirectResponse($url);
    }

    public function callbackAction(Request $request): JsonResponse
    {
        $state = $request->query->get('state');

        if (!$this->service->validateState($state)) {
            throw new BadRequestHttpException('Invalid state');
        }

        $code = $request->query->get('code');

        try {
            $tokens = $this->service->getToken($code);
            $accessToken = $tokens['access_token'];
            $userInfos = $this->service->getUserInfos($accessToken);
        } catch (\Exception $exception) {
            throw new BadRequestHttpException('Invalid code', $exception);
        }
        $user = $this->authService->createUser($userInfos['email'], $userInfos['given_name'], $userInfos['family_name']);

        $responseData = $this->authService->generateLoginResponse($user);

        return new JsonResponse($responseData);
    }
}
