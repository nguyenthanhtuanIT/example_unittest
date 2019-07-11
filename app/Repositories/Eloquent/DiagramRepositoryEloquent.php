<?php

namespace App\Repositories\Eloquent;

use App\Models\Diagram;
use App\Models\Vote;
use App\Presenters\DiagramPresenter;
use App\Repositories\Contracts\DiagramRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use Prettus\Repository\Eloquent\BaseRepository;

/**
 * Class DiagramRepositoryEloquent.
 *
 * @package namespace App\Repositories\Eloquent;
 */
class DiagramRepositoryEloquent extends BaseRepository implements DiagramRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return Diagram::class;
    }

    /**
     * Specify Presenter class name
     *
     * @return string
     */
    public function presenter()
    {
        return DiagramPresenter::class;
    }

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }

    /**
     * Custom index
     * @return Illuminate\Http\Response
     */
    public function custom()
    {
        return Diagram::all();
    }

    /**
     * custom function create
     * @param  array  $attributes
     * @return Illuminate\Http\Response
     */
    public function create(array $attributes)
    {
        $diagram = $this->model()::where([
            'row_of_seats' => $attributes['row_of_seats'],
            'room_id' => $attributes['room_id'],
        ])->count();

        if ($diagram) {
            return null;
        }
        $diagrams = parent::create($attributes);

        return $diagrams;
    }

/**
 * Get diagram by room
 * @param  int $room_id
 * @return Illuminate\Http\Response
 */
    public function getDiagramByRoom($roomId)
    {
        return $this->model()::where('room_id', $roomId);
    }

/**
 * Get diagram chair of vote
 * @param  int $vote_id [description]
 * @return Illuminate\Http\Response
 */
    public function getDiagramChairByVote($voteId)
    {

        $vote = Vote::find($voteId);

        if ($vote->room_id != 0) {
            $diagram = $this->getDiagramByRoom($vote->room_id)->get();
            return $diagram;
        }

        return null;
    }

    /**
     * Search diagram by room
     * @param  int $roomId
     * @return Illuminate\Http\Response
     */
    public function searchByRoomId($roomId)
    {
        $diagram = $this->getDiagramByRoom($roomId)->get();

        if ($diagram) {
            return $diagram;
        }

        return null;
    }

    /**
     * Delete diagram all by room
     * @param  int $roomId
     * @return Illuminate\Http\Response
     */
    public function delAll($roomId)
    {
        return $this->getDiagramByRoom($roomId)->delete();
    }
}
