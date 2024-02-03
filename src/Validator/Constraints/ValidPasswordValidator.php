<?php

namespace App\Validator\Constraints;

use App\Repository\UserRepository;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\PasswordHasher\Hasher\PasswordHasherFactoryInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class ValidPasswordValidator extends ConstraintValidator
{
    private Security $security;
    private PasswordHasherFactoryInterface $passwordHasher;
    private UserRepository $userRepository;

    public function __construct(protected PasswordHasherFactoryInterface $hasher, Security $security, UserRepository $userRepository)
    {
        $this->security = $security;
        $this->passwordHasher = $hasher;
        $this->userRepository = $userRepository;
    }

        public function validate($value, Constraint $constraint): void
    {
        dump('chakib google');
        $user = $this->userRepository->find($this->security->getUser());
        $hasher = $this->passwordHasher->getPasswordHasher($user);
        $hashedPassword = $hasher->hash($value);
        $currentPassword = $user->getPassword();
        dump($hashedPassword, $currentPassword);
        if ($hashedPassword !== $currentPassword) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ value }}', $value)
                ->addViolation();
        }
    }
}