<?php

namespace Tests\Unit\Entity\Auth;

use PHPUnit\Framework\TestCase;
use App\Entity\Auth\User;
use App\Entity\Appointment;
use App\Entity\Feedback;

class UserTest extends TestCase
{
    private $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = new User();
    }

    public function testEmail()
    {
        $email = 'test@example.com';
        $this->user->setEmail($email);
        $this->assertEquals($email, $this->user->getEmail());
    }

    public function testFirstName()
    {
        $firstName = 'John';
        $this->user->setFirstname($firstName);
        $this->assertEquals($firstName, $this->user->getFirstname());
    }

    public function testLastName()
    {
        $lastName = 'Doe';
        $this->user->setLastName($lastName);
        $this->assertEquals($lastName, $this->user->getLastName());
    }

    public function testLocale()
    {
        $locale = 'en';
        $this->user->setLocale($locale);
        $this->assertEquals($locale, $this->user->getLocale());
    }

    public function testAppointments()
    {
        $appointment = $this->createMock(Appointment::class);
        $this->user->addAppointment($appointment);

        $this->assertCount(1, $this->user->getAppointments());
        $this->assertTrue($this->user->getAppointments()->contains($appointment));

        $this->user->removeAppointment($appointment);
        $this->assertCount(0, $this->user->getAppointments());
    }

    public function testFeedback()
    {
        $feedback = $this->createMock(Feedback::class);
        $this->user->addFeedback($feedback);

        $this->assertCount(1, $this->user->getFeedback());
        $this->assertTrue($this->user->getFeedback()->contains($feedback));

        $this->user->removeFeedback($feedback);
        $this->assertCount(0, $this->user->getFeedback());
    }


    protected function tearDown(): void
    {
        unset($this->user);
    }
}
