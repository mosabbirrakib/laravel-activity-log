<?php

namespace AlMosabbirRakib\ActivityLog;

use AlMosabbirRakib\ActivityLog\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class ActivityLogger
{
    /**
     * Log an activity.
     *
     * @param string $description
     * @param string|null $type
     * @param array $properties
     * @param mixed $subject
     * @param mixed $causer
     * @return ActivityLog
     */
    public function log(
        string $description,
        ?string $type = 'default',
        array $properties = [],
        $subject = null,
        $causer = null
    ): ActivityLog {
        $causer = $causer ?? Auth::user();

        $activityLog = new ActivityLog([
            'description' => $description,
            'type' => $type,
            'properties' => $this->filterProperties($properties),
            'ip_address' => config('activity-log.log_ip_address') ? Request::ip() : null,
            'user_agent' => config('activity-log.log_user_agent') ? Request::userAgent() : null,
        ]);

        if ($causer) {
            $activityLog->causer()->associate($causer);
        }

        if ($subject) {
            $activityLog->subject()->associate($subject);
        }

        $activityLog->save();

        return $activityLog;
    }

    /**
     * Log a created event.
     *
     * @param mixed $subject
     * @param string|null $description
     * @param array $properties
     * @return ActivityLog
     */
    public function created($subject, ?string $description = null, array $properties = []): ActivityLog
    {
        $description = $description ?? 'Created ' . class_basename($subject);
        return $this->log($description, 'created', $properties, $subject);
    }

    /**
     * Log an updated event.
     *
     * @param mixed $subject
     * @param string|null $description
     * @param array $properties
     * @return ActivityLog
     */
    public function updated($subject, ?string $description = null, array $properties = []): ActivityLog
    {
        $description = $description ?? 'Updated ' . class_basename($subject);
        return $this->log($description, 'updated', $properties, $subject);
    }

    /**
     * Log a deleted event.
     *
     * @param mixed $subject
     * @param string|null $description
     * @param array $properties
     * @return ActivityLog
     */
    public function deleted($subject, ?string $description = null, array $properties = []): ActivityLog
    {
        $description = $description ?? 'Deleted ' . class_basename($subject);
        return $this->log($description, 'deleted', $properties, $subject);
    }

    /**
     * Log a login event.
     *
     * @param mixed $user
     * @return ActivityLog
     */
    public function login($user = null): ActivityLog
    {
        return $this->log('User logged in', 'login', [], null, $user ?? Auth::user());
    }

    /**
     * Log a logout event.
     *
     * @param mixed $user
     * @return ActivityLog
     */
    public function logout($user = null): ActivityLog
    {
        return $this->log('User logged out', 'logout', [], null, $user ?? Auth::user());
    }

    /**
     * Filter properties based on excluded attributes.
     *
     * @param array $properties
     * @return array
     */
    protected function filterProperties(array $properties): array
    {
        if (!config('activity-log.log_properties', true)) {
            return [];
        }

        $excluded = config('activity-log.excluded_attributes', []);

        return collect($properties)
            ->reject(function ($value, $key) use ($excluded) {
                return in_array($key, $excluded);
            })
            ->toArray();
    }

    /**
     * Get activity logs query builder.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query()
    {
        return ActivityLog::query();
    }

    /**
     * Get activity logs for a specific user.
     *
     * @param mixed $user
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function forUser($user)
    {
        return ActivityLog::where('causer_id', $user->id)
            ->where('causer_type', get_class($user));
    }

    /**
     * Get activity logs for a specific subject.
     *
     * @param mixed $subject
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function forSubject($subject)
    {
        return ActivityLog::where('subject_id', $subject->id)
            ->where('subject_type', get_class($subject));
    }
}

