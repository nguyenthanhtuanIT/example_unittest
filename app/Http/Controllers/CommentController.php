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
        return response()->json($comments);
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
        $comment = $this->repository->skipPresenter()->create($request->all());
        return response()->json($comment->presenter(), Response::HTTP_CREATED);
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

        return response()->json($comment);
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
        $comment = $this->repository->skipPresenter()->update($request->all(), $id);
        return response()->json($comment->presenter(), Response::HTTP_OK);
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
     * get comments of blog
     * @param  int blog_id
     * @return \Illuminate\Http\Response
     */
    public function getComments($blogId)
    {
        $result = $this->repository->commentsByBlog($blogId);
        return response()->json($result);
    }
}
