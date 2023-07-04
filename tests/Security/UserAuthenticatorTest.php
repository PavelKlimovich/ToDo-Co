<?php

namespace App\Tests\Security;

use App\Security\UserAuthenticator;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\CsrfTokenBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Util\TargetPathTrait;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class UserAuthenticatorTest extends TestCase
{
    private UserAuthenticator $userAuthenticator;
    private UrlGeneratorInterface $urlGenerator;
    private SessionInterface $session;

    protected function setUp(): void
    {
        $this->urlGenerator = $this->createMock(UrlGeneratorInterface::class);
        $this->session = $this->createMock(SessionInterface::class);
        $this->userAuthenticator = new UserAuthenticator($this->urlGenerator);
    }

    public function testAuthenticate(): void
    {
        $request = new Request();
        $request->setSession($this->session);
        $request->request->set('_username', 'test@example.com');
        $request->request->set('_password', 'password');
        $request->request->set('_csrf_token', 'valid_token');

        $passport = $this->userAuthenticator->authenticate($request);

        $this->assertInstanceOf(Passport::class, $passport);
        $badges = $passport->getBadges();
        $this->assertCount(3, $badges);
        $this->assertInstanceOf(UserBadge::class, array_values($badges)[0]);
        $this->assertInstanceOf(PasswordCredentials::class, array_values($badges)[1]);
        $this->assertInstanceOf(CsrfTokenBadge::class, array_values($badges)[2]);
        $this->assertEquals('test@example.com', array_values($badges)[0]->getUserIdentifier());
        $this->assertEquals('valid_token', array_values($badges)[2]->getCsrfToken());
    }

    public function testOnAuthenticationSuccessWithTargetPath(): void
    {
        $request = new Request();
        $request->setSession($this->session);

        $targetPath = '/';
        $this->session->method('get')->willReturn($targetPath);

        $response = $this->userAuthenticator->onAuthenticationSuccess($request, $this->createMock(TokenInterface::class), 'main');

        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertEquals($targetPath, $response->getTargetUrl());
    }

    public function testOnAuthenticationSuccessWithoutTargetPath(): void
    {
        $request = new Request();
        $request->setSession($this->session);
        $token = $this->createMock(TokenInterface::class);
        $firewallName = 'main';

        $this->urlGenerator->expects($this->once())
            ->method('generate')
            ->with('homepage')
            ->willReturn('/');

        $response = $this->userAuthenticator->onAuthenticationSuccess($request, $token, $firewallName);

        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertEquals('/', $response->getTargetUrl());
    }
}
