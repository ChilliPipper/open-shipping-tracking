# Open Shipping Tracking

**Open Shipping Tracking** is a lightweight yet powerful WordPress plugin that enhances WooCommerce by adding a complete shipping tracking system to your orders. It allows you to add a carrier, tracking code, and tracking URL, then automatically notifies customers and displays the information in their account.

## Table of Contents

- [Features](#features)
- [Installation](#installation)
- [Usage](#usage)
- [Internationalization](#internationalization)
- [Frequently Asked Questions (FAQ)](#frequently-asked-questions-faq)
- [Contributing](#contributing)
- [Changelog](#changelog)
- [License](#license)
- [Contact](#contact)

## Features

- **Add Shipping Tracking Information:** Easily add shipping carrier, tracking code, and tracking URL to each WooCommerce order.
- **HPOS Compatible:** Fully compatible with WooCommerce's High-Performance Order Storage for improved speed and scalability.
- **Automated & Manual Email Notifications:** Sends a customizable WooCommerce email with tracking info when an order is completed, with an option to resend it manually at any time.
- **Customer Account Integration:** Displays the tracking information directly on the customer's "My Account" order details page for easy access.
- **Admin Order List Column:** Adds a "Shipping Tracking" column to the main order list for at-a-glance visibility.
- **Secure & Robust:** Built with security in mind, using nonces and proper data sanitization.
- **Translation Ready:** Fully prepared for internationalization with a loaded text domain.

## Installation

Follow these steps to install and activate the **Open Shipping Tracking** plugin:

1. **Download the Plugin:**
   - Clone the repository:
     ```bash
     git clone https://github.com/chillipipper/open-shipping-tracking.git
     ```
   - Or download the ZIP file from the [Releases](https://github.com/chillipipper/open-shipping-tracking/releases) page.

2. **Upload to WordPress:**
   - Log in to your WordPress admin dashboard.
   - Navigate to **Plugins > Add New**.
   - Click on **Upload Plugin**.
   - Choose the downloaded ZIP file and click **Install Now**.

3. **Activate the Plugin:**
   - After installation, click **Activate Plugin** to enable it.

## Usage

Once activated, **Open Shipping Tracking** integrates seamlessly into your WooCommerce workflow.

### Adding & Managing Tracking Information

1. **Navigate to an Order:** Go to **WooCommerce > Orders** and click on an order to edit it.
2. **Find the Tracking Meta Box:** Scroll down to the **Shipping Tracking Information** section on the order edit page.
3. **Enter Tracking Details:** Fill in the **Shipping Carrier**, **Tracking Code**, and **Tracking URL**.
4. **Save the Order:** Click **Update** to save the information.

### Viewing Tracking Information

- **Admin Order List:** A "Shipping Tracking" column will appear in the main orders list, showing the carrier and a clickable tracking code.
- **Customer's Account:** Customers can view the same tracking information by going to **My Account > Orders** and clicking "View" on a specific order.

### Sending Tracking Emails

- **Automatic:** The tracking email is sent automatically when an order's status is first changed to **Completed**.
- **Manual:** After tracking info is saved, a **"Resend Shipping Tracking Email"** button will appear in the meta box. Click this to send the email at any time.

### Customizing the Email

You can customize the email template by navigating to **WooCommerce > Settings > Emails** and finding the **Shipping Tracking** email. You can manage the subject, heading, and other settings from there.

## Internationalization

**Open Shipping Tracking** is fully translatable.

1. **Language Files:** The main `.pot` file is located in the `/languages` directory.
2. **Translation Tools:** Use a tool like [Poedit](https://poedit.net/) or the [Loco Translate](https://wordpress.org/plugins/loco-translate/) plugin to create your `.po` and `.mo` files.
3. **Upload Translations:** Place your translation files in the `/languages` directory of the plugin. WordPress will automatically use the correct file based on your site's language setting.

## Frequently Asked Questions (FAQ)

### 1. **Does this plugin work with all shipping carriers?**

Yes, the plugin allows you to input any shipping carrier name, tracking code, and tracking URL. It's flexible and not limited to specific carriers.

### 2. **Is this plugin compatible with High-Performance Order Storage (HPOS)?**

Yes, as of version 1.1.0, the plugin is fully compatible with HPOS.

### 3. **How can I contribute to the plugin?**

See the [Contributing](#contributing) section below for guidelines.

## Contributing

Contributions are welcome! Whether you're fixing bugs, improving documentation, or adding new features, your help is appreciated.

### Steps to Contribute

1. **Fork the Repository**
2. **Clone Your Fork**
3. **Create a New Branch**
4. **Make Your Changes**
5. **Commit and Push**
6. **Submit a Pull Request**

## Changelog

### [1.2.0] - 2025-08-04

- **Compatibility:** Fixed translation loading for WordPress 6.7+ compatibility (moved from `plugins_loaded` to `init` hook).
- **Compatibility:** Added HPOS-compatible column hooks for WooCommerce 9.0+ new orders screen.
- **Security:** Enhanced nonce validation with additional referrer checks.
- **Feat:** Added additional email trigger for direct order status changes to improve delivery reliability.
- **Feat:** Implemented comprehensive error handling and logging for email operations.
- **Feat:** Added success/error notifications for manual email sending with proper user feedback.
- **Update:** Updated version requirements - WooCommerce 6.0-9.5, PHP 7.4+.
- **Docs:** Added CLAUDE.md development documentation for future maintenance.

### [1.1.0] - 2025-06-28

- **Feat:** Added manual "Resend Shipping Tracking Email" button on the order edit page.
- **Feat:** Added a "Shipping Tracking" column to the admin orders list for quick viewing.
- **Feat:** Display tracking information on the customer's "My Account" order view page.
- **Refactor:** Replaced `wp_mail` with a dedicated, customizable WooCommerce email class (`WC_Email_Shipping_Tracking`).
- **Refactor:** Upgraded all data handling to use WooCommerce CRUD methods for High-Performance Order Storage (HPOS) compatibility.
- **Security:** Added nonces and capability checks to the data saving process.
- **Feat:** Created HTML and plain text email templates.

### [1.0.0] - 2024-05-22

- Initial stable release.
- Add shipping carrier, tracking code, and tracking URL fields to WooCommerce orders.
- Send tracking information via email when order status changes to completed.
- Implement internationalization support.
- Ensure data sanitization and escaping for security.

## License

This plugin is licensed under the [GNU General Public License v2.0](LICENSE).

## Contact

For support or inquiries, please contact:

- **Author:** Marco Revilla (@ChilliPipper)
- **Email:** [revillamarco@gmail.com](mailto:revillamarco@gmail.com)
---

*Thank you for using **Open Shipping Tracking**! If you find this plugin helpful, please consider giving it a star ⭐ on [GitHub](https://github.com/chillipipper/open-shipping-tracking).*

