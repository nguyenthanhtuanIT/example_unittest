<?php

namespace App\Repositories\Eloquent;

use App\Mail\NotificationMessage;
use App\Models\Chair;
use App\Models\Cinema;
use App\Models\Diagram;
use App\Models\Films;
use App\Models\Room;
use App\Models\Statistical;
use App\Models\User;
use App\Models\Vote;
use App\Presenters\VotePresenter;
use App\Repositories\Contracts\VoteRepository;
use App\Services\UploadService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Mail;
use Prettus\Repository\Criteria\RequestCriteria;
use Prettus\Repository\Eloquent\BaseRepository;

/**
 * Class VoteRepositoryEloquent.
 *
 * @package namespace App\Repositories\Eloquent;
 */
class VoteRepositoryEloquent extends BaseRepository implements VoteRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return Vote::class;
    }

    /**
     * Specify Presenter class name
     *
     * @return string
     */
    public function presenter()
    {
        return VotePresenter::class;
    }

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }

    /**
     * Search vote by title
     * @param  string $title
     * @return \Illuminate\Http\Response
     */
    public function search($title)
    {
        $result = $this->model()::where('name_vote', 'like', '%' . $title . '%')->get();
        return $result;
    }

    /**
     * Custom create
     * @param  array  $attributes
     * @return \Illuminate\Http\Response
     */
    public function create(array $attributes)
    {
        $name = $attributes['background']->store('photos');
        $link = Storage::url($name);
        $attributes['background'] = $link;
        $vote = parent::create($attributes);
        $user = User::all();

        if ($vote->status_vote == Vote::VOTING) {
            foreach ($user as $value) {
                Mail::to($value->email)->queue(new NotificationMessage());
            }
        }
        return $vote;

    }

    /**
     * Custom update
     * @param  array  $attributes
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(array $attributes, $id)
    {
        if (!empty($attributes['background'])) {
            $name = $attributes['background']->store('photos');
            $link = Storage::url($name);
            $attributes['background'] = $link;
            $img = Vote::find($id);
            $imgOld = $img->background;
            $nameImg = explode('/', $imgOld);
            $url = "/photos/$nameImg[4]";

            if (UploadService::checkFileExist($url)) {
                Storage::delete('/photos/' . $nameImg[4]);
            }
        }
        $vote = parent::update($attributes, $id);

        return $vote;
    }

    /**
     * Show status vote
     * @return Illuminate\Http\Response
     */
    public function getStatus()
    {
        $vote = Vote::whereNotIn('status_vote', [Vote::END, Vote::CREATED])->first();

        if (!empty($vote)) {
            $chair = Chair::where('vote_id', $vote->id)->get(['chairs']);
            $date = Carbon::now()->toDateTimeString();

            if ($vote->time_registing <= $date && $date < $vote->time_booking_chair && $vote->status_vote != Vote::REGISTING) {
                $update = $vote->update(['status_vote' => Vote::REGISTING]);
            } elseif ($vote->time_booking_chair <= $date && $date < $vote->time_end && $vote->status_vote != Vote::BOOKING) {
                $update = $vote->update(['status_vote' => Vote::BOOKING]);

                if ($vote->room_id == 0 || $chair->count() == 0) {
                    return false;
                }
            } elseif ($date >= $vote->time_end && $vote->status_vote != Vote::END) {
                $update = $vote->update(['status_vote' => Vote::END]);
            }
            return [
                'id' => $vote->id,
                'background' => $vote->background,
                'status' => $vote->status_vote,
                'time_voting' => $vote->time_voting,
                'time_registing' => $vote->time_registing,
                'time_booking_chair' => $vote->time_booking_chair,
                'time_end' => $vote->time_end,
            ];

        }

        return null;
    }

    /**
     * Get infor of vote
     * @param  int $voteId
     * @return \Illuminate\Http\Response
     */
    public function info($voteId)
    {
        $result = null;
        $statistical = Statistical::where(['vote_id' => $voteId, 'movie_selected' => Films::SELECTED])->first();

        if (!empty($statistical)) {
            $film = Films::find($statistical->films_id);
            $vote = Vote::find($voteId);
            $rom = Room::find($vote->room_id);
            $chair = Chair::where('vote_id', $voteId)->get(['chairs']);

            if (empty($rom)) {
                $result = [
                    'poter' => $film->img,
                    'name_film' => $film->name_film,
                    'amount_vote' => $statistical->amount_votes,
                    'amount_registers' => $statistical->amount_registers,
                    'chairs' => $chair,
                ];

                if (!empty($vote->infor_time)) {
                    $times = new Carbon($vote->infor_time);
                    $date = $times->toDateString();
                    $time = $times->toTimeString();
                    $result['date'] = $date;
                    $result['time'] = $time;
                } else {
                    $result['time'] = $vote->infor_time;
                }
            } else {
                $cinema = Cinema::find($rom->cinema_id);
                $diagram = Diagram::where('room_id', $rom->id)->get(['row_of_seats', 'chairs']);
                $chair = Chair::where('vote_id', $voteId)->get(['chairs']);
                $result = [
                    'poter' => $film->img,
                    'name_film' => $film->name_film,
                    'amount_vote' => $statistical->amount_votes,
                    'amount_registers' => $statistical->amount_registers,
                    'cinema' => $cinema->name_cinema,
                    'address' => $cinema->address,
                    'room' => $rom->name_room,
                    'room_id' => $rom->id,
                    'diagram' => $diagram,
                    'chairs' => $chair,
                ];
                if (!empty($vote->infor_time)) {
                    $times = new Carbon($vote->infor_time);
                    $date = $times->toDateString();
                    $time = $times->toTimeString();
                    $result['date'] = $date;
                    $result['time'] = $time;
                } else {
                    $result['time'] = $vote->infor_time;
                }
            }
        }

        return $result;
    }
}
