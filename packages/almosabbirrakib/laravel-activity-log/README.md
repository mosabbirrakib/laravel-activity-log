# Laravel Activity Log

A comprehensive Laravel package for logging user activities with support for Blade, Vue 2, and Vue 3 frontends. Track and monitor all user activities in your Laravel application with a beautiful, professional UI.

## Features

- 🎯 **Easy Integration** - Simple trait-based implementation
- 🎨 **Multiple Frontend Options** - Blade, Vue 2, and Vue 3 components
- 📊 **Beautiful UI** - Professional dashboard with Tailwind CSS
- 🔍 **Advanced Filtering** - Search, filter by type, date range, and more
- 📱 **Responsive Design** - Works perfectly on all devices
- 🚀 **High Performance** - Optimized queries with proper indexing
- 🔒 **Secure** - Configurable middleware and authentication
- 📦 **Flexible** - Highly configurable with sensible defaults
- 🧹 **Automatic Cleanup** - Built-in command for log retention
- 🎭 **Multiple Log Types** - Created, updated, deleted, login, logout, and custom types

## Requirements

- PHP 8.0 or higher
- Laravel 9.x, 10.x, or 11.x
- MySQL 5.7+ / PostgreSQL 9.6+ / SQLite 3.8+

## Installation

### Step 1: Install via Composer

```bash
composer require almosabbirrakib/laravel-activity-log
```

### Step 2: Run the Installation Command

```bash
php artisan activity-log:install
```

This will publish:
- Configuration file
- Migration file
- Blade views
- Vue components
- Assets

### Step 3: Run Migrations

```bash
php artisan migrate
```

## Configuration

The configuration file is published at `config/activity-log.php`. Here are the key options:

```php
return [
    // Database table name
    'table_name' => 'activity_logs',
    
    // User model
    'user_model' => 'App\\Models\\User',
    
    // Route configuration
    'routes' => [
        'enabled' => true,
        'prefix' => 'activity-logs',
        'middleware' => ['web', 'auth'],
        'api_middleware' => ['api', 'auth:sanctum'],
    ],
    
    // Pagination
    'per_page' => 15,
    
    // Automatic logging
    'auto_log' => [
        'enabled' => false,
        'events' => ['created', 'updated', 'deleted'],
    ],
    
    // Log retention (days)
    'retention_days' => null, // null = keep forever
    
    // Privacy settings
    'log_ip_address' => true,
    'log_user_agent' => true,
    
    // Excluded attributes (won't be logged)
    'excluded_attributes' => [
        'password',
        'password_confirmation',
        'remember_token',
        'api_token',
    ],
];
```

## Usage

### Using the Trait (Recommended)

Add the `LogsActivity` trait to any model you want to track:

```php
use AlMosabbirRakib\ActivityLog\Traits\LogsActivity;

class Post extends Model
{
    use LogsActivity;
    
    // Your model code...
}
```

Now all create, update, and delete operations will be automatically logged (if `auto_log.enabled` is true in config).

### Manual Logging

#### Using the Facade

```php
use AlMosabbirRakib\ActivityLog\Facades\ActivityLog;

// Basic log
ActivityLog::log('User viewed dashboard', 'viewed');

// Log with properties
ActivityLog::log('User updated settings', 'updated', [
    'old_email' => 'old@example.com',
    'new_email' => 'new@example.com',
]);

// Log with subject
ActivityLog::log('Post published', 'published', [], $post);

// Predefined methods
ActivityLog::created($post);
ActivityLog::updated($post, 'Post title updated');
ActivityLog::deleted($post);
ActivityLog::login();
ActivityLog::logout();
```

#### Using Helper Functions

```php
// Basic log
activity_log('User viewed dashboard', 'viewed');

// Predefined helpers
activity_created($post);
activity_updated($post, 'Post updated');
activity_deleted($post);
activity_login();
activity_logout();
```

#### Using the Model Method

```php
$post = Post::find(1);
$post->logActivity('published', 'Post was published', [
    'published_at' => now(),
]);
```

### Retrieving Activity Logs

```php
use AlMosabbirRakib\ActivityLog\Models\ActivityLog;

// Get all logs
$logs = ActivityLog::latest()->paginate(15);

// Get logs for a specific user
$logs = ActivityLog::forCauser($user)->get();

// Get logs for a specific model
$logs = ActivityLog::forSubject($post)->get();

// Filter by type
$logs = ActivityLog::ofType('created')->get();

// Search in description
$logs = ActivityLog::search('login')->get();

// Date range
$logs = ActivityLog::dateRange('2024-01-01', '2024-12-31')->get();

// Using the facade
use AlMosabbirRakib\ActivityLog\Facades\ActivityLog;

$logs = ActivityLog::forUser($user)->get();
$logs = ActivityLog::forSubject($post)->get();
```

