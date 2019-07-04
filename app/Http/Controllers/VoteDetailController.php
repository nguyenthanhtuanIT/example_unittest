<?php

namespace App\Http\Controllers;

use App\Http\Requests\VoteDetailsCreateRequest;
use App\Http\Requests\VoteDetailsUpdateRequest;
use App\Repositories\Contracts\VoteDetailsRepository;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * Class VoteDetailsController.
 *
 * @package namespace App\Http\Controllers;
 */
class VoteDetailController extends Controller
{
    /**
     * @var VoteDetailsRepository
     */
    protected $repository;

    /**
     * VoteDetailsController constructor.
     *
     * @param VoteDetailsRepository $repository
     */
    public function __construct(VoteDetailsRepository $repository)
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
        $voteDetails = $this->repository->all($columns = ['*']);
        return response()->json($voteDetails);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  VoteDetailsCreateRequest $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $voteDetail = $this->repository->create($request->all());
        return response()->json($voteDetail, Response::HTTP_CREATED);
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
        $voteDetail = $this->repository->find($id);
        return response()->json($voteDetail);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  VoteDetailsUpdateRequest $request
     * @param  string $id
     *
     * @return Response
     */
    public function update(VoteDetailsUpdateRequest $request, $id)
    {
        $voteDetail = $this->repository->skipPresenter()->update($request->all(), $id);
        return response()->json($voteDetail->presenter(), Response::HTTP_OK);
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
     * Check user voted
     * @param  Request $request
     * @return \Illuminate\Http\Response
     */
    public function checkVoted(Request $request)
    {
        $check = $this->repository->checkVotes($request->all());
        return response()->json($check);
    }

    /**
     * User unvote
     * @param  Request $request
     * @return \Illuminate\Http\Response
     */
    public function unVoted(Request $request)
    {
        $unvote = $this->repository->delVote($request->all());
        return response()->json(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * Delete all vote detail by vote
     * @param  int  $vote_id
     * @return \Illuminate\Http\Response
     */
    public function deleteAll($vote_id)
    {
        $del = $this->repository->delAll($vote_id);
        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
