<?php

declare(strict_types=1);

namespace App\Tests\Integration;

use App\Factory\StoreFactory;
use App\Repository\StoreRepository;
use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Zenstruck\Foundry\Test\Factories;

final class StoreRepositoryTest extends KernelTestCase
{
    use Factories;

    /**
     * @param array{
     *     city?: string|null,
     *     name?: string|null,
     *     postalCode?: string|null
     * } $filters
     */
    #[DataProvider('filtersProvider')]
    public function testSearchWithFilters(array $filters, int $expectedCount): void
    {
        StoreFactory::createOne([
            'name' => 'Paris Shop 1',
            'city' => 'Paris',
            'postalCode' => '75001',
        ]);

        StoreFactory::createOne([
            'name' => 'Paris Boutique',
            'city' => 'Paris',
            'postalCode' => '75002',
        ]);

        StoreFactory::createOne([
            'name' => 'Lyon Shop',
            'city' => 'Lyon',
            'postalCode' => '69000',
        ]);

        StoreFactory::createOne([
            'name' => 'Shop Nice',
            'city' => 'Nice',
            'postalCode' => '06000',
        ]);

        StoreFactory::createOne([
            'name' => 'MegaStore',
            'city' => 'Lyon',
            'postalCode' => '69001',
        ]);

        $storeRepository = self::getContainer()->get(StoreRepository::class);

        $results = $storeRepository->searchWithFilters($filters);

        $this->assertCount($expectedCount, $results, 'Filters: ' . json_encode($filters, JSON_THROW_ON_ERROR));
    }

    public static function filtersProvider(): \Iterator
    {
        yield 'no filters returns all' => [[], 5];
        yield 'filter by exact city' => [
            [
                'city' => 'Paris',
            ],
            2,
        ];
        yield 'filter by partial name' => [
            [
                'name' => 'Shop',
            ],
            3,
        ];
        yield 'filter by postal code' => [
            [
                'postalCode' => '75001',
            ],
            1,
        ];
        yield 'city + name combined' => [
            [
                'city' => 'Paris',
                'name' => 'Shop',
            ],
            1,
        ];
        yield 'city + postal code combined' => [
            [
                'city' => 'Paris',
                'postalCode' => '75001',
            ],
            1,
        ];
    }
}
