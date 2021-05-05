<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Device extends Model
{
    protected $casts = [
        'created_at' => 'datetime:U',
        'updated_at' => 'datetime:U',
    ];

    protected $fillable = [
        'uuid',
        'os',
        'lang',
    ];

    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }
}
