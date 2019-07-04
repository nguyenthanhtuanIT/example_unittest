<?php
namespace App\Services;

use App\Models\Vote;

class VoteService
{
    /**
     * Add ticket when vote
     * @param int $voteId
     * @param int $number
     */
    public static function addTicket($voteId, $number)
    {
        $vote = Vote::find($voteId);
        $vote->total_ticket += $number;
        $vote->save();
    }

    /**
     * Update ticket when vote
     * @param int $voteId
     * @param int $numberOld
     * @param int $numberNew
     */
    public static function updateTicket($voteId, $numberOld, $numberNew)
    {
        $vote = Vote::find($voteId);
        $vote->total_ticket -= $numberOld;
        $vote->total_ticket += $numberNew;
        $vote->save();
    }

    /**
     * Update ticket when vote
     * @param int $voteId
     * @param int $number
     */
    public static function deleteTicket($voteId, $number)
    {
        $vote = Vote::find($voteId);
        $vote->total_ticket -= $number;
        $vote->save();
    }
}
