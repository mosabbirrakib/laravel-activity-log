# Package Structure

This document outlines the complete structure of the Laravel Activity Log package.

## Directory Structure

```
laravel-activity-log/
├── src/
│   ├── ActivityLogServiceProvider.php    # Main service provider
│   ├── ActivityLogger.php                # Core logging functionality
│   ├── helpers.php                       # Helper functions
│   │
│   ├── Console/
│   │   └── Commands/
│   │       ├── CleanActivityLogsCommand.php    # Cleanup command
│   │       └── InstallCommand.php              # Installation command
│   │
│   ├── Facades/
│   │   └── ActivityLog.php               # Facade for easy access
│   │
│   ├── Http/
│   │   └── Controllers/
│   │       └── ActivityLogController.php # API & Web controller
│   │
│   ├── Middleware/
│   │   └── LogActivity.php               # Request logging middleware
│   │
│   ├── Models/
│   │   └── ActivityLog.php               # ActivityLog model
│   │
│   ├── Traits/
│   │   └── LogsActivity.php              # Trait for models
│   │
│   ├── config/
│   │   └── activity-log.php              # Configuration file
│   │
│   ├── database/
│   │   └── migrations/
│   │       └── create_activity_logs_table.php.stub
│   │
│   ├── resources/
│   │   ├── views/
│   │   │   └── index.blade.php           # Main Blade view
│   │   │
│   │   ├── js/
│   │   │   └── components/
│   │   │       ├── ActivityLogVue2.vue   # Vue 2 component
│   │   │       ├── ActivityLogVue3.vue   # Vue 3 component
│   │   │       └── activity-log.css      # Component styles
│   │   │
│   │   └── assets/
│   │       └── js/
│   │           └── activity-log.js       # Vanilla JS for Blade
│   │
│   └── routes/
│       ├── web.php                       # Web routes
│       └── api.php                       # API routes
│
├── composer.json                         # Composer configuration
├── package.json                          # NPM configuration
├── .gitignore                           # Git ignore rules
├── LICENSE.md                           # MIT License
├── README.md                            # Main documentation
├── INSTALLATION.md                      # Installation guide
├── USAGE.md                             # Usage examples
├── QUICKSTART.md                        # Quick start guide
├── CHANGELOG.md                         # Version history
└── STRUCTURE.md                         # This file
```

## File Descriptions

### Core Files

#### `ActivityLogServiceProvider.php`
- Registers the package with Laravel
- Publishes configuration, migrations, views, and assets
- Registers routes and commands
- Loads views and migrations

#### `ActivityLogger.php`
- Core logging functionality
- Methods for logging different types of activities
- Property filtering and sanitization
- Query builder methods

#### `helpers.php`
- Global helper functions
- `activity_log()` - Log any activity
- `activity_created()` - Log creation
- `activity_updated()` - Log update
- `activity_deleted()` - Log deletion
- `activity_login()` - Log login
- `activity_logout()` - Log logout

### Models & Traits

#### `Models/ActivityLog.php`
- Eloquent model for activity logs
- Relationships (causer, subject)
- Query scopes (ofType, forCauser, forSubject, dateRange, search)
- Accessors (formatted_created_at, causer_name, subject_name, type_badge_color)

#### `Traits/LogsActivity.php`
- Trait for models to enable activity logging
- Automatic event listeners (created, updated, deleted)
- `logActivity()` method
- `activityLogs()` relationship
- `causedActivities()` relationship

### Controllers & Routes

#### `Http/Controllers/ActivityLogController.php`
- `index()` - Display Blade view
- `getLogs()` - Get paginated logs with filters (API)
- `show()` - Get single log (API)
- `getTypes()` - Get all log types (API)
- `getStats()` - Get statistics (API)
- `cleanup()` - Delete old logs (API)

#### `routes/web.php`
- `GET /activity-logs` - Main view

#### `routes/api.php`
- `GET /api/activity-logs` - Get logs
- `GET /api/activity-logs/{id}` - Get single log
- `GET /api/activity-logs/types` - Get types
- `GET /api/activity-logs/stats` - Get statistics
- `DELETE /api/activity-logs/cleanup` - Cleanup

### Commands

#### `Console/Commands/CleanActivityLogsCommand.php`
- Command: `activity-log:clean`
- Deletes old activity logs based on retention policy
- Options: `--days`, `--force`

#### `Console/Commands/InstallCommand.php`
- Command: `activity-log:install`
- Publishes all package assets
- Options: `--config`, `--migrations`, `--views`, `--components`, `--force`

### Middleware

#### `Middleware/LogActivity.php`
- Automatically logs HTTP requests
- Configurable skip paths
- Maps HTTP methods to activity types

### Frontend Components

#### `resources/views/index.blade.php`
- Complete Blade UI with Tailwind CSS
- Statistics cards
- Advanced filtering
- Responsive table
- Pagination
- Detail modal

#### `resources/js/components/ActivityLogVue2.vue`
- Vue 2 component with Options API
- Full feature parity with Blade view
- Reactive data and computed properties
- API integration

#### `resources/js/components/ActivityLogVue3.vue`
- Vue 3 component with Composition API
- Modern Vue 3 features (Teleport, setup script)
- TypeScript-ready structure
- API integration

