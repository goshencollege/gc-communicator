<?php
namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Repository\UserRepository;

class OverviewControllerTest extends WebTestCase
{

    /**
     * Load the /add URL and ensure that it gets a 200 status code
     * 
     * @author David King
     * 
     */
    public function testAdd()
    {
        $client = static::createClient();

        // This should return 302 since no user is authenticated
        $client->request('GET', '/add');
        $response = $client->getResponse();
        $this->assertEquals(302, $response->getStatusCode());

        // authenticate a test user
        $userRepo = static::$container->get(UserRepository::class);
        $testUser = $userRepo->findOneByUsername("david");
        $client->loginUser($testUser);

        // This should now return 200 since a user is authenticated
        $client->request('GET', '/add');
        $response = $client->getResponse();
        $this->assertEquals(200, $response->getStatusCode());
    }
        
    /**
     * Load the /overview URL and ensure that it gets a 200 status code
     * 
     * @author David King
     * 
     */
    public function testOverview()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/overview');

        $response = $client->getResponse();
        $this->assertEquals(200, $response->getStatusCode());

    }
}

//EOF
