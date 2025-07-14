# Deployment Guide for Countdown Timer for WooCommerce

This guide explains how to deploy the plugin to WordPress.org using the Git-SVN workflow.

## Prerequisites

1. **WordPress.org Account**
   - Create an account at https://wordpress.org/support/register/
   - Request commit access for the plugin

2. **SVN Client**
   ```bash
   # macOS
   brew install svn

   # Ubuntu/Debian
   sudo apt-get install subversion

   # Windows
   # Download from https://tortoisesvn.net/
   ```

3. **GitHub Repository**
   - Repository: https://github.com/twoelevenjay/woocommerce-countdown-timer
   - Ensure you have push access

## Setup

1. **Configure SVN Credentials**
   ```bash
   # Store your WordPress.org credentials
   export SVN_USERNAME="your-wordpress-org-username"
   export SVN_PASSWORD="your-wordpress-org-password"
   ```

2. **Configure Git**
   ```bash
   # Set up git user (if not already done)
   git config user.name "Your Name"
   git config user.email "your-email@example.com"
   ```

## Deployment Process

### Automatic Deployment (Recommended)

1. **Update Version Number**
   - Edit `countdown-timer-for-woocommerce.php`
   - Update the `Version:` header
   - Update version in `readme.txt` (both header and changelog)

2. **Commit Changes**
   ```bash
   git add -A
   git commit -m "Bump version to X.X.X"
   git push origin master
   ```

3. **Run Deploy Script**
   ```bash
   ./deploy.sh
   ```

   The script will:
   - Create a git tag
   - Push to GitHub
   - Sync with WordPress.org SVN
   - Create SVN tag
   - Deploy to WordPress.org

### Manual Deployment

1. **Clone SVN Repository**
   ```bash
   svn co https://plugins.svn.wordpress.org/countdown-timer-for-woocommerce svn-repo
   ```

2. **Copy Files to Trunk**
   ```bash
   rsync -rc --exclude-from=".distignore" ./ svn-repo/trunk/ --delete --delete-excluded
   ```

3. **Copy Assets**
   ```bash
   cp assets/*.png svn-repo/assets/
   ```

4. **Add New Files**
   ```bash
   cd svn-repo
   svn status | grep "^?" | awk '{print $2}' | xargs svn add
   ```

5. **Create Tag**
   ```bash
   svn cp trunk tags/X.X.X
   ```

6. **Commit**
   ```bash
   svn ci -m "Version X.X.X"
   ```

## GitHub Actions Deployment

For automatic deployment on tag push:

1. **Add Secrets to GitHub**
   - Go to Settings > Secrets
   - Add `SVN_USERNAME`
   - Add `SVN_PASSWORD`

2. **Push Tag**
   ```bash
   git tag vX.X.X
   git push origin vX.X.X
   ```

## Version Numbering

Follow semantic versioning:
- **Major** (X.0.0): Breaking changes
- **Minor** (0.X.0): New features
- **Patch** (0.0.X): Bug fixes

## Pre-Deployment Checklist

- [ ] Update version in main plugin file
- [ ] Update version in readme.txt
- [ ] Update changelog in readme.txt
- [ ] Test on latest WordPress version
- [ ] Test on latest WooCommerce version
- [ ] Run security checks
- [ ] Verify all strings are translatable
- [ ] Check JavaScript console for errors
- [ ] Validate readme.txt format

## WordPress.org Assets

Place in `assets/` directory:
- `banner-772x250.png` - Plugin banner
- `banner-1544x500.png` - Retina banner (optional)
- `icon-128x128.png` - Plugin icon
- `icon-256x256.png` - Retina icon
- `screenshot-1.png` - First screenshot
- `screenshot-2.png` - Additional screenshots

## Troubleshooting

### SVN Authentication Failed
```bash
svn auth --remove
# Then try again with correct credentials
```

### Permission Denied
Ensure you have commit access on WordPress.org

### Tag Already Exists
Delete local tag:
```bash
git tag -d vX.X.X
git push origin :refs/tags/vX.X.X
```

## Support

- WordPress.org Forums: https://wordpress.org/support/plugin/countdown-timer-for-woocommerce/
- GitHub Issues: https://github.com/twoelevenjay/woocommerce-countdown-timer/issues