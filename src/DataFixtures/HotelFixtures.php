<?php

namespace App\DataFixtures;

use App\Entity\Hotel;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class HotelFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        for($i = 1; $i <= 10; $i++) {
            $hotel = (new Hotel())
                ->setName("Hotel $i");
            $manager->persist($hotel);
        }

        $manager->flush();
    }
}
