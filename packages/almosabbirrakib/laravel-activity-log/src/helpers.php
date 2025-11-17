<?php

use AlMosabbirRakib\ActivityLog\Facades\ActivityLog;

if (!function_exists('activity_log')) {
    /**
     * Log an activity.
     *
     * @param string $description
     * @param string|null $type
     * @param array $properties
     * @param mixed $subject
     * @param mixed $causer
     * @return \AlMosabbirRakib\ActivityLog\Models\ActivityLog
     */
    function activity_log(
        string $description,
        ?string $type = 'default',
        array $properties = [],
        $subject = null,
        $causer = null
    ) {
        return ActivityLog::log($description, $type, $properties, $subject, $causer);
    }
}

if (!function_exists('activity_created')) {
    /**
     * Log a created event.
     *
     * @param mixed $subject
     * @param string|null $description
     * @param array $properties
     * @return \AlMosabbirRakib\ActivityLog\Models\ActivityLog
     */
    function activity_created($subject, ?string $description = null, array $properties = [])
    {
        return ActivityLog::created($subject, $description, $properties);
    }
}

if (!function_exists('activity_updated')) {
    /**
     * Log an updated event.
     *
     * @param mixed $subject
     * @param string|null $description
     * @param array $properties
     * @return \AlMosabbirRakib\ActivityLog\Models\ActivityLog
     */
    function activity_updated($subject, ?string $description = null, array $properties = [])
    {
        return ActivityLog::updated($subject, $description, $properties);
    }
}

if (!function_exists('activity_deleted')) {
    /**
     * Log a deleted event.
     *
     * @param mixed $subject
     * @param string|null $description
     * @param array $properties
     * @return \AlMosabbirRakib\ActivityLog\Models\ActivityLog
     */
    function activity_deleted($subject, ?string $description = null, array $properties = [])
    {
        return ActivityLog::deleted($subject, $description, $properties);
    }
}

if (!function_exists('activity_login')) {
    /**
     * Log a login event.
     *
     * @param mixed $user
     * @return \AlMosabbirRakib\ActivityLog\Models\ActivityLog
     */
    function activity_login($user = null)
    {
        return ActivityLog::login($user);
    }
}

if (!function_exists('activity_logout')) {
    /**
     * Log a logout event.
     *
     * @param mixed $user
     * @return \AlMosabbirRakib\ActivityLog\Models\ActivityLog
     */
    function activity_logout($user = null)
    {
        return ActivityLog::logout($user);
    }
}

