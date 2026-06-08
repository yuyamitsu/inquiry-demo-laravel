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
        'assignee_id',
        'priority',
        'due_date',
    ];
    
    public function logs()
    {
        return $this->hasMany(InquiryLog::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function comments()
    {
        return $this->hasMany(InquiryComment::class);
    }

    public function assignee()
    {
        return $this->belongsTo(User::class, 'assignee_id');
    }
    
}
