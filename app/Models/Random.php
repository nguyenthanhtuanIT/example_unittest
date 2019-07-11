<?php

namespace App\Models;

use App\Models\User;

/**
 * Class Random.
 *
 * @package namespace App\Models;
 */
class Random extends BaseModel
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['vote_id', 'viewers', 'seats'];

    /**
     * Get name of user
     * @return string
     */
    public function nameUser()
    {
        $user = User::find($this->viewers);
        if ($user) {
            return $user->full_name;
        }
    }
}
