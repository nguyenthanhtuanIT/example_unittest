<?php

namespace App\Repositories\Eloquent;

use App\Models\Image;
use App\Models\Register;
use App\Repositories\Contracts\UserRepository;
use App\Services\RoleService;
use App\User;
use Illuminate\Support\Facades\Storage;
use Prettus\Repository\Criteria\RequestCriteria;
use Prettus\Repository\Eloquent\BaseRepository;

/**
 * Class UserRepositoryEloquent.
 *
 * @package namespace App\Repositories\Eloquent;
 */
class UserRepositoryEloquent extends BaseRepository implements UserRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return User::class;
    }

    public function presenter()
    {
        return \App\Presenters\UserPresenter::class;
    }

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }

    /**
     * Override method create to add owners
     * @param  array  $attributes attributes from request
     * @return object
     */
    public function create(array $attributes)
    {
        $attributes['password'] = bcrypt($attributes['password']);
        $user = parent::create(array_except($attributes, 'role'));

        // find or create role admin
        if (!empty($attributes['role'])) {
            RoleService::add($user, $attributes['role']);
        }

        return $user;
    }

    /**
     * Custom update
     * @param  array  $attributes
     * @param  int $id
     * @return  \Illuminate\Http\Response
     */
    public function update(array $attributes, $id)
    {
        if (!empty($attributes['password'])) {
            $attributes['password'] = bcrypt($attributes['password']);
        }
        $user = parent::update(array_except($attributes, 'role', 'photo'), $id);

        if (!empty($attributes['role'])) {
            RoleService::sync($user, $attributes['role']);
        }

        if (!empty($attributes['photo'])) {
            if ($user->image) {
                Storage::delete($user->image->pathname);
                Storage::delete('thumbnails/' . $user->image->filename);
                $user->image->delete();
            }
            Image::where('id', $attributes['photo'])->update([
                'object_id' => $user->id,
                'object_type' => User::IMAGE_TYPE,
            ]);
        }
        return $this->find($id);
    }

    /**
     * Get list user to invite
     * @param  int$voteId [description]
     * @return  \Illuminate\Http\Response
     */
    public function getListUser($voteId)
    {
        $array = array();
        $arrayRegister = array();
        $registers = Register::where('vote_id', $voteId)->get();
        foreach ($registers as $value) {
            array_push($array, $value->user_id);
        }
        $registerNumber = Register::where('vote_id', $voteId)->where('ticket_number', '>', 1)->get();
        foreach ($registerNumber as $value) {
            $user = explode(',', $value->best_friend);
            for ($i = 0; $i < count($user); $i++) {
                if (is_numeric($user[$i])) {
                    $handing = (int) $user[$i];
                    $arrayRegister[] = $handing;
                } else {
                    $arrayRegister[] = $user[$i];
                }

            }
        }
        $arrayMerge = array_merge($array, $arrayRegister);
        $result = array_unique($arrayMerge);
        $users = User::whereNotIn('id', $result)->get(['id', 'avatar', 'full_name', 'email']);
        return response()->json($users);
    }
}
