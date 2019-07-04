<?php

namespace App\Http\Controllers;

use App\Http\Requests\CinemaCreateRequest;
use App\Http\Requests\CinemaUpdateRequest;
use App\Repositories\Contracts\CinemaRepository;
use Illuminate\Http\Response;

/**
 * Class CinemasController.
 *
 * @package namespace App\Http\Controllers;
 */
class CinemaController extends Controller
{
    /**
     * @var CinemaRepository
     */
    protected $repository;

    /**
     * CinemasController constructor.
     *
     * @param CinemaRepository $repository
     */
    public function __construct(CinemaRepository $repository)
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
        $cinemas = $this->repository->paginate($limit, $columns = ['*']);

        return $this->success($cinemas, trans('messages.cinemas.getListSuccess'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  CinemaCreateRequest $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(CinemaCreateRequest $request)
    {
        $cinema = $this->repository->create($request->all());
        return $this->success($cinema, trans('messages.cinemas.storeSuccess'), ['code' => Response::HTTP_CREATED]);
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
        $cinema = $this->repository->find($id);
        return $this->success($cinema, trans('messages.cinemas.showSuccess'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  CinemaUpdateRequest $request
     * @param  string $id
     *
     * @return Response
     */
    public function update(CinemaUpdateRequest $request, $id)
    {
        $cinema = $this->repository->update($request->all(), $id);
        return $this->success($cinema, trans('messages.cinemas.updateSuccess'));
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
        return $this->success([], trans('messages.cinemas.deleteSuccess'), ['code' => Response::HTTP_NO_CONTENT]);
    }
}
