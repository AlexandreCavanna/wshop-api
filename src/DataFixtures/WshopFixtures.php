<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Story\StoreStory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class WshopFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $storeStory = new StoreStory();
        $storeStory->build();

        $manager->flush();
    }
}
