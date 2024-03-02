<?php

namespace App\Tests\Functional\Api;


use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use ApiPlatform\Symfony\Bundle\Test\Client;
use App\Entity\Enum\UserRole;
use App\Factory\UserFactory;
use App\Factory\WordFactory;
use Symfony\Component\HttpFoundation\Response;
use Zenstruck\Foundry\Test\ResetDatabase;

class WordTest extends ApiTestCase
{

    use ResetDatabase;

    protected Client $client;

    protected ?string $token = null;

    public function setUp() : void
    {
        $this->client = self::createClient();
    }


    public function testGetWords() : void
    {

      //  WordFactory::createMany(10);

        $this->login();

        $data = $this->get('words');

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

    }


    protected function login() : void
    {

        $email = 'student@wordgenius.net';
        $plainPassword = 'Ra15Polak##';

        UserFactory::createOne([
            'roles' => [
                UserRole::ROLE_USER->value
            ],
            'email' => $email,
            'plainPassword' => $plainPassword,
            'givenName' => 'John',
            'familyName' => 'Doe'
        ]);

        $response = $this->post('login',[
            'email' => $email,
            'password' => $plainPassword,
        ],[
            'Content-Type' => 'application/json'
        ]);

        $this->token = $response['token'];

    }




    protected function get(string $url, array $query = []) : array
    {
        return $this->doRequest('GET',$url,$query);
    }


    protected function post(string $url, array $body = [],array $headers = []) : array
    {
        return $this->doRequest('POST',$url,[],$body,$headers);
    }

    private function doRequest(string $method, string $url,array $query = [],array $body = [], array $headers = []) : array
    {
        if($this->token && !isset($headers['Authorization'])) {
            $headers['Authorization'] = "Bearer {$this->token}";
        }

        if(!isset($headers['Content-Type'])) {
            $headers['Content-Type'] = 'application/vnd.api+json';
        }

        if(!isset($headers['Accept'])) {
            $headers['Accept'] = 'application/vnd.api+json';
        }

        if(!str_starts_with($url,'/')) {
            $url = "/api/v1/$url";
        }

        $response = $this->client->request($method,$url,[
            'query' => $query,
            'json' => $body,
            'headers' => $headers
        ]);

        return $response->toArray(false);

    }


}
