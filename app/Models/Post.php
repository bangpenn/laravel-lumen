<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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