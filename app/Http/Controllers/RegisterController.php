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
        $registers = $this->repository->all($colums = ['*']);
        return $this->success($registers, trans('messages.registers.getListSuccess'));
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

        if (is_null($register)) {
            return $this->error(trans('messages.errors.errorCreateRegister'), trans('messages.errors.badRequest'), Response::HTTP_BAD_REQUEST);
        }

        if (!$register) {
            return $this->error(trans('messages.errors.errorRegisterAdd'), trans('messages.errors.badRequest'), Response::HTTP_BAD_REQUEST);
        }
      
        return $this->success($register, trans('messages.registers.storeSuccess'), ['code' => Response::HTTP_CREATED]);
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
        return $this->success($register, trans('messages.registers.showSuccess'));
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
        $register = $this->repository->update($request->all(), $id);

        if (!$register) {
            return $this->error(trans('messages.errors.errorUpdateRegister'), trans('messages.errors.badRequest'), Response::HTTP_BAD_REQUEST);
        }
      
        return $this->success($register, trans('messages.registers.updateSuccess'));
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
        $delete = $this->repository->delete($id);

        if (!$delete) {
            return $this->error(trans('messages.errors.errorDeleteRegister'), trans('messages.errors.badRequest'), Response::HTTP_BAD_REQUEST);
        }

        return $this->success([], trans('messages.registers.deleteSuccess'), ['code' => Response::HTTP_NO_CONTENT]);
    }

    /**
     * convert data register to excel
     * @param int $vote_id
     * @return \Illuminate\Http\Response
     */
    public function Export($voteId)
    {
        return Excel::download(new RegistersExport($voteId), 'listregister.xlsx');
    }

    /**
     * Check user register
     * @param  Request $request
     * @return \Illuminate\Http\Response
     */
    public function checkRegistered(Request $request)
    {
        $checkRegister = $this->repository->checkRegister($request->all());
        return $this->success($checkRegister, trans('messages.registers.success'), ['isContainByDataString' => true]);
    }

    /**
     * User unRegister
     * @param  Request $request
     * @return \Illuminate\Http\Response
     */
    public function unRegister(Request $request)
    {
        $unRegister = $this->repository->delRegister($request->all());

        if ($unRegister) {
            return $this->success([], trans('messages.registers.success'), ['isContainByDataString' => true]);
        }
    }

    /**
     * User can refuses register
     * @param  Request $request
     * @return \Illuminate\Http\Response
     */
    public function guestRefuses(Request $request)
    {
        $status = $this->repository->guestRefuse($request->all());

        if ($status) {
            return $this->success($status, trans('messages.registers.success'), ['isContainByDataString' => true]);
        }

        return $this->error(trans('messages.errors.errorGuestRefuse'), trans('messages.errors.badRequest'), Response::HTTP_BAD_REQUEST);
    }

    /**
     * User agree invite
     * @param  Request $request
     * @return \Illuminate\Http\Response
     */
    public function userAgree(Request $request)
    {
        $result = $this->repository->agree($request->all());

        if ($result) {
            return $this->success($result, trans('messages.registers.success'), ['isContainByDataString' => true]);
        }

        return $this->error(trans('messages.errors.errorUserAgree'), trans('messages.errors.badRequest'), Response::HTTP_BAD_REQUEST);
    }
}
