<?php

namespace App\Repositories\Eloquent;

use App\Models\Chair;
use App\Models\ChooseChair;
use App\Models\Vote;
use App\Presenters\ChairPresenter;
use App\Repositories\Contracts\ChairRepository;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Response;
use Prettus\Repository\Criteria\RequestCriteria;
use Prettus\Repository\Eloquent\BaseRepository;

/**
 * Class ChairRepositoryEloquent.
 *
 * @package namespace App\Repositories\Eloquent;
 */
class ChairRepositoryEloquent extends BaseRepository implements ChairRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return Chair::class;
    }

    /**
     * Specify Presenter class name
     *
     * @return string
     */
    public function presenter()
    {
        return ChairPresenter::class;
    }

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }

    /**
     * @param  int $vote_id
     * @return \Illuminate\Http\Response
     */
    public function chairByVote($voteId)
    {
        return $this->model()::where('vote_id', $voteId);
    }

    /**
     * custom create
     * @param  array  $attributes
     * @return \Illuminate\Http\Response
     */
    public function create(array $attributes)
    {
        $count = $this->chairByVote($attributes['vote_id'])->count();
        if ($count) {
            return response()->json('attributes aready exited', Response::HTTP_BAD_REQUEST);
        } else {
            $vote = Vote::find($attributes['vote_id']);
            if ($vote->status_vote != 'booking_chair') {
                return response()->json('status votes not combined',
                    Response::HTTP_BAD_REQUEST);
            }
        }
        $chairs = parent::create($attributes);
        return $chairs;
    }

    /**
     * Get diagram chair by vote
     * @param  int $vote_id
     * @return  \Illuminate\Http\Response
     */
    public function diagramChairByVote($voteId)
    {
        $diagrams = $this->chairByVote($voteId)->get();
        return $diagrams;
    }

    /**
     * Update status chair
     * @param  array  $attributes
     * @return \Illuminate\Http\Response
     */
    public function updateChairs(array $attributes)
    {
        $voteId = $attributes['vote_id'];
        $result = $arrayChooseChairs = $arrayChairs = array();
        $chooseChairs = ChooseChair::where('vote_id', $voteId)->get();
        $chairs = $this->chairByVote($voteId)->get();
        foreach ($chairs as $val) {
            $array = $val->chairs;
            for ($i = 0; $i < count($array); $i++) {
                $arrayChairs[] = $array[$i];
            }
        }
        foreach ($chooseChairs as $val) {
            $array = explode(',', $val->seats);
            for ($i = 0; $i < count($array); $i++) {
                $arrayChooseChairs[] = $array[$i];
            }
        }
        $arrayDiff = array_diff($arrayChairs, $arrayChooseChairs);
        foreach ($arrayDiff as $key => $value) {
            $result[] = $value;
        }
        return response()->json($result);
    }

    /**
     * Delete all chair by vote
     * @param  int $voteId
     * @return \Illuminate\Http\Response
     */
    public function delAll($voteId)
    {
        $this->chairByVote($voteId)->delete();
        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
