<?php

namespace App\Models;

use App\Models\Blog;
use App\Models\User;

/**
 * Class Comment.
 *
 * @package namespace App\Models;
 */
class Comment extends BaseModel
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['user_id', 'blog_id', 'content'];

    /**
     * Get name of user
     * @return string
     */
    public function getUser()
    {
        $user = User::find($this->user_id);
        return $user->full_name;
    }

    /**
     * Get name of blog
     * @return string
     */
    public function getBlog()
    {
        $blog = Blog::find($this->blog_id);
        return $blog->name_blog;
    }

}
