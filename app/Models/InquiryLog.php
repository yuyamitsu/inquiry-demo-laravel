<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InquiryLog extends Model
{
    protected $fillable = [
        'inquiry_id',
        'action',
        'field_name',
        'before_value',
        'after_value',
        'message',
    ];

    public function inquiry()
    {
        return $this->belongsTo(Inquiry::class);
    }
}
