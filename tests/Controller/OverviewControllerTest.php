<?php
namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

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

        $crawler = $client->request('GET', '/add');

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
