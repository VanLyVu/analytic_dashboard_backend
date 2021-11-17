<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Review;
use App\Repository\HotelRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class ReviewFixtures extends Fixture implements OrderedFixtureInterface
{
    private HotelRepository $hotelRepository;

    public function __construct(HotelRepository $hotelRepository)
    {
        $this->hotelRepository = $hotelRepository;
    }

    /**
     * @param ObjectManager $manager
     * @throws \Exception
     */
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();
        $hotels = $this->hotelRepository->findAll();
        $manager->getConnection()->getConfiguration()->setSQLLogger(null);

        foreach (range(1, 1000) as $index) {
            echo "Index {$index} of 1000\n";
            $tempObjects = [];
            foreach (range(1, 100)  as $chunkIndex) {
                $review = new Review();
                $review->setHotel($hotels[array_rand($hotels)]);
                $review->setScore(random_int(1, 100));
                $review->setComment($faker->paragraph);
                $review->setCreatedDate($faker->dateTimeBetween('-2 years', 'now'));

                $manager->persist($review);

                $tempObjects[] = $review;

            }
            $manager->flush();

            foreach($tempObjects as $tempObject) {
                $manager->detach($tempObject);
            }

            $tempObjects = null;
            gc_enable();
            gc_collect_cycles();
        }
    }

    /**
     * Get the order of this fixture
     *
     * @return int
     */
    public function getOrder()
    {
        return 20;
    }
}
