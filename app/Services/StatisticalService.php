<?php
namespace App\Services;

use App\Models\Statistical;

class StatisticalService
{
    /**
     * Add statictical when vote
     * @param int $filmId
     * @param int $voteId
     */
    public static function addRow($filmId, $voteId)
    {
        if (!empty($voteId) && !empty($filmId)) {
            $statisticals = Statistical::where(['vote_id' => $voteId, 'films_id' => $filmId])->get();

            if ($statisticals->count() == 0) {
                $statisticals = new Statistical;
                $statisticals->vote_id = $voteId;
                $statisticals->films_id = $filmId;
                $statisticals->amount_votes += 1;
                $statisticals->save();
            } else {
                foreach ($statisticals as $value) {
                    $value->amount_votes += 1;
                    $value->save();
                }
            }
            
        }

    }

    /**
     * Update statictical when vote
     * @param  int $filmId
     * @param  int $voteId
     */
    public static function updateRow($filmId, $voteId)
    {
        $statisticals = Statistical::where(['vote_id' => $voteId, 'films_id' => $filmId])->get();
        foreach ($statisticals as $value) {
            $value->amount_votes -= 1;
            $value->save();
        }
    }

    /**
     * Increase amount vote
     * @param int $filmId
     * @param int $voteId
     */
    public static function addRegister($filmId, $voteId)
    {
        $statistical = Statistical::where(['vote_id' => $voteId, 'films_id' => $filmId])->first();
        $statistical->amount_registers += 1;
        $statistical->save();
    }

    /**
     * Reduction amount vote
     * @param int $filmId
     * @param int $voteId
     */
    public static function updateRegister($filmId, $voteId)
    {
        $statistical = Statistical::where(['vote_id' => $voteId, 'films_id' => $filmId])->first();
        $statistical->amount_registers -= 1;
        $statistical->save();

    }
}
