<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reminder extends Model
{
    protected $fillable = [
        'user_id',
        'email',
        'task',
        'remind_at',
        'sent',
    ];
}
