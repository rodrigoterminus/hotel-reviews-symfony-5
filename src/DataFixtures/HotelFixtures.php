<?php

namespace App\DataFixtures;

use App\Entity\Hotel;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class HotelFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $names = [
            'Hotel Samambaia',
            'Orchidea Hotel',
            'Motel Catus',
            'Hotel Era',
            'Hotel Espada de São Jorge',
            'Monstera Hotel',
            'Hotel Flor de Seda',
            'Hotel Lírio da Paz',
            'Jibóia Hotel',
            'Hotel Violeta',
        ];

        foreach ($names as $name) {
            $hotel = (new Hotel())
                ->setName($name);
            $manager->persist($hotel);
        }

        $manager->flush();
    }
}
