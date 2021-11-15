<?php

//declare(strict_types=1);

namespace App\Dto\Response\Transformer;


use App\Dto\Response\HotelDto;
use App\Entity\Hotel;

class HotelDtoTransformer extends AbstractResponseDtoTransformer
{

    /**
     * @param Hotel $hotel
     * @return HotelDto
     */
    public function transformFromObject($hotel): HotelDto
    {
        $hotelDto = new HotelDto();

        $hotelDto->id = $hotel->getId();
        $hotelDto->name = $hotel->getName();

        return $hotelDto;
    }
}