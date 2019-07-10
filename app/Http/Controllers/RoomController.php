<?php

namespace App\Http\Controllers;

use App\Http\Requests\RoomCreateRequest;
use App\Http\Requests\RoomUpdateRequest;
use App\Repositories\Contracts\RoomRepository;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * Class RoomsController.
 *
 * @package namespace App\Http\Controllers;
 */
class RoomController extends Controller
{
    /**
     * @var RoomRepository
     */
    protected $repository;

    /**
     * RoomsController constructor.
     *
     * @param RoomRepository $repository
     */
    public function __construct(RoomRepository $repository)
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
        $limit = request()->get('limit', null);
        $includes = request()->get('include', '');

        if ($includes) {
            $this->repository->with(explode(',', $includes));
        }
        $this->repository->pushCriteria(app('Prettus\Repository\Criteria\RequestCriteria'));
        $rooms = $this->repository->paginate($limit, $columns = ['*']);

        return $this->success($rooms, trans('messages.rooms.getListSuccess'));
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  RoomCreateRequest $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(RoomCreateRequest $request)
    {
        $room = $this->repository->create($request->all());
        return $this->success($room, trans('messages.rooms.storeSuccess'), ['code' => Response::HTTP_CREATED]);
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
        $room = $this->repository->find($id);
        return $this->success($room, trans('messages.rooms.showSuccess'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  RoomUpdateRequest $request
     * @param  string $id
     *
     * @return Response
     */
    public function update(RoomUpdateRequest $request, $id)
    {
        $room = $this->repository->update($request->all(), $id);
        return $this->success($room, trans('messages.rooms.updateSuccess'));
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
        return $this->success([], trans('messages.rooms.deleteSuccess'), ['code' => Response::HTTP_NO_CONTENT]);
    }

}
