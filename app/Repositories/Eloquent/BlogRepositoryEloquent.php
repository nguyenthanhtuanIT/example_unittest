<?php

namespace App\Repositories\Eloquent;

use App\Models\Blog;
use App\Presenters\BlogPresenter;
use App\Repositories\Contracts\BlogRepository;
use App\Services\UploadService;
use Illuminate\Support\Facades\Storage;
use Prettus\Repository\Criteria\RequestCriteria;
use Prettus\Repository\Eloquent\BaseRepository;

/**
 * Class BlogRepositoryEloquent.
 *
 * @package namespace App\Repositories\Eloquent;
 */
class BlogRepositoryEloquent extends BaseRepository implements BlogRepository
{
    /**
     * Specify Model class name
     *
     * @return object
     */
    public function model()
    {
        return Blog::class;
    }

    /**
     * Specify Presenter class name
     *
     * @return object
     */
    public function presenter()
    {
        return BlogPresenter::class;
    }

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }

    /**
     * Custom created add image
     * @param  array  $attributes
     * @return object
     */
    public function create(array $attributes)
    {
        $name = $attributes['img']->store('photos');
        $link = Storage::url($name);
        $attributes['img'] = $link;
        $blog = parent::create($attributes);

        return $blog;
    }

    /**
     * Custom update
     * @param  array  $attributes
     * @param  int  $id
     * @return object
     */
    public function update(array $attributes, $id)
    {
        if (isset($attributes['img'])) {
            $name = $attributes['img']->store('photos');
            $link = Storage::url($name);
            $attributes['img'] = $link;
            $img = Blog::find($id);
            $imgOld = $img->img;
            $nameImg = explode('/', $imgOld);
            $url = "/photos/$nameImg[5]";

            if (UploadService::checkFileExist($url)) {
                Storage::delete('/photos/' . $nameImg[5]);
            }
        }
        $blog = parent::update($attributes, $id);

        return $blog;
    }

    /**
     * Search blog by name
     * @param  string $key
     * @return object
     */
    public function searchBlog($key)
    {
        $blogs = Blog::where('name_blog', 'LIKE', "%{$key}%")
            ->paginate(8);
        return $blogs;
    }

    /**
     * Get all blog sort by id desc
     * @return object
     */
    public function getAll()
    {
        $blogs = Blog::orderBy('id', 'DESC')->paginate(8);
        return $blogs;
    }
}
