<?php

namespace App\Http\Controllers;

use App\Http\Requests\StatisticalCreateRequest;
use App\Http\Requests\StatisticalUpdateRequest;
use App\Repositories\Contracts\StatisticalRepository;
use Illuminate\Http\Response;

/**
 * Class StatisticalsController.
 *
 * @package namespace App\Http\Controllers;
 */
class StatisticalController extends Controller
{
    /**
     * @var StatisticalRepository
     */
    protected $repository;

    /**
     * StatisticalsController constructor.
     *
     * @param StatisticalRepository $repository
     */
    public function __construct(StatisticalRepository $repository)
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
        $statisticals = $this->repository->all($columns = ['*']);

        return $this->success($statisticals, trans('messages.statisticals.getListSuccess'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  StatisticalCreateRequest $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(StatisticalCreateRequest $request)
    {
        $statistical = $this->repository->create($request->all());
        return $this->success($statistical, trans('messages.statisticals.storeSuccess'), ['code' => Response::HTTP_CREATED]);
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
        $statistical = $this->repository->find($id);
        return $this->success($statistical, trans('messages.statisticals.showSuccess'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  StatisticalUpdateRequest $request
     * @param  string $id
     *
     * @return Response
     */
    public function update(StatisticalUpdateRequest $request, $id)
    {
        $statistical = $this->repository->update($request->all(), $id);
        return $this->success($statistical, trans('messages.statisticals.updateSuccess'));
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
        return $this->success([], trans('messages.statisticals.deleteSuccess'), ['code' => Response::HTTP_NO_CONTENT]);
    }

    /**
     * Get info of vote to add statistical
     * @param  int $vote_id
     * @return \Illuminate\Http\Response
     */
    public function getInfoByVote($voteId)
    {
        $result = $this->repository->infoByVote($voteId);

        if (empty($result)) {
            return $this->success($result, trans('messages.statisticals.movieEmpty'), ['isContainByDataString' => true]);
        }

        return $this->success($result, trans('messages.statisticals.success'), ['isContainByDataString' => true]);
    }

    /**
     * Get amount votes of film
     * @param  int $vote_id
     * @return \Illuminate\Http\Response
     */
    public function getAmountVote($voteId)
    {
        $result = $this->repository->amountVoteOfFilm($voteId);

        if (empty($result)) {
            return $this->success($result, trans('messages.statisticals.dataEmpty'), ['isContainByDataString' => true]);
        }
        return $this->success($result, trans('messages.statisticals.success'), ['isContainByDataString' => true]);
    }

    /**
     * Delete all by vote
     * @param  int $vote_id
     * @return \Illuminate\Http\Response
     */
    public function deleteAll($voteId)
    {
        $this->repository->delAll($voteId);
        return $this->success([], trans('messages.statisticals.deleteSuccess'), ['code' => Response::HTTP_NO_CONTENT]);
    }

    /**
     * Get info to statistical
     * @return \Illuminate\Http\Response
     */
    public function getInfo()
    {
        $result = $this->repository->infoAll();

        if (empty($result)) {
            return $this->success($result, trans('messages.statisticals.movieEmpty'), ['isContainByDataString' => true]);
        }

        return $this->success($result, trans('messages.statisticals.success'), ['isContainByDataString' => true]);
    }
}
