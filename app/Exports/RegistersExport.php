<?php

namespace App\Exports;

use App\Models\Films;
use App\Models\Register;
use App\Models\User;
use App\Models\Vote;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class RegistersExport implements FromCollection, WithHeadings
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public $voteId;

    public function __construct($voteId)
    {
        $this->voteId = $voteId;
    }

    /**
     * Show name of user
     * @return string
     */
    public function getUsers()
    {
        $registers = Register::where('vote_id', $this->voteId)->get();
        $users = User::all();
        foreach ($registers as $register) {
            foreach ($users as $user) {
                if ($register->user_id == $user->id) {
                    $name = $user->full_name;
                }
            }
        }

        return $name;
    }

    /**
     * Show email of user
     * @return string
     */
    public function getEmails()
    {
        $registers = Register::where('vote_id', $this->voteId)->get();
        $users = User::all();
        foreach ($registers as $register) {
            foreach ($users as $user) {
                if ($register->user_id == $user->id) {
                    $email = $user->email;
                }
            }
        }

        return $email;
    }

    /**
     * Show name votes file excel
     * @return string
     */
    public function getVotes()
    {
        $registers = Register::where('vote_id', $this->voteId)->get();
        $votes = Vote::all();
        foreach ($registers as $register) {
            foreach ($votes as $vote) {
                if ($register->vote_id == $vote->id) {
                    $result = $vote->name_vote;
                }
            }
        }

        return $result;
    }

    /**
     * Show name film file excel
     * @return string
     */
    public function getFilms()
    {
        $registers = Register::where('vote_id', $this->voteId)->get();
        $films = Films::all();
        foreach ($registers as $register) {
            foreach ($films as $film) {
                if ($register->film_id == $film->id) {
                    $result = $film->name_film;
                }
            }
        }

        return $result;
    }

    /**
     * Show list friends of user
     * @return array
     */
    public function getbestfriend()
    {
        $registers = Register::where('vote_id', $this->voteId)->get();
        $users = User::all();
        $arrayUser = [];
        foreach ($registers as $register) {
            if (!empty($register->best_friend)) {
                $arrayFriend = explode(',', $register->best_friend);
                for ($i = 0; $i < count($arrayFriend); $i++) {
                    if (is_numeric($arrayFriend[$i])) {
                        foreach ($users as $user) {
                            if ($arrayFriend[$i] == $user->id) {
                                $arrayUser[] = $user->full_name;
                            }
                        }
                    } else {
                        $arrayUser[] = $arrayFriend[$i];
                    }
                }
            }

        }

        return implode(',', $arrayUser);
    }

    /**
     * Show data file excel
     * @return string
     */
    public function collection()
    {
        $registers = Register::where('vote_id', $this->voteId)->get();
        if ($registers) {
            foreach ($registers as $register) {
                $data[] = [
                    '0' => $this->getUsers(),
                    '1' => $this->getEmails(),
                    '2' => $this->getVotes(),
                    '3' => $this->getFilms(),
                    '4' => $register->ticket_number,
                    '5' => $this->getbestfriend(),
                    '6' => $register->ticket_outsite,
                ];
            }
            return collect($data);
        }
        $data[] = [];

        return collect($data);
    }

    /**
     * Show name column file excel
     * @return string
     */
    public function headings(): array
    {
        return [
            'Name',
            'Email',
            'Name_vote',
            'Name_film',
            'Ticket_number',
            'Friends',
            'ticket_outsite',
        ];
    }
}
