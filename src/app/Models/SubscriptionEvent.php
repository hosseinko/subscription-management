<?php

namespace App\Models;

use App\Objects\Reports\EventsReport;
use Illuminate\Database\Eloquent\Model;

/**
 * Class SubscriptionEvent
 * @package App\Models
 */
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

    /**
     * @param $page
     * @param $perPage
     * @param $filters
     * @return EventsReport
     */
    public function generateReport($page, $perPage, $filters)
    {
        $query = $this->leftJoin('subscriptions', 'subscription_events.subscription_id', '=', 'subscriptions.id')
                      ->leftJoin('applications', 'subscriptions.application_id', '=', 'applications.id')
                      ->leftJoin('devices', 'subscriptions.device_id', '=', 'devices.id')
                      ->orderBy('subscription_events.id', 'DESC');

        if (is_array($filters) && count($filters) > 0) {
            if (isset($filters['status']) && !empty($filters['status'])) {
                $query->where('subscription_events.status', $filters['status']);
            }

            if (isset($filters['app_uuid']) && !empty($filters['app_uuid'])) {
                $query->where('applications.uuid', $filters['app_uuid']);
            }

            if (isset($filters['device_uuid']) && !empty($filters['device_uuid'])) {
                $query->where('devices.uuid', $filters['device_uuid']);
            }

            if (isset($filters['os']) && !empty($filters['os'])) {
                $query->where('devices.os', $filters['os']);
            }

            if (isset($filters['day']) && !empty($filters['day'])) {
                $query->whereDate('subscription_events.created_at', $filters['day']);
            }
        }

        $cnt = $query->count();
        $query->limit($perPage);
        $query->offset(($page - 1) * $perPage);
        $data = $query->get([
            'subscription_events.id as id',
            'subscription_events.status as status',
            'subscription_events.created_at as event_date',
            'applications.title as application_title',
            'applications.uuid as application_uuid',
            'devices.uuid as device_uuid',
            'devices.os as os',
            'devices.lang as lang',
        ]);

        return new EventsReport($data, $cnt, $page, $perPage);
    }
}
