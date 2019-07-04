<?php

namespace App\Http\Controllers;

use App\Http\Requests\ChairCreateRequest;
use App\Http\Requests\ChairUpdateRequest;
use App\Repositories\Contracts\ChairRepository;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * Class ChairsController.
 *
 * @package namespace App\Http\Controllers;
 */
class ChairController extends Controller
{
    /**
     * @var ChairRepository
     */
    protected $repository;

    /**
     * ChairsController constructor.
     *
     * @param ChairRepository $repository
     */
    public function __construct(ChairRepository $repository)
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
        $chairs = $this->repository->paginate($limit, $columns = ['*']);

        return $this->success($chairs, trans('messages.chairs.getListSuccess'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  ChairCreateRequest $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(ChairCreateRequest $request)
    {
        $chair = $this->repository->create($request->all());

        if (is_null($chair)) {
            return $this->error(trans('messages.errors.errorChairs'), trans('messages.errors.badRequest'), Response::HTTP_BAD_REQUEST);
        }

        return $this->success($chair, trans('messages.chairs.storeSuccess'), ['code' => Response::HTTP_CREATED]);
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
        $chair = $this->repository->find($id);
        return $this->success($chair, trans('messages.chairs.showSuccess'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  ChairUpdateRequest $request
     * @param  string $id
     *
     * @return Response
     */
    public function update(ChairUpdateRequest $request, $id)
    {
        $chair = $this->repository->update($request->all(), $id);
        return $this->success($chair, trans('messages.chairs.updateSuccess'));
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
        return $this->success([], trans('messages.chairs.deleteSuccess'), ['code' => Response::HTTP_NO_CONTENT]);
    }

    /**
     * Get diagram by vote
     * @param  int $vote_id
     * @return \Illuminate\Http\Response
     */
    public function getDiagramChairByVote($voteId)
    {
        $diagram = $this->repository->diagramChairByVote($voteId);
        return $this->success($diagram, trans('messages.chairs.success'));
    }

    /**
     * Update status chair to choose chair
     * @param  Request $request
     * @return \Illuminate\Http\Response
     */
    public function updateStatusChair(Request $request)
    {
        $result = $this->repository->updateChairs($request->all());
        return $this->success(['chairs' => $result], trans('messages.chairs.success'), ['isContainByDataString' => true]);
    }

    /**
     * Delete all chair by vote
     * @param int $vote_id
     * @return \Illuminate\Http\Response
     */
    public function deleteAll($voteId)
    {
        $this->repository->delAll($voteId);
        return $this->success([], trans('messages.chairs.deleteSuccess'), ['code' => Response::HTTP_NO_CONTENT]);
    }

}
