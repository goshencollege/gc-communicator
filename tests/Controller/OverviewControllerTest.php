<?php
namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Repository\UserRepository;
use App\Repository\AnnouncementRepository;
use Doctrine\ORM\EntityManagerInterface;

class OverviewControllerTest extends WebTestCase
{

    /**
     * Load the /add URL and ensure that it gets a 200 status code
     * 
     * @author Daniel Boling
     * 
     */
    public function test_add()
    {

        $client = static::createClient();

        // This should return 302 since no user is authenticated
        $client->request('GET', '/new');
        $response = $client->getResponse();
        $this->assertEquals(302, $response->getStatusCode());

        // authenticate a test user
        $userRepo = static::getContainer()->get(UserRepository::class);
        $testUser = $userRepo->findOneByUsername("test_user");
        $client->loginUser($testUser);

        // This should now return 200 since a user is authenticated
        $client->request('GET', '/new');
        $response = $client->getResponse();
        $this->assertEquals(200, $response->getStatusCode());
    }
        
    /**
     * Load the /overview URL and ensure that it gets a 200 status code
     * 
     * @author David King
     * 
     */
    public function test_overview()
    {

        $client = static::createClient();

        $client->request('GET', '/overview');
        $response = $client->getResponse();

        $this->assertEquals(200, $response->getStatusCode());

    }

    /** 
     * Ensure that announcements can be changed by only the original user or a moderator.
     *  
     * @author Daniel Boling
     */
    public function test_modify_announcement()
    {

        $client = static::createClient();



        // returns last result of all announcements
        $announcement = static::getContainer()
            ->get(AnnouncementRepository::class)
            ->findAll()
        ;
        $announcement = end($announcement);
        // the user should be fixture_user and the id should be 1 every time.

        // return 302 since no user is authenticated
        $client->request('GET', '/modify/announcement/' . $announcement->getId());
        $response = $client->getResponse();
        $this->assertEquals(302, $response->getStatusCode());

        // redirect to the unauthenticated page since the wrong user is authenticated
        $test_user = static::getContainer()
            ->get(UserRepository::class)
            ->findOneByUsername("test_user")
        ;
        $client->loginUser($test_user);
        $client->request('GET', '/modify/announcement/' . $announcement->getId());
        $response = $client->getResponse();
        $this->assertEquals(403, $response->getStatusCode());

        // authenticate a fixture user and check that the page returns a 200 code
        $client->loginUser($announcement->getUser());
        $client->request('GET', '/modify/announcement/' . $announcement->getId());
        $response = $client->getResponse();
        $this->assertEquals(200, $response->getStatusCode());

        $crawler = $client->request('GET', '/modify/announcement/' . $announcement->getId());
        $buttonCrawlerNode = $crawler->selectButton('Submit Announcement');
        $form = $buttonCrawlerNode->form();
        $pre_form_fields = $form->getValues();

        $announcement_subject = $pre_form_fields['announcement_form[subject]'];
        $announcement_author = $pre_form_fields['announcement_form[author]'];
        $this->assertSame($announcement->getSubject(), $announcement_subject);
        $this->assertSame($announcement->getAuthor(), $announcement_author);

        $client->submit($form, [
            'announcement_form[subject]' => 'Change',
            'announcement_form[text]' => 'change lorem ispum',
        ]);
        
        $post_form_fields = $form->getValues();

        // returns last result of all announcements
        $announcement = static::getContainer()
            ->get(AnnouncementRepository::class)
            ->findAll()
        ;
        $announcement = end($announcement);

        $announcement_subject = $post_form_fields['announcement_form[subject]'];
        $announcement_author = $post_form_fields['announcement_form[author]'];
        $this->assertSame($announcement->getSubject(), $announcement_subject);
        $this->assertSame($announcement->getAuthor(), $announcement_author);


        // authenticate a moderator user and check that the page returns a 200 code
        $moderator_user = static::getContainer()
            ->get(UserRepository::class)
            ->findOneByUsername('dboling')
        ;
        $client->loginUser($moderator_user);
        $client->request('GET', '/modify/announcement/' . $announcement->getId());
        $response = $client->getResponse();
        $this->assertEquals(200, $response->getStatusCode());

        $crawler = $client->request('GET', '/modify/announcement/' . $announcement->getId());
        $buttonCrawlerNode = $crawler->selectButton('Submit Announcement');
        $form = $buttonCrawlerNode->form();
        $pre_form_fields = $form->getValues();

        $announcement_subject = $pre_form_fields['announcement_form[subject]'];
        $announcement_author = $pre_form_fields['announcement_form[author]'];
        $this->assertSame($announcement->getSubject(), $announcement_subject);
        $this->assertSame($announcement->getAuthor(), $announcement_author);

        $client->submit($form, [
            'announcement_form[subject]' => 'Change',
            'announcement_form[text]' => 'change lorem ispum',
        ]);
        
        $post_form_fields = $form->getValues();

        // returns last result of all announcements
        $announcement = static::getContainer()
            ->get(AnnouncementRepository::class)
            ->findAll()
        ;
        $announcement = end($announcement);
        
        $announcement_subject = $post_form_fields['announcement_form[subject]'];
        $announcement_author = $post_form_fields['announcement_form[author]'];
        $this->assertSame($announcement->getSubject(), $announcement_subject);
        $this->assertSame($announcement->getAuthor(), $announcement_author);

        
    }
}


//EOF
