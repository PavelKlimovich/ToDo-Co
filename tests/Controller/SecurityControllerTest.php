<?php

namespace Tests\App\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SecurityControllerTest extends WebTestCase
{
    public function testLoginAction()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/login');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('form[action="/login"]');
        $this->assertSelectorNotExists('.alert-danger');
    }
}
