<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Store;
use App\Repository\StoreRepository;

final readonly class StoreSearch
{
    private const ALLOWED_SORTS = ['name', 'city', 'createdAt', 'updatedAt'];

    public function __construct(
        private StoreRepository $storeRepository,
    ) {
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
    public function searchStoresByFilters(array $filters, string $sort, ?string $direction = 'ASC'): array
    {
        $direction = strtoupper($direction ?? 'ASC');
        if (! in_array($direction, ['ASC', 'DESC'], true)) {
            $direction = 'ASC';
        }

        if (! in_array($sort, self::ALLOWED_SORTS, true)) {
            $sort = 'id';
        }

        return $this->storeRepository->searchWithFilters($filters, $sort, $direction);
    }
}
