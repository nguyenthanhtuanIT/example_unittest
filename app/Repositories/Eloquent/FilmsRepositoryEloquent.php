<?php

namespace App\Repositories\Eloquent;

use App\Models\Films;
use App\Models\Statistical;
use App\Models\Vote;
use App\Presenters\FilmsPresenter;
use App\Repositories\Contracts\filmsRepository;
use App\Services\UploadService;
use Illuminate\Support\Facades\Storage;
use Prettus\Repository\Criteria\RequestCriteria;
use Prettus\Repository\Eloquent\BaseRepository;

/**
 * Class FilmsRepositoryEloquent.
 *
 * @package namespace App\Repositories\Eloquent;
 */
class FilmsRepositoryEloquent extends BaseRepository implements FilmsRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return Films::class;
    }

    /**
     * Specify Presenter class name
     *
     * @return string
     */
    public function presenter()
    {
        return FilmsPresenter::class;
    }

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }

    /**
     * custom create
     * @param  array  $attributes
     * @return \Illuminate\Http\Response
     */
    public function create(array $attributes)
    {
        $attributes['curency'] = Films::PRICE;
        $name = $attributes['img']->store('photos');
        $link = Storage::url($name);
        $attributes['img'] = $link;
        $film = parent::create($attributes);

        return $film;
    }

    /**
     * custom update
     * @param  array  $attributes
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(array $attributes, $id)
    {
        if (isset($attributes['img'])) {
            $name = $attributes['img']->store('photos');
            $link = Storage::url($name);
            $attributes['img'] = $link;
            $img = Films::find($id);
            $imgOld = $img->img;
            $nameImg = explode('/', $imgOld);
            $url = "/photos/$nameImg[5]";

            if (UploadService::checkFileExist($url)) {
                Storage::delete('/photos/' . $nameImg[5]);
            }
        }
        $film = parent::update($attributes, $id);

        return $film;
    }

    /**
     * custom delete
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function delete($id)
    {
        $vote = vote::all();
        foreach ($vote as $value) {
            $list = $value->list_films;
            for ($i = 0; $i < count($list); $i++) {
                if ($list[$i] == $id) {
                    unset($list[$i]);
                    $films = implode(',', $list);
                    Vote::where('id', $value->id)->update(['list_films' => $films]);
                }
            }
        }
        return parent::delete($id);
    }
    /**
     * Get film has vote max
     * @param  int $voteId
     * @param  int $amountVotes
     * @return \Illuminate\Http\Response
     */
    public function getVoteMax($voteId, $amountVotes)
    {
        return Statistical::where(['vote_id' => $voteId, 'amount_votes' => $amountVotes]);
    }

    /**
     * Get film to vote
     * @return \Illuminate\Http\Response
     */
    public function getlistFilmToVote()
    {
        $vote = Vote::where('status_vote', Vote::VOTING)->first();

        if (!empty($vote)) {
            $convert = implode(',', $vote->list_films);
            $lists = explode(',', $convert);
            for ($i = 0; $i < count($lists); $i++) {
                $film = Films::find($lists[$i]);
                $arrayFilms[] = $film;
            }
            return $arrayFilms;
        }

        return null;
    }

    /**
     * Get film to register
     * @param  int $voteId
     * @return \Illuminate\Http\Response
     */
    public function filmToRegister($voteId)
    {
        $check = Statistical::where(['vote_id' => $voteId, 'movie_selected' => Films::SELECTED])->first();

        if (!$check) {
            $max = Statistical::where('vote_id', $voteId)->max('amount_votes');
            $statistical = $this->getVoteMax($voteId, $max)->get();

            if (count($statistical) == 1) {
                foreach ($statistical as $value) {
                    $film = Films::find($value->films_id);
                    $this->getVoteMax($voteId, $max)->update(['movie_selected' => Films::SELECTED]);
                    return $film;
                }
            } else {
                $random = $this->getVoteMax($voteId, $max)->get()->random();
                $films = Films::find($random->films_id);
                Statistical::where(['vote_id' => $voteId, 'films_id' => $films->id])->update(['movie_selected' => Films::SELECTED]);
                return $films;
            }
        }

        return Films::find($check->films_id);
    }
}
