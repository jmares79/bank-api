<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    /**
     * Get the product that owns the promotion
     */
    public function user()
    {
        return $this->belongsTo('App\User', 'user_id');
    }
}
