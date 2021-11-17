<?php

declare(strict_types=1);

namespace App\Tests\E2E\Controller;

use App\Entity\Hotel;
use App\Entity\Review;
use DateTime;

class HotelReportControllerTest extends WebTestCase
{
    /**
     * @test
     */
    public function hotels_request_returns_200_and_empty(): void
    {
        // Act
        $this->client->request('GET', '/api/hotel_reports/hotels');
        $response = $this->client->getResponse();

        // Assert
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals([], json_decode($response->getContent()));
    }

    /**
     * @test
     */
    public function hotels_request_returns_1_hotel(): void
    {
        // Arrange
        $hotel = new Hotel();
        $hotel->setName('TestHotel');
        $this->entityManager->persist($hotel);
        $this->entityManager->flush();

        // Act
        $this->client->request('GET', '/api/hotel_reports/hotels');
        $response = $this->client->getResponse();

        // Assert
        $this->assertJsonStringEqualsJsonString(
            json_encode([
                [
                    "id" => 1,
                    "name" => "TestHotel"
                ]
            ]), $response->getContent()
        );
    }

    /**
     * @test
     */
    public function hotels_request_returns_hotels(): void
    {
        // Arrange
        foreach (range(1, 2) as $index) {
            $hotel = new Hotel();
            $hotel->setName("TestHotel_{$index}");
            $this->entityManager->persist($hotel);
        }
        $this->entityManager->flush();

        // Act
        $this->client->request('GET', '/api/hotel_reports/hotels');
        $response = $this->client->getResponse();

        // Assert
        $this->assertJsonStringEqualsJsonString(
            json_encode([
                [
                    "id" => 1,
                    "name" => "TestHotel_1"
                ],
                [
                    "id" => 2,
                    "name" => "TestHotel_2"
                ]
            ]), $response->getContent()
        );
    }

    /**
     * @test
     */
    public function show_request_returns_200_and_empty_reviews(): void
    {
        // Act
        $this->client->request(
            'GET',
            '/api/hotel_reports/show',
            [
                'hotel_id' => 1,
                'date_from' => '2021-11-15',
                'date_to' => '2021-11-16'
            ]
        );
        $response = $this->client->getResponse();

        // Assert
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertJsonStringEqualsJsonString(
            json_encode([
                'hotel_id' => 1,
                'date_from' => '2021-11-15',
                'date_to' => '2021-11-16',
                'date_group' => 'daily',
                'review_dates' => [
                    [
                        'date' => '2021-11-15',
                        'review_count' => 0,
                        'average_score' => null
                    ],
                    [
                        'date' => '2021-11-16',
                        'review_count' => 0,
                        'average_score' => null
                    ],
                ]
            ]), $response->getContent()
        );

    }

    /**
     * @test
     */
    public function show_request_returns_daily_reviews(): void
    {
        // Arrange
        $hotels = [];
        foreach (range(1, 2) as $index) {
            $hotel = new Hotel();
            $hotel->setName("TestHotel_{$index}");
            $this->entityManager->persist($hotel);
            $this->entityManager->flush();
            $hotels[] = $hotel;
        }

        foreach (range(1, 30) as $index) {
            if ($index % 2 == 0) {
                continue;
            }
            $review = new Review();
            $review->setHotel($hotels[0]);
            $review->setScore($index);
            $review->setCreatedDate(new DateTime("2021-11-$index"));
            $this->entityManager->persist($review);
        }

        $this->entityManager->flush();


        // Act
        $this->client->request(
            'GET',
            '/api/hotel_reports/show',
            [
                'hotel_id' => 1,
                'date_from' => '2021-11-14',
                'date_to' => '2021-11-16'
            ]
        );
        $response = $this->client->getResponse();

        // Assert
        $this->assertJsonStringEqualsJsonString(
            json_encode([
                'hotel_id' => 1,
                'date_from' => '2021-11-14',
                'date_to' => '2021-11-16',
                'date_group' => 'daily',
                'review_dates' => [
                    [
                        'date' => '2021-11-14',
                        'review_count' => 0,
                        'average_score' => null
                    ],
                    [
                        'date' => '2021-11-15',
                        'review_count' => 1,
                        'average_score' => 15
                    ],
                    [
                        'date' => '2021-11-16',
                        'review_count' => 0,
                        'average_score' => null
                    ]
                ]
            ]), $response->getContent()
        );

    }

