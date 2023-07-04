<?php

namespace Tests\App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class TaskControllerTest extends WebTestCase
{
    public function testListAction()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/tasks');
        $this->assertResponseIsSuccessful();
    }

    public function testCreateAction()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/tasks/create');
        $this->assertResponseIsSuccessful();

        $form = $crawler->selectButton('Ajouter')->form();
        $form['task[title]'] = 'Nouvelle tâche';
        $form['task[content]'] = 'Description de la nouvelle tâche';

        $client->submit($form);
        $this->assertResponseRedirects('/tasks');

        $client->followRedirect();
        $this->assertSelectorTextContains('.alert-success', 'Superbe ! La tâche a été bien été ajoutée.');
        $this->assertSelectorTextContains('.caption', 'Nouvelle tâche');
    }

    public function testEditAction()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/tasks');

        $link = $crawler->selectLink('Nouvelle tâche')->link();
        $crawler = $client->click($link);
        $this->assertResponseIsSuccessful();

        $form = $crawler->selectButton('Modifier')->form();
        $form['task[title]'] = 'Tâche modifié';
        $form['task[content]'] = 'Description modifiée';

        $client->submit($form);
        $this->assertResponseRedirects('/tasks');

        $client->followRedirect();
        $this->assertSelectorTextContains('.alert-success', 'Superbe ! La tâche a bien été modifiée.');
        $this->assertSelectorTextContains('.caption', 'Tâche modifié');
    }

    public function testToggleTaskAction()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/tasks');

        $form = $crawler->selectButton('Marquer comme faite')->form();
        $crawler = $client->click($form);
        $this->assertResponseRedirects('/tasks');

        $client->followRedirect();
        $this->assertSelectorTextContains('.alert-success', 'La tâche Tâche modifié a bien été marquée comme faite.');
    }

    public function testDeleteTaskActionAuth()
    {
        $client = $this->addUser();
        $crawler = $client->request('GET', '/tasks');
        $form = $crawler->selectButton('Supprimer')->form();
        $client->submit($form);
        $this->assertResponseRedirects('/tasks');

        $client->followRedirect();
        $this->assertSelectorTextContains('.alert-success', 'Superbe ! La tâche a bien été supprimée.');
    }

    public function addUser()
    {
        $client = static::createClient();
        $entityManager = $client->getContainer()->get('doctrine')->getManager();

        $user = new User();
        $user->setUsername('john_doe');
        $user->setPassword('password');
        $user->setEmail('john@example.fr');
        $user->setRoles(["ROLE_ADMIN"]);
        $entityManager->persist($user);
        $entityManager->flush();

        $client->loginUser($user);

        return $client;
    }
}
