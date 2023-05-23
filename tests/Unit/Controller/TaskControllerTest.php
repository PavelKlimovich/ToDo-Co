<?php

namespace Tests\App\Unit\Controller;

use App\Controller\TaskController;
use App\Entity\Task;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class TaskControllerTest extends WebTestCase
{
    public function testListAction()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/tasks');

        $this->assertEquals(Response::HTTP_OK, $client->getResponse()->getStatusCode());
    }
}