#### `resources/js/components/activity-log.css`
- Shared styles for Vue components
- Responsive design
- Professional UI elements
- Consistent with Blade view

#### `resources/assets/js/activity-log.js`
- Vanilla JavaScript for Blade view
- Class-based architecture
- API integration
- DOM manipulation

### Configuration

#### `config/activity-log.php`
Configuration options:
- `table_name` - Database table name
- `user_model` - User model class
- `routes` - Route configuration
- `per_page` - Pagination default
- `date_format` - Date display format
- `auto_log` - Automatic logging settings
- `retention_days` - Log retention period
- `log_ip_address` - IP logging toggle
- `log_user_agent` - User agent logging toggle
- `log_properties` - Properties logging toggle
- `excluded_attributes` - Sensitive fields to exclude
- `ui` - UI configuration

### Database

#### `database/migrations/create_activity_logs_table.php.stub`
Creates table with:
- `id` - Primary key
- `type` - Activity type (indexed)
- `description` - Activity description
- `properties` - JSON metadata
- `subject_type`, `subject_id` - Polymorphic subject (indexed)
- `causer_type`, `causer_id` - Polymorphic causer (indexed)
- `ip_address` - User IP
- `user_agent` - Browser/client info
- `created_at`, `updated_at` - Timestamps (indexed)

### Documentation

#### `README.md`
- Complete package documentation
- Features overview
- Installation instructions
- Configuration guide
- Usage examples
- API documentation
- Frontend integration
- Artisan commands
- Security notes
- Performance tips

#### `INSTALLATION.md`
- Step-by-step installation guide
- Prerequisites
- Frontend setup (Blade, Vue 2, Vue 3)
- API setup
- Testing instructions
- Troubleshooting
- Production deployment

#### `USAGE.md`
- Practical usage examples
- Basic usage
- Model integration
- Controller examples
- Custom logging
- Retrieving logs
- Frontend integration
- Advanced usage
- Tips and best practices

#### `QUICKSTART.md`
- 5-minute quick start guide
- Essential commands
- Common use cases
- Basic examples
- Configuration highlights

#### `CHANGELOG.md`
- Version history
- Release notes
- Feature additions
- Bug fixes
- Breaking changes

## Key Features by Component

### Backend
- ✅ Service Provider with auto-discovery
- ✅ Eloquent model with relationships
- ✅ Trait for easy model integration
- ✅ Facade for convenient access
- ✅ Helper functions
- ✅ API controller with filtering
- ✅ Middleware for request logging
- ✅ Artisan commands
- ✅ Database migration
- ✅ Configuration file

### Frontend
- ✅ Blade view with Tailwind CSS
- ✅ Vue 2 component (Options API)
- ✅ Vue 3 component (Composition API)
- ✅ Vanilla JavaScript
- ✅ Responsive design
- ✅ Statistics dashboard
- ✅ Advanced filtering
- ✅ Pagination
- ✅ Search functionality
- ✅ Detail modal

### API
- ✅ RESTful endpoints
- ✅ Pagination support
- ✅ Advanced filtering
- ✅ Search functionality
- ✅ Statistics endpoint
- ✅ Cleanup endpoint
- ✅ Authentication support

### Documentation
- ✅ Comprehensive README
- ✅ Installation guide
- ✅ Usage examples
- ✅ Quick start guide
- ✅ Changelog
- ✅ License
- ✅ Structure documentation

## Package Dependencies

### PHP Dependencies (composer.json)
- `php`: ^8.0|^8.1|^8.2
- `illuminate/support`: ^9.0|^10.0|^11.0
- `illuminate/database`: ^9.0|^10.0|^11.0
- `illuminate/http`: ^9.0|^10.0|^11.0

### JavaScript Dependencies (package.json)
- `vue`: ^2.6.0 || ^3.0.0 (peer dependency)

### Frontend Assets
- Tailwind CSS (via CDN in Blade view)
- Font Awesome (via CDN for icons)

## Published Assets

When running `php artisan activity-log:install`, the following are published:

1. **Config** → `config/activity-log.php`
2. **Migrations** → `database/migrations/YYYY_MM_DD_HHMMSS_create_activity_logs_table.php`
3. **Views** → `resources/views/vendor/activity-log/`
4. **Components** → `resources/js/components/activity-log/`
5. **Assets** → `public/vendor/activity-log/`

## Routes

### Web Routes
- `GET /activity-logs` - Main dashboard

### API Routes
- `GET /api/activity-logs` - List logs
- `GET /api/activity-logs/{id}` - Show log
- `GET /api/activity-logs/types` - List types
- `GET /api/activity-logs/stats` - Statistics
- `DELETE /api/activity-logs/cleanup` - Cleanup

All routes are configurable via `config/activity-log.php`.

## Middleware

Default middleware configuration:
- **Web routes**: `['web', 'auth']`
- **API routes**: `['api', 'auth:sanctum']`

## Artisan Commands

- `activity-log:install` - Install package assets
- `activity-log:clean` - Clean old logs

## Testing

The package is designed to be testable with:
- PHPUnit for backend tests
- Jest/Vitest for frontend tests
- Laravel's testing utilities

## Version

Current version: **1.0.0**

## License

MIT License - See LICENSE.md

## Author

Al Mosabbir Rakib

---

This structure provides a complete, production-ready Laravel package for activity logging with multiple frontend options and comprehensive documentation.

