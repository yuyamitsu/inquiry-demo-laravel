<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use App\Models\Inquiry;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password', 'role' ])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function inquiryComments()
    {
        return $this->hasMany(InquiryComment::class);
    }

    public function inquiries()
    {
        return $this->hasMany(Inquiry::class);
    }

    public function assignedInquiries()
    {
        return $this->hasMany(Inquiry::class, 'assignee_id');
    }

    public function knowledgeArticles()
    {
        return $this->hasMany(KnowledgeArticle::class, 'created_by');
    }

}
