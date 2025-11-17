# Installation Guide

This guide will walk you through the complete installation and setup process for the Laravel Activity Log package.

## Prerequisites

Before installing, ensure your system meets these requirements:

- PHP 8.0 or higher
- Laravel 9.x, 10.x, or 11.x
- Composer
- MySQL 5.7+ / PostgreSQL 9.6+ / SQLite 3.8+

## Step-by-Step Installation

### 1. Install the Package

Install the package via Composer:

```bash
composer require almosabbirrakib/laravel-activity-log
```

### 2. Run the Installation Command

The package includes an installation command that will publish all necessary files:

```bash
php artisan activity-log:install
```

This command will publish:
- Configuration file to `config/activity-log.php`
- Migration file to `database/migrations/`
- Blade views to `resources/views/vendor/activity-log/`
- Vue components to `resources/js/components/activity-log/`
- Assets to `public/vendor/activity-log/`

#### Installation Options

You can also publish specific assets:

```bash
# Publish only configuration
php artisan activity-log:install --config

# Publish only migrations
php artisan activity-log:install --migrations

# Publish only views
php artisan activity-log:install --views

# Publish only Vue components
php artisan activity-log:install --components

# Force overwrite existing files
php artisan activity-log:install --force
```

### 3. Run Migrations

Create the `activity_logs` table in your database:

```bash
php artisan migrate
```

This will create a table with the following structure:
- `id` - Primary key
- `type` - Activity type (created, updated, deleted, etc.)
- `description` - Human-readable description
- `properties` - JSON field for additional data
- `subject_type` & `subject_id` - Polymorphic relation to the affected model
- `causer_type` & `causer_id` - Polymorphic relation to the user who caused the activity
- `ip_address` - User's IP address
- `user_agent` - User's browser/client information
- `created_at` & `updated_at` - Timestamps

### 4. Configure the Package (Optional)

Open `config/activity-log.php` and customize the settings according to your needs:

```php
return [
    // Customize table name
    'table_name' => 'activity_logs',
    
    // Set your user model
    'user_model' => 'App\\Models\\User',
    
    // Configure routes
    'routes' => [
        'enabled' => true,
        'prefix' => 'activity-logs',
        'middleware' => ['web', 'auth'],
        'api_middleware' => ['api', 'auth:sanctum'],
    ],
    
    // Enable automatic logging
    'auto_log' => [
        'enabled' => false, // Set to true to enable
        'events' => ['created', 'updated', 'deleted'],
    ],
    
    // Set log retention
    'retention_days' => 90, // null = keep forever
    
    // Privacy settings
    'log_ip_address' => true,
    'log_user_agent' => true,
];
```

### 5. Add the Trait to Your Models

Add the `LogsActivity` trait to any model you want to track:

```php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use AlMosabbirRakib\ActivityLog\Traits\LogsActivity;

class Post extends Model
{
    use LogsActivity;
    
    // Your model code...
}
```

### 6. Verify Installation

Visit the activity logs page in your browser:

```
http://your-app.test/activity-logs
```

You should see the activity logs dashboard.

## Frontend Setup

### For Blade (Default)

No additional setup required! The Blade views are ready to use.

### For Vue 2

1. Ensure Vue 2 is installed:

```bash
npm install vue@2
```

2. Import and register the component in your `resources/js/app.js`:

```javascript
import Vue from 'vue';
import ActivityLogVue2 from './components/activity-log/ActivityLogVue2.vue';

Vue.component('activity-log', ActivityLogVue2);

new Vue({
    el: '#app',
});
```

3. Use in your Blade template:

```blade
<div id="app">
    <activity-log api-base-url="/api/activity-logs"></activity-log>
</div>
```

4. Compile assets:

```bash
npm run dev
```

### For Vue 3

1. Ensure Vue 3 is installed:

```bash
npm install vue@3
```

2. Import and use the component in your `resources/js/app.js`:

```javascript
import { createApp } from 'vue';
import ActivityLogVue3 from './components/activity-log/ActivityLogVue3.vue';

const app = createApp({
    components: {
        ActivityLogVue3
    }
});

app.mount('#app');
```

3. Use in your template:

```vue
<template>
    <ActivityLogVue3 api-base-url="/api/activity-logs" />
</template>
```

4. Compile assets:

