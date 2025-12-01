<?php

declare(strict_types=1);

namespace App\Tests\API;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use App\Entity\Store;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Component\HttpFoundation\Response;

final class StoreApiTest extends ApiTestCase
{
    protected static ?bool $alwaysBootKernel = true;

    private EntityManagerInterface $entityManager;

    protected function setUp(): void
    {
        self::ensureKernelShutdown();

        $this->entityManager = self::getContainer()->get(EntityManagerInterface::class);

        $this->entityManager->createQuery('DELETE FROM App\Entity\Store s')
            ->execute();
        $this->entityManager->clear();
    }

    public function testCreateStore(): void
    {
        $payload = [
            'name' => 'Nouveau Magasin',
            'address' => '2 avenue de la République',
            'city' => 'Lyon',
            'postalCode' => '69001',
        ];

        $response = self::createClient()->request('POST', '/api/stores', [
            'json' => $payload,
        ]);

        self::assertResponseStatusCodeSame(Response::HTTP_CREATED);
        $data = $response->toArray();

        $this->assertArrayHasKey('id', $data);
        $this->assertSame($payload['name'], $data['name']);
        $this->assertSame($payload['city'], $data['city']);

        $store = $this->entityManager->getRepository(Store::class)->find($data['id']);
        $this->assertInstanceOf(Store::class, $store);
    }

    public function testShowStore(): void
    {
        $store = $this->createStore();

        $response = self::createClient()->request('GET', '/api/stores/' . $store->getId());

        self::assertResponseIsSuccessful();
        $data = $response->toArray();

        $this->assertSame((string) $store->getId(), $data['id']);
        $this->assertSame($store->getName(), $data['name']);
    }

    public function testUpdateStore(): void
    {
        $store = $this->createStore();
        $payload = [
            'name' => 'Magasin Modifié',
            'city' => 'Marseille',
        ];

        $response = self::createClient()->request('PATCH', '/api/stores/' . $store->getId(), [
            'json' => $payload,
        ]);

        self::assertResponseIsSuccessful();
        $data = $response->toArray();

        $this->assertSame('Magasin Modifié', $data['name']);
        $this->assertSame('Marseille', $data['city']);
    }

    public function testDeleteStore(): void
    {
        $store = $this->createStore();

        $response = self::createClient()->request('DELETE', '/api/stores/' . $store->getId());

        self::assertResponseStatusCodeSame(Response::HTTP_NO_CONTENT);
        $this->assertSame('', $response->getContent());

        $deleted = $this->entityManager->getRepository(Store::class)->find($store->getId());
        $this->assertNotInstanceOf(Store::class, $deleted);
    }

    /**
     * @param array<string, string> $filters
     * @param string[]              $expectedNames
     */
    #[DataProvider('provideIndexFiltersAndSorting')]
    public function testIndexWithFiltersAndSorting(array $filters, array $expectedNames, int $expectedCount): void
    {
        $this->createStore('Z-Mag', 'a');
        $this->createStore('A-Mag', 'b', 'Paris', '75002');
        $this->createStore('B-Mag', 'c', 'Lyon', '69001');

        $response = self::createClient()->request('GET', '/api/stores', [
            'query' => $filters,
        ]);

        self::assertResponseIsSuccessful();
        $data = $response->toArray();

        $this->assertCount($expectedCount, $data);
        $this->assertSame($expectedNames, array_column($data, 'name'));
    }

    /**
     * @return iterable<string, array{
     *     filters: array<string, string>,
     *     expectedNames: string[],
     *     expectedCount: int
     * }>
     */
    public static function provideIndexFiltersAndSorting(): iterable
    {
        yield 'Filter Paris + tri ASC' => [
            'filters' => [
                'city' => 'Paris',
                'sort' => 'name',
                'direction' => 'ASC',
            ],
            'expectedNames' => ['A-Mag', 'Z-Mag'],
            'expectedCount' => 2,
        ];

        yield 'Filter Paris + tri DESC' => [
            'filters' => [
                'city' => 'Paris',
                'sort' => 'name',
                'direction' => 'DESC',
            ],
            'expectedNames' => ['Z-Mag', 'A-Mag'],
            'expectedCount' => 2,
        ];

        yield 'Filter Lyon' => [
            'filters' => [
                'city' => 'Lyon',
            ],
            'expectedNames' => ['B-Mag'],
            'expectedCount' => 1,
        ];

        yield 'Without filter → sort by id' => [
            'filters' => [],
            'expectedNames' => ['Z-Mag', 'A-Mag', 'B-Mag'],
            'expectedCount' => 3,
        ];
    }

    public function testCreateStoreWithMissingFieldReturnsBadRequest(): void
    {
        $payload = [
            'address' => '2 avenue de la République',
            'city' => 'Lyon',
            'postalCode' => '69001',
        ];

        $response = self::createClient()->request('POST', '/api/stores', [
            'json' => $payload,
        ]);

        self::assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
        $data = $response->toArray(false);
        $this->assertArrayHasKey('error', $data);
        $this->assertIsString($data['error']);
        $this->assertStringContainsString('name', $data['error']);
    }

    private function createStore(
        string $name = 'Super Magasin',
        string $address = '1 rue de la Paix',
        string $city = 'Paris',
        string $postalCode = '75001'
    ): Store {
        $store = (new Store())
            ->setName($name)
            ->setAddress($address)
            ->setCity($city)
            ->setPostalCode($postalCode);

        $this->entityManager->persist($store);
        $this->entityManager->flush();

        return $store;
    }
}
