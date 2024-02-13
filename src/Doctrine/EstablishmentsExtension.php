<?php

namespace App\Doctrine;

use ApiPlatform\Doctrine\Orm\Extension\QueryCollectionExtensionInterface;
use ApiPlatform\Doctrine\Orm\Extension\QueryItemExtensionInterface;
use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Metadata\Operation;
use App\Entity\Appointment;
use App\Entity\Barber;
use App\Entity\Establishment;
use App\Enum\EstablishmentStatusEnum;
use App\Enum\RolesEnum;
use App\Repository\UserRepository;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bundle\SecurityBundle\Security;

final readonly class EstablishmentsExtension implements QueryCollectionExtensionInterface, QueryItemExtensionInterface
{
    public function __construct(private Security $security)
    {
    }

    public function applyToCollection(QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass, Operation $operation = null, array $context = []): void
    {
        $this->addWhere($queryBuilder, $resourceClass);
    }

    public function applyToItem(QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass, array $identifiers, Operation $operation = null, array $context = []): void
    {
        $this->addWhere($queryBuilder, $resourceClass);
    }

    private function addWhere(QueryBuilder $queryBuilder, string $resourceClass): void
    {
        $user = $this->security->getUser();
        $isProvider = $this->security->isGranted('ROLE_PROVIDER');

        if (
            Establishment::class !== $resourceClass) {
            return;
        }

        $rootAlias = $queryBuilder->getRootAliases()[0];

        if($isProvider) {
            $queryBuilder->leftJoin(sprintf('%s.provider', $rootAlias), 'p');
            $queryBuilder->andWhere('p.user = :current_user');
            $queryBuilder->setParameter('current_user', $user);
            return;
        }

        if(!$user) {
            $queryBuilder->andWhere(sprintf('%s.status = :active_status',  $rootAlias));
            $queryBuilder->setParameter('active_status', EstablishmentStatusEnum::ACTIVE->value);
        }
    }
}