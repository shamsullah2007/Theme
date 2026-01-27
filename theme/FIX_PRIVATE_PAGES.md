# Fix Pages "Coming Soon" Issue - Action Guide

## Problem
All pages except the home page show "Coming Soon" or are blank for guests. This happens when pages are set to **Private** or **Draft** status in WordPress.

## Solution

The theme has been updated with automatic fixes, but you may need to manually update your pages in WordPress Admin.

### Automatic Fixes Applied
✅ Functions added to `theme/functions.php`:
- `aurora_fix_page_visibility_for_guests()` - Automatically publishes critical WooCommerce pages
- `aurora_filter_private_pages_from_menu()` - Hides private pages from guest navigation
- Enhanced `aurora_enhance_product_search()` - Allows guests to view published pages

### Manual Steps (if pages still show as private)

1. **Log into WordPress Admin** → `/wp-admin/`

2. **For each page that shows "Coming Soon":**
   - Go to **Pages** → All Pages
   - Find the page (e.g., "Shop", "Cart", "Checkout", "My Account", "Login", "Register", etc.)
   - Click **Edit**
   - Look for **Visibility** section (usually on the right side)
   - Change from **Private** to **Public**
   - Click **Update**

3. **Pages that MUST be Public:**
   - Shop
   - Cart
   - Checkout
   - My Account
   - Login / Register
   - Sample Page
   - ProductManager (or your admin page)
   - Any other pages you created

### How to Check Page Status

In **WordPress Admin → Pages → All Pages**, look for:
- **Private** badge = Not visible to guests
- **Draft** badge = Not published
- **Published** (no badge) = Visible to all

### Steps to Publish All Pages

1. Go to **Pages** → **All Pages**
2. Select all pages using the checkbox at the top
3. Under "Bulk Actions", select "Change Status to Published"
4. Click "Apply"
5. Refresh the frontend

### Verify Guest Access

1. **Log out** or use **Incognito/Private Browser**
2. Visit each page:
   - `/shop/` - Should show products
   - `/cart/` - Should show shopping cart interface
   - `/checkout/` - Should show checkout form
   - `/my-account/` - Should show login form (for guests)
   - Click on other pages from navigation

## If Pages Still Don't Show

Try these troubleshooting steps:

### Option 1: Clear Transients
Add this to `functions.php` temporarily and reload the site:
```php
// Delete the transient cache
delete_transient( 'aurora_page_visibility_fixed' );
```

### Option 2: Force Page Status Update
If the automatic function didn't work, add this to `functions.php` and access admin panel once:
```php
// Force publish all pages on next admin load
add_action( 'admin_init', function() {
    $pages = get_pages( array( 'post_status' => array( 'private', 'draft' ) ) );
    foreach ( $pages as $page ) {
        wp_update_post( array(
            'ID'          => $page->ID,
            'post_status' => 'publish',
        ) );
    }
} );
```

### Option 3: Direct Database Query
If you have database access via phpMyAdmin:
```sql
UPDATE wp_posts 
SET post_status = 'publish' 
WHERE post_type = 'page' AND post_status IN ('private', 'draft');
```

## What Should Guests See After Fix

### Navigation Menu
- ✅ Cart
- ✅ Checkout  
- ✅ Login
- ✅ My Account
- ✅ ProductManager (if you want)
- ✅ Sample Page (if published)
- ✅ Shop

### Header
- ✅ Search bar (functional)
- ✅ Cart icon (shows count)
- ✅ Login button (appears for guests)
- ✅ Register button (appears for guests)

### Pages Guests Can Access
- ✅ Home page with featured products
- ✅ Shop page with all products
- ✅ Individual product pages (click product)
- ✅ Add to cart (works for guests)
- ✅ Cart page (view/modify items)
- ✅ Checkout page (place order without account)

## Testing Checklist

- [ ] Log out or use incognito browser
- [ ] Home page loads with products
- [ ] Click Shop → see all products
- [ ] Click on a product → see details
- [ ] Click "Add to Cart" → product added
- [ ] Click cart icon → cart page shows
- [ ] Click "Proceed to Checkout" → checkout form appears
- [ ] Checkout form has billing fields (no login requirement)
- [ ] Can place order without creating account

## Still Having Issues?

Check WordPress logs for errors:
- `/wp-content/debug.log` (if debug logging enabled)
- Contact hosting provider if database access needed

## Need Help?

If pages still show as coming soon after these steps:
1. Verify pages exist in WordPress admin (Pages → All Pages)
2. Check page content isn't blank
3. Look for visibility/privacy plugins that might restrict access
4. Clear browser cache and WordPress cache
5. Check if theme has any additional access controls in custom code
