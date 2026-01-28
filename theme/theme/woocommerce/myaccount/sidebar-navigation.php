<?php
/**
 * My Account Sidebar Navigation
 */

defined( 'ABSPATH' ) || exit;

// Get current user
$user = wp_get_current_user();
$current_url = isset( $_SERVER['REQUEST_URI'] ) ? esc_url_raw( $_SERVER['REQUEST_URI'] ) : '';

// Menu items
$menu_items = [
    'dashboard' => [
        'label' => __( 'Dashboard', 'aurora' ),
        'url' => wc_get_page_permalink( 'myaccount' ),
        'icon' => 'home',
    ],
    'orders' => [
        'label' => __( 'Orders', 'aurora' ),
        'url' => wc_get_page_permalink( 'myaccount' ) . 'orders/',
        'icon' => 'shopping-bag',
    ],
    'downloads' => [
        'label' => __( 'Downloads', 'aurora' ),
        'url' => wc_get_page_permalink( 'myaccount' ) . 'downloads/',
        'icon' => 'download',
    ],
    'addresses' => [
        'label' => __( 'Addresses', 'aurora' ),
        'url' => wc_get_page_permalink( 'myaccount' ) . 'edit-address/',
        'icon' => 'map-pin',
    ],
    'account-details' => [
        'label' => __( 'Account Details', 'aurora' ),
        'url' => wc_get_page_permalink( 'myaccount' ) . 'edit-account/',
        'icon' => 'user',
    ],
    'logout' => [
        'label' => __( 'Log out', 'aurora' ),
        'url' => wp_logout_url( wc_get_page_permalink( 'shop' ) ),
        'icon' => 'log-out',
    ],
];

?>

<style>
.aurora-myaccount-sidebar {
    background: linear-gradient(135deg, #0b57d0 0%, #0845a8 100%);
    border-radius: 16px;
    padding: 30px 0;
    box-shadow: 0 8px 24px rgba(11, 87, 208, 0.15);
    overflow: hidden;
}

.sidebar-header {
    padding: 0 25px 25px;
    border-bottom: 1px solid rgba(255, 255, 255, 0.15);
    margin-bottom: 20px;
}

.sidebar-user-info {
    display: flex;
    align-items: center;
    gap: 15px;
}

.sidebar-avatar {
    width: 56px;
    height: 56px;
    border-radius: 50%;
    object-fit: cover;
    border: 3px solid rgba(255, 255, 255, 0.3);
    transition: transform 0.3s ease;
}

.sidebar-avatar:hover {
    transform: scale(1.05);
}

.sidebar-user-details {
    flex: 1;
}

.sidebar-username {
    font-weight: 600;
    color: white;
    font-size: 15px;
    margin-bottom: 3px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.sidebar-user-email {
    font-size: 12px;
    color: rgba(255, 255, 255, 0.8);
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.sidebar-menu {
    list-style: none;
    margin: 0;
    padding: 0;
}

.sidebar-menu-item {
    margin: 0;
    padding: 0;
}

.sidebar-menu-link {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 14px 25px;
    color: rgba(255, 255, 255, 0.85);
    text-decoration: none;
    font-size: 15px;
    font-weight: 500;
    transition: all 0.3s ease;
    position: relative;
}

.sidebar-menu-link:hover {
    color: white;
    background: rgba(255, 255, 255, 0.1);
    padding-left: 30px;
}

.sidebar-menu-link.active {
    color: white;
    background: rgba(255, 255, 255, 0.15);
    border-left: 4px solid white;
    padding-left: 21px;
}

.sidebar-menu-icon {
    width: 20px;
    height: 20px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 16px;
}

/* Icon styles using unicode symbols */
.icon-home::before { content: '🏠'; }
.icon-shopping-bag::before { content: '🛍️'; }
.icon-download::before { content: '⬇️'; }
.icon-map-pin::before { content: '📍'; }
.icon-user::before { content: '👤'; }
.icon-log-out::before { content: '🚪'; }

@media (max-width: 768px) {
    .aurora-myaccount-sidebar {
        padding: 20px 0;
        margin-bottom: 30px;
    }

    .sidebar-header {
        padding: 0 20px 20px;
        margin-bottom: 15px;
    }

    .sidebar-menu-link {
        padding: 12px 20px;
    }

    .sidebar-menu-link:hover {
        padding-left: 25px;
    }

    .sidebar-menu-link.active {
        padding-left: 16px;
    }

    .sidebar-avatar {
        width: 48px;
        height: 48px;
    }

    .sidebar-username {
        font-size: 14px;
    }

    .sidebar-user-email {
        font-size: 11px;
    }
}
</style>

<aside class="aurora-myaccount-sidebar">
    <!-- Sidebar Header with User Info -->
    <div class="sidebar-header">
        <div class="sidebar-user-info">
            <img 
                src="<?php echo esc_url( aurora_get_user_profile_image( $user->ID ) ); ?>" 
                alt="<?php echo esc_attr( $user->display_name ); ?>"
                class="sidebar-avatar"
            >
            <div class="sidebar-user-details">
                <div class="sidebar-username"><?php echo esc_html( $user->display_name ); ?></div>
                <div class="sidebar-user-email"><?php echo esc_html( $user->user_email ); ?></div>
            </div>
        </div>
    </div>

    <!-- Sidebar Menu -->
    <nav class="sidebar-nav">
        <ul class="sidebar-menu">
            <?php foreach ( $menu_items as $key => $item ) : ?>
                <li class="sidebar-menu-item">
                    <a 
                        href="<?php echo esc_url( $item['url'] ); ?>"
                        class="sidebar-menu-link <?php echo strpos( $current_url, $key ) !== false || ( $key === 'dashboard' && strpos( $current_url, 'my-account' ) !== false && strpos( $current_url, 'orders' ) === false && strpos( $current_url, 'downloads' ) === false && strpos( $current_url, 'edit' ) === false ) ? 'active' : ''; ?>"
                    >
                        <span class="sidebar-menu-icon icon-<?php echo esc_attr( $item['icon'] ); ?>"></span>
                        <span><?php echo esc_html( $item['label'] ); ?></span>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    </nav>
</aside>
