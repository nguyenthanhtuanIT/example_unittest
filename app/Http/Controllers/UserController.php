<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserCreateRequest;
use App\Http\Requests\UserRegisterRequest;
use App\Http\Requests\UserUpdateRequest;
use App\Repositories\Contracts\UserRepository;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

/**
 * Class UsersController.
 *
 * @package namespace App\Http\Controllers;
 */
class UserController extends Controller
{
    /**
     * @var UserRepository
     */
    protected $repository;

    /**
     * UsersController constructor.
     *
     * @param UserRepository $repository
     */
    public function __construct(UserRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = $this->repository->all($columns = ['*']);
        return response()->json($user);
    }

    /**
     * Display a info of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function me(Request $request)
    {
        $user = $this->repository->find(auth()->user()->id);
        return response()->json($user);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  UserCreateRequest $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(UserCreateRequest $request)
    {
        $user = $this->repository->skipPresenter()->create($request->all());
        return $this->presenterPostJson($user);
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $user = $this->repository->find($id);
        return response()->json($user);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  UserUpdateRequest $request
     * @param  string $id
     *
     * @return Response
     */
    public function update(UserUpdateRequest $request, $id)
    {
        $user = $this->repository->update($request->all(), $id);
        return response()->json($user, Response::HTTP_CREATED);

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $this->repository->delete($id);
        return response()->json(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  UserRegisterRequest $request
     *
     * @return \Illuminate\Http\Response
     */
    public function register(UserRegisterRequest $request)
    {
        $user = $this->repository->skipPresenter()->create(array_merge($request->all(), ['role' => 'member']));
        return $this->presenterPostJson($user);
    }

    /**
     * Get list user to invite
     * @param  Request $request
     * @return \Illuminate\Http\Response
     */
    public function listUsers(Request $request)
    {
        $user = $this->repository->getListUser($request->vote_id);
        return response()->json($user);
    }
}
