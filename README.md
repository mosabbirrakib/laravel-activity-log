# Laravel Activity Log

<p align="center">
<a href="https://packagist.org/packages/almosabbirrakib/laravel-activity-log"><img src="https://img.shields.io/packagist/dt/almosabbirrakib/laravel-activity-log" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/almosabbirrakib/laravel-activity-log"><img src="https://img.shields.io/packagist/v/almosabbirrakib/laravel-activity-log" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/almosabbirrakib/laravel-activity-log"><img src="https://img.shields.io/packagist/l/almosabbirrakib/laravel-activity-log" alt="License"></a>
<a href="https://packagist.org/packages/almosabbirrakib/laravel-activity-log"><img src="https://img.shields.io/packagist/php-v/almosabbirrakib/laravel-activity-log" alt="PHP Version"></a>
</p>

A comprehensive, production-ready Laravel package for logging and monitoring user activities with support for **Blade**, **Vue 2**, and **Vue 3** frontends. Track every action in your application with a beautiful, professional dashboard.

---

## ✨ Features

- 🎯 **Easy Integration** - Simple trait-based implementation, ready in minutes
- 🎨 **Multiple Frontend Options** - Choose between Blade, Vue 2, or Vue 3 components
- 📊 **Beautiful Dashboard** - Professional UI with Tailwind CSS and responsive design
- 🔍 **Advanced Filtering** - Search, filter by type, date range, user, and subject
- 📈 **Statistics Dashboard** - Real-time stats (total, today, this week, this month)
- 🚀 **High Performance** - Optimized queries with proper database indexing
- 🔒 **Secure by Default** - Configurable middleware, authentication, and sensitive data exclusion
- 📦 **Highly Configurable** - Extensive configuration options with sensible defaults
- 🧹 **Automatic Cleanup** - Built-in Artisan command for log retention management
- 🎭 **Multiple Log Types** - Created, updated, deleted, login, logout, and custom types
- 🔌 **RESTful API** - Complete API endpoints for external integrations
- 🌐 **Polymorphic Relations** - Track activities on any model
- 💾 **Flexible Storage** - Store additional metadata as JSON properties
- 🛠️ **Helper Functions** - Convenient global helpers for quick logging
- 📱 **Mobile Responsive** - Works perfectly on all screen sizes

---

## 📋 Requirements

- **PHP:** 8.0 or higher
- **Laravel:** 9.x, 10.x, or 11.x
- **Database:** MySQL 5.7+ / PostgreSQL 9.6+ / SQLite 3.8+

---

## 🚀 Quick Start

### Installation

```bash
# Install the package
composer require almosabbirrakib/laravel-activity-log

# Run the installation command
php artisan activity-log:install

# Run migrations
php artisan migrate
```

### Basic Usage

```php
use AlMosabbirRakib\ActivityLog\Facades\ActivityLog;

// Log a simple activity
ActivityLog::log('User viewed dashboard');

// Log with type and properties
ActivityLog::log('Settings updated', 'updated', [
    'theme' => 'dark',
    'language' => 'en'
]);

// Using helper functions
activity_log('User performed action');
activity_login();
activity_logout();
```

### Add to Your Models

```php
use AlMosabbirRakib\ActivityLog\Traits\LogsActivity;

class Post extends Model
{
    use LogsActivity;
}

// Now all model events are automatically logged!
$post = Post::create(['title' => 'My Post']); // Logged
$post->update(['title' => 'Updated']); // Logged
$post->delete(); // Logged
```

### View Activity Logs

Visit the dashboard in your browser:
```
http://your-app.test/activity-logs
```

---

## 📖 Documentation

### Table of Contents

