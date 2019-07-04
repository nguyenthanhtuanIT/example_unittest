<?php

namespace App\Models;

use App\Models\Cinema;
use App\Models\Room;
use App\Models\User;
use App\Models\Vote;

/**
 * Class Vote.
 *
 * @package namespace App\Models;
 */
class Vote extends BaseModel
{
    const CREATED = 'created';
    const VOTING = 'voting';
    const REGISTING = 'registing';
    const BOOKING = 'booking_chair';
    const END = 'end';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name_vote', 'list_films', 'user_id', 'room_id',
        'background', 'status_vote', 'detail', 'time_voting', 'time_registing',
        'time_booking_chair', 'time_end', 'total_ticket', 'infor_time'];

    /**
     * Get list film of vote
     * @return array
     */
    public function getFilms()
    {
        $lists = implode(',', $this->list_films);
        $listFilms = explode(',', $lists);
        $result = [];
        for ($i = 0; $i < count($listFilms); $i++) {
            $film = Films::select('id', 'name_film')->find($listFilms[$i]);
            $result[] = $film;
        }

        return $result;
    }

    /**
     * Custom attribute list films
     * @return array
     */
    public function getListFilmsAttribute($value)
    {
        return explode(',', $value);
    }

    /**
     * Get info of room
     * @return array
     */
    public function inforRooms()
    {
        $room = Room::find($this->room_id);
        $result = [];

        if ($room) {
            $cinema = Cinema::find($room->cinema_id);
            $result = [
                'name_room' => $room->name_room,
                'cinema' => $cinema->name_cinema,
            ];
        }

        return $result;
    }

    /**
     * Get name of user
     * @return string
     */
    public function getUser()
    {
        $user = User::find($this->user_id);
        return $user->full_name;
    }
}
