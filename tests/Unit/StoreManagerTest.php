<?php

declare(strict_types=1);

namespace App\Tests\Unit;

use App\Entity\Store;
use App\Service\StoreManager;
use Doctrine\ORM\EntityManagerInterface;
use InvalidArgumentException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class StoreManagerTest extends TestCase
{
    /**
     * @var EntityManagerInterface&MockObject
     */
    private \PHPUnit\Framework\MockObject\MockObject $entityManager;

    private StoreManager $storeManager;

    protected function setUp(): void
    {
        parent::setUp();

        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->storeManager = new StoreManager($this->entityManager);
    }

    public function testCreate(): void
    {
        $data = [
            'name' => 'Wshop',
            'address' => '1 rue de la Paix',
            'city' => 'Paris',
            'postalCode' => '75001',
        ];

        $persistedStore = null;

        $this->entityManager
            ->expects($this->once())
            ->method('persist')
            ->with(self::callback(static function (Store $store) use (&$persistedStore): bool {
                $persistedStore = $store;

                return true;
            }));

        $this->entityManager
            ->expects($this->once())
            ->method('flush');

        $store = $this->storeManager->createStore($data);

        $this->assertSame('Wshop', $store->getName());
        $this->assertSame('1 rue de la Paix', $store->getAddress());
        $this->assertSame('Paris', $store->getCity());
        $this->assertSame('75001', $store->getPostalCode());
        $this->assertSame($persistedStore, $store);
    }

    public function testCreateThrowsWhenRequiredFieldIsMissing(): void
    {
        $data = [
            'address' => '1 rue de la Paix',
            'city' => 'Paris',
            'postalCode' => '75001',
        ];

        $this->entityManager
            ->expects($this->never())
            ->method('persist');

        $this->entityManager
            ->expects($this->never())
            ->method('flush');

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Field "name" is required');

        $this->storeManager->createStore($data);
    }

    public function testUpdate(): void
    {
        $store = (new Store())
            ->setName('Old name')
            ->setAddress('Old address')
            ->setCity('Old city')
            ->setPostalCode('00000');

        $data = [
            'name' => 'New name',
            'city' => 'New city',
        ];

        $this->entityManager
            ->expects($this->never())
            ->method('persist');

        $this->entityManager
            ->expects($this->once())
            ->method('flush');

        $updated = $this->storeManager->updateStore($store, $data);

        $this->assertSame($store, $updated, 'The same Store object must be returned.');
        $this->assertSame('New name', $updated->getName());
        $this->assertSame('New city', $updated->getCity());
        $this->assertSame('Old address', $updated->getAddress());
        $this->assertSame('00000', $updated->getPostalCode());
    }

    public function testDelete(): void
    {
        $store = new Store();

        $this->entityManager
            ->expects($this->once())
            ->method('remove')
            ->with($store);

        $this->entityManager
            ->expects($this->once())
            ->method('flush');

        $this->storeManager->deleteStore($store);

        $this->addToAssertionCount(1);
    }
}
