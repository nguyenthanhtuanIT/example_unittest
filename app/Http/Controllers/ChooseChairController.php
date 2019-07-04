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
        return response()->json($chooseChairs);
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
        $chooseChair = $this->repository->skipPresenter()->create($request->all());
        return response()->json($chooseChair, Response::HTTP_CREATED);
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
        return response()->json($chooseChair);
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
        $chooseChair = $this->repository->skipPresenter()->update($request->all(), $id);
        return response()->json($chooseChair, Response::HTTP_OK);
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
     * Get ticket user buy
     * @param  Request $request
     * @return \Illuminate\Http\Response
     */
    public function ticketOfUser(Request $request)
    {
        $ticket = $this->repository->ticketUser($request->all());
        return response()->json($ticket);
    }

    /**
     * Get chair user choosed
     * @param  Request $request
     * @return \Illuminate\Http\Response
     */
    public function checkUserChoosed(Request $request)
    {
        $check = $this->repository->checkChoosed($request->all());
        return response()->json($check);
    }

    /**
     * Allow user rechoose chair
     * @param  Request $request
     * @return \Illuminate\Http\Response
     */
    public function reChooses(Request $request)
    {
        $re = $this->repository->reChoose($request->all());
        return response()->json($re, Response::HTTP_CREATED);
    }

    /**
     * Random chair
     * @param  Request $request
     * @return \Illuminate\Http\Response
     */
    public function randChairs(Request $request)
    {
        $ac = $this->repository->randChair($request->all());
        return $ac;
    }

    /**
     * Delete all chair user choose by vote
     * @param  int $vote_id
     * @return \Illuminate\Http\Response
     */
    public function deleteAll($vote_id)
    {
        $del = $this->repository->delAll($vote_id);
        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
