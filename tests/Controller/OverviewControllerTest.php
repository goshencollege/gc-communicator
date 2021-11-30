<?php
namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Repository\UserRepository;
use App\Repository\AnnouncementRepository;
use Doctrine\ORM\EntityManagerInterface;

class OverviewControllerTest extends WebTestCase
{
            
    /**
     * Load all authentication-required pages, check for 302, then sign into an admin user, and check *all* sites again (except functional pages).
     * The only two pages not currently loaded are ones that update a field upon access, such as approval of announcements and activiation of categories.
     * 
     * @author Daniel Boling
     * 
     */
    public function test_urls()
    {

        $client = static::createClient();

        // This should return 302 since no user is authenticated
        $pages = array(
            '/new',
            '/overview/user',
            '/category/new',
            '/category/list',
        );

        foreach ($pages as $page){
            // This should return 302 since no user is authenticated
            $client->request('GET', $page);
            $response = $client->getResponse();
            $this->assertEquals(302, $response->getStatusCode());
        }

        // authenticate a test user
        $userRepo = static::getContainer()->get(UserRepository::class);
        $testUser = $userRepo->findOneByUsername("dboling");
        $client->loginUser($testUser);

        $pages = array(
            '/overview',
            '/new',
            '/overview/user',
            '/category/new',
            '/category/list',
            '/moderation/announcements',
            '/login'
        );

        foreach ($pages as $page){
            // these should all now return 200 since a user is authenticated
            $client->request('GET', $page);
            $response = $client->getResponse();
            $this->assertEquals(200, $response->getStatusCode());
        }

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

        $moderator_user = static::getContainer()
            ->get(UserRepository::class)
            ->findOneByUsername("dboling")
        ;

        $users = array($announcement->getUser(), $moderator_user);
        foreach ($users as $user){
            // authenticate a fixture user and check that the page returns a 200 code
            $client->loginUser($user);
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

    /**
     * Test file uploading, access, deletion, and change.
     * 
     * @author Daniel Boling
     */
    public function test_files()
    {

        $client = static::createClient();

        // redirect to the unauthenticated page since the wrong user is authenticated
        $test_user = static::getContainer()
            ->get(UserRepository::class)
            ->findOneByUsername("test_user")
        ;
        $client->loginUser($test_user);

        

    }
       
}


//EOF
