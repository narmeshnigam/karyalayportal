# Hostinger Deployment Guide

This guide explains how to deploy the Karyalay Portal to Hostinger hosting.

## Prerequisites

1. Hostinger hosting account with PHP 8.0+ support
2. MySQL database created on Hostinger
3. Git repository connected to Hostinger (for auto-deployment)

## Deployment Steps

### 1. Connect Git Repository

1. Log in to Hostinger hPanel
2. Go to **Git** section
3. Connect your repository
4. Set the deployment branch (usually `main` or `master`)

### 2. Configure Environment

Create a `.env` file in the root directory with your production settings:

```bash
# Application Settings
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com

# Production Database Credentials
DB_LIVE_HOST=localhost
DB_LIVE_PORT=3306
DB_LIVE_NAME=your_database_name
DB_LIVE_USER=your_database_user
DB_LIVE_PASS=your_database_password
DB_LIVE_UNIX_SOCKET=

# Local Database Credentials (for development)
DB_LOCAL_HOST=localhost
DB_LOCAL_PORT=3306
DB_LOCAL_NAME=karyalay_portal
DB_LOCAL_USER=root
DB_LOCAL_PASS=
DB_LOCAL_UNIX_SOCKET=

# Payment Gateway
RAZORPAY_KEY_ID=your_razorpay_key_id
RAZORPAY_KEY_SECRET=your_razorpay_key_secret
RAZORPAY_WEBHOOK_SECRET=your_razorpay_webhook_secret

# Email Configuration
SMTP_HOST=your_smtp_host
SMTP_PORT=587
SMTP_USERNAME=your_smtp_username
SMTP_PASSWORD=your_smtp_password
SMTP_ENCRYPTION=tls

# Admin Settings
ADMIN_EMAIL=admin@yourdomain.com
```

### 3. Set Document Root

In Hostinger hPanel:
1. Go to **Websites** > **Manage**
2. Go to **Advanced** > **PHP Configuration**
3. Ensure PHP version is 8.0 or higher

The `.htaccess` file in the root directory will handle routing automatically.

### 4. File Permissions

Ensure these directories are writable:
- `storage/` - For logs and cache
- `uploads/` - For user uploads
- `config/` - For installation lock file

```bash
chmod 755 storage/
chmod 755 uploads/
chmod 755 config/
```

### 5. Run Installation Wizard

1. Visit `https://yourdomain.com/install/`
2. Follow the installation wizard steps
3. Configure database, admin user, and SMTP settings

## URL Structure

The application uses the following URL structure:

| URL | Description |
|-----|-------------|
| `/` | Redirects to `/public/` |
| `/public/` | Public-facing pages (home, pricing, etc.) |
| `/admin/` | Admin panel |
| `/app/` | Customer portal |
| `/install/` | Installation wizard |

## Troubleshooting

### 500 Internal Server Error

1. Check PHP error logs in Hostinger hPanel
2. Ensure `.htaccess` files are properly uploaded
3. Verify PHP version is 8.0+
4. Check file permissions

### Database Connection Issues

1. Verify database credentials in `.env`
2. Ensure database exists and user has proper permissions
3. Check if `DB_LIVE_*` credentials are set for production

### Asset Loading Issues

1. Clear browser cache
2. Verify `assets/` directory is accessible
3. Check `.htaccess` rules are working

## Auto-Deployment with Git

When you push to the connected branch:
1. Hostinger automatically pulls the latest code
2. The `.htaccess` files handle routing
3. Environment detection uses `DB_LIVE_*` credentials on production

## Security Notes

1. Never commit `.env` file to Git
2. Use strong database passwords
3. Enable HTTPS on your domain
4. Keep PHP and dependencies updated
