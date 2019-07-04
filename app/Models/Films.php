<?php

namespace App\Models;

/**
 * Class Films.
 *
 * @package namespace App\Models;
 */
class Films extends BaseModel
{
    const SELECTED = 1;
    const NOTSELECT = 0;
    const PRICE = 'đ';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name_film', 'img', 'projection_date', 'projection_time', 'language', 'age_limit', 'detail', 'trailer_url', 'price_film', 'curency', 'movies_type'];
    public function getMoviesTypeAttribute($value)
    {
        return explode(',', $value);
    }
}
