# Licensing System Documentation

## Overview

WPSeed includes a complete licensing system for managing premium features, updates, and extensions. This system allows you to:

- Validate license keys
- Control access to premium features
- Deliver automatic updates
- Install free and premium extensions
- Support multiple license types

## Components

### 1. License Manager (`license-manager.php`)
Handles license validation, activation, and update checks.

### 2. Extension Installer (`extension-installer.php`)
Downloads and installs extensions from your server.

### 3. License Settings Page
User interface for managing licenses in WordPress admin.

## Setup

### Step 1: Configure License Server

In your plugin's main file or configuration:

```php
// Initialize license manager
$license_manager = new WPSeed_License_Manager(
    'https://your-license-server.com',  // License server URL
    'your-product-id',                   // Unique product ID
    '1.0.0'                              // Product version
);

// Initialize extension installer
$extension_installer = new WPSeed_Extension_Installer(
    'https://your-extension-server.com'  // Extension server URL
);
```

### Step 2: Set License Server URL

Define in your plugin constants:

```php
define( 'WPSEED_LICENSE_SERVER', 'https://your-server.com' );
define( 'WPSEED_PRODUCT_ID', 'wpseed-plugin' );
```

## License Types

The system supports four license types:

1. **Single Site** - Use on one website
2. **Multi-Site** - Use on up to 5 websites  
3. **Unlimited** - Use on unlimited websites
4. **Developer** - For agencies and developers

## Advanced Features

### Grace Period

Licenses include a 7-day grace period after expiration:
- Updates continue to work during grace period
- Admin notices warn about expiration
- After grace period, updates are blocked

### License Transfer

Transfer licenses between sites:
```php
$license_manager->transfer_license( 'https://new-site.com' );
```

### License Upgrade

Upgrade from one license type to another:
```php
$license_manager->upgrade_license( $new_license_key );
```

### Offline Activation

Development environments are automatically detected:
- localhost
- 127.0.0.1
- .local domains
- .test domains
- staging subdomains

These don't count toward site limits.

### Site Count Tracking

Monitor license usage:
```php
$data = $license_manager->get_license_data();
echo $data['sites_used'] . ' / ' . $data['sites_allowed'];
```

### Renewal Reminders

Automatic admin notices:
- 30 days before expiration
- During grace period
- After expiration

Dismissible for 7 days.

## API Endpoints

Your license server should implement these REST API endpoints:

### Activate License
```
POST /wp-json/wpseed-license/v1/activate
```

**Parameters:**
- `license_key` - The license key
- `site_url` - The site URL
- `product_id` - Product identifier

**Response:**
```json
{
    "success": true,
    "message": "License activated successfully",
    "license_type": "single",
    "expires": "2025-12-31"
}
```

### Deactivate License
```
POST /wp-json/wpseed-license/v1/deactivate
```

### Check License Status
```
POST /wp-json/wpseed-license/v1/check
```

**Response:**
```json
{
    "status": "active",
    "license_type": "unlimited",
    "expires": "2025-12-31",
    "sites_used": 3,
    "sites_allowed": 999
}
```

### Check for Updates
```
POST /wp-json/wpseed-license/v1/update_check
```

**Response:**
```json
{
    "new_version": "1.2.0",
    "download_url": "https://server.com/download/plugin.zip",
    "url": "https://your-site.com/changelog",
    "changelog": "Bug fixes and improvements"
}
```

### Get Plugin Info
```
POST /wp-json/wpseed-license/v1/plugin_info
```

### Get License Info
```
POST /wp-json/wpseed-license/v1/info
```

## Extension System

### List Extensions
```
GET /wp-json/wpseed-extensions/v1/list
```

**Response:**
```json
{
    "extensions": [
        {
            "slug": "premium-feature",
            "name": "Premium Feature",
            "description": "Advanced premium functionality",
            "version": "1.0.0",
            "price": 49.00,
            "type": "premium",
            "requires_license": true
        },
        {
            "slug": "free-addon",
            "name": "Free Addon",
            "description": "Free additional features",
            "version": "1.0.0",
            "price": 0,
            "type": "free",
            "requires_license": false
        }
    ]
}
```

