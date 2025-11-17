# Publishing to Packagist Guide

This guide will walk you through publishing the Laravel Activity Log package to Packagist.

## Prerequisites

Before publishing, ensure you have:

- ✅ A GitHub account
- ✅ A Packagist account (sign up at https://packagist.org)
- ✅ Git installed on your machine
- ✅ All package files are complete and tested

## Step 1: Prepare Your Repository

### 1.1 Initialize Git Repository (if not already done)

```bash
cd /Users/almosabbirrakib/Workspace/laravel-activity-log
git init
```

### 1.2 Create .gitignore

Ensure you have a proper `.gitignore` file in the root:

```gitignore
/vendor/
/node_modules/
.env
.env.backup
.phpunit.result.cache
Homestead.json
Homestead.yaml
npm-debug.log
yarn-error.log
.DS_Store
Thumbs.db
.idea/
.vscode/
*.log
composer.lock
package-lock.json
```

### 1.3 Stage and Commit Files

```bash
# Add all files
git add .

# Commit
git commit -m "Initial release v1.0.0"
```

## Step 2: Create GitHub Repository

### 2.1 Create Repository on GitHub

1. Go to https://github.com/new
2. Repository name: `laravel-activity-log`
3. Description: "A comprehensive Laravel package for logging user activities with support for Blade, Vue 2, and Vue 3 frontends"
4. Make it **Public** (required for free Packagist)
5. **Do NOT** initialize with README, .gitignore, or license (you already have these)
6. Click "Create repository"

### 2.2 Push to GitHub

```bash
# Add remote
git remote add origin https://github.com/almosabbirrakib/laravel-activity-log.git

# Push to main branch
git branch -M main
git push -u origin main
```

## Step 3: Create a Release Tag

Packagist uses Git tags for versioning.

```bash
# Create and push a tag for version 1.0.0
git tag -a v1.0.0 -m "Release version 1.0.0"
git push origin v1.0.0
```

## Step 4: Submit to Packagist

### 4.1 Sign Up / Log In to Packagist

1. Go to https://packagist.org
2. Sign up or log in (you can use GitHub OAuth)

### 4.2 Submit Your Package

1. Click "Submit" in the top navigation
2. Enter your GitHub repository URL:
   ```
   https://github.com/almosabbirrakib/laravel-activity-log
   ```
3. Click "Check"
4. If validation passes, click "Submit"

### 4.3 Verify Package Information

Packagist will read your `composer.json` and display:
- Package name: `almosabbirrakib/laravel-activity-log`
- Description
- Keywords
- License
- Authors

## Step 5: Set Up Auto-Update (Recommended)

### Option A: GitHub Service Hook (Recommended)

1. Go to your Packagist package page
2. Click your username → "My Packages"
3. Click on your package
4. Copy the API Token shown
5. Go to your GitHub repository settings
6. Navigate to "Webhooks" → "Add webhook"
7. Payload URL: `https://packagist.org/api/github?username=almosabbirrakib`
8. Content type: `application/json`
9. Secret: (leave empty)
10. Select "Just the push event"
11. Click "Add webhook"

### Option B: Manual Update

If you don't set up auto-update, you'll need to manually update on Packagist after each push:
1. Go to your package page on Packagist
2. Click "Update" button

## Step 6: Verify Installation

Test that your package can be installed:

```bash
# In a fresh Laravel project
composer require almosabbirrakib/laravel-activity-log
```

## Step 7: Add Badges to README

Your README already includes badges. Once published, they will automatically work:

```markdown
[![Total Downloads](https://img.shields.io/packagist/dt/almosabbirrakib/laravel-activity-log)](https://packagist.org/packages/almosabbirrakib/laravel-activity-log)
[![Latest Stable Version](https://img.shields.io/packagist/v/almosabbirrakib/laravel-activity-log)](https://packagist.org/packages/almosabbirrakib/laravel-activity-log)
[![License](https://img.shields.io/packagist/l/almosabbirrakib/laravel-activity-log)](https://packagist.org/packages/almosabbirrakib/laravel-activity-log)
```

## Releasing New Versions

### For Bug Fixes (Patch Version: 1.0.x)

```bash
# Make your changes
git add .
git commit -m "Fix: Description of bug fix"

# Create patch version tag
git tag -a v1.0.1 -m "Bug fixes"
git push origin main
git push origin v1.0.1
```

### For New Features (Minor Version: 1.x.0)

```bash
# Make your changes
git add .
git commit -m "Feature: Description of new feature"

# Create minor version tag
git tag -a v1.1.0 -m "New features"
git push origin main
git push origin v1.1.0
```

### For Breaking Changes (Major Version: x.0.0)

```bash
# Make your changes
git add .
git commit -m "Breaking: Description of breaking changes"

# Create major version tag
git tag -a v2.0.0 -m "Major release with breaking changes"
git push origin main
git push origin v2.0.0
```

### Update CHANGELOG.md

Always update your CHANGELOG.md before releasing:

```markdown
## [1.0.1] - 2024-11-18

### Fixed
- Fixed issue with date filtering
- Corrected pagination bug

### Changed
- Improved performance of query scopes
```

## Best Practices

### 1. Semantic Versioning

Follow [Semantic Versioning](https://semver.org/):
- **MAJOR** (1.0.0 → 2.0.0): Breaking changes
- **MINOR** (1.0.0 → 1.1.0): New features, backward compatible
- **PATCH** (1.0.0 → 1.0.1): Bug fixes, backward compatible

### 2. Keep CHANGELOG.md Updated

Document all changes in CHANGELOG.md following [Keep a Changelog](https://keepachangelog.com/) format.

### 3. Write Tests

Add tests before releasing:
```bash
composer test
```

### 4. Tag Releases Properly

Always create annotated tags with meaningful messages:
```bash
git tag -a v1.0.0 -m "Initial release with full feature set"
```

### 5. Update Documentation

Keep README.md and other documentation up to date with each release.

## Troubleshooting

### Package Not Found

**Problem:** `composer require` says package not found

**Solutions:**
1. Wait a few minutes for Packagist to index
2. Clear Composer cache: `composer clear-cache`
3. Verify package name matches exactly: `almosabbirrakib/laravel-activity-log`

### Auto-Update Not Working

**Problem:** New commits don't trigger Packagist update

**Solutions:**
1. Check GitHub webhook is active and has recent deliveries
2. Verify webhook URL is correct
3. Manually update on Packagist as fallback

### Composer.json Validation Errors

**Problem:** Packagist rejects your composer.json

**Solutions:**
1. Validate locally: `composer validate`
2. Check JSON syntax
3. Ensure all required fields are present

## Package Checklist

Before publishing, verify:

- [ ] `composer.json` is valid (`composer validate`)
- [ ] Package name follows format: `vendor/package`
- [ ] License is specified (MIT)
- [ ] README.md is comprehensive
- [ ] CHANGELOG.md is present
- [ ] LICENSE.md is present
- [ ] All files are committed
- [ ] Repository is pushed to GitHub
- [ ] Version tag is created and pushed
- [ ] Package is submitted to Packagist
- [ ] Auto-update webhook is configured
- [ ] Installation tested in fresh Laravel project

## Post-Publication

### 1. Announce Your Package

- Share on Twitter with #Laravel hashtag
- Post in Laravel News
- Share in Laravel communities (Reddit, Discord, etc.)
- Write a blog post about your package

### 2. Monitor Issues

- Watch your GitHub repository for issues
- Respond to user questions promptly
- Fix bugs and release patches

### 3. Gather Feedback

- Ask users for feedback
- Consider feature requests
- Improve documentation based on questions

## Support

If you encounter issues:

1. Check [Packagist Documentation](https://packagist.org/about)
2. Review [Composer Documentation](https://getcomposer.org/doc/)
3. Ask in Laravel communities
4. Open an issue on GitHub

## Quick Reference Commands

```bash
# Validate composer.json
composer validate

# Create and push tag
git tag -a v1.0.0 -m "Release message"
git push origin v1.0.0

# List all tags
git tag -l

# Delete a tag (if needed)
git tag -d v1.0.0
git push origin :refs/tags/v1.0.0

# Clear Composer cache
composer clear-cache

# Test installation
composer require almosabbirrakib/laravel-activity-log
```

## Resources

- [Packagist](https://packagist.org)
- [Composer Documentation](https://getcomposer.org/doc/)
- [Semantic Versioning](https://semver.org/)
- [Keep a Changelog](https://keepachangelog.com/)
- [Laravel Package Development](https://laravel.com/docs/packages)

---

**Congratulations!** Your package is now published and available to the Laravel community! 🎉

