<?php

namespace App\Http\Controllers;

use App\Http\Requests\ChooseChairCreateRequest;
use App\Http\Requests\ChooseChairUpdateRequest;
use App\Repositories\Contracts\ChooseChairRepository;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * Class ChooseChairsController.
 *
 * @package namespace App\Http\Controllers;
 */
class ChooseChairController extends Controller
{
    /**
     * @var ChooseChairRepository
     */
    protected $repository;

    /**
     * ChooseChairsController constructor.
     *
     * @param ChooseChairRepository $repository
     */
    public function __construct(ChooseChairRepository $repository)
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
        $chooseChairs = $this->repository->paginate($limit, $columns = ['*']);

        return $this->success($chooseChairs, trans('messages.chooseChairs.getListSuccess'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  ChooseChairCreateRequest $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $chooseChair = $this->repository->create($request->all());

        if (is_null($chooseChair)) {
            return $this->error(trans('messages.errors.errorChooseChairs'), trans('messages.errors.badRequest'), Response::HTTP_BAD_REQUEST);
        }

        return $this->success($chooseChair, trans('messages.chooseChairs.storeSuccess'), ['code' => Response::HTTP_CREATED]);
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
        $chooseChair = $this->repository->find($id);
        return $this->success($chooseChair, trans('messages.chooseChairs.showSuccess'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  ChooseChairUpdateRequest $request
     * @param  string $id
     *
     * @return Response
     */
    public function update(Request $request, $id)
    {
        $chooseChair = $this->repository->update($request->all(), $id);
        return $this->success($chooseChair, trans('messages.chooseChairs.updateSuccess'));
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
        return $this->success([], trans('messages.chooseChairs.deleteSuccess'), ['code' => Response::HTTP_NO_CONTENT]);
    }

    /**
     * Get ticket user buy
     * @param  Request $request
     * @return \Illuminate\Http\Response
     */
    public function ticketOfUser(Request $request)
    {
        $ticket = $this->repository->ticketUser($request->all());
        return $this->success($ticket, trans('messages.chooseChairs.success'));
    }

    /**
     * Get chair user choosed
     * @param  Request $request
     * @return \Illuminate\Http\Response
     */
    public function checkUserChoosed(Request $request)
    {
        $check = $this->repository->checkChoosed($request->all());
        return $this->success($check, trans('messages.chooseChairs.success'));
    }

    /**
     * Allow user rechoose chair
     * @param  Request $request
     * @return \Illuminate\Http\Response
     */
    public function reChooses(Request $request)
    {
        $reChoose = $this->repository->reChoose($request->all());
        return $this->success($reChoose, trans('messages.chooseChairs.rechooseSuccess'), ['code' => Response::HTTP_CREATED]);
    }

    /**
     * Random chair
     * @param  Request $request
     * @return \Illuminate\Http\Response
     */
    public function randChairs(Request $request)
    {
        $random = $this->repository->randChair($request->all());

        if (is_null($random['data'])) {
            return $this->error(trans('messages.errors.errorChooseChairsInvalid'), trans('messages.errors.badRequest'), Response::HTTP_BAD_REQUEST);
        }

        if (empty($random['data'])) {
            return $this->error(trans('messages.errors.errorChooseChairsEmpty'), trans('messages.errors.badRequest'), Response::HTTP_BAD_REQUEST);
        }

        return $this->success($random, trans('messages.chooseChairs.success'));
    }

    /**
     * Delete all chair user choose by vote
     * @param  int $vote_id
     * @return \Illuminate\Http\Response
     */
    public function deleteAll($voteId)
    {
        $this->repository->delAll($voteId);
        return $this->success([], trans('messages.chooseChairs.deleteSuccess'), ['code' => Response::HTTP_NO_CONTENT]);
    }
}
