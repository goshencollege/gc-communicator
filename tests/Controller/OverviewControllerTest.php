<?php
namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class OverviewControllerTest extends WebTestCase
{

    public function testAdd()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/add');

        $response = $client->getResponse();
        $this->assertEquals(200, $response->getStatusCode());

    }
}