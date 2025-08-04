# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

This is a WordPress plugin called "Open Shipping Tracking" that extends WooCommerce with shipping tracking functionality. The plugin allows store administrators to add carrier information, tracking codes, and tracking URLs to orders, automatically notifying customers via customizable emails.

## Architecture

### Core Components

**Main Plugin File**: `open-shipping-tracking.php`
- Single-file plugin containing all functionality
- Uses WordPress hooks and WooCommerce's order system
- Implements HPOS (High-Performance Order Storage) compatibility

**Key Classes**:
- `WC_Email_Shipping_Tracking`: Custom WooCommerce email class that handles tracking notifications

**Email Templates**:
- `templates/emails/customer-shipping-tracking.php`: HTML email template
- `templates/emails/plain/customer-shipping-tracking.php`: Plain text email template

### Data Storage

The plugin stores three custom meta fields on WooCommerce orders:
- `_ost_shipping_carrier`: The shipping carrier name
- `_ost_tracking_code`: The tracking number/code
- `_ost_tracking_url`: The URL to track the shipment

All data is stored using WooCommerce CRUD methods for HPOS compatibility.

### Key Functionality

1. **Admin Interface**: Adds tracking fields to the order edit page with nonce security
2. **Email System**: Custom WooCommerce email that triggers when orders are marked as completed
3. **Customer Display**: Shows tracking info on customer "My Account" pages
4. **Admin List View**: Adds tracking column to the orders list for quick reference
5. **Manual Email Resend**: Button to manually trigger tracking emails

## Development Notes

### WordPress/WooCommerce Integration
- Uses proper WordPress hooks (`add_action`, `add_filter`)
- Follows WooCommerce email class structure
- Implements proper security with nonces and capability checks
- Uses WooCommerce CRUD methods for order data manipulation

### Internationalization
- Text domain: `open-shipping-tracking`
- Translation files in `/languages` directory
- All user-facing strings wrapped with `__()` or `esc_html_e()`

### Security Practices
- All user inputs sanitized with `sanitize_text_field()` and `esc_url_raw()`
- Output escaped with `esc_html()`, `esc_attr()`, and `esc_url()`
- Nonce verification for form submissions
- Capability checks for administrative actions

## Testing

This is a WordPress plugin - testing requires:
1. WordPress installation with WooCommerce active
2. Manual testing through WordPress admin interface
3. Test with different order statuses and email configurations

No automated test suite is present in the codebase.