    /**
     * @test
     */
    public function show_request_returns_weekly_reviews(): void
    {
        // Arrange
        $hotels = [];
        foreach (range(1, 2) as $index) {
            $hotel = new Hotel();
            $hotel->setName("TestHotel_{$index}");
            $this->entityManager->persist($hotel);
            $this->entityManager->flush();
            $hotels[] = $hotel;
        }

        foreach (range(1, 90) as $index) {
            if ($index/7 % 2 == 0) {
                continue;
            }

            $review = new Review();
            $review->setHotel($hotels[0]);
            $review->setScore($index);
            $review->setCreatedDate(new DateTime("2021-01-04 +{$index} day"));
            $this->entityManager->persist($review);
        }

        $this->entityManager->flush();


        // Act
        $this->client->request(
            'GET',
            '/api/hotel_reports/show',
            [
                'hotel_id' => 1,
                'date_from' => '2021-01-01',
                'date_to' => '2021-02-01'
            ]
        );
        $response = $this->client->getResponse();

        // Assert
        $this->assertJsonStringEqualsJsonString(
            json_encode([
                'hotel_id' => 1,
                'date_from' => '2021-01-01',
                'date_to' => '2021-02-01',
                'date_group' => 'weekly',
                'review_dates' => [
                    [
                        'date' => '2020-12-28',
                        'review_count' => 0,
                        'average_score' => null
                    ],
                    [
                        'date' => '2021-01-04',
                        'review_count' => 0,
                        'average_score' => null
                    ],
                    [
                        'date' => '2021-01-11',
                        'review_count' => 7,
                        'average_score' => (7+13)/2
                    ],
                    [
                        'date' => '2021-01-18',
                        'review_count' => 0,
                        'average_score' => null
                    ],
                    [
                        'date' => '2021-01-25',
                        'review_count' => 7,
                        'average_score' => (21+27)/2
                    ],
                    [
                        'date' => '2021-02-01',
                        'review_count' => 0,
                        'average_score' => null
                    ]
                ]
            ]), $response->getContent()
        );

    }

    /**
     * @test
     */
    public function show_request_returns_monthly_reviews(): void
    {
        // Arrange
        $hotels = [];
        foreach (range(1, 2) as $index) {
            $hotel = new Hotel();
            $hotel->setName("TestHotel_{$index}");
            $this->entityManager->persist($hotel);
            $this->entityManager->flush();
            $hotels[] = $hotel;
        }

        foreach (range(1, 12) as $index) {
            if ($index % 2 == 0) {
                continue;
            }
            $review = new Review();
            $review->setHotel($hotels[0]);
            $review->setScore($index);
            $review->setCreatedDate(new DateTime("2021-{$index}-{$index}"));
            $this->entityManager->persist($review);
        }

        $this->entityManager->flush();


        // Act
        $this->client->request(
            'GET',
            '/api/hotel_reports/show',
            [
                'hotel_id' => 1,
                'date_from' => '2021-03-04',
                'date_to' => '2021-06-06'
            ]
        );
        $response = $this->client->getResponse();

        // Assert
        $this->assertJsonStringEqualsJsonString(
            json_encode([
                'hotel_id' => 1,
                'date_from' => '2021-03-04',
                'date_to' => '2021-06-06',
                'date_group' => 'monthly',
                'review_dates' => [
                    [
                        'date' => '2021-03-01',
                        'review_count' => 0,
                        'average_score' => null
                    ],
                    [
                        'date' => '2021-04-01',
                        'review_count' => 0,
                        'average_score' => null
                    ],
                    [
                        'date' => '2021-05-01',
                        'review_count' => 1,
                        'average_score' => 5
                    ],
                    [
                        'date' => '2021-06-01',
                        'review_count' => 0,
                        'average_score' => null
                    ]
                ]
            ]), $response->getContent()
        );
    }
}
