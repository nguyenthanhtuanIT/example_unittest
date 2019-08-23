<?php

namespace App\Transformers;

use App\Models\Room;

/**
 * Class RoomTransformer.
 *
 * @package namespace App\Transformers;
 */
class RoomTransformer extends \App\Transformers\BaseTransformer
{
    /**
     * Array attribute doesn't parse.
     */
    public $ignoreAttributes = [];

    /**
     * List of resources possible to include
     *
     * @var array
     */
    protected $availableIncludes = ['cinema'];

    /**
     * List of resources possible to include
     *
     * @var array
     */
    protected $defaultIncludes = [];

    /**
     * Transform the custom field entity.
     *
     * @return array
     */
    public function customAttributes($model): array
    {

        return [
            'cinema' => $model->getCinema(),
        ];
    }

    public function includeCinema(Room $room)
    {
        $cinema = $room->cinema;
        return $this->item($cinema, new CinemaTransformer(), 'Cinemas');
    }
}
