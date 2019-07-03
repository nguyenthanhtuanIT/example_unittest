<?php

namespace App\Http\Controllers;

use App\Http\Requests\FilmsCreateRequest;
use App\Http\Requests\FilmsUpdateRequest;
use App\Repositories\Contracts\FilmsRepository;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * Class FilmsController.
 *
 * @package namespace App\Http\Controllers;
 */
class FilmsController extends Controller
{
    /**
     * @var FilmsRepository
     */
    protected $repository;

    /**
     * FilmsController constructor.
     *
     * @param FilmsRepository $repository
     */
    public function __construct(FilmsRepository $repository)
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

        $films = $this->repository->paginate($limit, $columns = ['*']);

        return response()->json($films);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  FilmsCreateRequest $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $film = $this->repository->create($request->all());

        return response()->json($film, Response::HTTP_CREATED);
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
        $film = $this->repository->find($id);

        return response()->json($film);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  FilmsUpdateRequest $request
     * @param  string $id
     *
     * @return Response
     */
    public function update(Request $request, $id)
    {
        $film = $this->repository->update($request->all(), $id);
        return response()->json($film, Response::HTTP_OK);
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
    public function listFilmToVote()
    {
        $film = $this->repository->getlistFilmToVote();
        return response()->json($film);
    }
    public function getFilmToRegister(Request $request)
    {
        $film = $this->repository->filmToRegister($request->vote_id);
        return $this->repository->parserResult($film);
    }
}
