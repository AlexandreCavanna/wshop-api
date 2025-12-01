<?php

declare(strict_types=1);

namespace App\Tests\Unit;

use App\Entity\Store;
use App\Repository\StoreRepository;
use App\Service\StoreSearch;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class StoreSearchTest extends TestCase
{
    /**
     * @var StoreRepository&MockObject
     */
    private \PHPUnit\Framework\MockObject\MockObject $storeRepository;

    private StoreSearch $storeSearch;

    protected function setUp(): void
    {
        parent::setUp();

        $this->storeRepository = $this->createMock(StoreRepository::class);
        $this->storeSearch = new StoreSearch($this->storeRepository);
    }

    public function testSearchStoresByFiltersWithValidSortAndDirection(): void
    {
        $filters = [
            'city' => 'Paris',
            'name' => 'Wshop',
            'postalCode' => '75001',
        ];

        $expectedStores = [$this->createMock(Store::class)];

        $this->storeRepository
            ->expects($this->once())
            ->method('searchWithFilters')
            ->with($filters, 'name', 'ASC')
            ->willReturn($expectedStores);

        $result = $this->storeSearch->searchStoresByFilters($filters, 'name');

        $this->assertSame($expectedStores, $result);
    }

    public function testSearchStoresByFiltersFallsBackToAscOnInvalidDirection(): void
    {
        $filters = [];

        $this->storeRepository
            ->expects($this->once())
            ->method('searchWithFilters')
            ->with($filters, 'city', 'ASC')
            ->willReturn([]);

        $this->storeSearch->searchStoresByFilters($filters, 'city', 'INVALID');
    }

    public function testSearchStoresByFiltersDefaultsDirectionToAscWhenNull(): void
    {
        $filters = [];

        $this->storeRepository
            ->expects($this->once())
            ->method('searchWithFilters')
            ->with($filters, 'city', 'ASC')
            ->willReturn([]);

        $this->storeSearch->searchStoresByFilters($filters, 'city', null);
    }

    public function testSearchStoresByFiltersFallsBackToIdOnInvalidSort(): void
    {
        $filters = [];

        $this->storeRepository
            ->expects($this->once())
            ->method('searchWithFilters')
            ->with($filters, 'id', 'ASC')
            ->willReturn([]);

        $this->storeSearch->searchStoresByFilters($filters, 'not_a_field');
    }

    public function testSearchStoresByFiltersKeepsAllowedSort(): void
    {
        $filters = [];

        $this->storeRepository
            ->expects($this->once())
            ->method('searchWithFilters')
            ->with($filters, 'createdAt', 'ASC')
            ->willReturn([]);

        $this->storeSearch->searchStoresByFilters($filters, 'createdAt');
    }
}
