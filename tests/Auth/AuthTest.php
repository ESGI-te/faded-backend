<?php

namespace Tests\Unit\Entity\Auth;

use PHPUnit\Framework\TestCase;
use App\Entity\Auth\User;
use App\Enum\UserAccountTypeEnum;

class AuthTest extends TestCase
{
    private $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = new User();
    }

    public function testIdIsGenerated()
    {
        $this->assertNotNull($this->user->getId());
    }

    public function testRoles()
    {
        $roles = ['ROLE_USER', 'ROLE_ADMIN'];
        $this->user->setRoles($roles);
        $this->assertEquals($roles, $this->user->getRoles());
    }

    public function testAccountType()
    {
        $this->user->setAccountType(UserAccountTypeEnum::ADMIN->value);
        $this->assertEquals(['ROLE_ADMIN'], $this->user->getRoles());    
        $this->user->setAccountType(UserAccountTypeEnum::USER->value);
        $this->assertEquals(['ROLE_USER'], $this->user->getRoles());
    }


    protected function tearDown(): void
    {
        unset($this->user);
    }
}
