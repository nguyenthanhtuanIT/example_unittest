<?php

namespace App\Http\Controllers;

use App\Http\Requests\VoteCreateRequest;
use App\Http\Requests\VoteUpdateRequest;
use App\Models\Vote;
use App\Repositories\Contracts\VoteRepository;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * Class VotesController.
 *
 * @package namespace App\Http\Controllers;
 */
class VoteController extends Controller
{
    /**
     * @var VoteRepository
     */
    protected $repository;

    /**
     * VotesController constructor.
     *
     * @param VoteRepository $repository
     */
    public function __construct(VoteRepository $repository)
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
        $votes = $this->repository->all($colums = ['*']);
        return $this->success($votes, trans('messages.votes.getListSuccess'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  VoteCreateRequest $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(VoteCreateRequest $request)
    {
        $vote = $this->repository->create($request->all());

        return $this->success($vote, trans('messages.votes.storeSuccess'), ['code' => Response::HTTP_CREATED]);
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
        $vote = $this->repository->find($id);
        return $this->success($vote, trans('messages.votes.showSuccess'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  VoteUpdateRequest $request
     * @param  string $id
     *
     * @return Response
     */
    public function update(VoteUpdateRequest $request, $id)
    {
        $vote = $this->repository->update($request->all(), $id);
        return $this->success($vote, trans('messages.votes.updateSuccess'));
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
        return $this->success([], trans('messages.votes.deleteSuccess'), ['code' => Response::HTTP_NO_CONTENT]);
    }

    /**
     * Search vote by title
     * @param  Request $request
     * @return \Illuminate\Http\Response
     */
    public function searchByTitle(Request $request)
    {
        $result = $this->repository->search($request->title);
        return $this->success($result, trans('messages.votes.success'), ['isContainByDataString' => true]);
    }

    /**
     * Show status of vote
     * @return \Illuminate\Http\Response
     */
    public function showStatusVote()
    {
        $vote = $this->repository->getStatus();

        if (is_null($vote)) {
            return $this->success($vote, trans('messages.votes.dataEmpty'), ['isContainByDataString' => true]);
        }

        if (!$vote) {
            return $this->success($vote, trans('messages.votes.buyChairs'), ['isContainByDataString' => true]);
        }

        return $this->success($vote, trans('messages.votes.success'), ['isContainByDataString' => true]);
    }

    /**
     * Show info of vote
     * @param  Request $request
     * @return \Illuminate\Http\Response
     */
    public function infoVotes(Request $request)
    {
        $info = $this->repository->info($request->vote_id);

        if (is_null($info)) {
            return $this->success($info, trans('messages.votes.dataEmpty'), ['isContainByDataString' => true]);
        }

        return $this->success($info, trans('messages.votes.success'), ['isContainByDataString' => true]);
    }
}
