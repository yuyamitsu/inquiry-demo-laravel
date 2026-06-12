<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Faq extends Model
{
    protected $fillable = [
        'title',
        'category',
        'body',
        'sort_order',
        'is_published',
    ];
}
