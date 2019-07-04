<?php

namespace App\Http\Controllers;

use App\Exports\RegistersExport;
use App\Http\Requests\RegisterCreateRequest;
use App\Http\Requests\RegisterUpdateRequest;
use App\Repositories\Contracts\RegisterRepository;
use Excel;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * Class RegistersController.
 *
 * @package namespace App\Http\Controllers;
 */
class RegisterController extends Controller
{
    /**
     * @var RegisterRepository
     */
    protected $repository;

    /**
     * RegistersController constructor.
     *
     * @param RegisterRepository $repository
     */
    public function __construct(RegisterRepository $repository)
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
        $register = $this->repository->all($colums = ['*']);
        return response()->json($register, Response::HTTP_OK);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  RegisterCreateRequest $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $register = $this->repository->create($request->all());
        return response()->json($register, Response::HTTP_CREATED);
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
        $register = $this->repository->find($id);
        return response()->json($register);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  RegisterUpdateRequest $request
     * @param  string $id
     *
     * @return Response
     */
    public function update(Request $request, $id)
    {
        $register = $this->repository->skipPresenter()
            ->update($request->all(), $id);
        return response()->json($register->presenter(), Response::HTTP_OK);
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
     * convert data register to excel
     * @param int $vote_id
     * @return \Illuminate\Http\Response
     */
    public function Export($vote_id)
    {
        return Excel::download(new RegistersExport($vote_id), 'listregister.xlsx');
    }

    /**
     * Check user register
     * @param  Request $request
     * @return \Illuminate\Http\Response
     */
    public function checkRegistered(Request $request)
    {
        $array = $this->repository->checkRegister($request->all());
        return $array;
    }

    /**
     * User unRegister
     * @param  Request $request
     * @return \Illuminate\Http\Response
     */
    public function unRegister(Request $request)
    {
        $unregister = $this->repository->delRegister($request->all());
        return $unregister;
    }

    /**
     * User can refuses register
     * @param  Request $request
     * @return \Illuminate\Http\Response
     */
    public function guestRefuses(Request $request)
    {
        $status = $this->repository->guestRefuse($request->all());
        return response()->json(['status' => $status], Response::HTTP_OK);
    }

    /**
     * User agree invite
     * @param  Request $request
     * @return \Illuminate\Http\Response
     */
    public function userAgree(Request $request)
    {
        $result = $this->repository->agree($request->all());
        return response()->json($result);
    }
}
