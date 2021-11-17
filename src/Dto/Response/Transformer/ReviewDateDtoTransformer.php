<?php

declare(strict_types=1);

namespace App\Dto\Response\Transformer;

use App\Common\ReviewDate;
use App\Dto\Response\ReviewDateDto;

class ReviewDateDtoTransformer extends AbstractResponseDtoTransformer
{

    /**
     * @param ReviewDate $reviewDate
     * @return ReviewDateDto
     */
    public function transformFromObject($reviewDate): ReviewDateDto
    {
        $reviewDateDto = new ReviewDateDto();

        $reviewDateDto->date = $reviewDate->date;
        $reviewDateDto->review_count = $reviewDate->reviewCount;
        $reviewDateDto->average_score = $reviewDate->averageScore;

        return $reviewDateDto;
    }
}