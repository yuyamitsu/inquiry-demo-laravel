<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Inquiry extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'email',
        'title',
        'category',
        'body',
        'status',
        'admin_reply',
    ];
    
    public function logs()
    {
        return $this->hasMany(InquiryLog::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