```bash
npm run dev
```

## API Setup (Optional)

If you're using the API endpoints, ensure Laravel Sanctum is installed and configured:

```bash
composer require laravel/sanctum
php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"
php artisan migrate
```

Update your `config/activity-log.php` to use Sanctum middleware:

```php
'routes' => [
    'api_middleware' => ['api', 'auth:sanctum'],
],
```

## Testing the Installation

### 1. Test Manual Logging

Create a test route in `routes/web.php`:

```php
use AlMosabbirRakib\ActivityLog\Facades\ActivityLog;

Route::get('/test-activity-log', function () {
    ActivityLog::log('Test activity logged successfully!', 'test');
    return 'Activity logged! Check /activity-logs';
})->middleware('auth');
```

Visit `/test-activity-log` and then check `/activity-logs` to see your log.

### 2. Test Model Logging

Enable automatic logging in `config/activity-log.php`:

```php
'auto_log' => [
    'enabled' => true,
    'events' => ['created', 'updated', 'deleted'],
],
```

Then create, update, or delete a model that uses the `LogsActivity` trait:

```php
$post = Post::create([
    'title' => 'Test Post',
    'content' => 'This is a test',
]);
```

Check `/activity-logs` to see the automatically logged activity.

### 3. Test API Endpoints

Test the API using curl or Postman:

```bash
# Get all logs
curl -X GET http://your-app.test/api/activity-logs \
  -H "Authorization: Bearer YOUR_TOKEN"

# Get statistics
curl -X GET http://your-app.test/api/activity-logs/stats \
  -H "Authorization: Bearer YOUR_TOKEN"
```

## Troubleshooting

### Issue: Routes not found

**Solution**: Clear your route cache:
```bash
php artisan route:clear
php artisan route:cache
```

### Issue: Views not found

**Solution**: Clear your view cache:
```bash
php artisan view:clear
```

### Issue: Configuration not updating

**Solution**: Clear your config cache:
```bash
php artisan config:clear
php artisan config:cache
```

### Issue: Migration fails

**Solution**: Check if the table already exists:
```bash
php artisan migrate:status
```

If it exists, you can skip the migration or drop it first:
```bash
php artisan migrate:rollback --step=1
php artisan migrate
```

### Issue: Unauthorized access to activity logs

**Solution**: Ensure you're logged in and the middleware is configured correctly in `config/activity-log.php`.

### Issue: Vue components not working

**Solution**: 
1. Ensure you've compiled your assets: `npm run dev`
2. Check browser console for errors
3. Verify the component is properly imported and registered
4. Check that the API endpoints are accessible

## Updating the Package

To update to the latest version:

```bash
composer update almosabbirrakib/laravel-activity-log
```

After updating, republish the assets if needed:

```bash
php artisan activity-log:install --force
```

## Uninstalling

To uninstall the package:

1. Remove the package:
```bash
composer remove almosabbirrakib/laravel-activity-log
```

2. Drop the table (optional):
```bash
php artisan migrate:rollback
```

3. Remove published files (optional):
```bash
rm config/activity-log.php
rm -rf resources/views/vendor/activity-log
rm -rf resources/js/components/activity-log
rm -rf public/vendor/activity-log
```

## Next Steps

After installation, check out:

- [README.md](README.md) - Complete package documentation
- [USAGE.md](USAGE.md) - Practical usage examples
- [CHANGELOG.md](CHANGELOG.md) - Version history and changes

## Support

If you encounter any issues during installation:

1. Check the [troubleshooting section](#troubleshooting) above
2. Review the [documentation](README.md)
3. Open an issue on [GitHub](https://github.com/almosabbirrakib/laravel-activity-log/issues)
4. Contact support at almosabbirrakib@example.com

## Production Deployment

Before deploying to production:

1. Set appropriate retention days in config
2. Set up scheduled cleanup:
```php
// In app/Console/Kernel.php
$schedule->command('activity-log:clean --days=90 --force')->daily();
```
3. Ensure proper authentication is configured
4. Review and adjust middleware settings
5. Test all functionality in staging environment
6. Optimize autoloader:
```bash
composer install --optimize-autoloader --no-dev
```
7. Cache configuration:
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

Congratulations! Your Laravel Activity Log package is now installed and ready to use! 🎉

