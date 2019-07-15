<?php

namespace App\Repositories\Eloquent;

use App\Models\VoteDetails;
use App\Presenters\VoteDetailsPresenter;
use App\Repositories\Contracts\VoteDetailsRepository;
use App\Services\StatisticalService;
use Prettus\Repository\Criteria\RequestCriteria;
use Prettus\Repository\Eloquent\BaseRepository;

/**
 * Class VoteDetailsRepositoryEloquent.
 *
 * @package namespace App\Repositories\Eloquent;
 */
class VoteDetailsRepositoryEloquent extends BaseRepository implements VoteDetailsRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return VoteDetails::class;
    }

    /**
     * Specify Presenter class name
     *
     * @return string
     */
    public function presenter()
    {
        return VoteDetailsPresenter::class;
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
     * @return  \Illuminate\Http\Response
     */
    public function create(array $attributes)
    {
        $voteDetails = parent::create($attributes);
        $add = StatisticalService::addRow($voteDetails['data']['attributes']['film_id'], $voteDetails['data']['attributes']['vote_id']);
        $add = StatisticalService::addRow($voteDetails['data']['attributes']['film_id'], $voteDetails['data']['attributes']['vote_id']);

        if (!$add) {
            return false;
        }

        return $voteDetails;
    }

    /**
     * Custom delete
     * @param  int $id
     * @return  Illuminate\Http\Response;
     */
    public function delete($id)
    {
        $voteDetail = VoteDetails::find($id);
        $upload = StatisticalService::updateRow($voteDetail->film_id, $voteDetail->vote_id);

        if (!$upload) {
            return false;
        }

        return parent::delete($id);
    }

    /**
     * Check User voted
     * @param  array  $attributes
     * @return Illuminate\Http\Response
     */
    public function checkVotes(array $attributes)
    {
        $useId = $attributes['user_id'];
        $voteId = $attributes['vote_id'];
        $lists = [];
        $voteDetail = $this->model()::where(['user_id' => $useId, 'vote_id' => $voteId])->get();

        if (count($voteDetail) != 0) {
            foreach ($voteDetail as $value) {
                $lists[] = $value->film_id;
            }
            return $lists;
        }

        return $lists;
    }

    /**
     * Delete vote
     * @param  array  $attributes
     * @return Illuminate\Http\Response
     */
    public function delVote(array $attributes)
    {
        $voteDetail = VoteDetails::where([
            'vote_id' => $attributes['vote_id'],
            'film_id' => $attributes['film_id'],
            'user_id' => $attributes['user_id'],
        ])->first();

        return $this->delete($voteDetail->id);
    }

    /**
     * Delete votedetail by vote
     * @param  array  $attributes
     * @return Illuminate\Http\Response
     */
    public function delAll($voteId)
    {
        return VoteDetails::where('vote_id', $voteId)->delete();
    }
}
