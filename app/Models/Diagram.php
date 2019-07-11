<?php

namespace App\Models;

use App\Models\Room;

/**
 * Class Diagram.
 *
 * @package namespace App\Models;
 */
class Diagram extends BaseModel
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    //public $timestamps = false;

    protected $fillable = ['room_id', 'row_of_seats', 'amount_chairs_of_row', 'chairs'];

    /**
     * Custom attribute chair
     * @return array
     */
    public function getChairsAttribute($value)
    {
        return explode(',', $value);
    }

    /**
     * Get name of room
     * @return string
     */
    public function getRoom()
    {
        $room = Room::find($this->room_id);
        return $room->name_room;
    }
}
