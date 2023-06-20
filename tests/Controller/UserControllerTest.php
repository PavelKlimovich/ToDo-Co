<?php 

namespace Tests\App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UserControllerTest extends WebTestCase
{
    public function testListAction()
    {
        $client = static::createClient();
        $client->request('GET', '/users');

        $this->assertResponseRedirects('/login');
    }

    public function testCreateAction()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/users/create');

        $this->assertResponseIsSuccessful();

        $form = $crawler->filter('form[name=user]')->form([
            'user[username]'            => 'John Doe',
            'user[email]'               => 'john@example.com',
            'user[password][first]'     => 'password123',
            'user[password][second]'    => 'password123',
            'user[roles]'               => 'ROLE_ADMIN',
        ]);

        $client->submit($form);

        $this->assertResponseRedirects('/');
        $client->followRedirect();

        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('.alert-success');
    }

    public function testEditAction()
    {
        $client = static::createClient();
        $entityManager = $client->getContainer()->get('doctrine')->getManager();
        $userRepository = $entityManager->getRepository(User::class);
        $user = $userRepository->find(2);
        $client->loginUser($user);

        $crawler = $client->request('GET', '/users/2/edit');

        $this->assertResponseIsSuccessful();

        $form = $crawler->filter('form[name=user]')->form([
            'user[username]'            => 'Updated Name',
            'user[email]'               => 'updated@example.com',
            'user[password][first]'     => 'newpassword',
            'user[password][second]'    => 'newpassword',
        ]);

        $client->submit($form);

        $this->assertResponseRedirects('/users');
        $client->followRedirect();

        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('.alert-success');
    }

    public function testListActionIfAuth()
    {
        $client = static::createClient();
        $entityManager = $client->getContainer()->get('doctrine')->getManager();
        $userRepository = $entityManager->getRepository(User::class);
        $user = $userRepository->find(2);
        $client->loginUser($user);

        $client->request('GET', '/users');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Liste des utilisateurs');
    }
}
