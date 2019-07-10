<?php

namespace App\Http\Controllers;

use App\Http\Requests\ImageCreateRequest;
use App\Repositories\Contracts\ImageRepository;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;

/**
 * Class ImagesController.
 *
 * @package namespace App\Http\Controllers;
 */
class ImageController extends Controller
{
    /**
     * @var ImageRepository
     */
    protected $repository;
    /**
     * ImagesController constructor.
     *
     * @param ImageRepository $repository
     */
    public function __construct(ImageRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  ImageCreateRequest $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(ImageCreateRequest $request)
    {
        $image = $this->repository->skipPresenter()->create($request->all());

        return $this->presenterPostJson($image);
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
        $item = $this->repository->skipPresenter()->find($id);
        $item->delete();
        if (file_exists($item->filename)) {
            Storage::delete('thumbnails/' . $item->filename);
        }

        return $this->success([], trans('messages.images.deleteSuccess'), ['code' => Response::HTTP_NO_CONTENT]);
    }
}
