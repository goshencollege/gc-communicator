<?php
namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Repository\UserRepository;

class SecurityControllerTest extends WebTestCase
{
    /**
     * Load the /add URL and ensure that it gets redirected to /login
     * 
     * @author David King
     * 
     */
    public function testLoginRedirect()
    {
        $client = static::createClient();

        // This should return 302 since no user is authenticated
        $crawler = $client->request('GET', '/add');
        $response = $client->getResponse();
        $this->assertEquals(302, $response->getStatusCode());
        $this->assertEquals("/login", $response->getTargetUrl());
    }
}