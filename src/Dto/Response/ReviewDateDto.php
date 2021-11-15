<?php

declare(strict_types=1);

namespace App\Dto\Response;

use JMS\Serializer\Annotation as Serialization;

class ReviewDateDto
{
    /**
     * @Serialization\Type("string")
     */
    public string $date;

    /**
     * @Serialization\Type("int")
     */
    public int $review_count;

    /**
     * @Serialization\Type("int")
     */
    public float $average_score;


}