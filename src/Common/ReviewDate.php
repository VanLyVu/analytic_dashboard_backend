<?php

declare(strict_types=1);

namespace App\Common;

class ReviewDate
{
    public int $hotelId;

    public string $date;

    public int $reviewCount;

    public ?float $averageScore;

    public function __construct(
        int $hotelId, string $date, int $reviewCount = 0, ?float $averageScore = null
    ) {
        $this->hotelId = $hotelId;
        $this->date = $date;
        $this->reviewCount = $reviewCount;
        $this->averageScore = $averageScore;
    }
}