### Download Extension
```
POST /wp-json/wpseed-extensions/v1/download
```

**Parameters:**
- `extension` - Extension slug
- `license_key` - License key (for premium)
- `site_url` - Site URL

**Response:**
```json
{
    "download_url": "https://server.com/extensions/premium-feature.zip"
}
```

## Usage Examples

### Check if License is Active

```php
$license_manager = new WPSeed_License_Manager(
    WPSEED_LICENSE_SERVER,
    WPSEED_PRODUCT_ID,
    WPSEED_VERSION
);

if ( $license_manager->is_license_active() ) {
    // Show premium features
}
```

### Get License Information

```php
$license_info = $license_manager->get_license_info();

if ( $license_info ) {
    echo 'License Type: ' . $license_info['license_type'];
    echo 'Expires: ' . $license_info['expires'];
}
```

### Install Extension

```php
$installer = new WPSeed_Extension_Installer( WPSEED_LICENSE_SERVER );

$result = $installer->install_extension( 'premium-feature', $license_key );

if ( is_wp_error( $result ) ) {
    echo $result->get_error_message();
} else {
    echo 'Extension installed successfully!';
}
```

### Check if Extension is Installed

```php
if ( $installer->is_extension_installed( 'premium-feature' ) ) {
    echo 'Extension is installed';
}

if ( $installer->is_extension_active( 'premium-feature' ) ) {
    echo 'Extension is active';
}
```

## Security

### Best Practices

1. **Always validate license keys** before granting access
2. **Use HTTPS** for all API communications
3. **Implement rate limiting** on license server
4. **Log activation attempts** for security monitoring
5. **Verify site URLs** to prevent license sharing

### Nonce Verification

All forms include nonce verification:

```php
check_admin_referer( 'wpseed_license_action' );
```

### Capability Checks

```php
if ( ! current_user_can( 'manage_options' ) ) {
    wp_die( 'Permission denied' );
}
```

## Customization

### Custom License Validation

```php
add_filter( 'wpseed_license_validation', function( $is_valid, $license_key ) {
    // Custom validation logic
    return $is_valid;
}, 10, 2 );
```

### Custom Update Check Interval

```php
add_filter( 'wpseed_update_check_interval', function( $interval ) {
    return DAY_IN_SECONDS * 7; // Check weekly
} );
```

### Modify Extension List

```php
add_filter( 'wpseed_extensions_list', function( $extensions ) {
    // Add or modify extensions
    return $extensions;
} );
```

## Troubleshooting

### License Won't Activate

1. Check license server URL is correct
2. Verify API endpoints are accessible
3. Check for SSL certificate issues
4. Review server error logs

### Updates Not Showing

1. Verify license is active
2. Check update check transient: `wpseed_license_check_{product_id}`
3. Clear transient to force check
4. Verify server returns correct version number

### Extension Installation Fails

1. Check file permissions
2. Verify download URL is accessible
3. Check for plugin conflicts
4. Review WordPress debug log

## Payment Integration

### PayPal Integration Example

```php
// After successful PayPal payment
$license_key = generate_license_key();

// Store in database
save_license( array(
    'key'          => $license_key,
    'email'        => $customer_email,
    'type'         => 'single',
    'status'       => 'active',
    'expires'      => date( 'Y-m-d', strtotime( '+1 year' ) ),
    'sites_allowed' => 1
) );

// Email license to customer
send_license_email( $customer_email, $license_key );
```

## Server-Side Implementation

See `docs/LICENSE-SERVER-SETUP.md` for complete server-side implementation guide.

## Support

For licensing system questions:
- Check server logs for API errors
- Verify license key format
- Test API endpoints manually
- Contact support with error messages
