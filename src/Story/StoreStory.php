<?php

declare(strict_types=1);

namespace App\Story;

use App\Factory\StoreFactory;
use Zenstruck\Foundry\Attribute\AsFixture;
use Zenstruck\Foundry\Story;

#[AsFixture(name: 'store')]
final class StoreStory extends Story
{
    public function build(): void
    {
        StoreFactory::createMany(15);
    }
}
