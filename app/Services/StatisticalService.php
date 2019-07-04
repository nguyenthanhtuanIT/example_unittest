<?php
namespace App\Services;

use App\Models\Statistical;

class StatisticalService
{
    /**
     * Add statictical when vote
     * @param int $filmId
     * @param int $voteId
     * @return bool
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
            }
            foreach ($statisticals as $value) {
                $value->amount_votes += 1;
                $value->save();
            }
            return true;
        }

        return false;
    }

    /**
     * Update statictical when vote
     * @param  int $filmId
     * @param  int $voteId
     * @return bool
     */
    public static function updateRow($filmId, $voteId)
    {
        $statisticals = Statistical::where(['vote_id' => $voteId, 'films_id' => $filmId])->get();
        if ($statisticals) {
            foreach ($statisticals as $value) {
                $value->amount_votes -= 1;
                $value->save();
            }
            return true;
        }

        return false;
    }

    /**
     * Increase amount vote
     * @param int $filmId
     * @param int $voteId
     * @return bool
     */
    public static function addRegister($filmId, $voteId)
    {
        $statistical = Statistical::where(['vote_id' => $voteId, 'films_id' => $filmId])->first();

        if ($statistical) {
            $statistical->amount_registers += 1;
            $statistical->save();
            return true;
        }

        return false;
    }

    /**
     * Reduction amount vote
     * @param int $filmId
     * @param int $voteId
     * @return bool
     */
    public static function updateRegister($filmId, $voteId)
    {
        $statistical = Statistical::where(['vote_id' => $voteId, 'films_id' => $filmId])->first();

        if ($statistical) {
            $statistical->amount_registers -= 1;
            $statistical->save();
            return true;
        }

        return false;

    }
}
