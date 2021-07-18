<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
     /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    protected $fillable = [
        'summary', 'completed_at'
    ];


    public function user()
    {
        return $this->belongsTo('App\User');
    }
    
}
