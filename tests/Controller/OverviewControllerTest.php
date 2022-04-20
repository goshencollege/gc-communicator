<?php
namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Repository\UserRepository;
use App\Repository\AnnouncementRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Finder\Finder;

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
            '/',
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
        // creates a client for the test to use

        // returns last result of all announcements
        $announcement = static::getContainer()
          ->get(AnnouncementRepository::class)
          ->findAll()
        ;
        $announcement = end($announcement);
        // the user should be fixture_user and the id should be 1 every time.

        $client->request('GET', '/modify/announcement/' . $announcement->getId());
        $response = $client->getResponse();
        $this->assertEquals(302, $response->getStatusCode());
        // return 302 since no user is authenticated

        $test_user = static::getContainer()
            ->get(UserRepository::class)
            ->findOneByUsername("test_user")
        ;
        $client->loginUser($test_user);
        $client->request('GET', '/modify/announcement/' . $announcement->getId());
        $response = $client->getResponse();
        $this->assertEquals(403, $response->getStatusCode());
        // redirect to the unauthenticated page since the wrong user is authenticated

        $moderator_user = static::getContainer()
        // pulls the fixture-defined moderator account for permissions testing
            ->get(UserRepository::class)
            ->findOneByUsername("dboling")
        ;

        $users = array($announcement->getUser(), $moderator_user);
        // loads both the fixture announcement owner and the moderator to test both permissions
        foreach ($users as $user){
            // authenticate a fixture user and check that the page returns a 200 code
            $client->loginUser($user);
            $client->request('GET', '/modify/announcement/' . $announcement->getId());
            $response = $client->getResponse();
            $this->assertEquals(200, $response->getStatusCode());

            $crawler = $client->request('GET', '/modify/announcement/' . $announcement->getId());
            $buttonCrawlerNode = $crawler->selectButton('Modify');
            $form = $buttonCrawlerNode->form();
            $pre_form_fields = $form->getValues();

            $announcement_subject = $pre_form_fields['ann_modify_form[subject]'];
            $announcement_author = $pre_form_fields['ann_modify_form[author]'];
            $this->assertSame($announcement->getSubject(), $announcement_subject);
            $this->assertSame($announcement->getAuthor(), $announcement_author);

            $client->submit($form, [
                'ann_modify_form[subject]' => 'Change',
                'ann_modify_form[text]' => 'change lorem ispum',
            ]);
            
            $post_form_fields = $form->getValues();
            // pulls all form indices from the form

            // returns last result of all announcements
            $announcement = static::getContainer()
                ->get(AnnouncementRepository::class)
                ->findAll()
            ;
            $announcement = end($announcement);

            $announcement_subject = $post_form_fields['ann_modify_form[subject]'];
            $announcement_author = $post_form_fields['ann_modify_form[author]'];
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

        $upload_finder = new Finder();
        // start of the process to remove all files with "dummy-file" as the file name

        $upload_finder->files()->in('./public/uploads/files')->name('*dummy-file*');
        // temporary function for deleting all matching (fuzzy) files
        // this will be removed in a future deletion update
        if ($upload_finder->hasResults()) {
            foreach ($upload_finder as $upload_file) {
                unlink($upload_file);
            }

        }

        $client = static::createClient();
        $test_user = static::getContainer()
            ->get(UserRepository::class)
            ->findOneByUsername("test_user")
        ;
        $client->loginUser($test_user);
        $client->request('GET', '/new');
        $response = $client->getResponse();
        $this->assertEquals(200, $response->getStatusCode());
        // authenticate a test user and check that the page returns a 200 code

        $crawler = $client->request('GET', '/new');
        $buttonCrawlerNode = $crawler->selectButton('Submit');
        $form = $buttonCrawlerNode->form();

        $form['ann_new_form']['announcementFile']['file']->upload('bin/dummy-file.txt');
        $form['ann_new_form']['category']->select('3');
        
        $client->submit($form, [
            'ann_new_form[subject]' => 'file_subject',
            'ann_new_form[author]' => 'file_author',
            'ann_new_form[text]' => 'change lorem ispum',
        ]);

        $pre_finder = new Finder();
        // start the process for finding the test file

        $pre_finder->files()->in('bin')->name('*dummy-file*');
        if ($pre_finder->hasResults()) {
            foreach ($pre_finder as $pre_file) {
                // there should only ever be one result, this is just for syntax
                $pre_file_hash = hash_file('md5', $pre_file);

            }
        }

        $post_finder = new Finder();
        // start of the process to check for test file upload

        $post_finder->files()->in('./public/uploads/files')->name('*dummy-file*');
        if ($post_finder->hasResults()) {
            foreach ($post_finder as $post_file) {
                $post_file_hash = hash_file('md5', $post_file);

            }
        }

        $this->assertSame($pre_file_hash, $post_file_hash);


        $pre_finder = new Finder();
        // start the process for finding the test file

        $pre_finder->files()->in('bin')->name('*runtests*');
        if ($pre_finder->hasResults()) {
            foreach ($pre_finder as $pre_file) {
                // there should only ever be one result, this is just for syntax
                $pre_file_hash = hash_file('md5', $pre_file);

            }
        }

        $post_finder = new Finder();
        // start of the process to check for test file upload

        $post_finder->files()->in('./public/uploads/files')->name('*dummy-file*');
        if ($post_finder->hasResults()) {
            foreach ($post_finder as $post_file) {
                $post_file_hash = hash_file('md5', $post_file);
                
            }
        }

        $this->assertNotSame($pre_file_hash, $post_file_hash);
    }
       
}


//EOF
