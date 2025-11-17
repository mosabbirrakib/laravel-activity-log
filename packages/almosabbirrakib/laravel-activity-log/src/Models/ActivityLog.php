<?php

namespace AlMosabbirRakib\ActivityLog\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Builder;

class ActivityLog extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'type',
        'description',
        'properties',
        'subject_type',
        'subject_id',
        'causer_type',
        'causer_id',
        'ip_address',
        'user_agent',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'properties' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the table associated with the model.
     *
     * @return string
     */
    public function getTable()
    {
        return config('activity-log.table_name', 'activity_logs');
    }

    /**
     * Get the subject (the model being acted upon).
     *
     * @return MorphTo
     */
    public function subject(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Get the causer (the user performing the action).
     *
     * @return MorphTo
     */
    public function causer(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Scope a query to only include logs of a given type.
     *
     * @param Builder $query
     * @param string $type
     * @return Builder
     */
    public function scopeOfType(Builder $query, string $type): Builder
    {
        return $query->where('type', $type);
    }

    /**
     * Scope a query to only include logs for a specific causer.
     *
     * @param Builder $query
     * @param Model $causer
     * @return Builder
     */
    public function scopeForCauser(Builder $query, Model $causer): Builder
    {
        return $query->where('causer_type', get_class($causer))
            ->where('causer_id', $causer->id);
    }

    /**
     * Scope a query to only include logs for a specific subject.
     *
     * @param Builder $query
     * @param Model $subject
     * @return Builder
     */
    public function scopeForSubject(Builder $query, Model $subject): Builder
    {
        return $query->where('subject_type', get_class($subject))
            ->where('subject_id', $subject->id);
    }

    /**
     * Scope a query to filter by date range.
     *
     * @param Builder $query
     * @param string $from
     * @param string|null $to
     * @return Builder
     */
    public function scopeDateRange(Builder $query, string $from, ?string $to = null): Builder
    {
        $query->whereDate('created_at', '>=', $from);

        if ($to) {
            $query->whereDate('created_at', '<=', $to);
        }

        return $query;
    }

    /**
     * Scope a query to search in description.
     *
     * @param Builder $query
     * @param string $search
     * @return Builder
     */
    public function scopeSearch(Builder $query, string $search): Builder
    {
        return $query->where('description', 'like', "%{$search}%");
    }

    /**
     * Get the formatted created at date.
     *
     * @return string
     */
    public function getFormattedCreatedAtAttribute(): string
    {
        return $this->created_at->format(config('activity-log.date_format', 'Y-m-d H:i:s'));
    }

    /**
     * Get the causer name.
     *
     * @return string
     */
    public function getCauserNameAttribute(): string
    {
        if (!$this->causer) {
            return 'System';
        }

        return $this->causer->name ?? $this->causer->email ?? 'Unknown';
    }

    /**
     * Get the subject name.
     *
     * @return string|null
     */
    public function getSubjectNameAttribute(): ?string
    {
        if (!$this->subject) {
            return null;
        }

        return $this->subject->name ?? $this->subject->title ?? class_basename($this->subject_type);
    }

    /**
     * Get the type badge color.
     *
     * @return string
     */
    public function getTypeBadgeColorAttribute(): string
    {
        return match ($this->type) {
            'created' => 'green',
            'updated' => 'blue',
            'deleted' => 'red',
            'login' => 'indigo',
            'logout' => 'gray',
            default => 'gray',
        };
    }
}

