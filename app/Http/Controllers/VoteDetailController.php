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
        $voteDetails = $this->repository->all($columns = ['*']);
        return $this->success($voteDetails, trans('messages.voteDetails.getListSuccess'));
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

        if (!$voteDetail) {
            return $this->error(trans('messages.errors.errorVoteDetail'), trans('messages.errors.badRequest'), Response::HTTP_BAD_REQUEST);
        }

        return $this->success($voteDetail, trans('messages.voteDetails.storeSuccess'), ['code' => Response::HTTP_CREATED]);
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
        return $this->success($voteDetail, trans('messages.voteDetails.showSuccess'));
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
        $voteDetail = $this->repository->update($request->all(), $id);
        return $this->success($voteDetail, trans('messages.voteDetails.updateSuccess'));
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
            return $this->error(trans('messages.errors.errorVoteDetail'), trans('messages.errors.badRequest'), Response::HTTP_BAD_REQUEST);
        }

        return $this->success([], trans('messages.voteDetails.deleteSuccess'), ['code' => Response::HTTP_NO_CONTENT]);
    }

    /**
     * Check user voted
     * @param  Request $request
     * @return \Illuminate\Http\Response
     */
    public function checkVoted(Request $request)
    {
        $check = $this->repository->checkVotes($request->all());
        return $this->success(['data' => $check], trans('messages.voteDetails.success'));
    }

    /**
     * User unvote
     * @param  Request $request
     * @return \Illuminate\Http\Response
     */
    public function unVoted(Request $request)
    {
        $this->repository->delVote($request->all());
        return $this->success([], trans('messages.voteDetails.deleteSuccess'), ['code' => Response::HTTP_NO_CONTENT]);
    }

    /**
     * Delete all vote detail by vote
     * @param  int  $vote_id
     * @return \Illuminate\Http\Response
     */
    public function deleteAll($voteId)
    {
        $this->repository->delAll($voteId);
        return $this->success([], trans('messages.voteDetails.deleteSuccess'), ['code' => Response::HTTP_NO_CONTENT]);
    }
}
