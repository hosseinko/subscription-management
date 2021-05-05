<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    protected $casts = [
        'expire_date' => 'datetime:U',
        'created_at'  => 'datetime:U',
        'updated_at'  => 'datetime:U',
    ];

    protected $fillable = [
        'application_id',
        'device_id',
        'subscription_status',
        'expired_date',
        'token'
    ];

    public function application()
    {
        return $this->belongsTo(Application::class);
    }

    public function device()
    {
        return $this->belongsTo(Device::class);
    }

    public function events()
    {
        return $this->hasMany(SubscriptionEvent::class);
    }

    public function alreadyRegistered($appId, $deviceId)
    {
        return $this->where('application_id', $appId)
                    ->where('device_id', $deviceId)
                    ->first();

    }

}
