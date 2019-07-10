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

        $VoteDetails = parent::create($attributes);
        StatisticalService::addRow($VoteDetails['data']['attributes']['film_id'], $VoteDetails['data']['attributes']['vote_id']);

        return $VoteDetails;

    }

    /**
     * Custom delete
     * @param  int $id
     * @return  Illuminate\Http\Response;
     */
    public function delete($id)
    {
        $votedetail = VoteDetails::find($id);
        StatisticalService::updateRow($votedetail->film_id, $votedetail->vote_id);

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
        $votedetails = $this->model()::where(['user_id' => $useId, 'vote_id' => $voteId])->get();
      
        if (count($votedetails) != 0) {
            foreach ($votedetails as $value) {
                $lists[] = $value->film_id;
            }
            return ['data' => $lists];
        }

        return ['data' => ''];
    }

    /**
     * Delete vote
     * @param  array  $attributes
     * @return Illuminate\Http\Response
     */
    public function delVote(array $attributes)
    {
        $votedetail = VoteDetails::where([
            'vote_id' => $attributes['vote_id'],
            'film_id' => $attributes['film_id'],
            'user_id' => $attributes['user_id'],
        ])->first();

        return $this->delete($votedetail->id);
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
