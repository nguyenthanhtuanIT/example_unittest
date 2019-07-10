<?php

namespace App\Repositories\Eloquent;

use App\Models\Chair;
use App\Models\ChooseChair;
use App\Models\Register;
use App\Models\Vote;
use App\Presenters\ChooseChairPresenter;
use App\Repositories\Contracts\ChooseChairRepository;
use App\User;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Prettus\Repository\Criteria\RequestCriteria;
use Prettus\Repository\Eloquent\BaseRepository;

/**
 * Class ChooseChairRepositoryEloquent.
 *
 * @package namespace App\Repositories\Eloquent;
 */
class ChooseChairRepositoryEloquent extends BaseRepository implements ChooseChairRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return ChooseChair::class;
    }

    /**
     * Specify Presenter class name
     *
     * @return string
     */
    public function presenter()
    {
        return ChooseChairPresenter::class;
    }

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }

    /**
     * Get chair choosed of user by vote
     * @param  int $userId
     * @param  int $voteId
     * @return \Illuminate\Http\Response
     */
    public function getOfUserByVote($userId, $voteId)
    {
        return $this->model()::where([
            'user_id' => $userId,
            'vote_id' => $voteId,
        ]);
    }
    /**
     * custom create
     * @param  array  $attributes
     * @return \Illuminate\Http\Response
     */
    public function create(array $attributes)
    {
        $count = $this->getOfUserByVote($attributes['user_id'], $attributes['vote_id'])->get();
        if ($count->count()) {
            foreach ($count as $value) {
                $chairs = $this->model()::whereNotIn('id', [$value->id])->where('vote_id', $attributes['vote_id'])->get();
            }
            $seat = explode(',', $attributes['seats']);
            foreach ($chairs as $val) {
                $chair = explode(',', $val->seats);
                for ($i = 0; $i < count($chair); $i++) {
                    for ($j = 0; $j < count($seat); $j++) {
                        if ($chair[$i] == $seat[$j]) {
                            return response()->json('seats not empty');
                            break;
                        }
                    }
                }
            }
            $delete = $this->model()::find($value->id);
            $delete->delete();
            $results = parent::create($attributes);
            return $results;
        } else {
            $chairs = $this->model()::where('vote_id', $attributes['vote_id'])->get();
            $seat = explode(',', $attributes['seats']);
            foreach ($chairs as $val) {
                $chair = explode(',', $val->seats);
                for ($i = 0; $i < count($chair); $i++) {
                    for ($j = 0; $j < count($seat); $j++) {
                        if ($chair[$i] == $seat[$j]) {
                            return response()->json('seats not empty');
                            break;
                        }
                    }
                }
            }
        }
        $result = parent::create($attributes);
        return $result;
    }

    /**
     * Get total ticket of user
     * @param  array  $attributes
     * @return \Illuminate\Http\Response
     */
    public function ticketUser(array $attributes)
    {
        $ticket = Register::where('user_id', Auth::user()->id)
            ->where('vote_id', $attributes['vote_id'])->get(['ticket_number']);
        return response()->json($ticket[0]);
    }

    /**
     * Check status choose chair of user
     * @param  array  $attributes
     * @return \Illuminate\Http\Response
     */
    public function checkChoosed(array $attributes)
    {
        $check = false;
        $user = '';
        $register = Register::where('vote_id', $attributes['vote_id'])->where('ticket_number', '>', 1)->get();
        foreach ($register as $value) {
            $arrayFriend = explode(',', $value->best_friend);
            for ($i = 0; $i < count($arrayFriend); $i++) {
                if ($arrayFriend[$i] == $attributes['user_id']) {
                    $user = $value->user_id;
                    break;
                }
            }
        }
        if ($user == '') {
            $choosed = $this->getOfUserByVote($attributes['user_id'], $attributes['vote_id'])->first();

            if (!$choosed) {
                $choosed = $this->getOfUserByVote($user, $attributes['vote_id'])->first();
            }
            if ($choosed->count() != 0) {
                $check = true;
                return array('check' => $check, 'seats' => $choosed->seats);
            }
        }
        return array('check' => $check);
    }

    /**
     * User rechoose chairs
     * @param  array  $attributes
     * @return \Illuminate\Http\Response
     */
    public function reChoose(array $attributes)
    {
        $find = $this->getOfUserByVote($attributes['user_id'], $attributes['vote_id'])->first();
        $delete = $this->model()::find($find->id);
        $delete->delete();
        $result = parent::create($attributes);
        return $result;
    }

    /**
     * handling data
     * @param  array  $attributes
     * @return \Illuminate\Http\Response
     */
    public function randChair(array $attributes)
    {
        $vote = Vote::find($attributes['vote_id']);
        if ($vote->status_vote != 'booking_chair') {
            return response()->json('time is invalid', Response::HTTP_BAD_REQUEST);
        } else {
            $register = Register::where('vote_id', $vote->id)->get();
            $chairs = Chair::where('vote_id', $vote->id)->get();
            if (count($chairs) == 0) {
                return response()->json('not data chairs', Response::HTTP_BAD_REQUEST);
            }

            $publish = $seats = $viewers = $array = $temp = $arrayName = $array_result = array();
            foreach ($register as $value) {
                if ($value->ticket_number == 1) {
                    $name = User::find($value->user_id);
                    $arrayName[] = array($name->full_name);
                } elseif ($value->ticket_number > 1) {
                    $findUser = User::find($value->user_id);
                    $array = array($findUser->full_name);
                    $friend = explode(',', $value->best_friend);
                    for ($i = 0; $i < count($friend); $i++) {
                        if (is_numeric($friend[$i])) {
                            $convert = (int) $friend[$i];
                            if ($convert != 0) {
                                $name = User::find($convert);
                                $array[] = $name->full_name;
                            }
                        } else {
                            $array[] = $friend[$i];
                        }
                    }
                    $temp[] = $array;
                }
            }
            $viewers = array_merge($arrayName, $temp);
            //seats
            foreach ($chairs as $value) {
                $arrayChairs = $value->chairs;
                $arraySeats = array();
                for ($i = 0; $i < count($arrayChairs); $i++) {
                    $arraySeats[] = $arrayChairs[$i];
                }
                sort($arraySeats, SORT_STRING);
                for ($i = 0; $i < count($arraySeats); $i++) {
                    if ($i == (count($arraySeats) - 1)) {
                        $handingString = substr($arraySeats[$i], 0, 1);
                        $numberSeat = (int) substr($arraySeats[$i], 1);
                        $subString = substr($arraySeats[$i - 1], 0, 1);
                        $numberSeatConvert = (int) substr($arraySeats[$i - 1], 1);
                        if (ord($handingString) == ord($subString) && $numberSeatConvert == $numberSeat - 1) {
                            $publish[] = $arraySeats[$i];
                            $array_result[] = $publish;
                        } else {
                            $array_result[] = $publish;
                            $publish = array($arraySeats[$i]);
                            $array_result[] = $publish;
                        }
                    } else {
                        if (empty($publish)) {
                            $publish = array($arraySeats[$i]);
                        } else {
                            $handingString = substr($arraySeats[$i], 0, 1);
                            $numberSeat = (int) substr($arraySeats[$i], 1);
                            $subString = substr($arraySeats[$i - 1], 0, 1);
                            $numberSeatConvert = (int) substr($arraySeats[$i - 1], 1);
                            if (ord($handingString) == ord($subString) && $numberSeatConvert == $numberSeat - 1) {
                                $publish[] = $arraySeats[$i];
                            } else {
                                $array_result[] = $publish;
                                $publish = array($arraySeats[$i]);
                            }
                        }
                    }
                }
                $seats = $array_result;
                $result = $this->shuffle_seats($seats, $viewers, $vote->id);
                return $result;
            }
        }
    }

    /**
     * Delete all chair user choosed by vote
     * @param  int $vote_id
     * @return \Illuminate\Http\Response
     */
    public function delAll($voteId)
    {
        $del = ChooseChair::where('vote_id', $voteId)->delete();
        return response()->json(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * random seats
     * @param  array  $seats
     * @param  array  $viewers
     * @param  int $vote_id
     * @return \Illuminate\Http\Response
     */
    public function shuffle_seats($seats = [], $viewers = [], $vote_id)
    {
        $seats = array_values($seats);
        $viewers = array_values($viewers);
        // seats or viewers list is empty
        if (empty($seats) || empty($viewers)) {
            return [
                'status' => 'success',
                'data' => [],
            ];
        }
        // validate inputs
        $original_seats = $original_viewers = [];
        foreach ($seats as $key => $seats_group) {
            if (!is_array($seats_group)) {
                return response()->json('The data is invalid (seats)', Response::HTTP_BAD_REQUEST);
            } elseif (!empty($seats_group)) {
                $original_seats = array_merge($original_seats, $seats_group);
            } else {
                unset($seats[$key]);
            }
        }
        foreach ($viewers as $key => $viewers_group) {
            if (!is_array($viewers_group)) {
                return response()->json('The data is invalid (viewers)', Response::HTTP_BAD_REQUEST);
            } elseif (!empty($viewers_group)) {
                $original_viewers = array_merge($original_viewers, $viewers_group);
            } else {
                unset($viewers[$key]);
            }
        }
        // number of viewers must smaller than number of seats
        if (count($original_viewers) > count($original_seats)) {
            return response()->json('number of viewers must smaller than number of seats', Response::HTTP_BAD_REQUEST);
        }
        // prepare data: sort viewers and shuffle seats...
        shuffle($viewers);
        usort($viewers, function ($a, $b) {
            if (count($a) < count($b)) {return 1;}
            if (count($a) > count($b)) {return -1;}
            return 0;
        });
        shuffle($seats);
        // count the items of each group
        $seats_count = [];
        foreach ($seats as $key => $group) {
            $seats_count[$key] = count($group);
        }
        // set positions to viewers
        $positions = $this->array_2_slots($viewers, $seats_count);
        // set viewer to seat randomly
        $viewer_to_seat = [];
        foreach ($seats as $group_key => $seat_group) {
            if (!empty($positions[$group_key])) {
                shuffle($positions[$group_key]);
                $list = call_user_func_array('array_merge', $positions[$group_key]);
                foreach ($seat_group as $seat_key => $seat) {
                    $viewer_to_seat[$seat] = $list[$seat_key] ?? '';
                }
            }
        }
        // back to original order of seats
        $results = [];
        foreach ($original_seats as $key => $seat) {
            $results[$seat] = $viewer_to_seat[$seat] ?? '';
        }
        return [
            'vote_id' => $vote_id,
            'status' => 'success',
            'data' => $results,
        ];
    }

    /**
     * Set array to slots
     * @param: $array1, $slots, $positions
     * @return: list with format 'key' (from $array2) => 'value' (from $array1)
     * @author: AuTN
     */
    private function array_2_slots($array1 = [], &$slots = [], &$positions = [])
    {
        foreach ($array1 as $array1_group_key => $array1_group) {
            $i = 0;
            $max_available_slots_of_array2 = [0];
            foreach ($slots as $slots_group_key => $slots_group_value) {
                if ($slots_group_value > array_values($max_available_slots_of_array2)[0]) {
                    $max_available_slots_of_array2 = [
                        $slots_group_key => $slots_group_value,
                    ];
                }
                if (count($array1_group) <= $slots_group_value) {
                    // set to list
                    $positions[$slots_group_key][] = $array1_group;
                    $slots[$slots_group_key] = $slots_group_value - count($array1_group);
                    break;
                } elseif (++$i == count($slots)) {
                    // if not enoght slots, break to 2 lists
                    reset($max_available_slots_of_array2);
                    $max_slots_key = key($max_available_slots_of_array2);
                    $part1 = array_slice($array1_group, 0, $slots[$max_slots_key]);
                    $part2 = array_slice($array1_group, $slots[$max_slots_key]);
                    $positions[$max_slots_key][] = $part1;
                    $slots[$max_slots_key] = 0;
                    $this->array_2_slots([$part2], $slots, $positions);
                }
            }
        }
        return $positions;
    }
}
