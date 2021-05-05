<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubscriptionEvent extends Model
{
    protected $casts = [
        'expire_date' => 'datetime:U',
        'created_at'  => 'datetime:U',
        'updated_at'  => 'datetime:U',
    ];

    protected $fillable = [
        'subscription_id',
        'status',
    ];

    public function subscription()
    {
        return $this->belongsTo(Subscription::class);
    }
}
