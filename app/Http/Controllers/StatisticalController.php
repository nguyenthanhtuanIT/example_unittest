<?php

namespace App\Http\Controllers;

use App\Http\Requests\StatisticalCreateRequest;
use App\Http\Requests\StatisticalUpdateRequest;
use App\Repositories\Contracts\StatisticalRepository;
use Illuminate\Http\Request;
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
        return response()->json($statisticals);
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
        return response()->json($statistical, Response::HTTP_CREATED);
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
        return response()->json($statistical);
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
        return response()->json($statistical, Response::HTTP_OK);
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
     * Get info of vote to add statistical
     * @param  int $vote_id
     * @return \Illuminate\Http\Response
     */
    public function getInforByVote($voteId)
    {
        $result = $this->repository->inforByVote($voteId);
        return response()->json($result);
    }

    /**
     * Get amount votes of film
     * @param  int $vote_id
     * @return \Illuminate\Http\Response
     */
    public function getAmountVote($voteId)
    {
        $result = $this->repository->amountVoteOfFilm($voteId);
        return response()->json($result);
    }

    /**
     * Delete all by vote
     * @param  int $vote_id
     * @return \Illuminate\Http\Response
     */
    public function deleteAll($voteId)
    {
        $del = $this->repository->delAll($voteId);
        return response()->json(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * Get info to statistical
     * @return \Illuminate\Http\Response
     */
    public function getInfor()
    {
        $res = $this->repository->inforAll();
        return $res;
    }
}
