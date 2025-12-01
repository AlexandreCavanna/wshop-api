<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Store;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Store>
 */
class StoreRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Store::class);
    }

    /**
     * @param array{
     *     city?: string|null,
     *     name?: string|null,
     *     postalCode?: string|null
     * } $filters
     *
     * @return Store[]
     */
    public function searchWithFilters(array $filters = [], ?string $sort = null, ?string $direction = null): array
    {
        $queryBuilder = $this->createQueryBuilder('s');

        if (isset($filters['city'])) {
            $queryBuilder->andWhere('s.city = :city')
                ->setParameter('city', $filters['city']);
        }

        if (isset($filters['name'])) {
            $queryBuilder->andWhere('s.name LIKE :name')
                ->setParameter('name', '%' . $filters['name'] . '%');
        }

        if (isset($filters['postalCode'])) {
            $queryBuilder->andWhere('s.postalCode = :postalCode')
                ->setParameter('postalCode', $filters['postalCode']);
        }

        $allowedSorts = ['id', 'name', 'city', 'createdAt', 'updatedAt'];

        if (! in_array($sort, $allowedSorts, true)) {
            $sort = 'id';
        }

        $direction = strtoupper($direction ?? 'ASC');
        if (! in_array($direction, ['ASC', 'DESC'], true)) {
            $direction = 'ASC';
        }

        $queryBuilder->orderBy('s.' . $sort, $direction);

        /** @var Store[] $result */
        $result = $queryBuilder->getQuery()
            ->getResult();

        return $result;
    }
}
