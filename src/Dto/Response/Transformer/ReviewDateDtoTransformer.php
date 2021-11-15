<?php

declare(strict_types=1);

namespace App\Dto\Response\Transformer;


use App\Dto\Response\ReviewDateDto;
use DateTime;

class ReviewDateDtoTransformer extends AbstractResponseDtoTransformer
{

    public function transformFromObject($data): ReviewDateDto
    {
        $reviewDateDto = new ReviewDateDto();

        $reviewDateDto->date = $data['date'];
        $reviewDateDto->review_count = $data['review_count'];
        $reviewDateDto->average_score = (float) $data['average_score'];

        return $reviewDateDto;
    }
}