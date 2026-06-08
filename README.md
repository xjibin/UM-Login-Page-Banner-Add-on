# UM Login Page Banner Add-on

Adds a configurable, clickable banner to the [Ultimate Member](https://wordpress.org/plugins/ultimate-member/) login page via the `[um_banner]` shortcode.

| | |
|---|---|
| **Contributors** | [xjibin](https://github.com/xjibin) |
| **Tags** | ultimate member, login, banner, shortcode |
| **Requires at least** | WordPress 5.6 |
| **Tested up to** | WordPress 6.8 |
| **Requires PHP** | 7.2 |
| **Stable tag** | 1.0.0 |
| **License** | [GPLv2 or later](https://www.gnu.org/licenses/gpl-2.0.html) |

## Description

UM Login Page Banner Add-on lets you show a promotional banner beside your Ultimate Member login form **without touching the login form or its authentication**.

From a simple admin page you set two things:

- **Banner Image URL** — the image to display.
- **Redirect URL** — where visitors go when they click the banner.

You then place the shortcode on your login page:

```text
[um_banner=1000]
```

The banner is output with the CSS class `quod-login-banner`, so any styling you already have for that class on the login page applies automatically. The plugin never alters the Ultimate Member form, its nonce, or its workflows.

## Installation

1. Upload the plugin ZIP via **Plugins → Add New → Upload Plugin**, then **Activate**.
2. Go to **UM Login Banner** in the admin menu.
3. Enter the **Banner Image URL** and the **Redirect URL**, then **Save Changes**.
4. Edit your login page and add the shortcode `[um_banner=1000]` where the banner should appear.

## Frequently Asked Questions

### Does this change the login form or break authentication?

No. The plugin only outputs a banner element via a shortcode. The Ultimate Member login form, nonce, redirect, and all workflows are untouched.

### What if I leave the Redirect URL empty?

The banner is rendered as a non-clickable image (still using the same CSS class).

## Changelog

### 1.0.0

- Initial release: admin settings (image URL + redirect URL) and the `[um_banner]` shortcode.

## License

This plugin is licensed under the GPL v2 or later.
See [https://www.gnu.org/licenses/gpl-2.0.html](https://www.gnu.org/licenses/gpl-2.0.html).