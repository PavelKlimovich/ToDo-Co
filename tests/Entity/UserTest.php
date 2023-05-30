<?php

namespace Tests\App\Entity;

use PHPUnit\Framework\TestCase;
use App\Entity\User;

class UserTest extends TestCase
{
    public function testSetUsername()
    {
        $user = new User();
        $username = "test_username";
        
        $user->setUsername($username);
        
        $this->assertEquals($username, $user->getUsername());
    }
    
    public function testSetEmail()
    {
        $user = new User();
        $email = "test@example.com";
        
        $user->setEmail($email);
        
        $this->assertEquals($email, $user->getEmail());
    }
    
    public function testGetUserIdentifier()
    {
        $user = new User();
        $email = "test@example.com";
        $user->setEmail($email);
        
        $userIdentifier = $user->getUserIdentifier();
        
        $this->assertEquals($email, $userIdentifier);
    }
    
    public function testGetRoles()
    {
        $user = new User();
        $userRoles = ["ROLE_ADMIN", "ROLE_USER"];
        $user->setRoles($userRoles);
        
        $roles = $user->getRoles();
        
        $this->assertContains("ROLE_ADMIN", $roles);
        $this->assertContains("ROLE_USER", $roles);
    }
    
    public function testSetPassword()
    {
        $user = new User();
        $password = "test_password";
        
        $user->setPassword($password);
        
        $this->assertEquals($password, $user->getPassword());
    }
}
