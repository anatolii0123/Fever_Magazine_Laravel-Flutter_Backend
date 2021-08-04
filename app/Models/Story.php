<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Story extends Model
{
    use HasFactory;

    protected $table = "stories";
    public $timestamps = false;

    protected $fillable = [
        'id',
        'title',
        'content',
        'featured_image',
        'card_type',
        'card_link',
        'category_id',
        'likes',
        'view_count',
        'story_date',
        'status',
        'user_id',
    ];

    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }

    public function category()
    {
        return $this->belongsTo('App\Models\Category');
    }

    public function comments(){
        return $this->hasMany('App\Models\Comment');
    }
    
}
