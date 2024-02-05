<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Security\Core\User\UserInterface;

class UserInfoControllerTest extends WebTestCase
{
    public function testInvoke()
{
    $client = static::createClient();
    $testUser = $this->createMock(UserInterface::class);

    $roles = ['ROLE_USER']; 

    $token = new \Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken($testUser, 'main', $roles);
    $client->getContainer()->get('security.token_storage')->setToken($token);

    $client->request('GET', '/api/auth/user');

    $this->assertResponseIsSuccessful();
}

}
