<?php

namespace App\Models;

use App\Models\Films;
use App\Models\User;
use App\Models\Vote;

/**
 * Class VoteDetails.
 *
 * @package namespace App\Models;
 */
class VoteDetails extends BaseModel
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['user_id', 'film_id', 'vote_id'];

    /**
     * Get name of film
     * @return string
     */
    public function getFilm()
    {
        $film = Films::find($this->film_id);
        return $film->name_film;
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

    /**
     * Get name of vote
     * @return string
     */
    public function getVote()
    {
        $vote = Vote::find($this->vote_id);
        return $vote->name_vote;
    }
}
