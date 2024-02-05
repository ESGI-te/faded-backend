<?php

namespace Tests\Unit\Serializer;

use PHPUnit\Framework\TestCase;
use App\Serializer\AppointmentPlainIdentifierDenormalizer;
use ApiPlatform\Api\IriConverterInterface;
use App\Entity\Appointment;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

class AppointmentPlainIdentifierDenormalizerTest extends TestCase
{
    private $denormalizer;
    private $iriConverter;

    protected function setUp(): void
    {
        $this->iriConverter = $this->createMock(IriConverterInterface::class);
        $this->denormalizer = new AppointmentPlainIdentifierDenormalizer($this->iriConverter);
    }

    public function testSupportsDenormalization()
    {
        $data = ['establishment' => 'some_value'];
        $this->assertTrue($this->denormalizer->supportsDenormalization($data, Appointment::class, 'json'));
    }

    public function testDenormalize()
    {
        $data = ['barber' => '1', 'establishment' => '2', 'service' => '3'];
        $expectedData = [
            'barber' => '/barbers/1',
            'establishment' => '/establishments/2',
            'service' => '/services/3'
        ];

        $this->iriConverter
            ->method('getIriFromResource')
            ->willReturnCallback(function ($resource, $context) {
                return '/' . strtolower($resource::class) . '/' . $context['uri_variables']['id'];
            });

        $denormalizedData = $this->denormalizer->denormalize($data, Appointment::class, 'json');

        $this->assertEquals($expectedData, $denormalizedData);
    }


    protected function tearDown(): void
    {
        unset($this->iriConverter, $this->denormalizer);
    }
}
