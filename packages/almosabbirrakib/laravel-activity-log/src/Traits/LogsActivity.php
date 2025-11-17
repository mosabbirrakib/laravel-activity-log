<?php

namespace AlMosabbirRakib\ActivityLog\Traits;

use AlMosabbirRakib\ActivityLog\Facades\ActivityLog;
use Illuminate\Database\Eloquent\Model;

trait LogsActivity
{
    /**
     * Boot the trait.
     *
     * @return void
     */
    protected static function bootLogsActivity()
    {
        if (!config('activity-log.auto_log.enabled', false)) {
            return;
        }

        $events = config('activity-log.auto_log.events', ['created', 'updated', 'deleted']);

        if (in_array('created', $events)) {
            static::created(function (Model $model) {
                $model->logActivity('created');
            });
        }

        if (in_array('updated', $events)) {
            static::updated(function (Model $model) {
                $model->logActivity('updated');
            });
        }

        if (in_array('deleted', $events)) {
            static::deleted(function (Model $model) {
                $model->logActivity('deleted');
            });
        }
    }

    /**
     * Log an activity for this model.
     *
     * @param string $type
     * @param string|null $description
     * @param array $properties
     * @return \AlMosabbirRakib\ActivityLog\Models\ActivityLog
     */
    public function logActivity(string $type, ?string $description = null, array $properties = [])
    {
        $description = $description ?? $this->getActivityDescription($type);
        $properties = array_merge($this->getActivityProperties($type), $properties);

        return ActivityLog::log($description, $type, $properties, $this);
    }

    /**
     * Get the activity description.
     *
     * @param string $type
     * @return string
     */
    protected function getActivityDescription(string $type): string
    {
        $modelName = class_basename($this);

        return match ($type) {
            'created' => "{$modelName} was created",
            'updated' => "{$modelName} was updated",
            'deleted' => "{$modelName} was deleted",
            default => "{$modelName} {$type}",
        };
    }

    /**
     * Get the activity properties.
     *
     * @param string $type
     * @return array
     */
    protected function getActivityProperties(string $type): array
    {
        $properties = [];

        if ($type === 'created') {
            $properties['attributes'] = $this->attributesToArray();
        }

        if ($type === 'updated' && method_exists($this, 'getChanges')) {
            $properties['old'] = $this->getOriginal();
            $properties['attributes'] = $this->getChanges();
        }

        if ($type === 'deleted') {
            $properties['attributes'] = $this->attributesToArray();
        }

        // Filter out excluded attributes
        $excluded = config('activity-log.excluded_attributes', []);
        
        foreach ($properties as $key => $value) {
            if (is_array($value)) {
                $properties[$key] = collect($value)
                    ->reject(function ($val, $attr) use ($excluded) {
                        return in_array($attr, $excluded);
                    })
                    ->toArray();
            }
        }

        return $properties;
    }

    /**
     * Get all activity logs for this model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function activityLogs()
    {
        return $this->morphMany(
            \AlMosabbirRakib\ActivityLog\Models\ActivityLog::class,
            'subject'
        );
    }

    /**
     * Get all activities caused by this model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function causedActivities()
    {
        return $this->morphMany(
            \AlMosabbirRakib\ActivityLog\Models\ActivityLog::class,
            'causer'
        );
    }
}

