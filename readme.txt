=== Timed featured products for WooCommerce ===
Contributors: marketingparadise
Tags: woocommerce, featured products, schedule, product management
Requires at least: 5.8
Tested up to: 6.9
Stable tag: 1.0.1
Requires PHP: 7.4
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Automatically remove the "featured" status from your WooCommerce products after a specific number of days.

== Description ==

WooCommerce natively allows you to highlight specific products by marking them as "Featured". But what happens when a promotion ends or a new product is no longer "new"? You have to remember to uncheck them manually.

**Timed featured products for WooCommerce** automates this process. This plugin allows you to tell WooCommerce exactly how many days a product should remain featured. Once that time passes, the product will automatically lose its featured status and return to being a normal product in your catalog.

**Main Features:**

* **Global Settings:** Set a default number of days for all your featured products to remain highlighted.
* **Product-Level Override:** Need a specific product to be featured for a longer or shorter time? You can easily override the global settings directly from the product's General tab.
* **Automated Expiration:** The plugin runs a lightweight daily check to automatically un-feature products that have reached their expiration limit.
* **Admin Column:** Keep track of your catalog easily with a new column in the WooCommerce product list showing the remaining featured days for each item.
* **Lightweight & Clean:** Developed strictly following WordPress and WooCommerce best practices (CRUD methods) to ensure zero impact on your store's frontend performance.

== Installation ==

**Automatic Installation (Recommended):**

1. From your WordPress dashboard, navigate to `Plugins > Add New`.
2. Search for "Timed featured products for WooCommerce".
3. Click "Install Now" and then "Activate".
4. Go to `WooCommerce > Timed featured products` to set your global default days.

**Manual Installation:**

1. Download the plugin's `.zip` file from the WordPress.org repository.
2. Unzip the file and upload the `timed-featured-products-for-woocommerce` folder to the `/wp-content/plugins/` directory of your WordPress installation.
3. Activate the plugin from the `Plugins` menu in your dashboard.
4. Configure your global settings in `WooCommerce > Timed featured products`.

== Frequently Asked Questions ==

= What happens when the featured time expires? =

The product is NOT deleted, hidden, or set to draft. It simply loses its "Featured" status (the star icon is unchecked) and returns to behaving like a standard product in your store's catalog.

= Can I set different expiration times for specific products? =

Yes! While the plugin has a global setting that acts as the default, you can go to the "General" tab of any individual product and set a specific number of days just for that item. This individual setting will override the global one.

= How does the plugin check for expired products? =

To ensure your store's frontend performance is never affected, the plugin does not check expiration dates on every page load. Instead, it uses a native WordPress background task (WP-Cron) that runs once a day at 3:00 AM (server time). 
*Note: In a future update, we plan to include an option so you can customize the exact time this daily task runs.*

= What happens if I leave the "Featured days" field empty on a product? =

The priority rules are simple: If you leave the field completely empty, the product will immediately stop being featured. If you type a number, it will be featured for that specific amount of days.

== Screenshots ==

1. The general settings page where you configure the default global featured days.
2. The General tab inside the product edit page, showing the individual override field.
3. The WooCommerce product list backend, displaying the new "Featured Days" column.

== Changelog ==

= 1.0.1 =
* New: Added a link to settings in plugin action links.

= 1.0.0 =
* Initial release of the plugin.
* Added global settings for default featured days.
* Added individual override field in the product data meta box.
* Implemented daily WP-Cron task at 3:00 AM to automatically remove featured status.
* Added "Featured Days" column to the admin product list.