<?php

namespace Tests\App\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DefaultControllerTest extends WebTestCase
{
    public static function setUpBeforeClass(): void
    {
        exec('php bin/console doctrine:database:create --env=test');
        exec('php bin/console doctrine:migrations:migrate --env=test --no-interaction');
    }

    public function testIndex()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertSelectorTextContains('a', 'To Do List app');
    }
}
