<?php

namespace App\Http\Controllers;

use App\Http\Requests\DiagramCreateRequest;
use App\Http\Requests\DiagramUpdateRequest;
use App\Repositories\Contracts\DiagramRepository;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * Class DiagramsController.
 *
 * @package namespace App\Http\Controllers;
 */
class DiagramController extends Controller
{
    /**
     * @var DiagramRepository
     */
    protected $repository;

    /**
     * DiagramsController constructor.
     *
     * @param DiagramRepository $repository
     */
    public function __construct(DiagramRepository $repository)
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
        $diagrams = $this->repository->custom();

        return $this->success(['data' => $diagrams], trans('messages.diagrams.getListSuccess'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  DiagramCreateRequest $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(DiagramCreateRequest $request)
    {
        $diagram = $this->repository->create($request->all());

        if (is_null($diagram)) {
            return $this->error(trans('messages.errors.errorCreateDiagram'), trans('messages.errors.badRequest'), Response::HTTP_BAD_REQUEST);
        }

        return ['data' => $diagram];
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
        $diagram = $this->repository->find($id);
        return $this->success($diagram, trans('messages.diagrams.showSuccess'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  DiagramUpdateRequest $request
     * @param  string $id
     *
     * @return Response
     */
    public function update(DiagramUpdateRequest $request, $id)
    {
        $diagram = $this->repository->update($request->all(), $id);
        return $this->success($diagram, trans('messages.diagrams.updateSuccess'));
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
        return $this->success([], trans('messages.diagrams.deleteSuccess'), ['code' => Response::HTTP_NO_CONTENT]);
    }

    /**
     * Get Diagram chair by vote
     * @param  Request $request
     * @return \Illuminate\Http\Response
     */
    public function diagramChairByVote(Request $request)
    {
        $result = $this->repository->getDiagramChairByVote($request->vote_id);
        return $this->success($result, trans('messages.diagrams.success'));
    }

    /**
     * Get diagram by room
     * @param  Request $request
     * @return \Illuminate\Http\Response
     */
    public function searchByRoom(Request $request)
    {
        $search = $this->repository->searchByRoomId($request->room_id);
        $result = $this->repository->parserResult($search);

        return $this->success($result, trans('messages.diagrams.success'));
    }

    /**
     * Delete all diagram by room_id
     * @param  int $room_id
     * @return \Illuminate\Http\Response
     */
    public function deleteAll($roomId)
    {
        $this->repository->delAll($roomId);
        return $this->success([], trans('messages.diagrams.deleteSuccess'), ['code' => Response::HTTP_NO_CONTENT]);
    }
}
