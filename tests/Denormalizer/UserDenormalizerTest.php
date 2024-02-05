<?php

namespace Tests\Unit\Denormalizer;

use PHPUnit\Framework\TestCase;
use App\Denormalizer\UserDenormalizer;
use App\Entity\Auth\User;
use Symfony\Component\PasswordHasher\Hasher\PasswordHasherFactoryInterface;
use Symfony\Component\PasswordHasher\PasswordHasherInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Bundle\SecurityBundle\Security;

class UserDenormalizerTest extends TestCase
{
    private $denormalizer;
    private $hasherFactory;
    private $hasher;
    private $normalizer;
    private $security;

    protected function setUp(): void
    {
        $this->hasherFactory = $this->createMock(PasswordHasherFactoryInterface::class);
        $this->hasher = $this->createMock(PasswordHasherInterface::class);
        $this->normalizer = $this->createMock(ObjectNormalizer::class);
        $this->security = $this->createMock(Security::class);

        $this->denormalizer = new UserDenormalizer($this->security, $this->hasherFactory, $this->normalizer);
    }

    public function testSupportsDenormalization()
    {
        $this->assertTrue($this->denormalizer->supportsDenormalization([], User::class));
        $this->assertFalse($this->denormalizer->supportsDenormalization([], \stdClass::class));
    }

    public function testDenormalize()
    {
        $plainPassword = 'plainPassword';
        $hashedPassword = 'hashedPassword';

        $user = new User();
        $user->setPlainPassword($plainPassword);

        $this->normalizer
            ->expects($this->once())
            ->method('denormalize')
            ->willReturn($user);

        $this->hasherFactory
            ->expects($this->once())
            ->method('getPasswordHasher')
            ->with($user)
            ->willReturn($this->hasher);

        $this->hasher
            ->expects($this->once())
            ->method('hash')
            ->with($plainPassword)
            ->willReturn($hashedPassword);

        $denormalizedUser = $this->denormalizer->denormalize([], User::class);

        $this->assertEquals($hashedPassword, $denormalizedUser->getPassword());
        $this->assertNull($denormalizedUser->getPlainPassword());
    }

    public function testDenormalizeWithoutPlainPassword()
    {
    $user = new User();

    $this->normalizer
        ->expects($this->once())
        ->method('denormalize')
        ->willReturn($user);

    $denormalizedUser = $this->denormalizer->denormalize([], User::class);

    $this->assertNull($denormalizedUser->getPlainPassword());
    $this->assertSame('', $denormalizedUser->getPassword()); // Updated assertion
    }   


    protected function tearDown(): void
    {
        unset($this->denormalizer, $this->hasherFactory, $this->hasher, $this->normalizer, $this->security);
    }
}
