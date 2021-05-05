<?php

namespace App\Models;

use App\Exceptions\ResourceNotFoundException;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Application extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $casts = [
        'market_credentials' => 'array'
    ];

    protected $fillable = [
        'title',
        'uuid',
        'event_endpoint_url',
        'market_credentials'
    ];

    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }

    public function getApplicationByUuid($uuid)
    {
        $application = $this->where('uuid', $uuid)->first();

        if (!$application) {
            throw new ResourceNotFoundException(__('errors.application_not_found'), 404);
        }

        return $application;
    }
}
