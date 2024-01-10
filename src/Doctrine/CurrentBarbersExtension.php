<?php

namespace App\Doctrine;

use ApiPlatform\Doctrine\Orm\Extension\QueryCollectionExtensionInterface;
use ApiPlatform\Doctrine\Orm\Extension\QueryItemExtensionInterface;
use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Metadata\Operation;
use App\Entity\Appointment;
use App\Entity\Barber;
use App\Enum\RolesEnum;
use App\Repository\UserRepository;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bundle\SecurityBundle\Security;

final readonly class CurrentBarbersExtension implements QueryCollectionExtensionInterface, QueryItemExtensionInterface
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
        $isBarber = $this->security->isGranted('ROLE_BARBER');

        if (
            Barber::class !== $resourceClass || (!$isProvider && !$isBarber)) {
            return;
        }

        $rootAlias = $queryBuilder->getRootAliases()[0];

        if($isBarber) {
            $queryBuilder->andWhere(sprintf('%s.user = :current_user', $rootAlias));
            $queryBuilder->setParameter('current_user', $user);
            return;
        }

        $queryBuilder->leftJoin(sprintf('%s.establishment', $rootAlias), 'e');
        $queryBuilder->leftJoin(sprintf('%s.provider', $rootAlias), 'p');
        $queryBuilder->andWhere($queryBuilder->expr()->orX(
            $queryBuilder->expr()->isNull('e.id'),
            $queryBuilder->expr()->eq('p.user', ':current_user')
        ));
        $queryBuilder->setParameter('current_user', $user);
    }
}