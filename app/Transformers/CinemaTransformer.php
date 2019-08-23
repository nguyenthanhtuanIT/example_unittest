<?php

namespace App\Transformers;

use App\Models\Cinema;

/**
 * Class CinemaTransformer.
 *
 * @package namespace App\Transformers;
 */
class CinemaTransformer extends BaseTransformer
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
    protected $availableIncludes = ['room'];

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

        ];
    }

    // public function transform($model)
    // {
    //     return [
    //         'id' => $model->id,
    //         'name' => $model->name_cinema,
    //     ];
    // }

    public function includeRoom(Cinema $cinema)
    {
        $room = $cinema->room;
        return $this->collection($room, new RoomTransformer(), 'room');
    }
}
