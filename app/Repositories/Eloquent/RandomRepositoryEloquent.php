<?php

namespace App\Repositories\Eloquent;

use App\Models\Random;
use App\Presenters\RandomPresenter;
use App\Repositories\Contracts\RandomRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use Prettus\Repository\Eloquent\BaseRepository;

/**
 * Class RandomRepositoryEloquent.
 *
 * @package namespace App\Repositories\Eloquent;
 */
class RandomRepositoryEloquent extends BaseRepository implements RandomRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return Random::class;
    }

    /**
     * Specify Presenter class name
     *
     * @return string
     */
    public function presenter()
    {
        return RandomPresenter::class;
    }

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }

    /**
     * Custom create
     * @param  array  $attributes
     * @return \Illuminate\Http\Response
     */
    public function create(array $attributes)
    {
        $voteId = 0;
        $result = null;
        $array = explode(';', $attributes['rand']);
        for ($i = 0; $i < count($array); $i++) {
            $convert = explode(',', $array[$i]);
            for ($j = 0; $j < count($convert); $j++) {
                $voteId = $convert[0];
            }
        }
        $check = Random::where('vote_id', $voteId)->count();

        if ($check != 0) {
            return $result;
        }
        $array = explode(';', $attributes['rand']);
        for ($i = 0; $i < count($array); $i++) {
            $arrayChill = explode(',', $array[$i]);
            $voteId = $arrayChill[0];
            $random = new Random;
            $random->vote_id = $arrayChill[0];
            $random->seats = $arrayChill[1];
            $random->viewers = $arrayChill[2];
            $random->save();
        }
        $result = Random::where('vote_id', $voteId)->get();

        return $result;
    }

    /**
     * Get chair by vote
     * @param  int $voteId
     * @return \Illuminate\Http\Response
     */
    public function chairsByVote($voteId)
    {
        return Random::where('vote_id', $voteId)->get();
    }

    /**
     * Delete all chair by vote
     * @param  int $voteId
     * @return \Illuminate\Http\Response
     */
    public function delAll($voteId)
    {
        return Random::where('vote_id', $voteId)->delete();
    }

}
