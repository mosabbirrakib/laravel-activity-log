<?php

namespace AlMosabbirRakib\ActivityLog\Middleware;

use Closure;
use Illuminate\Http\Request;
use AlMosabbirRakib\ActivityLog\Facades\ActivityLog;

class LogActivity
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        // Only log successful requests
        if ($response->isSuccessful()) {
            $this->logRequest($request, $response);
        }

        return $response;
    }

    /**
     * Log the request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  mixed  $response
     * @return void
     */
    protected function logRequest(Request $request, $response)
    {
        $method = $request->method();
        $path = $request->path();
        $user = $request->user();

        // Skip logging for certain routes
        if ($this->shouldSkip($path)) {
            return;
        }

        $description = $this->getDescription($method, $path);
        $type = $this->getType($method);

        ActivityLog::log(
            $description,
            $type,
            [
                'method' => $method,
                'path' => $path,
                'query' => $request->query(),
            ],
            null,
            $user
        );
    }

    /**
     * Determine if the request should be skipped.
     *
     * @param  string  $path
     * @return bool
     */
    protected function shouldSkip(string $path): bool
    {
        $skipPaths = [
            'activity-logs',
            'api/activity-logs',
            '_debugbar',
            'telescope',
            'horizon',
        ];

        foreach ($skipPaths as $skipPath) {
            if (str_starts_with($path, $skipPath)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get the description for the log.
     *
     * @param  string  $method
     * @param  string  $path
     * @return string
     */
    protected function getDescription(string $method, string $path): string
    {
        return "{$method} request to {$path}";
    }

    /**
     * Get the type for the log.
     *
     * @param  string  $method
     * @return string
     */
    protected function getType(string $method): string
    {
        return match ($method) {
            'POST' => 'created',
            'PUT', 'PATCH' => 'updated',
            'DELETE' => 'deleted',
            default => 'viewed',
        };
    }
}

