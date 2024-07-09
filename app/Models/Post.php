<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @OA\Schema(
 *     schema="Post",
 *     required={"title", "content"},
 *     @OA\Property(property="id", type="integer", format="int64", example=1),
 *     @OA\Property(property="title", type="string", example="Post Title"),
 *     @OA\Property(property="content", type="string", example="Post Content")
 * )
 */

class Post extends Model
{

    /**
     * @var string
     */
    protected $table = 'posts';

    /**
     * @var array
     */

     protected $fillable = [
        'title', 'content',
     ];

}