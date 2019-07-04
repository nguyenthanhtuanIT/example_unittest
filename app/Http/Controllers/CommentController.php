<?php

namespace App\Http\Controllers;

use App\Http\Requests\CommentCreateRequest;
use App\Http\Requests\CommentUpdateRequest;
use App\Repositories\Contracts\CommentRepository;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * Class CommentsController.
 *
 * @package namespace App\Http\Controllers;
 */
class CommentController extends Controller
{
    /**
     * @var CommentRepository
     */
    protected $repository;

    /**
     * CommentsController constructor.
     *
     * @param CommentRepository $repository
     */
    public function __construct(CommentRepository $repository)
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
        $comments = $this->repository->paginate($limit, $columns = ['*']);

        return $this->success($comments, trans('messages.comments.getListSuccess'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  CommentCreateRequest $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(CommentCreateRequest $request)
    {
        $comment = $this->repository->create($request->all());
        return $this->success($comment, trans('messages.comments.storeSuccess'), ['code' => Response::HTTP_CREATED]);
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
        $comment = $this->repository->find($id);
        return $this->success($comment, trans('messages.comments.showSuccess'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  CommentUpdateRequest $request
     * @param  string $id
     *
     * @return Response
     */
    public function update(CommentUpdateRequest $request, $id)
    {
        $comment = $this->repository->update($request->all(), $id);
        return $this->success($comment, trans('messages.comments.updateSuccess'));
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
        return $this->success([], trans('messages.comments.deleteSuccess'), ['code' => Response::HTTP_NO_CONTENT]);
    }

    /**
     * get comments of blog
     * @param  int blog_id
     * @return \Illuminate\Http\Response
     */
    public function getComments($blogId)
    {
        $result = $this->repository->commentsByBlog($blogId);
        return $this->success($result, trans('messages.comments.success'), ['isContainByDataString' => true]);
    }
}
