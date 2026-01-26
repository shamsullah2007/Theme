# Guest Shopping Guide - Aurora Storefront

## Overview
Your Aurora Storefront theme is fully configured to allow guest users (non-registered, non-logged-in users) to:
- Browse all products
- View product details
- Add products to cart
- View and modify cart
- Place orders
- Complete checkout without registration

## How It Works

### 1. **Product Browsing (Shop Page)**
- **URL:** `/shop/`
- **Access:** Available to all users (logged in or guest)
- **Features:**
  - Browse all products with consistent card layout
  - Filter by price (min/max)
  - Filter by minimum rating (2+, 3+, 4+)
  - Sort products (default, price, rating, etc.)
  - Responsive grid layout (auto-fit columns)

### 2. **Product Details (Single Product Page)**
- **Access:** Available to all users
- **Features:**
  - View full product images
  - Display product title, price, rating
  - Show product meta information (SKU, categories)
  - View detailed description
  - See related/upsell products
  - **Add to Cart button** (always visible)

### 3. **Add to Cart**
- **Availability:** Works for guests and logged-in users
- **Process:**
  1. Click "Add to Cart" button on product card or single product page
  2. Product is added to cart session (stored in browser)
  3. Cart count updates in header immediately
  4. User can continue shopping or go to cart

### 4. **Shopping Cart (View & Modify)**
- **URL:** `/cart/`
- **Access:** Available to all users
- **Features:**
  - View all items in cart
  - Adjust product quantities
  - Remove items from cart
  - See subtotal and shipping estimates
  - Update cart before checkout
  - **Proceed to Checkout button** always visible

### 5. **Checkout (Place Order)**
- **URL:** `/checkout/`
- **Access:** Available to all users (no login required)
- **Checkout Fields Shown:**
  - Billing address (required)
  - Shipping address (optional, same as billing by default)
  - Order notes
  - Payment method selection
  - Order review summary

### 6. **Guest Checkout Configuration**
The theme includes the following guest-friendly settings:

**File:** `theme/functions.php`
- Guest checkout enabled by default
- No account creation required during checkout
- Option fields removed for guests
- Billing fields displayed for all users

**WooCommerce Native Support:**
- Cart functionality works for guests (stored in cookies/sessions)
- Checkout accepts guest email for order notifications
- Order confirmation emails sent to guest email address
- Order history retrievable via email confirmation link

## Navigation

### Header Elements for Guests
When **NOT logged in**, guests see:
- **Login** button - Links to login page
- **Register** button - Links to registration page
- **Cart icon** - Always visible with item count
- **Search bar** - Category and keyword search
- **Product pages navigation** - Browse all available pages

### Header Elements When **Logged In**
- **Account** button - My account dashboard
- **Logout** button - Exit account
- **Add Product** button (if admin/seller) - Only for authorized users
- **Cart icon** - Always visible with item count

## Key Template Files

### Shop/Catalog
- `theme/woocommerce.php` - Shop page wrapper with filters
- `theme/template-parts/product-card.php` - Product card display (includes Add to Cart)
- `theme/assets/css/theme.css` - Grid layout styling for products

### Single Product
- `theme/woocommerce/single-product/content-single-product.php` - Product details layout
- `theme/woocommerce/single-product/product-image.php` - Product image display

### Cart & Checkout
- `theme/woocommerce/cart/cart.php` - Shopping cart page
- `theme/woocommerce/checkout/checkout.php` - Checkout form

## Features Verified

✅ **Shop accessible to guests**
✅ **Product details accessible to guests**
✅ **Add to Cart works for guests**
✅ **Cart page accessible to guests**
✅ **Checkout accessible to guests**
✅ **Guest checkout (no registration required)**
✅ **Product filtering available to guests**
✅ **Product search available to guests**
✅ **Cart count updates in header for guests**

## Testing Guest Shopping Flow

1. **As a guest user (without logging in):**
   - Navigate to `/shop/`
   - Filter products by price or rating
   - Click on a product to view details
   - Click "Add to Cart"
   - View cart at `/cart/`
   - Modify quantities or remove items
   - Click "Proceed to Checkout"
   - Fill in billing information
   - Select payment method
   - Place order

2. **Order confirmation:**
   - Guest receives confirmation email
   - Can view order status with email link
   - No account required

## Customization Options (if needed)

If you want to customize guest checkout:

1. **Allow account creation during checkout:**
   - Go to WooCommerce Settings → Accounts & Privacy
   - Enable "Allow customers to create an account during checkout"

2. **Require registration before checkout:**
   - Add permission check in `theme/functions.php`
   - Redirect to login page if guest attempts checkout

3. **Hide products from guests:**
   - Add post visibility restrictions in admin panel
   - Modify `theme/woocommerce.php` to check user permissions

## Summary

Your store is ready for guest shopping! Customers do not need to create an account or log in to:
- Browse your complete product catalog
- View detailed product information
- Add items to their cart
- Complete a purchase

This maximizes conversion by removing registration barriers while still allowing customers to create accounts if they choose to.
