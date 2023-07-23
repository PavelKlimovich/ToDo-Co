<?php

namespace Tests\App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UserControllerTest extends WebTestCase
{

    private KernelBrowser|null $client = null;

    public function setUp() : void
    {
        $this->client = static::createClient();
    }

    public function testListAction()
    {
        $this->client->request('GET', '/users');
        $this->assertResponseRedirects('/login');
    }

    public function testCreateAction()
    {
        $crawler = $this->client->request('GET', '/users/create');
        $this->assertResponseIsSuccessful();
        
        $form = $crawler->filter('form[name=user]')->form([
            'user[username]'            => 'John Doe',
            'user[email]'               => 'john@example.com',
            'user[password][first]'     => '$2y$04$feLHYwU5ZHIgP4B1Gkgd1efJCLpwXbjrimvIuSipb9M/Y6nt2RVeq',
            'user[password][second]'    => '$2y$04$feLHYwU5ZHIgP4B1Gkgd1efJCLpwXbjrimvIuSipb9M/Y6nt2RVeq',
            'user[roles]'               => 'ROLE_ADMIN',
        ]);

        $this->client->submit($form);
        
        $this->assertResponseRedirects('/');
        $this->client->followRedirect();
        
        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('.alert-success');
    }

    public function testEditAction()
    {
        $entityManager = $this->client->getContainer()->get('doctrine')->getManager();
        $userRepository = $entityManager->getRepository(User::class);
        $user = $userRepository->findOneByEmail('john@example.com');
        $this->client->loginUser($user);

        $crawler = $this->client->request('GET', '/users/'.$user->getId().'/edit');

        $this->assertResponseIsSuccessful();

        $form = $crawler->filter('form[name=user]')->form([
            'user[username]'            => 'Updated Name',
            'user[email]'               => 'updated@example.com',
            'user[password][first]'     => '$2y$04$feLHYwU5ZHIgP4B1Gkgd1efJCLpwXbjrimvIuSipb9M/Y6nt2RVeq',
            'user[password][second]'    => '$2y$04$feLHYwU5ZHIgP4B1Gkgd1efJCLpwXbjrimvIuSipb9M/Y6nt2RVeq',
        ]);

        $this->client->submit($form);

        $this->assertResponseRedirects('/users');
        $this->client->followRedirect();

        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('.alert-success');
    }

    public function testListActionIfAuth()
    {
        $entityManager = $this->client->getContainer()->get('doctrine')->getManager();
        $userRepository = $entityManager->getRepository(User::class);
        $user = $userRepository->findOneByEmail('updated@example.com');
        $this->client->loginUser($user);

        $this->client->request('GET', '/users');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Liste des utilisateurs');
    }
}
