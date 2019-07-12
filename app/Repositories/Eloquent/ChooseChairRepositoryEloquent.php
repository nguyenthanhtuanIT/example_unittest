<?php

namespace App\Repositories\Eloquent;

use App\Models\Chair;
use App\Models\ChooseChair;
use App\Models\Register;
use App\Models\User;
use App\Models\Vote;
use App\Presenters\ChooseChairPresenter;
use App\Repositories\Contracts\ChooseChairRepository;
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
        $count = $this->getOfUserByVote($attributes['user_id'], $attributes['vote_id'])->first();
        $result = null;
        $seat = explode(',', $attributes['seats']);

        if (!empty($count)) {
            $chairs = $this->model()::whereNotIn('id', [$count->id])->where('vote_id', $attributes['vote_id'])->get();
            $this->model()::find($count->id)->delete();
        } else {
            $chairs = $this->model()::where('vote_id', $attributes['vote_id'])->get();
        }
        foreach ($chairs as $val) {
            $chair = explode(',', $val->seats);
            for ($i = 0; $i < count($chair); $i++) {
                for ($j = 0; $j < count($seat); $j++) {
                    if ($chair[$i] == $seat[$j]) {
                        return $result;
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

        return $ticket[0];
    }

    /**
     * Check status choose chair of user
     * @param  array  $attributes
     * @return \Illuminate\Http\Response
     */
    public function checkChoosed(array $attributes)
    {
        $check = false;
        $result = [];
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
        }
        $choosed = $this->getOfUserByVote($user, $attributes['vote_id'])->first();

        if ($choosed) {
            $check = true;
            $result = ['check' => $check, 'seats' => $choosed->seats];
        }
        $result = ['check' => $check];

        return $result;
    }
    /**
     * User rechoose chairs
     * @param  array  $attributes
     * @return \Illuminate\Http\Response
     */
    public function reChoose(array $attributes)
    {
        $find = $this->getOfUserByVote($attributes['user_id'], $attributes['vote_id'])->first();
        $this->model()::find($find->id)->delete();
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
        $result = false;

        if ($vote->status_vote != Vote::BOOKING) {
            return $result;
        } else {
            $register = Register::where('vote_id', $vote->id)->get();
            $chairs = Chair::where('vote_id', $vote->id)->get();

            if (count($chairs) == 0) {
                return $result;
            }
            $publish = $seats = $viewers = $array = $temp = $arrayName = $array_result = [];
            foreach ($register as $value) {
                if ($value->ticket_number == 1) {
                    $name = User::find($value->user_id);
                    $arrayName[] = $name->full_name;
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
                $arraySeats = [];
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
                            $arrayResult[] = $publish;
                        } else {
                            $arrayResult[] = $publish;
                            $publish = array($arraySeats[$i]);
                            $arrayResult[] = $publish;
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
                                $arrayResult[] = $publish;
                                $publish = array($arraySeats[$i]);
                            }
                        }
                    }
                }
                $seats = $arrayResult;
                $result = $this->shuffleSeats($seats, $viewers, $vote->id);

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
        return ChooseChair::where('vote_id', $voteId)->delete();
    }

    /**
     * random seats
     * @param  array  $seats
     * @param  array  $viewers
     * @param  int $voteId
     * @return bool
     */
    public function shuffleSeats($seats = [], $viewers = [], $voteId)
    {
        $seats = array_values($seats);
        $viewers = array_values($viewers);
        $results = false;

        // seats or viewers list is empty
        if (empty($seats) || empty($viewers)) {
            return $results = null;
        }
        // validate inputs
        $originalSeats = $originalViewers = [];
        foreach ($seats as $key => $seatsGroup) {
            if (!is_array($seatsGroup)) {
                return $results;
            } elseif (!empty($seatsGroup)) {
                $originalSeats = array_merge($originalSeats, $seatsGroup);
            } else {
                unset($seats[$key]);
            }
        }

        foreach ($viewers as $key => $viewersGroup) {
            if (!is_array($viewersGroup)) {
                return $results;
            } elseif (!empty($viewersGroup)) {
                $originalViewers = array_merge($originalViewers, $viewersGroup);
            } else {
                unset($viewers[$key]);
            }
        }

        // number of viewers must smaller than number of seats
        if (count($originalViewers) > count($originalSeats)) {
            return $results;
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
        $seatsCount = [];
        foreach ($seats as $key => $group) {
            $seatsCount[$key] = count($group);
        }
        // set positions to viewers
        $positions = $this->arrayToSlots($viewers, $seatsCount);
        // set viewer to seat randomly
        $viewerToSeat = [];
        foreach ($seats as $groupKey => $seatGroup) {
            if (!empty($positions[$groupKey])) {
                shuffle($positions[$groupKey]);
                $list = call_user_func_array('array_merge', $positions[$groupKey]);
                foreach ($seatGroup as $seatKey => $seat) {
                    $viewerToSeat[$seat] = $list[$seatKey] ?? '';
                }
            }
        }
        // back to original order of seats

        foreach ($originalSeats as $key => $seat) {
            $results[$seat] = $viewerToSeat[$seat] ?? '';
        }

        return [
            'vote_id' => $voteId,
            'results' => $results,
        ];
    }

    /**
     * Set array to slots
     * @param: $temp, $slots, $positions
     * @return: list with format 'key' (from $array2) => 'value' (from $temp)
     * @author: AuTN
     */
    private function arrayToSlots($temp = [], &$slots = [], &$positions = [])
    {
        foreach ($temp as $arrayGroupKey => $tempGroup) {
            $i = 0;
            $maxAvailableSlotsOfArray = [0];
            foreach ($slots as $slotsGroupKey => $slotsGroupValue) {
                if ($slotsGroupValue > array_values($maxAvailableSlotsOfArray)[0]) {
                    $maxAvailableSlotsOfArray = [
                        $slotsGroupKey => $slotsGroupValue,
                    ];
                }

                if (count($tempGroup) <= $slotsGroupValue) {
                    // set to list
                    $positions[$slotsGroupKey][] = $tempGroup;
                    $slots[$slotsGroupKey] = $slotsGroupValue - count($tempGroup);
                    break;
                } elseif (++$i == count($slots)) {
                    // if not enoght slots, break to 2 lists
                    reset($maxAvailableSlotsOfArray);
                    $maxSlotsKey = key($maxAvailableSlotsOfArray);
                    $part1 = array_slice($tempGroup, 0, $slots[$maxSlotsKey]);
                    $part2 = array_slice($tempGroup, $slots[$maxSlotsKey]);
                    $positions[$maxSlotsKey][] = $part1;
                    $slots[$maxSlotsKey] = 0;
                    $this->arrayToSlots([$part2], $slots, $positions);
                }
            }
        }

        return $positions;
    }
}
