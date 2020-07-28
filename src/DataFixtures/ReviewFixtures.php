<?php

namespace App\DataFixtures;


use App\Entity\Hotel;
use App\Entity\Review;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Faker;

class ReviewFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $counter = 0;
        $hotels = [];
        $timestampNow = \time();
        $timestamp2YearsAgo = (new \DateTime('2 years ago'))->getTimestamp();
        $faker = Faker\Factory::create();

        while ($counter < 1000) {
            $hotelId = \mt_rand(1, 10);
            $hotels[$hotelId] ??= $manager->getRepository(Hotel::class)->find($hotelId);
            $timestamp = mt_rand($timestamp2YearsAgo, $timestampNow);
            $createdDate = (new \DateTime())->setTimestamp($timestamp);
            $comment = $faker->sentences(\mt_rand(10, 20), true);

            $review = (new Review())
                ->setHotel($hotels[$hotelId])
                ->setScore(\mt_rand(0, 100))
                ->setComment($comment)
                ->setCreatedDate($createdDate);
            $manager->persist($review);

            $counter++;
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            HotelFixtures::class
        ];
    }
}
