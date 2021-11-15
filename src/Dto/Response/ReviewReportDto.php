<?php

declare(strict_types=1);

namespace App\Dto\Response;

use JMS\Serializer\Annotation as Serialization;

class ReviewReportDto
{
    /**
     * @Serialization\Type("int")
     * @Serialization\SerializedName("hotel_id")
     */
    public int $hotel_id;

    /**
     * @Serialization\Type("string")
     */
    public string $date_from;

    /**
     * @Serialization\Type("string")
     */
    public string $date_to;

    /**
     * @Serialization\Type("string")
     */
    public string $date_group;

    /**
     * @Serialization\Type("array<App\Dto\Response\ReviewDateDto>")
     * @Serialization\SerializedName("reports")
     */
    public array $review_dates;
}