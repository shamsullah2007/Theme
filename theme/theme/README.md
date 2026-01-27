# Aurora Storefront
A clean, responsive WooCommerce-ready WordPress theme inspired by modern marketplaces. Built for fast setup, mega navigation, product highlights, and customizable homepage sections—with beautiful animations and engaging UI effects.

## Product Management & Categories

### Category Management
Each product category can have its own custom color:
1. Go to WordPress Admin → Products → Categories
2. Edit or create a category
3. Find the "Category Color" field and select a color
4. Save the category
5. All products in that category will display titles in that color!

### Admin Product Form
When adding or editing products:
- **Categories field**: Multi-select dropdown showing all categories with color indicators
- Hold Ctrl (Cmd on Mac) to select multiple categories
- Selected categories appear with blue gradient background
- Category colors automatically display on product headings in the store

### Category Color Display
- Product titles in the shop show in their primary category's color
- Single product page title displays in the category color
- Category colors help customers quickly identify product types
- Colors are consistent across the entire store

## Features
- **Smooth Animations**: Fade-in, slide-in, and scale animations on page load and scroll
- **Interactive UI**: Hover effects, ripple buttons, hover cards with elevation
- **Responsive Design**: Mobile-first approach with breakpoints for all screen sizes
- **WooCommerce Ready**: Full template overrides for products, cart, checkout
- **Admin Pages**: User login/registration and admin product management
- **Performance Optimized**: Clean CSS and minimal JavaScript for fast load times

## Quick install
1. Zip the `theme` folder or copy it into `wp-content/themes/aurora-storefront`.
2. In WP Admin, activate the theme.
3. Install and activate WooCommerce. Create the shop, cart, checkout, and account pages via WooCommerce setup if not already present.
4. Assign menus to `Primary`, `Utility`, and `Footer` locations.
5. Add widgets to `Shop Filters` (e.g., Product Categories, Price Filter) and `Footer` as needed.
6. Visit Customizer → Aurora Homepage to set the deals title and featured category IDs.

## Homepage
- Hero section uses site title/description and header image fallback.
- Deals grid pulls latest 8 products.
- Featured categories use Customizer IDs.
- Trending slider shows popular products.

## Shop & search
- Custom `woocommerce.php` wraps shop pages with a filter column supporting price and rating query vars plus widget area.
- Search bar in header targets products with category dropdown.

## Assets
- Styles: `assets/css/theme.css`
- Scripts: `assets/js/theme.js`

## WooCommerce Templates
This theme includes full WooCommerce template overrides in the `woocommerce/` folder:
- **Single product**: Product image gallery with thumbnails, variations, pricing, add-to-cart, reviews, related products.
- **Cart**: Full cart table with quantity controls, remove links, and cart totals.
- **Checkout**: Billing/shipping form sections, order review, and payment.
- **Product loops**: Title, price, and button hooks for easy plugin integration.

## Plugin Compatibility
Tested with and optimized for dropshipping plugins:
- **AliDropship** – Syncs products, tracks supplier info; theme displays cost/margin fields via meta.
- **Oberlo** – Variant handling, product import, fulfilled orders.
- **WooCommerce native** – All core features (variations, reviews, ratings, stock).

## Admin & User Pages

### User Pages (Public Shortcodes)
Create new WordPress pages and add these shortcodes:

**Login Page**
- Add `[aurora_login_form]` to a page
- Displays login form with "Lost Password" and "Register" links
- Redirects to account page on successful login

**Registration Page**
- Add `[aurora_registration_form]` to a page
- New user registration with email verification
- Password validation (minimum 6 characters)
- Prevents duplicate usernames/emails

### Admin-Only Pages
**Product Management Dashboard**
- Add `[aurora_admin_product_manager]` to a page
- Restricted to users with "manage_woocommerce_products" or "manage_options" capability
- Features:
  - List all products with pagination
  - Add new products (name, SKU, price, stock, description)
  - Edit existing products
  - Delete products with confirmation
  - Quick search and sort by product properties

### Setup Instructions
1. Create 3 new pages in WordPress:
   - "Login" → Add `[aurora_login_form]`
   - "Register" → Add `[aurora_registration_form]`
   - "Manage Products" (for admins) → Add `[aurora_admin_product_manager]`
2. Add links to these pages in your primary or utility menu
3. Users cannot access admin pages without proper permissions
4. Forms include CSRF protection via nonces

## Customization
- Adjust colors/branding via CSS variables defined in `style.css` (`--aurora-primary`, `--aurora-accent`, etc.)
- Modify animation speeds in CSS files (look for `duration` values like `0.6s ease`)
- Add custom single product sections via the `do_action( 'woocommerce_single_product_summary' )` hook in `woocommerce/single-product/content-single-product.php`
- Modify cart/checkout via template overrides in `woocommerce/cart/` and `woocommerce/checkout/`
- Disable animations by removing `animation` properties from CSS (if performance is a concern on old devices)

## Animation Effects Included
- **Page Load**: Fade-in and slide-in animations for header, hero, products, and sections
- **Hover**: Lift effect on product cards, color transitions on links, scale on thumbnails
- **Interactions**: Button ripple effects, form focus animations, cart count pulse
- **Scroll**: Parallax effect on hero, reveal animations for products
- **Navigation**: Underline animation on nav links, dropdown smooth transitions

## Assets
- Styles: `assets/css/theme.css`, `assets/css/woocommerce.css`, `assets/css/admin-pages.css`
- Scripts: `assets/js/theme.js`, `assets/js/animations.js`

## Notes
- Add a `screenshot.png` at the theme root to show a preview in WP Admin.
- WooCommerce pages (shop, cart, checkout, account) are auto-detected and styled consistently.
- Theme supports Elementor core locations for homepage/template builders.
- All HTML generated by dropshipping plugins will inherit theme styles automatically.
- Animations use CSS keyframes and JavaScript Intersection Observer for smooth, performant effects.
- Mobile-optimized: Animations scale down on smaller devices for better performance.
