<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KnowledgeArticle extends Model
{
    use HasFactory;

    protected $fillable = [
        'inquiry_id',
        'created_by',
        'title',
        'category',
        'body',
        'is_published',
    ];

    public function inquiry()
    {
        return $this->belongsTo(Inquiry::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
