# Location Radius Redirect (WordPress Plugin)

Checks a visitor’s **current GPS location** when they click **“Order Now”**.  
If the visitor is within the configured **radius (KM)** from the restaurant coordinates, they’re **redirected** to your contactless ordering URL.  
If they **deny location**, are **outside range**, or their browser **doesn’t support geolocation**, a **lightbox message** is shown.

---

## Features

- ✅ Admin settings page (Latitude, Longitude, Radius KM, Redirect URL, messages)
- ✅ Works with **menu items** and normal page buttons/links
- ✅ Uses browser **Geolocation API**
- ✅ Lightbox popup for errors / “outside delivery area”
- ✅ Removes theme-injected `dropdown-toggle` class from the Order Now link (prevents mobile/tablet split issues)
- ✅ No WooCommerce dependency

---

## Requirements

- WordPress **6.0+**
- PHP **7.4+**
- HTTPS website (browser geolocation generally requires HTTPS)

---

## Installation

### Option A — Upload plugin folder
1. Create a folder:
   `wp-content/plugins/location-radius-redirect/`
2. Put all plugin files inside that folder.
3. In WordPress Admin → **Plugins** → activate **Location Radius Redirect**.

### Option B — ZIP upload
1. Zip the folder `location-radius-redirect/`
2. WordPress Admin → **Plugins → Add New → Upload Plugin**
3. Upload the zip and activate.

---

## Configuration (Admin Settings)

Go to:

**WordPress Admin → Settings → Location Radius Redirect**

Set:

- **Restaurant Latitude** (example: `25.0185625`)
- **Restaurant Longitude** (example: `55.1401875`)
- **Allowed Radius (KM)** (example: `10`)
- **Redirect URL**  
  Example:
  `https://app.ipos247.com/contactless/#/0c078165d578c0ed60fca00d6c4de792b9f72f399b64bd11e6b8ef089c7d6a18`

Optional:

- **Bind Selector (CSS selector)** if you want to attach behavior to an existing menu/link
    - Example: `#menu-item-815 > a`
    - Example: `.custom-order-now-btn > a`
- Customize popup messages:
    - Location denied message
    - Outside radius message
    - Browser not supported message

Click **Save Changes**.

---

## Getting Latitude / Longitude

From Google Maps:
1. Open the restaurant location on Google Maps.
2. Right click the exact location → **What’s here?**
3. Copy the decimal coordinates.

In many Google Maps URLs, you can also find coordinates like:
- `!3d25.0185625!4d55.1401875` → **lat = 25.0185625, lng = 55.1401875**
- Or `@25.0177037,55.132718,...` → map center (less accurate than `!3d/!4d`)

---

## Usage

You have **two ways** to use the plugin.

### 1) Use Shortcode (recommended for pages)
Place this shortcode anywhere:

```text
[order_now_button]