- [Installation Guide](#-installation)
- [Configuration](#-configuration)
- [Usage Examples](#-usage)
- [Frontend Integration](#-frontend-integration)
- [API Endpoints](#-api-endpoints)
- [Artisan Commands](#-artisan-commands)
- [Advanced Usage](#-advanced-usage)

---

## 🔧 Configuration

After installation, configure the package in `config/activity-log.php`:

```php
return [
    // Database table name
    'table_name' => 'activity_logs',

    // User model
    'user_model' => 'App\\Models\\User',

    // Routes
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
        'enabled' => false, // Set to true to enable
        'events' => ['created', 'updated', 'deleted'],
    ],

    // Log retention (null = keep forever)
    'retention_days' => 90,

    // Privacy settings
    'log_ip_address' => true,
    'log_user_agent' => true,

    // Excluded attributes (won't be logged)
    'excluded_attributes' => [
        'password',
        'remember_token',
        'api_token',
    ],
];
```

---

## 💡 Usage

### Using the Trait (Recommended)

Add the `LogsActivity` trait to any model:

```php
use AlMosabbirRakib\ActivityLog\Traits\LogsActivity;

class Post extends Model
{
    use LogsActivity;
}
```

Enable automatic logging in config:
```php
'auto_log' => [
    'enabled' => true,
    'events' => ['created', 'updated', 'deleted'],
],
```

### Using the Facade

```php
use AlMosabbirRakib\ActivityLog\Facades\ActivityLog;

// Simple log
ActivityLog::log('User viewed dashboard');

// With type
ActivityLog::log('Report generated', 'export');

// With properties
ActivityLog::log('Settings updated', 'updated', [
    'old_theme' => 'light',
    'new_theme' => 'dark',
]);

// With subject
ActivityLog::log('Post published', 'published', [], $post);

// Predefined methods
ActivityLog::created($post);
ActivityLog::updated($post, 'Post content updated');
ActivityLog::deleted($post);
ActivityLog::login($user);
ActivityLog::logout($user);
```

### Using Helper Functions

```php
// Simple logging
activity_log('User performed action');

// Predefined helpers
activity_created($post);
activity_updated($post, 'Post updated');
activity_deleted($post);
activity_login();
activity_logout();
```

### Controller Examples

```php
class PostController extends Controller
{
    public function store(Request $request)
    {
        $post = Post::create($request->validated());

        ActivityLog::created($post, 'New post created: ' . $post->title);

        return redirect()->route('posts.show', $post);
    }

    public function update(Request $request, Post $post)
    {
        $oldTitle = $post->title;
        $post->update($request->validated());

        ActivityLog::updated($post, 'Post updated', [
            'old_title' => $oldTitle,
            'new_title' => $post->title,
        ]);

        return redirect()->route('posts.show', $post);
    }
}
```

### Retrieving Logs

```php
use AlMosabbirRakib\ActivityLog\Models\ActivityLog;

// Get all logs
$logs = ActivityLog::latest()->paginate(15);

// Filter by type
$createdLogs = ActivityLog::ofType('created')->get();

// Filter by user
$userLogs = ActivityLog::forCauser($user)->get();

// Filter by subject
$postLogs = ActivityLog::forSubject($post)->get();

// Date range
$logs = ActivityLog::dateRange('2024-01-01', '2024-12-31')->get();

// Search
$logs = ActivityLog::search('login')->get();

// Combined filters
$logs = ActivityLog::ofType('updated')
    ->dateRange('2024-01-01', '2024-12-31')
    ->search('post')
    ->latest()
    ->paginate(20);

// From model relationships
$post = Post::find(1);
$logs = $post->activityLogs;

$user = User::find(1);
$activities = $user->causedActivities;
```

---

## 🎨 Frontend Integration

### Blade (Default)

The Blade view is automatically available at `/activity-logs`:

```blade
<!-- Include in your layout -->
<a href="{{ route('activity-logs.index') }}">Activity Logs</a>

<!-- Or embed directly -->
@include('activity-log::index')
```

### Vue 2 Integration

```javascript
// In resources/js/app.js
import Vue from 'vue';
import ActivityLogVue2 from './components/activity-log/ActivityLogVue2.vue';

Vue.component('activity-log', ActivityLogVue2);

new Vue({
    el: '#app',
});
```

```blade
<!-- In your Blade template -->
<div id="app">
    <activity-log api-base-url="/api/activity-logs"></activity-log>
</div>
```

### Vue 3 Integration

```javascript
// In resources/js/app.js
import { createApp } from 'vue';
import ActivityLogVue3 from './components/activity-log/ActivityLogVue3.vue';

const app = createApp({
    components: {
        ActivityLogVue3
    }
});

app.mount('#app');
```

```vue
<!-- In your template -->
<template>
    <ActivityLogVue3 api-base-url="/api/activity-logs" />
</template>
```

---

## 🔌 API Endpoints

All API endpoints are prefixed with `/api/activity-logs`:

### Get All Logs
```http
GET /api/activity-logs
```

**Query Parameters:**
- `page` - Page number (default: 1)
- `per_page` - Items per page (default: 15)
- `search` - Search in description
- `type` - Filter by type
- `date_from` - Start date (Y-m-d)
- `date_to` - End date (Y-m-d)
- `causer_id` - Filter by user ID
- `causer_type` - Filter by user type
- `subject_id` - Filter by subject ID
- `subject_type` - Filter by subject type

**Example:**
```bash
curl -X GET "http://your-app.test/api/activity-logs?page=1&per_page=15&type=created&search=post" \
  -H "Authorization: Bearer YOUR_TOKEN"
```

### Get Single Log
```http
GET /api/activity-logs/{id}
```

### Get Log Types
```http
GET /api/activity-logs/types
```

### Get Statistics
```http
GET /api/activity-logs/stats
```

Returns:
```json
{
    "total": 1250,
    "today": 45,
    "this_week": 320,
    "this_month": 890
}
```

### Cleanup Old Logs
```http
DELETE /api/activity-logs/cleanup?days=90
```

---

## 🛠️ Artisan Commands

### Install Package Assets

```bash
# Install all assets
php artisan activity-log:install

# Install specific assets
php artisan activity-log:install --config
php artisan activity-log:install --migrations
php artisan activity-log:install --views
php artisan activity-log:install --components

# Force overwrite existing files
php artisan activity-log:install --force
```

### Clean Old Logs

```bash
# Interactive cleanup
php artisan activity-log:clean

# Clean logs older than 30 days
php artisan activity-log:clean --days=30

# Force cleanup without confirmation
php artisan activity-log:clean --days=90 --force
```

### Scheduled Cleanup

Add to `app/Console/Kernel.php`:

```php
protected function schedule(Schedule $schedule)
{
    // Clean logs older than 90 days, daily at 2 AM
    $schedule->command('activity-log:clean --days=90 --force')
             ->dailyAt('02:00');
}
```

---

## 🔥 Advanced Usage

### Custom Activity Descriptions

```php
class Post extends Model
{
    use LogsActivity;

    protected function getActivityDescription(string $type): string
    {
        return match ($type) {
            'created' => "New post created: {$this->title}",
            'updated' => "Post updated: {$this->title}",
            'deleted' => "Post deleted: {$this->title}",
            default => parent::getActivityDescription($type),
        };
    }
}
```

### Custom Activity Properties

```php
class Post extends Model
{
    use LogsActivity;

    protected function getActivityProperties(string $type): array
    {
        return [
            'title' => $this->title,
            'category' => $this->category->name,
            'author' => $this->author->name,
            'status' => $this->status,
        ];
    }
}
```

### Middleware for Automatic Request Logging

Add to `app/Http/Kernel.php`:

```php
protected $middlewareGroups = [
    'web' => [
        // ... other middleware
        \AlMosabbirRakib\ActivityLog\Middleware\LogActivity::class,
    ],
];
```

Or apply to specific routes:

```php
Route::middleware(['auth', \AlMosabbirRakib\ActivityLog\Middleware\LogActivity::class])
    ->group(function () {
        Route::resource('posts', PostController::class);
    });
```

### E-commerce Example

```php
// Order placed
ActivityLog::log('Order placed', 'order_placed', [
    'order_id' => $order->id,
    'total' => $order->total,
    'items_count' => $order->items->count(),
], $order);

// Payment processed
ActivityLog::log('Payment processed', 'payment', [
    'amount' => $payment->amount,
    'method' => $payment->method,
    'transaction_id' => $payment->transaction_id,
], $payment);
```

---

## 🎯 Use Cases

- **Audit Trails** - Track all changes to critical data
- **User Monitoring** - Monitor user actions and behavior
- **Security** - Detect suspicious activities
- **Compliance** - Meet regulatory requirements (GDPR, HIPAA, etc.)
- **Debugging** - Trace issues and understand user flows
- **Analytics** - Analyze user behavior patterns
- **Customer Support** - Help users by reviewing their activity history

---

## 🔒 Security

- **Authentication Required** - All routes protected by authentication middleware
- **Sensitive Data Exclusion** - Passwords and tokens automatically excluded
- **Configurable Privacy** - Control IP and user agent logging
- **API Authentication** - Laravel Sanctum support for API endpoints
- **Customizable Middleware** - Add your own authorization logic

---

## ⚡ Performance

- **Optimized Queries** - Proper database indexing on all searchable columns
- **Eager Loading** - Relationships loaded efficiently
- **Pagination** - Large datasets handled with pagination
- **Configurable Retention** - Automatic cleanup prevents database bloat
- **Minimal Overhead** - Lightweight implementation with no performance impact

---

## 🧪 Testing

```bash
# Run tests (coming soon)
composer test
```

---

## 📝 Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

---

## 🤝 Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

1. Fork the repository
2. Create your feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add some amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

---

## 🐛 Security Vulnerabilities

If you discover a security vulnerability, please send an e-mail to Al Mosabbir Rakib via [mrakib50.cse@gmail.com](mailto:mrakib50.cse@gmail.com). All security vulnerabilities will be promptly addressed.

---

## 📄 License

The Laravel Activity Log package is open-sourced software licensed under the [MIT license](LICENSE.md).

---

## 👨‍💻 Author

**Al-Mosabbir Rakib**

- GitHub: [@almosabbirrakib](https://github.com/mosabbirrakib)
- Email: mrakib50.cse@gmail.com

---

## 🙏 Acknowledgments

- Built with ❤️ for the Laravel community
- Inspired by the need for simple, beautiful activity logging
- Thanks to all contributors and users

---

<p align="center">
Made with ❤️ by <a href="https://github.com/mosabbirrakib">Al-Mosabbir Rakib</a>
</p>

<p align="center">
⭐ If you find this package helpful, please consider giving it a star on GitHub! ⭐
</p>
