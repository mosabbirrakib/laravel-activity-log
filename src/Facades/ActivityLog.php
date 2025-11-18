<?php

namespace AlMosabbirRakib\ActivityLog\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static \AlMosabbirRakib\ActivityLog\Models\ActivityLog log(string $description, string|null $type = 'default', array $properties = [], mixed $subject = null, mixed $causer = null)
 * @method static \AlMosabbirRakib\ActivityLog\Models\ActivityLog created(mixed $subject, string|null $description = null, array $properties = [])
 * @method static \AlMosabbirRakib\ActivityLog\Models\ActivityLog updated(mixed $subject, string|null $description = null, array $properties = [])
 * @method static \AlMosabbirRakib\ActivityLog\Models\ActivityLog deleted(mixed $subject, string|null $description = null, array $properties = [])
 * @method static \AlMosabbirRakib\ActivityLog\Models\ActivityLog login(mixed $user = null)
 * @method static \AlMosabbirRakib\ActivityLog\Models\ActivityLog logout(mixed $user = null)
 * @method static \Illuminate\Database\Eloquent\Builder query()
 * @method static \Illuminate\Database\Eloquent\Builder forUser(mixed $user)
 * @method static \Illuminate\Database\Eloquent\Builder forSubject(mixed $subject)
 *
 * @see \AlMosabbirRakib\ActivityLog\ActivityLogger
 */
class ActivityLog extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'activity-log';
    }
}

