<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InquiryComment extends Model
{
    protected $fillable = [
        'inquiry_id',
        'user_id',
        'body',
        'is_internal',
    ];

    public function inquiry()
    {
        return $this->belongsTo(Inquiry::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
