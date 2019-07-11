<?php

namespace App\Models;

use App\Models\User;
use App\Models\Vote;

/**
 * Class ChooseChair.
 *
 * @package namespace App\Models;
 */
class ChooseChair extends BaseModel
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['user_id', 'vote_id', 'seats'];

    /**
     * Get name of user
     * @return string
     */
    public function getUser()
    {
        $user = User::find($this->user_id);
        return $user->full_name;
    }

    /**
     * Get name vote
     * @return string
     */
    public function getVote()
    {
        $vote = Vote::find($this->vote_id);
        return $vote->name_vote;
    }

}
