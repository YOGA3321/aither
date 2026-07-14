<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['user_id', 'name', 'api_key', 'secret_key'])]
class Device extends Model
{
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
