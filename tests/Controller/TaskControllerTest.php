<?php

namespace Tests\App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class TaskControllerTest extends WebTestCase
{
    public $user;
    private KernelBrowser|null $client = null;

    public function setUp() : void
    {
        $this->client = static::createClient();
    }
    public function testListAction()
    {
        $crawler = $this->client->request('GET', '/tasks/list/all');
        $this->assertResponseIsSuccessful();
    }

    public function testCreateAction()
    {
        $this->addUser();
        $this->client->loginUser($this->getUser());
        $crawler = $this->client->request('GET', '/tasks/create');
        $this->assertResponseIsSuccessful();

        $form = $crawler->selectButton('Ajouter')->form();
        $form['task[title]'] = 'Nouvelle tâche';
        $form['task[content]'] = 'Description de la nouvelle tâche';

        $this->client->submit($form);
        $this->assertResponseRedirects('/tasks/list/progress');

        $this->client->followRedirect();
        $this->assertSelectorTextContains('.alert-success', 'Superbe ! La tâche a été bien été ajoutée.');
        $this->assertSelectorTextContains('.caption', 'Nouvelle tâche');
    }

    public function testEditAction()
    {
        $this->client->loginUser($this->getUser());
        $crawler = $this->client->request('GET', '/tasks/list/progress');

        $link = $crawler->selectLink('Nouvelle tâche')->link();
        $crawler = $this->client->click($link);
        $this->assertResponseIsSuccessful();

        $form = $crawler->selectButton('Modifier')->form();
        $form['task[title]'] = 'Tâche modifié';
        $form['task[content]'] = 'Description modifiée';

        $this->client->submit($form);
        $this->assertResponseRedirects('/tasks/list/progress');

        $this->client->followRedirect();
        $this->assertSelectorTextContains('.alert-success', 'Superbe ! La tâche a bien été modifiée.');
        $this->assertSelectorTextContains('.caption', 'Tâche modifié');
    }

    public function testToggleTaskAction()
    {
        $this->client->loginUser($this->getUser());
        $crawler = $this->client->request('GET', '/tasks/list/progress');

        $form = $crawler->selectButton('Marquer comme faite')->form();
        $crawler = $this->client->click($form);
        $this->assertResponseRedirects('/tasks/list/ended');

        $this->client->followRedirect();
        $this->assertSelectorTextContains('.alert-success', 'La tâche Tâche modifié a bien été marquée faite.');
    }

    public function testDeleteTaskActionAuth()
    {
        $this->client->loginUser($this->getUser());
        $crawler = $this->client->request('GET', '/tasks/list/ended');
        $form = $crawler->selectButton('Supprimer')->form();
        $this->client->submit($form);
        $this->assertResponseRedirects('/tasks/list/ended');

        $this->client->followRedirect();
        $this->assertSelectorTextContains('.alert-success', 'Superbe ! La tâche a bien été supprimée.');
    }

    public function addUser()
    {
        $entityManager = $this->client->getContainer()->get('doctrine')->getManager();

        $user = new User();
        $user->setUsername('john_doe');
        $user->setPassword('password');
        $user->setEmail('john@example.fr');
        $user->setRoles(["ROLE_ADMIN"]);
        $entityManager->persist($user);
        $entityManager->flush();

        $this->user = $user;
    }

    public function getUser()
    {
        $userRepository = $this->client->getContainer()->get('doctrine.orm.entity_manager')->getRepository(User::class);
        $user = $userRepository->findOneByEmail('john@example.fr');

        return $user;
    }
}
