# Quick Start Guide

Get up and running with Laravel Activity Log in 5 minutes!

## Installation (2 minutes)

```bash
# 1. Install the package
composer require almosabbirrakib/laravel-activity-log

# 2. Run the installation command
php artisan activity-log:install

# 3. Run migrations
php artisan migrate
```

## Basic Usage (3 minutes)

### 1. Add Trait to Your Model

```php
use AlMosabbirRakib\ActivityLog\Traits\LogsActivity;

class Post extends Model
{
    use LogsActivity;
}
```

### 2. Enable Auto-Logging (Optional)

In `config/activity-log.php`:

```php
'auto_log' => [
    'enabled' => true,
    'events' => ['created', 'updated', 'deleted'],
],
```

### 3. Log Activities Manually

```php
use AlMosabbirRakib\ActivityLog\Facades\ActivityLog;

// Simple log
ActivityLog::log('User viewed dashboard');

// Log with type and properties
ActivityLog::log('Settings updated', 'updated', [
    'theme' => 'dark',
    'language' => 'en'
]);

// Using helper functions
activity_log('User performed action');
activity_created($post);
activity_updated($post);
activity_deleted($post);
activity_login();
activity_logout();
```

### 4. View Activity Logs

Visit in your browser:
```
http://your-app.test/activity-logs
```

## Common Use Cases

### Track User Login/Logout

```php
// In your LoginController
public function login(Request $request)
{
    if (Auth::attempt($credentials)) {
        activity_login(Auth::user());
        return redirect()->intended('dashboard');
    }
}

public function logout(Request $request)
{
    activity_logout(Auth::user());
    Auth::logout();
    return redirect('/');
}
```

### Track CRUD Operations

```php
// In your Controller
public function store(Request $request)
{
    $post = Post::create($request->validated());
    ActivityLog::created($post, 'New post created: ' . $post->title);
    return redirect()->route('posts.show', $post);
}

public function update(Request $request, Post $post)
{
    $post->update($request->validated());
    ActivityLog::updated($post, 'Post updated');
    return redirect()->route('posts.show', $post);
}

public function destroy(Post $post)
{
    $title = $post->title;
    $post->delete();
    ActivityLog::deleted($post, "Post deleted: {$title}");
    return redirect()->route('posts.index');
}
```

### Track Custom Actions

```php
// E-commerce example
ActivityLog::log('Order placed', 'order_placed', [
    'order_id' => $order->id,
    'total' => $order->total,
    'items_count' => $order->items->count(),
], $order);

// File upload example
ActivityLog::log('File uploaded', 'upload', [
    'filename' => $file->getClientOriginalName(),
    'size' => $file->getSize(),
]);
```

## Retrieve Logs

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

// Search
$logs = ActivityLog::search('login')->get();

// Date range
$logs = ActivityLog::dateRange('2024-01-01', '2024-12-31')->get();

// From model
$post = Post::find(1);
$logs = $post->activityLogs;

$user = User::find(1);
$activities = $user->causedActivities;
```

## Vue Integration

### Vue 2

```javascript
// In resources/js/app.js
import ActivityLogVue2 from './components/activity-log/ActivityLogVue2.vue';
Vue.component('activity-log', ActivityLogVue2);
```

```blade
<!-- In your Blade template -->
<div id="app">
    <activity-log api-base-url="/api/activity-logs"></activity-log>
</div>
```

### Vue 3

```javascript
// In resources/js/app.js
import { createApp } from 'vue';
import ActivityLogVue3 from './components/activity-log/ActivityLogVue3.vue';

const app = createApp({
    components: { ActivityLogVue3 }
});
app.mount('#app');
```

```vue
<!-- In your template -->
<template>
    <ActivityLogVue3 api-base-url="/api/activity-logs" />
</template>
```

## API Endpoints

```bash
# Get all logs (with filters)
GET /api/activity-logs?page=1&per_page=15&type=created&search=keyword

# Get specific log
GET /api/activity-logs/{id}

# Get log types
GET /api/activity-logs/types

# Get statistics
GET /api/activity-logs/stats

# Clean old logs
DELETE /api/activity-logs/cleanup?days=90
```

## Scheduled Cleanup

In `app/Console/Kernel.php`:

```php
protected function schedule(Schedule $schedule)
{
    // Clean logs older than 90 days, daily at 2 AM
    $schedule->command('activity-log:clean --days=90 --force')
             ->dailyAt('02:00');
}
```

## Configuration Highlights

Key settings in `config/activity-log.php`:

```php
// Table name
'table_name' => 'activity_logs',

// User model
'user_model' => 'App\\Models\\User',

// Routes
'routes' => [
    'enabled' => true,
    'prefix' => 'activity-logs',
    'middleware' => ['web', 'auth'],
],

// Auto-logging
'auto_log' => [
    'enabled' => false,
    'events' => ['created', 'updated', 'deleted'],
],

// Retention
'retention_days' => null, // null = keep forever

// Privacy
'log_ip_address' => true,
'log_user_agent' => true,

// Excluded attributes
'excluded_attributes' => [
    'password',
    'remember_token',
    'api_token',
],
```

## Artisan Commands

```bash
# Install package
php artisan activity-log:install

# Install specific assets
php artisan activity-log:install --config
php artisan activity-log:install --migrations
php artisan activity-log:install --views
php artisan activity-log:install --components

# Clean old logs
php artisan activity-log:clean
php artisan activity-log:clean --days=30
php artisan activity-log:clean --days=90 --force
```

## Tips

1. **Use descriptive messages** - Make logs easy to understand
2. **Include relevant data** - Add properties that provide context
3. **Don't log sensitive data** - Passwords and tokens are auto-excluded
4. **Set up cleanup** - Prevent database bloat with scheduled cleanup
5. **Use appropriate types** - Stick to predefined types for consistency
6. **Enable auto-logging** - For models you want to track automatically

## Next Steps

- Read the [full documentation](README.md)
- Check out [usage examples](USAGE.md)
- Review [installation guide](INSTALLATION.md)
- Explore the [changelog](CHANGELOG.md)

## Need Help?

- 📖 [Full Documentation](README.md)
- 💡 [Usage Examples](USAGE.md)
- 🔧 [Installation Guide](INSTALLATION.md)
- 🐛 [Report Issues](https://github.com/almosabbirrakib/laravel-activity-log/issues)
- 📧 [Email Support](mailto:almosabbirrakib@example.com)

---

**That's it!** You're now ready to track activities in your Laravel application. Happy logging! 🎉

