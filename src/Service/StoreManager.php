<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Store;
use Doctrine\ORM\EntityManagerInterface;
use InvalidArgumentException;

final readonly class StoreManager
{
    private const ALLOWED_FIELDS = ['name', 'address', 'city', 'postalCode'];

    public function __construct(
        private EntityManagerInterface $entityManager,
    ) {
    }

    /**
     * @param array{
     *     name?: string,
     *     address?: string,
     *     city?: string,
     *     postalCode?: string
     * } $data
     */
    public function createStore(array $data): Store
    {
        $this->assertRequiredFields($data);

        /** @var array{
         *     name: string,
         *     address: string,
         *     city: string,
         *     postalCode: string
         * } $data
         */
        $store = (new Store())
            ->setName($data['name'])
            ->setAddress($data['address'])
            ->setCity($data['city'])
            ->setPostalCode($data['postalCode']);

        $this->entityManager->persist($store);
        $this->entityManager->flush();

        return $store;
    }

    /**
     * @param array{
     *     name?: string,
     *     address?: string,
     *     city?: string,
     *     postalCode?: string
     * } $data
     */
    public function updateStore(Store $store, array $data): Store
    {
        if (array_key_exists('name', $data)) {
            $store->setName($data['name']);
        }

        if (array_key_exists('address', $data)) {
            $store->setAddress($data['address']);
        }

        if (array_key_exists('city', $data)) {
            $store->setCity($data['city']);
        }

        if (array_key_exists('postalCode', $data)) {
            $store->setPostalCode($data['postalCode']);
        }

        $this->entityManager->flush();

        return $store;
    }

    public function deleteStore(Store $store): void
    {
        $this->entityManager->remove($store);
        $this->entityManager->flush();
    }

    /**
     * @param array<string,mixed> $data
     */
    private function assertRequiredFields(array $data): void
    {
        foreach (self::ALLOWED_FIELDS as $field) {
            if (empty($data[$field])) {
                throw new InvalidArgumentException(sprintf('Field "%s" is required', $field));
            }
        }
    }
}
