<?php

namespace App\State;

use ApiPlatform\State\ProcessorInterface;
use App\Entity\Establishment;
use App\Enum\EstablishmentStatusEnum;
use Doctrine\ORM\EntityManagerInterface;
use ApiPlatform\Metadata\Operation;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

final class EstablishmentStatusProcessor implements ProcessorInterface
{

    /**
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(readonly EntityManagerInterface $entityManager)
    {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): void
    {
        $status = $data->getStatus();
        /* In draft, only name is mandatory */
        if($status === EstablishmentStatusEnum::DRAFT->value) {
           $error = $this->checkName($data->getName());
           if(isset($error)) {
               throw new BadRequestHttpException($error, null, 400);
           }
        }

        if($status === EstablishmentStatusEnum::ACTIVE->value) {
            $errors = $this->getEstablishmentError($data);
            $stringifyErrors = implode(", ", $errors);

            if(count($errors) > 0) {
                throw new BadRequestHttpException($stringifyErrors, null, 400);
            }
        }

        $this->entityManager->persist($data);
        $this->entityManager->flush();

    }

    private function getEstablishmentError(Establishment $establishment):array
    {
        $errors = [];

        $nameError = $this->checkName($establishment->getName());
        $addressError = $this->checkAddress($establishment->getAddress());
        $emailError = $this->checkEmail($establishment->getEmail());
        $phoneError = $this->checkPhone($establishment->getPhone());
        $coverError = $this->checkCoverImage($establishment->getCover());
        $servicesError = $this->checkServices((array)$establishment->getServices());
        $barbersError = $this->checkBarbers((array)$establishment->getBarbers());

        if (isset($nameError)) {
           $errors[] = $nameError;
        }
        if (isset($addressError)) {
           $errors[] = $addressError;
        }
        if (isset($emailError)) {
           $errors[] = $emailError;
        }
        if (isset($phoneError)) {
           $errors[] = $phoneError;
        }
        if (isset($coverError)) {
           $errors[] = $coverError;
        }
        if (isset($servicesError)) {
           $errors[] = $servicesError;
        }
        if (isset($barbersError)) {
           $errors[] = $barbersError;
        }

        return $errors;
    }
    private function checkName($name): ?string {
        if(!isset($name)) {
            return "Missing establishment name";
        }
        return null;
    }

    private function checkAddress($address): ?string {
        if(!isset($address)) {
            return "Missing establishment address";
        }
        return null;
    }

    private function checkEmail($email): ?string {
        if(!isset($email)) {
            return "Missing establishment email";
        }
        return null;
    }

    private function checkPhone($phone): ?string {
        if(!isset($phone)) {
            return "Missing establishment phone";
        }
        return null;
    }

    private function checkServices(array $services): ?string {
        if(!isset($services) && count($services) === 0) {
            return "The establishment needs at least one service";
        }
        return null;
    }

    private function checkBarbers(array $barbers): ?string {
        if(!isset($barbers) && count($barbers) === 0) {
            return "The establishment needs at least one barber";
        }
        return null;
    }

    private function checkCoverImage($coverImage): ?string {
        if(!isset($coverImage)) {
            return "Missing establishment cover image";
        }
        return null;
    }

}