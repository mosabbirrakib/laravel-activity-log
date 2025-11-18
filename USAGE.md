# Usage Examples

This document provides practical examples of how to use the Laravel Activity Log package.

## Table of Contents

- [Basic Usage](#basic-usage)
- [Model Integration](#model-integration)
- [Controller Examples](#controller-examples)
- [Custom Logging](#custom-logging)
- [Retrieving Logs](#retrieving-logs)
- [Frontend Integration](#frontend-integration)
- [Advanced Usage](#advanced-usage)

## Basic Usage

### Simple Activity Logging

```php
use AlMosabbirRakib\ActivityLog\Facades\ActivityLog;

// Log a simple activity
ActivityLog::log('User viewed dashboard');

// Log with type
ActivityLog::log('User exported report', 'export');

// Log with properties
ActivityLog::log('Settings updated', 'updated', [
    'theme' => 'dark',
    'language' => 'en'
]);
```

### Using Helper Functions

```php
// Simple log
activity_log('User performed action');

// With type and properties
activity_log('Data exported', 'export', [
    'format' => 'csv',
    'rows' => 1000
]);
```

## Model Integration

### Adding the Trait

```php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use AlMosabbirRakib\ActivityLog\Traits\LogsActivity;

class Post extends Model
{
    use LogsActivity;
    
    protected $fillable = ['title', 'content', 'status'];
}
```

### Automatic Logging

Enable automatic logging in `config/activity-log.php`:

```php
'auto_log' => [
    'enabled' => true,
    'events' => ['created', 'updated', 'deleted'],
],
```

Now all model events will be automatically logged:

```php
// This will automatically log a "created" activity
$post = Post::create([
    'title' => 'My First Post',
    'content' => 'Hello World',
]);

// This will automatically log an "updated" activity
$post->update(['title' => 'Updated Title']);

// This will automatically log a "deleted" activity
$post->delete();
```

### Manual Model Logging

```php
$post = Post::find(1);

// Log a custom activity
$post->logActivity('published', 'Post was published', [
    'published_at' => now(),
    'published_by' => auth()->id(),
]);

// Using predefined methods
activity_created($post);
activity_updated($post, 'Post content updated');
activity_deleted($post);
```

## Controller Examples

### User Authentication

```php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    public function login(Request $request)
    {
        // Your authentication logic...
        
        if (Auth::attempt($credentials)) {
            // Log successful login
            activity_login(Auth::user());
            
            return redirect()->intended('dashboard');
        }
        
        return back()->withErrors(['email' => 'Invalid credentials']);
    }
    
    public function logout(Request $request)
    {
        // Log logout before actually logging out
        activity_logout(Auth::user());
        
        Auth::logout();
        
        return redirect('/');
    }
}
```

### CRUD Operations

```php
namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use AlMosabbirRakib\ActivityLog\Facades\ActivityLog;

class PostController extends Controller
{
    public function store(Request $request)
    {
        $post = Post::create($request->validated());
        
        // Log the creation
        ActivityLog::created($post, 'New post created: ' . $post->title);
        
        return redirect()->route('posts.show', $post);
    }
    
    public function update(Request $request, Post $post)
    {
        $oldTitle = $post->title;
        $post->update($request->validated());
        
        // Log with old and new values
        ActivityLog::updated($post, 'Post updated', [
            'old_title' => $oldTitle,
            'new_title' => $post->title,
        ]);
        
        return redirect()->route('posts.show', $post);
    }
    
    public function destroy(Post $post)
    {
        $title = $post->title;
        $post->delete();
        
        // Log deletion
        ActivityLog::deleted($post, "Post deleted: {$title}");
        
        return redirect()->route('posts.index');
    }
    
    public function publish(Post $post)
    {
        $post->update(['status' => 'published']);
        
        // Log custom action
        ActivityLog::log(
            "Post published: {$post->title}",
            'published',
            ['post_id' => $post->id],
            $post
        );
        
        return back()->with('success', 'Post published!');
    }
}
```

### API Controller

```php
namespace App\Http\Controllers\Api;

use App\Models\Product;
use Illuminate\Http\Request;
use AlMosabbirRakib\ActivityLog\Facades\ActivityLog;

class ProductController extends Controller
{
    public function update(Request $request, Product $product)
    {
        $changes = $request->validated();
        $product->update($changes);
        
        // Log API update
        ActivityLog::log(
            "Product updated via API: {$product->name}",
            'updated',
            [
                'changes' => $changes,
                'api_version' => 'v1',
            ],
            $product
        );
        
        return response()->json($product);
    }
}
```

## Custom Logging

### E-commerce Examples

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
    'status' => $payment->status,
    'transaction_id' => $payment->transaction_id,
], $payment);

// Refund issued
ActivityLog::log('Refund issued', 'refund', [
    'order_id' => $order->id,
    'amount' => $refund->amount,
    'reason' => $refund->reason,
], $order);
```

### File Operations

```php
// File uploaded
ActivityLog::log('File uploaded', 'upload', [
    'filename' => $file->getClientOriginalName(),
    'size' => $file->getSize(),
    'mime_type' => $file->getMimeType(),
]);

// File downloaded
ActivityLog::log('File downloaded', 'download', [
    'filename' => $document->filename,
    'file_id' => $document->id,
], $document);

// File deleted
ActivityLog::log('File deleted', 'deleted', [
    'filename' => $document->filename,
    'deleted_by' => auth()->user()->name,
], $document);
```

### User Management

```php
// User role changed
ActivityLog::log('User role changed', 'role_changed', [
    'user_id' => $user->id,
    'old_role' => $oldRole,
    'new_role' => $newRole,
], $user);

// Password changed
ActivityLog::log('Password changed', 'password_changed', [
    'user_id' => $user->id,
    'changed_at' => now(),
]);

// Email verified
ActivityLog::log('Email verified', 'email_verified', [
    'email' => $user->email,
], $user);
```

## Retrieving Logs

### Basic Queries

```php
use AlMosabbirRakib\ActivityLog\Models\ActivityLog;

// Get all logs
$logs = ActivityLog::latest()->paginate(15);

// Get logs with relationships
$logs = ActivityLog::with(['causer', 'subject'])->latest()->get();

// Get specific log
$log = ActivityLog::find(1);
```

### Filtering

```php
// By type
$createdLogs = ActivityLog::ofType('created')->get();

// By user
$userLogs = ActivityLog::forCauser($user)->get();

// By subject
$postLogs = ActivityLog::forSubject($post)->get();

// By date range
$logs = ActivityLog::dateRange('2024-01-01', '2024-12-31')->get();

// Search
$logs = ActivityLog::search('login')->get();

// Combined filters
$logs = ActivityLog::ofType('updated')
    ->dateRange('2024-01-01', '2024-12-31')
    ->search('post')
    ->latest()
    ->paginate(20);
```

### Using Relationships

```php
// Get all logs for a post
$post = Post::find(1);
$logs = $post->activityLogs;

// Get all activities by a user
$user = User::find(1);
$activities = $user->causedActivities;

// Get recent activities for a user
$recentActivities = $user->causedActivities()
    ->latest()
    ->take(10)
    ->get();
```

## Frontend Integration

### Blade Component

```blade
<!-- In your view -->
<div class="container">
    <h1>Activity Logs</h1>
    @include('activity-log::index')
</div>
```

### Vue 2 Integration

```javascript
// In your main.js or app.js
import Vue from 'vue';
import ActivityLogVue2 from './components/activity-log/ActivityLogVue2.vue';

Vue.component('activity-log', ActivityLogVue2);

new Vue({
    el: '#app',
});
```

```vue
<!-- In your template -->
<template>
    <div>
        <h1>Activity Logs</h1>
        <activity-log api-base-url="/api/activity-logs"></activity-log>
    </div>
</template>
```

### Vue 3 Integration

```javascript
// In your main.js
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
    <div>
        <h1>Activity Logs</h1>
        <ActivityLogVue3 api-base-url="/api/activity-logs" />
    </div>
</template>
```

## Advanced Usage

### Custom Activity Description

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
        $properties = parent::getActivityProperties($type);
        
        // Add custom properties
        $properties['category'] = $this->category->name;
        $properties['author'] = $this->author->name;
        
        return $properties;
    }
}
```

### Scheduled Cleanup

```php
// In app/Console/Kernel.php
protected function schedule(Schedule $schedule)
{
    // Clean logs older than 90 days, every day at 2 AM
    $schedule->command('activity-log:clean --days=90 --force')
             ->dailyAt('02:00');
}
```

### Custom Middleware Usage

```php
// In routes/web.php
Route::middleware(['auth', \AlMosabbirRakib\ActivityLog\Middleware\LogActivity::class])
    ->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index']);
        Route::resource('posts', PostController::class);
    });
```

### Querying with Facade

```php
use AlMosabbirRakib\ActivityLog\Facades\ActivityLog;

// Get logs for current user
$myLogs = ActivityLog::forUser(auth()->user())->get();

// Get logs for a specific subject
$postLogs = ActivityLog::forSubject($post)->get();

// Use the query builder
$logs = ActivityLog::query()
    ->where('type', 'login')
    ->whereDate('created_at', today())
    ->get();
```

## Tips and Best Practices

1. **Use Descriptive Messages**: Make your log descriptions clear and informative
2. **Include Relevant Data**: Add properties that will help you understand what happened
3. **Don't Log Sensitive Data**: Passwords, tokens, and other sensitive data are automatically excluded
4. **Use Appropriate Types**: Use predefined types when possible for consistency
5. **Clean Old Logs**: Set up automatic cleanup to prevent database bloat
6. **Index Your Queries**: The package includes proper indexes, but add more if needed
7. **Use Eager Loading**: Always eager load relationships when displaying multiple logs
8. **Customize for Your Needs**: Override methods in the trait to customize behavior

## Troubleshooting

### Logs Not Appearing

1. Check if routes are enabled in config
2. Verify authentication middleware is working
3. Check if auto_log is enabled if using trait
4. Verify database migration has run

### Performance Issues

1. Add indexes to frequently queried columns
2. Use pagination instead of loading all logs
3. Set up log retention and cleanup
4. Use eager loading for relationships

### Frontend Not Working

1. Verify API routes are accessible
2. Check authentication (Sanctum for API)
3. Ensure CORS is configured if using separate frontend
4. Check browser console for errors

