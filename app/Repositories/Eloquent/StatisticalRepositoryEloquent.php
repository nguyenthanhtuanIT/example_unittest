<?php

namespace App\Repositories\Eloquent;

use App\Models\Films;
use App\Models\Register;
use App\Models\Statistical;
use App\Models\Vote;
use App\Presenters\StatisticalPresenter;
use App\Repositories\Contracts\StatisticalRepository;
use Illuminate\Http\Response;
use Prettus\Repository\Criteria\RequestCriteria;
use Prettus\Repository\Eloquent\BaseRepository;

/**
 * Class StatisticalRepositoryEloquent.
 *
 * @package namespace App\Repositories\Eloquent;
 */
class StatisticalRepositoryEloquent extends BaseRepository implements StatisticalRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return Statistical::class;
    }

    /**
     * Specify Presenter class name
     *
     * @return string
     */
    public function presenter()
    {
        return StatisticalPresenter::class;
    }

    /**
     * Custom delete
     * @param  array  $attributes
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(array $attributes, $id)
    {
        if (!empty($attributes['movie_selected'])) {
            $statistical = Statistical::find($id);
            $voteId = $statistical->vote_id;
            $check = Statistical::where(['vote_id' => $voteId, 'movie_selected' => Films::SELECTED])->get();
            if ($check->count() == 1) {
                foreach ($check as $value) {
                    $value->update(['movie_selected' => Films::NOTSELECT]);
                }
            }

        }
        $result = parent::update($attributes, $id);
        return $result;
    }

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }

    /**
     * Get infor vote to statitical
     *
     * @return \Illuminate\Http\Response
     */
    public function inforAll()
    {
        $result = array();
        $voteId = Statistical::get()->unique('vote_id');
        foreach ($voteId as $value) {
            $vote = Vote::find($value->vote_id);
            $statistical = Statistical::where(['vote_id' => $value->vote_id,
                'movie_selected' => Films::SELECTED])->first();
            if ($statistical) {
                $film = Films::find($statistical->films_id);
                $ticketOutsite = Register::where('vote_id', $value->vote_id)->sum('ticket_outsite');
                $result[] = array('name_vote' => $vote->name_vote,
                    'films' => $film->name_film,
                    'amount_vote' => $statistical->amount_votes,
                    'amount_register' => $statistical->amount_registers,
                    'total_ticket' => $vote->total_ticket,
                    'ticket_outsite' => $ticketOutsite);
            } else {
                return response()->json(['status' => 'film selected not data']);
            }
        }
        return $result;
    }

    /**
     * Get info by vote
     * @param  int $voteId
     * @return  \Illuminate\Http\Response
     */
    public function inforByVote($voteId)
    {
        $vote = Vote::find($voteId);

        $statistical = Statistical::where(['vote_id' => $voteId,
            'movie_selected' => Films::SELECTED])->first();
        if ($statistical) {
            $film = Films::find($statistical->films_id);
            $ticket_outsite = Register::where('vote_id', $voteId)->sum('ticket_outsite');
            return response()->json(['name_vote' => $vote->name_vote,
                'films' => $film->name_film,
                'amount_vote' => $statistical->amount_votes,
                'amount_register' => $statistical->amount_registers,
                'total_ticket' => $vote->total_ticket,
                'ticket_outsite' => $ticket_outsite]);
        } else {
            return response()->json(['status' => 'film selected not data']);
        }
    }

    public function amountVoteOfFilm($voteId)
    {
        $infor = array();
        $vote = Vote::find($voteId);
        if ($vote) {
            $statisticals = Statistical::where('vote_id', $voteId)->get();
            $films = Films::all();
            foreach ($statisticals as $statistical) {
                foreach ($films as $value) {
                    if ($statistical->films_id != null) {
                        if ($statistical->films_id == $value->id) {
                            $infor[] = array($value->name_film, $statistical->amount_votes);
                        }
                    } else {
                        return $result = array('status' => 'not data');
                    }
                }
            }
            return $result = array('name_vote' => $vote->name_vote, 'infor' => $infor);
        } else {
            return $result = array('status' => 'not data');
        }
    }

    public function delAll($voteId)
    {
        $del = Statistical::where('vote_id', $voteId)->delete();
        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
