<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use HasFactory;

    protected $table = "comments";
    public $timestamps = false;
    
    //Casts of the model dates
    protected $casts = [
        'comment_date' => 'datetime'
    ];

    protected $fillable = [
        'id',
        'user_id',
        'story_id',
        'comment_date',
        'type',
        'status',
        'content',
    ];
    
    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }

    public function story()
    {
        return $this->belongsTo('App\Models\Story');
    }
    
}
