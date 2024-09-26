# Open Shipping Tracking

**Open Shipping Tracking** is a WordPress plugin designed to seamlessly integrate with WooCommerce. It adds shipping carrier details, tracking codes, and tracking URLs to WooCommerce orders. Additionally, it automatically sends tracking information to customers via email when their order status changes to "Completed."

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
- **Automated Email Notifications:** Sends a customized email containing tracking information to customers when their order is marked as completed.
- **Admin Order Page Integration:** Provides a user-friendly interface on the WooCommerce admin order edit page to manage tracking details.
- **Internationalization Ready:** Fully translatable, allowing you to cater to a multilingual audience.
- **Secure Data Handling:** Implements proper sanitization and escaping to ensure data security.

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

Once activated, **Open Shipping Tracking** integrates with WooCommerce and provides the following functionalities:

### Adding Tracking Information

1. **Navigate to Orders:**
   - Go to **WooCommerce > Orders** in your WordPress admin dashboard.

2. **Edit an Order:**
   - Click on an order you wish to add tracking information to.

3. **Add Tracking Details:**
   - Scroll down to the **Shipping Tracking Information** section.
   - Enter the **Shipping Carrier**, **Tracking Code**, and **Tracking URL**.
   - Click **Update** to save the information.

### Automated Email Notifications

When an order's status changes to **Completed**, the plugin automatically sends an email to the customer containing the tracking information. Ensure that your WooCommerce email settings are correctly configured to utilize this feature.

## Internationalization

**Open Shipping Tracking** is fully translatable and ready for international use.

### Translating the Plugin

1. **Locate the Language Files:**
   - The language files are stored in the `/languages` directory within the plugin folder.

2. **Use a Translation Tool:**
   - You can use tools like [Poedit](https://poedit.net/) or plugins like [Loco Translate](https://wordpress.org/plugins/loco-translate/) to manage translations.

3. **Create a `.pot` File:**
   - Extract translatable strings and generate a `.pot` file using your preferred tool.

4. **Add Translations:**
   - Create `.po` and `.mo` files for your target language and place them in the `/languages` directory.

5. **Verify Translations:**
   - Switch your WordPress site language to ensure translations are correctly applied.

## Frequently Asked Questions (FAQ)

### 1. **Does this plugin work with all shipping carriers?**

Yes, the plugin allows you to input any shipping carrier name, tracking code, and tracking URL. It's flexible and not limited to specific carriers.

### 2. **Can I customize the email template?**

Currently, the email template is predefined. However, you can customize it by modifying the plugin's code or extending its functionality.

### 3. **Is the plugin compatible with the latest version of WooCommerce?**

I'll try to regularly test and maintain it to ensure compatibility with the latest WooCommerce versions. Please check the [Releases](https://github.com/chillipipper/open-shipping-tracking/releases) page for updates.

### 4. **How can I contribute to the plugin?**

See the [Contributing](#contributing) section below for guidelines.

## Contributing

Contributions are welcome! Whether you're fixing bugs, improving documentation, or adding new features, your help is appreciated since I don't have a lot of time.

### Steps to Contribute

1. **Fork the Repository:**
   - Click the **Fork** button at the top-right corner of this page.

2. **Clone Your Fork:**
   ```bash
   git clone https://github.com/your-username/open-shipping-tracking.git
   ```

3. **Create a New Branch:**
   ```bash
   git checkout -b feature/your-feature-name
   ```

4. **Make Your Changes:**
   - Implement your feature or bug fix.

5. **Commit Your Changes:**
   ```bash
   git commit -m "Add your commit message here"
   ```

6. **Push to Your Fork:**
   ```bash
   git push origin feature/your-feature-name
   ```

7. **Submit a Pull Request:**
   - Go to the original repository and create a pull request from your fork.

### Code of Conduct

Please ensure your contributions adhere to the [Code of Conduct](CODE_OF_CONDUCT.md).

## Changelog

### [0.1.0] - 2024-04-27

- Initial beta release.
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

