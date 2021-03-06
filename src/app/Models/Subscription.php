<?php

namespace App\Models;

use App\Exceptions\ResourceNotFoundException;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Subscription
 * @package App\Models
 */
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
        'receipt',
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

    /**
     * @param $appId
     * @param $deviceId
     * @return mixed
     */
    public function alreadyRegistered($appId, $deviceId)
    {
        return $this->where('application_id', $appId)
                    ->where('device_id', $deviceId)
                    ->first();
    }

    /**
     * @param $clientToken
     * @return mixed
     * @throws ResourceNotFoundException
     */
    public function getSubscriptionByClientToken($clientToken)
    {
        $subscription = $this->where('token', $clientToken)->first();
        if (!$subscription) {
            throw new ResourceNotFoundException(__("errors.subscription_not_found"), 404);
        }

        return $subscription;
    }

    /**
     * @param $subscriptionId
     * @return mixed
     * @throws ResourceNotFoundException
     */
    public function getSubscriptionById($subscriptionId)
    {
        $subscription = $this->find($subscriptionId);
        if (!$subscription) {
            throw new ResourceNotFoundException(__("errors.subscription_not_found"), 404);
        }

        return $subscription;
    }

}
