[![License](https://img.shields.io/badge/license-GPL--2.0%2B-red.svg)](https://github.com/21press/flareo/blob/master/license.txt)

# Flareo - Setup Documentation

## 📋 Table of Contents

1. [Overview](#overview)
2. [Installation](#installation)
7. [Developer Guide](#developer-guide)

---

## Overview


== Description ==

= Key Features =

= Requirements =
* WordPress 6.0 or newer.
* PHP 8.0 or newer.

== Installation ==

1. Upload the plugin files to the /wp-content/plugins/really-simple-featured-image directory or install it via Plugins -> Add New.
2. Activate Really Simple Featured Image through the Plugins screen.
3. Navigate to JetixWP -> Auto Featured Image to pick your default source and supported post types.
4. Save a post without a featured image to see the automation in action.

== Frequently Asked Questions ==

= Where can I get help? =
You can get help by sending us an email at support@jetixwp.com.

= Which post types are supported? =
Any post type that has thumbnail support can use automatic featured images. Enable or disable individual post types on the settings screen.

= Will the plugin overwrite an existing featured image? =
No. Really Simple Featured Image only assigns a featured image when the post does not already have one.

= Can I switch between images and video thumbnails? =
Yes. Set the default source to either "Image in Post Content" or "Video in Post Content" in the settings. Video thumbnails currently support YouTube, Vimeo, and Dailymotion.

= What happens with remote images? =
If the image does not already exist in your media library, the plugin downloads it and creates a local attachment before assigning it as the featured image.


### System Requirements

- WordPress 6.0 or higher
- PHP 8.0 or higher

---

## Installation

### Method 1: Via Our Site

1. Download the plugin zip file from [21Press.com >> Flareo](https://21press.com/plugins/flareo/#pricing)
2. Navigate to **Plugins > Add New**
3. Upload the `flareo.zip` downloaded zip via the Upload selector and hit Install.
4. Go to **Plugins** in WordPress admin and find "Flareo".
5. Once installed, click **Activate Plugin**
6. You'll see a new menu item: **Flareo**

### Method 2: Via WordPress Plugin Directory

1. Log in to your WordPress dashboard
2. Navigate to **Plugins > Add New**
3. Search for "Flareo"
4. Click **Install Now** button
5. Once installed, click **Activate Plugin**
6. You'll see a new menu item: **Flareo**

---

### Debug Mode

Enable debug mode for development and troubleshooting:

```php
define( 'P21_DEBUG', true );
```

**What Changes in Debug Mode:**
- Asset versioning uses file modification times (no caching)
- Stricter error reporting
- Additional logging information

**When to Use:**
- During development
- When troubleshooting issues
- When making changes to CSS/JS files

---

## Support

- **Documentation:** See this file and the plugin settings pages
- **Issues:** [GitHub Issues](https://github.com/21press/flareo/issues)
- **Email:** support@21press.com

---