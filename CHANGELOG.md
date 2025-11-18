# Changelog

All notable changes to `laravel-activity-log` will be documented in this file.

## [1.0.0] - 2024-11-17

### Added
- Initial release
- Activity logging functionality with trait support
- Blade view component with professional UI
- Vue 2 component with full functionality
- Vue 3 component with Composition API
- API endpoints for fetching logs with pagination and filtering
- Database migration for activity_logs table
- Service provider with auto-discovery
- Configuration file with extensive options
- Helper functions for easy logging
- Artisan commands for installation and cleanup
- Middleware for automatic request logging
- Support for multiple log types (created, updated, deleted, login, logout, custom)
- Advanced filtering (search, type, date range, user, subject)
- Statistics dashboard
- Responsive design with Tailwind CSS
- IP address and user agent logging
- Properties/metadata support
- Automatic attribute exclusion for sensitive data
- Log retention and cleanup functionality
- Comprehensive documentation

### Features
- Easy integration with Laravel models via trait
- Multiple frontend options (Blade, Vue 2, Vue 3)
- RESTful API endpoints
- Configurable routes and middleware
- Automatic or manual logging
- Polymorphic relationships for flexible subject/causer tracking
- Query scopes for easy filtering
- Formatted date display
- Badge colors for different log types
- Modal detail view
- Pagination with customizable items per page
- Search functionality
- Date range filtering
- Type filtering
- User and subject filtering
- Statistics (total, today, this week, this month)
- Clean command for old logs
- Install command for easy setup

### Security
- Authentication middleware on all routes
- Sensitive attribute exclusion
- Configurable privacy settings
- Laravel Sanctum support for API

### Performance
- Optimized database queries
- Proper indexing on all searchable columns
- Eager loading for relationships
- Efficient pagination

## [Unreleased]

### Planned
- Export functionality (CSV, Excel, PDF)
- Email notifications for specific activities
- Activity log dashboard widget
- More detailed statistics and charts
- Activity log archiving
- Custom event listeners
- Webhook support
- Multi-language support
- Dark mode for UI
- Activity log comparison
- Bulk operations
- Advanced search with operators
- Custom log types configuration
- Activity log templates

