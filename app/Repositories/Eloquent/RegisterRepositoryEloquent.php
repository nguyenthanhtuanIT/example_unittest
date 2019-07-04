<?php

namespace App\Repositories\Eloquent;

use App\Mail\MailAgree;
use App\Mail\MailCancel;
use App\Mail\MailFeedback;
use App\Models\Register;
use App\Presenters\RegisterPresenter;
use App\Repositories\Contracts\RegisterRepository;
use App\Services\StatisticalService;
use App\Services\VoteService;
use App\User;
use Mail;
use Prettus\Repository\Criteria\RequestCriteria;
use Prettus\Repository\Eloquent\BaseRepository;

/**
 * Class RegisterRepositoryEloquent.
 *
 * @package namespace App\Repositories\Eloquent;
 */
class RegisterRepositoryEloquent extends BaseRepository implements RegisterRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return Register::class;
    }

    /**
     * Specify Presenter class name
     *
     * @return string
     */
    public function presenter()
    {
        return RegisterPresenter::class;
    }

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }

    /**
     * Custom create function
     * @param  array  $attributes
     * @return \Illuminate\Http\Response
     */
    public function create(array $attributes)
    {
        $ticketOutsite = 0;
        $register = null;
        $count = $this->model()::where([
            'user_id' => $attributes['user_id'],
            'vote_id' => $attributes['vote_id'],
        ])->count();
        $user = User::find($attributes['user_id']);
        $ticketNumber = $attributes['ticket_number'];

        if ($count) {
            return $register;
        } else {

            if (!empty($attributes['best_friend'])) {
                $friends = explode(',', $attributes['best_friend']);
                if ($ticketNumber > count($friends)) {
                    $ticketOutsite = $ticketNumber - 1 - count($friends);
                }
                for ($i = 1; $i <= $ticketOutsite; $i++) {
                    $friends[] = "$user->full_name $i";
                }
                for ($i = 0; $i < count($friends); $i++) {
                    if (empty($friends[$i])) {
                        unset($friends[$i]);
                    }
                }
                for ($i = 0; $i < count($friends); $i++) {
                    $user = User::find($friends[$i]);
                    Mail::to($user->email)->queue(new MailInvite($user));
                }
                $attributes['best_friend'] = implode(',', $friends);
            } else {
                $listFriend = explode(',', $attributes['best_friend']);
                $ticketOutsite = $ticketNumber - 1;
                for ($i = 1; $i <= $ticketOutsite; $i++) {
                    $a[] = "$user->full_name $i";
                }
                for ($i = 0; $i < count($listFriend); $i++) {
                    if (empty($listFriend[$i])) {
                        unset($listFriend[$i]);
                    }
                }
                $attributes['best_friend'] = implode(',', $listFriend);
            }
            $attributes['ticket_outsite'] = $ticketOutsite;
            $register = parent::create($attributes);
            StatisticalService::addRegister($register['data']['attributes']['film_id'], $register['data']['attributes']['vote_id']);
            VoteService::addTicket($register['data']['attributes']['vote_id'], $register['data']['attributes']['ticket_number']);

            return $register;
        }

    }

    /**
     * Custom delete
     * @param  int $id
     * @return Illuminate\Http\Response
     */
    public function delete($id)
    {
        $find = Register::find($id);
        StatisticalService::updateRegister($find->film_id, $find->vote_id);
        VoteService::deleteTicket($find->vote_id, $find->ticket_number);
        return parent::delete($id);
    }

    /**
     * Custom update
     * @param  array  $attributes
     * @param  int $id
     * @returnIlluminate\Http\Response
     */
    public function update(array $attributes, $id)
    {
        $register = parent::update($attributes, $id);

        if (!empty($attributes['ticket_number'])) {
            $find = Register::find($id);
            $numberOld = $find->ticket_number;
            $numberNew = $register->ticket_number;
            VoteService::updateTicket($find->vote_id, $numberOld, $numberNew);
        }

        return $register;
    }

    /**
     * Get user register
     * @param  int $userId
     * @param  int $voteId
     * @return  Illuminate\Http\Response
     */
    public function getUserRegister($userId, $voteId)
    {
        return Register::where(['user_id' => $userId, 'vote_id' => $voteId]);
    }

    /**
     * Check User Register
     * @param  array  $attributes
     * @return Illuminate\Http\Response
     */
    public function checkRegister(array $attributes)
    {
        $check = false;
        $guest = false;
        $agree = false;
        $userRegister = $this->getUserRegister($attributes['user_id'], $attributes['vote_id'])->get();
        $register = Register::where('vote_id', $attributes['vote_id'])->where('ticket_number', '>', 1)->get();

        if ($userRegister->count() != 0) {
            foreach ($userRegister as $value) {
                $check = true;
                return [
                    'check' => $check,
                    'guest' => $guest,
                    'user_id' => $value->user_id,
                    'ticket_number' => $value->ticket_number,
                ];
            }
        } elseif ($register->count() != 0) {
            foreach ($register as $value) {
                $people = explode(',', $value->best_friend);
                for ($i = 0; $i < count($people); $i++) {
                    if ($people[$i] == $attributes['user_id']) {
                        $check = true;
                        $guest = true;
                        $id = $value->user_id;
                        $user = User::find($id);

                        if (!empty($value->agree)) {
                            $arrayAgree = explode(',', $value->agree);
                            for ($i = 0; $i < count($arrayAgree); $i++) {
                                if ($arrayAgree[$i] == $attributes['user_id']) {
                                    $agree = true;
                                    break;
                                }
                            }
                            return [
                                'check' => $check,
                                'guest' => $guest,
                                'user_id' => $id,
                                'fullname' => $user->full_name,
                                'avatar' => $user->avatar,
                                'agree' => $agree,
                            ];
                        } else {
                            return [
                                'check' => $check,
                                'guest' => $guest,
                                'fullname' => $user->full_name,
                                'avatar' => $user->avatar,
                                'user_id' => $id,
                            ];
                            break;
                        }
                    }
                }
            }
        }

        return ['check' => $check, 'guest' => $guest];
    }

    /**
     * Delete register
     * @param  array  $attributes
     * @return Illuminate\Http\Response
     */
    public function delRegister(array $attributes)
    {
        $register = $this->getUserRegister($attributes['user_id'], $attributes['vote_id'])->first();
        $user = User::find($attributes['user_id']);

        if (!empty($register->best_friend)) {
            $arrayFriends = explode(',', $register->best_friend);
            for ($i = 0; $i < count($arrayFriends); $i++) {
                if (is_numeric($arrayFriends[$i])) {
                    $guest = User::find($arrayFriends[$i]);
                    Mail::to($guest->email)->queue(new MailCancel($user));
                }
            }
        }

        return $this->delete($register->id);
    }

    /**
     * User Refuse
     * @param  array  $attributes
     * @return Illuminate\Http\Response
     */
    public function guestRefuse(array $attributes)
    {
        $voteId = $attributes['vote_id'];
        $userId = $attributes['user_id'];
        $guestId = $attributes['guest_id'];
        $userGuest = $this->getUserRegister($userId, $voteId)->first();
        $arrayFriends = explode(',', $userGuest->best_friend);
        for ($i = 0; $i < count($arrayFriends); $i++) {
            if ($arrayFriends[$i] == $guestId) {
                unset($arrayFriends[$i]);
                break;
            }
        }
        $convert = implode(',', $arrayFriends);
        $number = count($arrayFriends) + 1;
        $update = $this->getUserRegister($userId, $voteId)->update(['best_friend' => $convert, 'ticket_number' => $number]);

        if ($update == 1) {
            $newRegister = $this->getUserRegister($userId, $voteId)->first();
            VoteService::updateTicket($voteId, $userGuest->ticket_number, $newRegister->ticket_number);
        }
        $user = User::find($userId);
        Mail::to($user->email)->queue(new MailFeedback());

        return $result = 'success';
    }

    /**
     * User feedback invite
     * @param  array  $attributes
     * @return Illuminate\Http\Response
     */
    public function agree(array $attributes)
    {
        $registers = $this->getUserRegister($attributes['user_id'], $attributes['vote_id'])->first();
        $arrayAgree = [];

        if (!empty($registers->agree)) {
            $arrayAgree = explode(',', $registers->agree);
        }
        $arrayAgree[] = $attributes['guest_id'];
        $agree = implode(',', $arrayAgree);
        $update = $this->getUserRegister($attributes['user_id'], $attributes['vote_id'])->update(['agree' => $agree]);

        if ($update == 1) {
            $user = User::find($attributes['user_id']);
            Mail::to($user->email)->queue(new MailAgree());
            return ['result' => 'success'];
        }

        return ['result' => 'fail'];
    }
}
