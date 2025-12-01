<?php

declare(strict_types=1);

namespace App\Factory;

use App\Entity\Store;
use Zenstruck\Foundry\Persistence\PersistentObjectFactory;

/**
 * @extends PersistentObjectFactory<Store>
 */
final class StoreFactory extends PersistentObjectFactory
{
    #[\Override]
    public static function class(): string
    {
        return Store::class;
    }

    #[\Override]
    protected function defaults(): array
    {
        /** @var string $words */
        $words = self::faker()->words(2, true);

        /** @var string $suffix */
        $suffix = self::faker()->randomElement(['Shop', 'Store', 'Market', 'Boutique']);

        return [
            'address' => self::faker()->address(),
            'city' => self::faker()->city(),
            'name' => sprintf('%s %s', $words, $suffix),
            'postalCode' => self::faker()->postcode(),
        ];
    }
}