### Accessing Logs from Models

```php
// Get all activity logs for a model
$post = Post::find(1);
$logs = $post->activityLogs;

// Get all activities caused by a user
$user = User::find(1);
$activities = $user->causedActivities;
```

## Frontend Integration

### Blade View

The package includes a complete Blade view with all functionality. Access it at:

```
/activity-logs
```

Or include it in your own views:

```blade
@include('activity-log::index')
```

### Vue 2 Component

1. Publish the components:
```bash
php artisan vendor:publish --tag=activity-log-components
```

2. Register the component:
```javascript
import ActivityLogVue2 from './components/activity-log/ActivityLogVue2.vue';

Vue.component('activity-log', ActivityLogVue2);
```

3. Use in your template:
```vue
<activity-log api-base-url="/api/activity-logs"></activity-log>
```

### Vue 3 Component

1. Publish the components (if not already done):
```bash
php artisan vendor:publish --tag=activity-log-components
```

2. Import and use:
```javascript
import ActivityLogVue3 from './components/activity-log/ActivityLogVue3.vue';

export default {
    components: {
        ActivityLogVue3
    }
}
```

3. Use in your template:
```vue
<ActivityLogVue3 api-base-url="/api/activity-logs" />
```

## API Endpoints

The package provides the following API endpoints:

```
GET    /api/activity-logs              - Get paginated logs with filters
GET    /api/activity-logs/{id}         - Get a specific log
GET    /api/activity-logs/types        - Get all log types
GET    /api/activity-logs/stats        - Get statistics
DELETE /api/activity-logs/cleanup      - Clean old logs
```

### API Query Parameters

```
?page=1                    - Page number
?per_page=15              - Items per page
?search=keyword           - Search in description
?type=created             - Filter by type
?date_from=2024-01-01     - Filter from date
?date_to=2024-12-31       - Filter to date
?causer_id=1              - Filter by user ID
?causer_type=App\Models\User - Filter by user type
```

## Artisan Commands

### Install Package
```bash
php artisan activity-log:install
```

Options:
- `--force` - Overwrite existing files
- `--config` - Publish config only
- `--migrations` - Publish migrations only
- `--views` - Publish views only
- `--components` - Publish components only
- `--all` - Publish all assets

### Clean Old Logs
```bash
php artisan activity-log:clean
```

Options:
- `--days=30` - Number of days to keep
- `--force` - Skip confirmation

Schedule it in your `app/Console/Kernel.php`:
```php
protected function schedule(Schedule $schedule)
{
    $schedule->command('activity-log:clean --days=90 --force')
             ->daily();
}
```

## Middleware

To automatically log all HTTP requests, add the middleware to your routes:

```php
// In routes/web.php or routes/api.php
Route::middleware(['auth', \AlMosabbirRakib\ActivityLog\Middleware\LogActivity::class])
    ->group(function () {
        // Your routes here
    });
```

Or register it globally in `app/Http/Kernel.php`:

```php
protected $middleware = [
    // ...
    \AlMosabbirRakib\ActivityLog\Middleware\LogActivity::class,
];
```

## Customization

### Custom Activity Types

You can create custom activity types:

```php
ActivityLog::log('User exported data', 'export', [
    'format' => 'csv',
    'records' => 1000,
]);
```

### Custom Properties

Add any data you want to track:

```php
ActivityLog::log('Payment processed', 'payment', [
    'amount' => 99.99,
    'currency' => 'USD',
    'gateway' => 'stripe',
    'transaction_id' => 'txn_123456',
]);
```

### Customizing the UI

Publish the views and modify them:

```bash
php artisan vendor:publish --tag=activity-log-views
```

Views will be published to `resources/views/vendor/activity-log/`.

## Security

- All routes are protected by authentication middleware by default
- Sensitive attributes (passwords, tokens) are automatically excluded from logs
- IP addresses and user agents can be disabled in config
- API endpoints support Laravel Sanctum authentication

## Performance

The package is optimized for performance:
- Proper database indexes on all searchable columns
- Efficient queries with eager loading
- Configurable pagination
- Optional log retention/cleanup

## Testing

```bash
composer test
```

## Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

## License

This package is open-sourced software licensed under the [MIT license](LICENSE.md).

## Credits

- **Al Mosabbir Rakib**
- [All Contributors](../../contributors)

## Support

For support, please open an issue on GitHub or contact almosabbirrakib@example.com.

