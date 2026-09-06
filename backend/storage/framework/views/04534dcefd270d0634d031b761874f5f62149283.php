<style>
/* Premium Sidebar Overrides */
.aiz-sidebar {
    background: linear-gradient(180deg, #0e0e18 0%, #15152b 100%) !important;
    border-right: 1px solid rgba(255, 255, 255, 0.05) !important;
    box-shadow: 4px 0 30px rgba(0, 0, 0, 0.25) !important;
}

.aiz-side-nav-logo-wrap {
    background: rgba(14, 14, 24, 0.95) !important;
    backdrop-filter: blur(10px);
    border-bottom: 1px solid rgba(255, 255, 255, 0.05) !important;
    padding: 22px 24px !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
}

.aiz-side-nav-logo-wrap a {
    padding: 0 !important;
}

.aiz-side-nav-logo-wrap img {
    max-height: 38px !important;
    height: auto !important;
    transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.aiz-side-nav-logo-wrap img:hover {
    transform: scale(1.04);
}

/* User Profile Section */
.aiz-sidebar-profile {
    background: rgba(255, 255, 255, 0.03);
    border: 1px solid rgba(255, 255, 255, 0.06);
    transition: all 0.3s ease;
}
.aiz-sidebar-profile:hover {
    background: rgba(255, 255, 255, 0.05);
    border-color: rgba(247, 123, 11, 0.2);
}

/* Menu Search */
#menu-search {
    background: rgba(255, 255, 255, 0.04) !important;
    border: 1px solid rgba(255, 255, 255, 0.08) !important;
    border-radius: 12px !important;
    color: #ffffff !important;
    padding: 10px 16px !important;
    height: 42px !important;
    font-size: 13px !important;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
}

#menu-search::placeholder {
    color: #6a6c8e !important;
}

#menu-search:focus {
    background: rgba(255, 255, 255, 0.07) !important;
    border-color: var(--primary) !important;
    box-shadow: 0 0 0 3px rgba(247, 123, 11, 0.2) !important;
    outline: none !important;
}

/* Menu Wrapper list */
.aiz-side-nav-wrap {
    padding: 20px 14px !important;
}

.aiz-side-nav-item {
    margin-bottom: 5px;
}

.aiz-side-nav-list .aiz-side-nav-link {
    border-radius: 10px !important;
    padding: 11px 16px !important;
    font-weight: 500 !important;
    color: #9295b3 !important;
    transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1) !important;
    align-items: center !important;
    border: 1px solid transparent;
}

.aiz-side-nav-list .aiz-side-nav-link:hover {
    color: #ffffff !important;
    background: rgba(255, 255, 255, 0.04) !important;
    border-color: rgba(255, 255, 255, 0.03);
}

/* Active State */
.aiz-side-nav-list .aiz-side-nav-link.active,
.aiz-side-nav-list .aiz-side-nav-link.level-2-active,
.aiz-side-nav-list .aiz-side-nav-link.level-3-active {
    color: #ffffff !important;
    background: linear-gradient(135deg, var(--primary) 0%, #d45e00 100%) !important;
    box-shadow: 0 4px 15px rgba(247, 123, 11, 0.25) !important;
    border-color: transparent !important;
}

/* Icons */
.aiz-side-nav-list .aiz-side-nav-icon {
    font-size: 1.2rem !important;
    color: #6c7096 !important;
    margin-right: 12px !important;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    transition: all 0.25s ease !important;
}

[dir="rtl"] .aiz-side-nav-list .aiz-side-nav-icon {
    margin-right: 0px !important;
    margin-left: 12px !important;
}

.aiz-side-nav-list .aiz-side-nav-link:hover .aiz-side-nav-icon {
    color: #ffffff !important;
    transform: translateY(-1px) scale(1.08);
}

.aiz-side-nav-list .aiz-side-nav-link.active .aiz-side-nav-icon,
.aiz-side-nav-list .aiz-side-nav-link.level-2-active .aiz-side-nav-icon {
    color: #ffffff !important;
}

/* Submenu Level-2 & Level-3 styling */
.aiz-side-nav-list .level-2,
.aiz-side-nav-list .level-3 {
    background: rgba(0, 0, 0, 0.15) !important;
    border-radius: 10px !important;
    border: 1px solid rgba(255, 255, 255, 0.02) !important;
}

/* Apply margin and padding ONLY when expanded or collapsing */
.aiz-side-nav-list .level-2.mm-show,
.aiz-side-nav-list .level-2.mm-collapsing,
.aiz-side-nav-list .level-3.mm-show,
.aiz-side-nav-list .level-3.mm-collapsing {
    margin: 5px 8px !important;
    padding: 6px 0 !important;
}

/* Ensure completely collapsed elements have 0 height, no padding, margin, border, or overflow visibility */
.aiz-side-nav-list .level-2:not(.mm-show):not(.mm-collapsing),
.aiz-side-nav-list .level-3:not(.mm-show):not(.mm-collapsing) {
    margin: 0 !important;
    padding: 0 !important;
    border: none !important;
    height: 0 !important;
    overflow: hidden !important;
    visibility: hidden !important;
}

.aiz-side-nav-list .level-2 .aiz-side-nav-link {
    padding: 10px 16px 10px 42px !important;
    font-size: 0.82rem !important;
    color: #8c8ea7 !important;
    background: transparent !important;
    box-shadow: none !important;
}

[dir="rtl"] .aiz-side-nav-list .level-2 .aiz-side-nav-link {
    padding: 10px 42px 10px 16px !important;
}

.aiz-side-nav-list .level-2 .aiz-side-nav-link:hover {
    color: #ffffff !important;
    background: rgba(255, 255, 255, 0.03) !important;
}

.aiz-side-nav-list .level-2 .aiz-side-nav-link:after {
    position: absolute;
    content: "";
    width: 6px;
    height: 6px;
    border-radius: 50%;
    border: 1.5px solid #575979;
    background: transparent;
    left: 20px;
    top: calc(50% - 3px);
    transition: all 0.25s ease;
}

[dir="rtl"] .aiz-side-nav-list .level-2 .aiz-side-nav-link:after {
    left: auto;
    right: 20px;
}

.aiz-side-nav-list .level-2 .aiz-side-nav-link:hover:after,
.aiz-side-nav-list .level-2 .aiz-side-nav-link.active:after {
    background: var(--primary) !important;
    border-color: var(--primary) !important;
    box-shadow: 0 0 8px var(--primary);
}

/* Scrollbar Customization (Firefox & Webkit) */
.c-scrollbar {
    scrollbar-width: thin !important;
    scrollbar-color: rgba(255, 255, 255, 0.12) transparent !important;
}
.c-scrollbar::-webkit-scrollbar {
    width: 6px !important;
    height: 6px !important;
}
.c-scrollbar::-webkit-scrollbar-track {
    background: transparent !important;
}
.c-scrollbar::-webkit-scrollbar-thumb {
    background: rgba(255, 255, 255, 0.12) !important;
    border-radius: 10px !important;
}
.c-scrollbar::-webkit-scrollbar-thumb:hover {
    background: rgba(255, 255, 255, 0.20) !important;
}
</style>

<div class="aiz-sidebar-wrap">
    <div class="aiz-sidebar left c-scrollbar">
        <div class="aiz-side-nav-logo-wrap">
            <a href="<?php echo e(route('admin.dashboard')); ?>" class="d-block text-left">
                <?php if(get_setting('system_logo_white') != null): ?>
                    <img class="mw-100" src="<?php echo e(uploaded_asset(get_setting('system_logo_white'))); ?>" class="brand-icon" alt="<?php echo e(get_setting('site_name')); ?>">
                <?php else: ?>
                    <img class="mw-100" src="<?php echo e(static_asset('assets/img/logo.png')); ?>" class="brand-icon" alt="<?php echo e(get_setting('site_name')); ?>">
                <?php endif; ?>
            </a>
        </div>

        <!-- Admin Profile Widget -->
        <div class="aiz-sidebar-profile px-3 py-3 mb-3 mx-3 rounded-xl d-flex align-items-center" style="border-radius: 12px; margin-top: 15px; gap: 12px;">
            <div class="position-relative shrink-0">
                <div class="avatar avatar-sm bg-soft-primary text-primary rounded-circle d-flex align-items-center justify-content-center font-weight-bold" style="width: 38px; height: 38px; font-size: 15px; background: rgba(247, 123, 11, 0.15); color: var(--primary);">
                    <?php if(Auth::user()->avatar_original != null): ?>
                        <img src="<?php echo e(uploaded_asset(Auth::user()->avatar_original)); ?>" onerror="this.onerror=null;this.src='<?php echo e(static_asset('assets/img/avatar-place.png')); ?>';" class="rounded-circle" style="width: 38px; height: 38px; object-fit: cover;">
                    <?php else: ?>
                        <?php echo e(strtoupper(substr(Auth::user()->name, 0, 1))); ?>

                    <?php endif; ?>
                </div>
                <span class="position-absolute bottom-0 right-0 bg-success border border-white rounded-circle" style="width: 10px; height: 10px; border-width: 2px !important; bottom: 2px; right: 2px;"></span>
            </div>
            <div class="overflow-hidden flex-grow-1" style="min-width: 0;">
                <h6 class="text-white mb-0 text-truncate font-weight-bold" style="font-size: 13px; line-height: 1.2;"><?php echo e(Auth::user()->name); ?></h6>
                <span class="text-muted d-block text-truncate text-uppercase font-weight-bold mt-1" style="font-size: 9px; letter-spacing: 0.8px; opacity: 0.6; color: #a2a3b7 !important;"><?php echo e(Auth::user()->user_type); ?></span>
            </div>
        </div>

        <div class="aiz-side-nav-wrap">
            <div class="px-20px mb-3">
                <input class="form-control bg-soft-secondary border-0 form-control-sm text-white" type="text" name="" placeholder="<?php echo e(translate('Search in menu')); ?>" id="menu-search" onkeyup="menuSearch()">
            </div>
            <ul class="aiz-side-nav-list" id="search-menu">
            </ul>
            <ul class="aiz-side-nav-list" id="main-menu" data-toggle="aiz-side-menu">
                <li class="aiz-side-nav-item">
                    <a href="<?php echo e(route('admin.dashboard')); ?>" class="aiz-side-nav-link">
                        <i class="las la-home aiz-side-nav-icon"></i>
                        <span class="aiz-side-nav-text"><?php echo e(translate('Dashboard')); ?></span>
                    </a>
                </li>

                <!-- POS Addon-->
                <?php if(addon_is_activated('pos_system')): ?>
                    <?php if(Auth::user()->user_type == 'admin' || in_array('1', json_decode(Auth::user()->staff->role->permissions))): ?>
                        <li class="aiz-side-nav-item">
                            <a href="#" class="aiz-side-nav-link">
                                <i class="las la-tasks aiz-side-nav-icon"></i>
                                <span class="aiz-side-nav-text"><?php echo e(translate('POS System')); ?></span>
                                <?php if(env("DEMO_MODE") == "On"): ?>
                                    <span class="badge badge-inline badge-danger">Addon</span>
                                <?php endif; ?>
                                <span class="aiz-side-nav-arrow"></span>
                            </a>
                            <ul class="aiz-side-nav-list level-2">
                                <li class="aiz-side-nav-item">
                                    <a href="<?php echo e(route('poin-of-sales.index')); ?>" class="aiz-side-nav-link <?php echo e(areActiveRoutes(['poin-of-sales.index', 'poin-of-sales.create'])); ?>">
                                        <span class="aiz-side-nav-text"><?php echo e(translate('POS Manager')); ?></span>
                                    </a>
                                </li>
                                <li class="aiz-side-nav-item">
                                    <a href="<?php echo e(route('poin-of-sales.activation')); ?>" class="aiz-side-nav-link">
                                        <span class="aiz-side-nav-text"><?php echo e(translate('POS Configuration')); ?></span>
                                    </a>
                                </li>
                            </ul>
                        </li>
                    <?php endif; ?>
                <?php endif; ?>

            <!-- Product -->
                <?php if(Auth::user()->user_type == 'admin' || in_array('2', json_decode(Auth::user()->staff->role->permissions))): ?>
                    <li class="aiz-side-nav-item">
                        <a href="#" class="aiz-side-nav-link">
                            <i class="las la-shopping-cart aiz-side-nav-icon"></i>
                            <span class="aiz-side-nav-text"><?php echo e(translate('Products')); ?></span>
                            <span class="aiz-side-nav-arrow"></span>
                        </a>
                        <!--Submenu-->
                        <ul class="aiz-side-nav-list level-2">
                            <li class="aiz-side-nav-item">
                                <a class="aiz-side-nav-link" href="<?php echo e(route('products.create')); ?>">
                                    <span class="aiz-side-nav-text"><?php echo e(translate('Add New product')); ?></span>
                                </a>
                            </li>
                            <li class="aiz-side-nav-item">
                                <a href="<?php echo e(route('products.all')); ?>" class="aiz-side-nav-link">
                                    <span class="aiz-side-nav-text"><?php echo e(translate('All Products')); ?></span>
                                </a>
                            </li>
                            <li class="aiz-side-nav-item">
                                <a href="<?php echo e(route('products.admin')); ?>" class="aiz-side-nav-link <?php echo e(areActiveRoutes(['products.admin', 'products.create', 'products.admin.edit'])); ?>" >
                                    <span class="aiz-side-nav-text"><?php echo e(translate('In House Products')); ?></span>
                                </a>
                            </li>
                            <?php if(get_setting('vendor_system_activation') == 1): ?>
                                <li class="aiz-side-nav-item">
                                    <a href="<?php echo e(route('products.seller')); ?>" class="aiz-side-nav-link <?php echo e(areActiveRoutes(['products.seller', 'products.seller.edit'])); ?>">
                                        <span class="aiz-side-nav-text"><?php echo e(translate('Seller Products')); ?></span>
                                    </a>
                                </li>
                            <?php endif; ?>
                            <li class="aiz-side-nav-item">
                                <a href="<?php echo e(route('digitalproducts.index')); ?>" class="aiz-side-nav-link <?php echo e(areActiveRoutes(['digitalproducts.index', 'digitalproducts.create', 'digitalproducts.edit'])); ?>">
                                    <span class="aiz-side-nav-text"><?php echo e(translate('Digital Products')); ?></span>
                                </a>
                            </li>
                            <li class="aiz-side-nav-item">
                                <a href="<?php echo e(route('product_bulk_upload.index')); ?>" class="aiz-side-nav-link" >
                                    <span class="aiz-side-nav-text"><?php echo e(translate('Bulk Import')); ?></span>
                                </a>
                            </li>
                            <li class="aiz-side-nav-item">
                                <a href="<?php echo e(route('product_bulk_export.index')); ?>" class="aiz-side-nav-link">
                                    <span class="aiz-side-nav-text"><?php echo e(translate('Bulk Export')); ?></span>
                                </a>
                            </li>
                            <li class="aiz-side-nav-item">
                                <a href="<?php echo e(route('categories.index')); ?>" class="aiz-side-nav-link <?php echo e(areActiveRoutes(['categories.index', 'categories.create', 'categories.edit'])); ?>">
                                    <span class="aiz-side-nav-text"><?php echo e(translate('Category')); ?></span>
                                </a>
                            </li>
                            <li class="aiz-side-nav-item">
                                <a href="<?php echo e(route('brands.index')); ?>" class="aiz-side-nav-link <?php echo e(areActiveRoutes(['brands.index', 'brands.create', 'brands.edit'])); ?>" >
                                    <span class="aiz-side-nav-text"><?php echo e(translate('Brand')); ?></span>
                                </a>
                            </li>
                            <li class="aiz-side-nav-item">
                                <a href="<?php echo e(route('attributes.index')); ?>" class="aiz-side-nav-link <?php echo e(areActiveRoutes(['attributes.index','attributes.create','attributes.edit'])); ?>">
                                    <span class="aiz-side-nav-text"><?php echo e(translate('Attribute')); ?></span>
                                </a>
                            </li>
                            <li class="aiz-side-nav-item">
                                <a href="<?php echo e(route('colors')); ?>" class="aiz-side-nav-link <?php echo e(areActiveRoutes(['attributes.index','attributes.create','attributes.edit'])); ?>">
                                    <span class="aiz-side-nav-text"><?php echo e(translate('Colors')); ?></span>
                                </a>
                            </li>
                            <li class="aiz-side-nav-item">
                                <a href="<?php echo e(route('reviews.index')); ?>" class="aiz-side-nav-link">
                                    <span class="aiz-side-nav-text"><?php echo e(translate('Product Reviews')); ?></span>
                                </a>
                            </li>
                        </ul>
                    </li>
                <?php endif; ?>

                <!-- Auction Product -->
                <?php if(addon_is_activated('auction')): ?>
                    <li class="aiz-side-nav-item">
                        <a href="#" class="aiz-side-nav-link">
                            <i class="las la-gavel aiz-side-nav-icon"></i>
                            <span class="aiz-side-nav-text"><?php echo e(translate('Auction Products')); ?></span>
                            <?php if(env("DEMO_MODE") == "On"): ?>
                                <span class="badge badge-inline badge-danger">Addon</span>
                            <?php endif; ?>
                            <span class="aiz-side-nav-arrow"></span>
                        </a>
                        <!--Submenu-->
                        <ul class="aiz-side-nav-list level-2">
                            <li class="aiz-side-nav-item">
                                <a class="aiz-side-nav-link" href="<?php echo e(route('auction_products.create')); ?>">
                                    <span class="aiz-side-nav-text"><?php echo e(translate('Add New auction product')); ?></span>
                                </a>
                            </li>
                            <li class="aiz-side-nav-item">
                                <a href="<?php echo e(route('auction.all_products')); ?>" class="aiz-side-nav-link <?php echo e(areActiveRoutes(['auction_products.edit','product_bids.show'])); ?>">
                                    <span class="aiz-side-nav-text"><?php echo e(translate('All Auction Products')); ?></span>
                                </a>
                            </li>
                            <li class="aiz-side-nav-item">
                                <a href="<?php echo e(route('auction.inhouse_products')); ?>" class="aiz-side-nav-link">
                                    <span class="aiz-side-nav-text"><?php echo e(translate('Inhouse Auction Products')); ?></span>
                                </a>
                            </li>
                            <li class="aiz-side-nav-item">
                                <a href="<?php echo e(route('auction.seller_products')); ?>" class="aiz-side-nav-link">
                                    <span class="aiz-side-nav-text"><?php echo e(translate('Seller Auction Products')); ?></span>
                                </a>
                            </li>
                            <li class="aiz-side-nav-item">
                                <a href="<?php echo e(route('auction_products_orders')); ?>" class="aiz-side-nav-link <?php echo e(areActiveRoutes(['auction_products_orders.index'])); ?>">
                                    <span class="aiz-side-nav-text"><?php echo e(translate('Auction Products Orders')); ?></span>
                                </a>
                            </li>
                        </ul>
                    </li>
                <?php endif; ?>

                <!-- Wholesale Product -->
                <?php if(addon_is_activated('wholesale')): ?>
                    <li class="aiz-side-nav-item">
                        <a href="#" class="aiz-side-nav-link">
                            <i class="las la-luggage-cart aiz-side-nav-icon"></i>
                            <span class="aiz-side-nav-text"><?php echo e(translate('Wholesale Products')); ?></span>
                            <?php if(env("DEMO_MODE") == "On"): ?>
                                <span class="badge badge-inline badge-danger">Addon</span>
                            <?php endif; ?>
                            <span class="aiz-side-nav-arrow"></span>
                        </a>
                        <!--Submenu-->
                        <ul class="aiz-side-nav-list level-2">
                            <li class="aiz-side-nav-item">
                                <a class="aiz-side-nav-link" href="<?php echo e(route('wholesale-products.create')); ?>">
                                    <span class="aiz-side-nav-text"><?php echo e(translate('Add new wholesale product')); ?></span>
                                </a>
                            </li>
                            <li class="aiz-side-nav-item">
                                <a href="<?php echo e(route('wholesale-products.index')); ?>" class="aiz-side-nav-link <?php echo e(areActiveRoutes(['wholesale-products.edit','wholesale-products.show'])); ?>">
                                    <span class="aiz-side-nav-text"><?php echo e(translate('All wholesale products')); ?></span>
                                </a>
                            </li>
                        </ul>
                    </li>
                <?php endif; ?>

                <!-- Sale -->
                <li class="aiz-side-nav-item">
                    <a href="#" class="aiz-side-nav-link">
                        <i class="las la-money-bill aiz-side-nav-icon"></i>
                        <span class="aiz-side-nav-text"><?php echo e(translate('Sales')); ?></span>
                        <span class="aiz-side-nav-arrow"></span>
                    </a>
                    <!--Submenu-->
                    <ul class="aiz-side-nav-list level-2">
                        <?php if(Auth::user()->user_type == 'admin' || in_array('3', json_decode(Auth::user()->staff->role->permissions))): ?>
                            <li class="aiz-side-nav-item">
                                <a href="<?php echo e(route('all_orders.index')); ?>" class="aiz-side-nav-link <?php echo e(areActiveRoutes(['all_orders.index', 'all_orders.show'])); ?>">
                                    <span class="aiz-side-nav-text"><?php echo e(translate('All Orders')); ?></span>
                                </a>
                            </li>
                        <?php endif; ?>

                        <?php if(Auth::user()->user_type == 'admin' || in_array('4', json_decode(Auth::user()->staff->role->permissions))): ?>
                            <li class="aiz-side-nav-item">
                                <a href="<?php echo e(route('inhouse_orders.index')); ?>" class="aiz-side-nav-link <?php echo e(areActiveRoutes(['inhouse_orders.index', 'inhouse_orders.show'])); ?>" >
                                    <span class="aiz-side-nav-text"><?php echo e(translate('Inhouse orders')); ?></span>
                                </a>
                            </li>
                        <?php endif; ?>
                        <?php if(Auth::user()->user_type == 'admin' || in_array('5', json_decode(Auth::user()->staff->role->permissions))): ?>
                            <li class="aiz-side-nav-item">
                                <a href="<?php echo e(route('seller_orders.index')); ?>" class="aiz-side-nav-link <?php echo e(areActiveRoutes(['seller_orders.index', 'seller_orders.show'])); ?>">
                                    <span class="aiz-side-nav-text"><?php echo e(translate('Seller Orders')); ?></span>
                                </a>
                            </li>
                        <?php endif; ?>
                        <?php if(Auth::user()->user_type == 'admin' || in_array('6', json_decode(Auth::user()->staff->role->permissions))): ?>
                            <li class="aiz-side-nav-item">
                                <a href="<?php echo e(route('pick_up_point.order_index')); ?>" class="aiz-side-nav-link <?php echo e(areActiveRoutes(['pick_up_point.order_index','pick_up_point.order_show'])); ?>">
                                    <span class="aiz-side-nav-text"><?php echo e(translate('Pick-up Point Order')); ?></span>
                                </a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </li>

                <!-- Deliver Boy Addon-->
                <?php if(addon_is_activated('delivery_boy')): ?>
                    <?php if(Auth::user()->user_type == 'admin' || in_array('1', json_decode(Auth::user()->staff->role->permissions))): ?>
                        <li class="aiz-side-nav-item">
                            <a href="#" class="aiz-side-nav-link">
                                <i class="las la-truck aiz-side-nav-icon"></i>
                                <span class="aiz-side-nav-text"><?php echo e(translate('Delivery Boy')); ?></span>
                                <?php if(env("DEMO_MODE") == "On"): ?>
                                    <span class="badge badge-inline badge-danger">Addon</span>
                                <?php endif; ?>
                                <span class="aiz-side-nav-arrow"></span>
                            </a>
                            <ul class="aiz-side-nav-list level-2">
                                <li class="aiz-side-nav-item">
                                    <a href="<?php echo e(route('delivery-boys.index')); ?>" class="aiz-side-nav-link">
                                        <span class="aiz-side-nav-text"><?php echo e(translate('All Delivery Boy')); ?></span>
                                    </a>
                                </li>
                                <li class="aiz-side-nav-item">
                                    <a href="<?php echo e(route('delivery-boys.create')); ?>" class="aiz-side-nav-link">
                                        <span class="aiz-side-nav-text"><?php echo e(translate('Add Delivery Boy')); ?></span>
                                    </a>
                                </li>
                                <li class="aiz-side-nav-item">
                                    <a href="<?php echo e(route('delivery-boys-payment-histories')); ?>" class="aiz-side-nav-link">
                                        <span class="aiz-side-nav-text"><?php echo e(translate('Payment Histories')); ?></span>
                                    </a>
                                </li>
                                <li class="aiz-side-nav-item">
                                    <a href="<?php echo e(route('delivery-boys-collection-histories')); ?>" class="aiz-side-nav-link">
                                        <span class="aiz-side-nav-text"><?php echo e(translate('Collected Histories')); ?></span>
                                    </a>
                                </li>
                                <li class="aiz-side-nav-item">
                                    <a href="<?php echo e(route('delivery-boy.cancel-request')); ?>" class="aiz-side-nav-link">
                                        <span class="aiz-side-nav-text"><?php echo e(translate('Cancel Request')); ?></span>
                                    </a>
                                </li>
                                <li class="aiz-side-nav-item">
                                    <a href="<?php echo e(route('delivery-boy-configuration')); ?>" class="aiz-side-nav-link">
                                        <span class="aiz-side-nav-text"><?php echo e(translate('Configuration')); ?></span>
                                    </a>
                                </li>
                            </ul>
                        </li>
                    <?php endif; ?>
                <?php endif; ?>

            <!-- Refund addon -->
                <?php if(addon_is_activated('refund_request')): ?>
                    <?php if(Auth::user()->user_type == 'admin' || in_array('7', json_decode(Auth::user()->staff->role->permissions))): ?>
                        <li class="aiz-side-nav-item">
                            <a href="#" class="aiz-side-nav-link">
                                <i class="las la-backward aiz-side-nav-icon"></i>
                                <span class="aiz-side-nav-text"><?php echo e(translate('Refunds')); ?></span>
                                <?php if(env("DEMO_MODE") == "On"): ?>
                                    <span class="badge badge-inline badge-danger">Addon</span>
                                <?php endif; ?>
                                <span class="aiz-side-nav-arrow"></span>
                            </a>
                            <ul class="aiz-side-nav-list level-2">
                                <li class="aiz-side-nav-item">
                                    <a href="<?php echo e(route('refund_requests_all')); ?>" class="aiz-side-nav-link <?php echo e(areActiveRoutes(['refund_requests_all', 'reason_show'])); ?>">
                                        <span class="aiz-side-nav-text"><?php echo e(translate('Refund Requests')); ?></span>
                                    </a>
                                </li>
                                <li class="aiz-side-nav-item">
                                    <a href="<?php echo e(route('paid_refund')); ?>" class="aiz-side-nav-link">
                                        <span class="aiz-side-nav-text"><?php echo e(translate('Approved Refunds')); ?></span>
                                    </a>
                                </li>
                                <li class="aiz-side-nav-item">
                                    <a href="<?php echo e(route('rejected_refund')); ?>" class="aiz-side-nav-link">
                                        <span class="aiz-side-nav-text"><?php echo e(translate('rejected Refunds')); ?></span>
                                    </a>
                                </li>
                                <li class="aiz-side-nav-item">
                                    <a href="<?php echo e(route('refund_time_config')); ?>" class="aiz-side-nav-link">
                                        <span class="aiz-side-nav-text"><?php echo e(translate('Refund Configuration')); ?></span>
                                    </a>
                                </li>
                            </ul>
                        </li>
                    <?php endif; ?>
                <?php endif; ?>


            <!-- Customers -->
                <?php if(Auth::user()->user_type == 'admin' || in_array('8', json_decode(Auth::user()->staff->role->permissions))): ?>
                    <li class="aiz-side-nav-item">
                        <a href="#" class="aiz-side-nav-link">
                            <i class="las la-user-friends aiz-side-nav-icon"></i>
                            <span class="aiz-side-nav-text"><?php echo e(translate('Customers')); ?></span>
                            <span class="aiz-side-nav-arrow"></span>
                        </a>
                        <ul class="aiz-side-nav-list level-2">
                            <li class="aiz-side-nav-item">
                                <a href="<?php echo e(route('customers.index')); ?>" class="aiz-side-nav-link">
                                    <span class="aiz-side-nav-text"><?php echo e(translate('Customer list')); ?></span>
                                </a>
                            </li>
                            <?php if(get_setting('classified_product') == 1): ?>
                                <li class="aiz-side-nav-item">
                                    <a href="<?php echo e(route('classified_products')); ?>" class="aiz-side-nav-link">
                                        <span class="aiz-side-nav-text"><?php echo e(translate('Classified Products')); ?></span>
                                    </a>
                                </li>
                                <li class="aiz-side-nav-item">
                                    <a href="<?php echo e(route('customer_packages.index')); ?>" class="aiz-side-nav-link <?php echo e(areActiveRoutes(['customer_packages.index', 'customer_packages.create', 'customer_packages.edit'])); ?>">
                                        <span class="aiz-side-nav-text"><?php echo e(translate('Classified Packages')); ?></span>
                                    </a>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </li>
                <?php endif; ?>

            <!-- Sellers -->
                <?php if((Auth::user()->user_type == 'admin' || in_array('9', json_decode(Auth::user()->staff->role->permissions))) && get_setting('vendor_system_activation') == 1): ?>
                    <li class="aiz-side-nav-item">
                        <a href="#" class="aiz-side-nav-link">
                            <i class="las la-user aiz-side-nav-icon"></i>
                            <span class="aiz-side-nav-text"><?php echo e(translate('Sellers')); ?></span>
                            <span class="aiz-side-nav-arrow"></span>
                        </a>
                        <ul class="aiz-side-nav-list level-2">
                            <li class="aiz-side-nav-item">
                                <?php
                                    $sellers = \App\Models\Seller::where('verification_status', 0)->where('verification_info', '!=', null)->count();
                                ?>
                                <a href="<?php echo e(route('sellers.index')); ?>" class="aiz-side-nav-link <?php echo e(areActiveRoutes(['sellers.index', 'sellers.create', 'sellers.edit', 'sellers.payment_history','sellers.approved','sellers.profile_modal','sellers.show_verification_request'])); ?>">
                                    <span class="aiz-side-nav-text"><?php echo e(translate('All Seller')); ?></span>
                                    <?php if($sellers > 0): ?><span class="badge badge-info"><?php echo e($sellers); ?></span> <?php endif; ?>
                                </a>
                            </li>
                            <li class="aiz-side-nav-item">
                                <a href="<?php echo e(route('sellers.payment_histories')); ?>" class="aiz-side-nav-link">
                                    <span class="aiz-side-nav-text"><?php echo e(translate('Payouts')); ?></span>
                                </a>
                            </li>
                            <li class="aiz-side-nav-item">
                                <a href="<?php echo e(route('withdraw_requests_all')); ?>" class="aiz-side-nav-link">
                                    <span class="aiz-side-nav-text"><?php echo e(translate('Payout Requests')); ?></span>
                                </a>
                            </li>
                            <li class="aiz-side-nav-item">
                                <a href="<?php echo e(route('business_settings.vendor_commission')); ?>" class="aiz-side-nav-link">
                                    <span class="aiz-side-nav-text"><?php echo e(translate('Seller Commission')); ?></span>
                                </a>
                            </li>

                            <?php if(addon_is_activated('seller_subscription')): ?>
                                <li class="aiz-side-nav-item">
                                    <a href="<?php echo e(route('seller_packages.index')); ?>" class="aiz-side-nav-link <?php echo e(areActiveRoutes(['seller_packages.index', 'seller_packages.create', 'seller_packages.edit'])); ?>">
                                        <span class="aiz-side-nav-text"><?php echo e(translate('Seller Packages')); ?></span>
                                        <?php if(env("DEMO_MODE") == "On"): ?>
                                            <span class="badge badge-inline badge-danger">Addon</span>
                                        <?php endif; ?>
                                    </a>
                                </li>
                            <?php endif; ?>
                            <li class="aiz-side-nav-item">
                                <a href="<?php echo e(route('seller_verification_form.index')); ?>" class="aiz-side-nav-link">
                                    <span class="aiz-side-nav-text"><?php echo e(translate('Seller Verification Form')); ?></span>
                                </a>
                            </li>
                        </ul>
                    </li>
                <?php endif; ?>
                <?php if(Auth::user()->user_type == 'admin' || in_array('22', json_decode(Auth::user()->staff->role->permissions))): ?>
                    <li class="aiz-side-nav-item">
                        <a href="<?php echo e(route('uploaded-files.index')); ?>" class="aiz-side-nav-link <?php echo e(areActiveRoutes(['uploaded-files.create'])); ?>">
                            <i class="las la-folder-open aiz-side-nav-icon"></i>
                            <span class="aiz-side-nav-text"><?php echo e(translate('Uploaded Files')); ?></span>
                        </a>
                    </li>
                <?php endif; ?>
            <!-- Reports -->
                <?php if(Auth::user()->user_type == 'admin' || in_array('10', json_decode(Auth::user()->staff->role->permissions))): ?>
                    <li class="aiz-side-nav-item">
                        <a href="#" class="aiz-side-nav-link">
                            <i class="las la-file-alt aiz-side-nav-icon"></i>
                            <span class="aiz-side-nav-text"><?php echo e(translate('Reports')); ?></span>
                            <span class="aiz-side-nav-arrow"></span>
                        </a>
                        <ul class="aiz-side-nav-list level-2">
                            <li class="aiz-side-nav-item">
                                <a href="<?php echo e(route('in_house_sale_report.index')); ?>" class="aiz-side-nav-link <?php echo e(areActiveRoutes(['in_house_sale_report.index'])); ?>">
                                    <span class="aiz-side-nav-text"><?php echo e(translate('In House Product Sale')); ?></span>
                                </a>
                            </li>
                            <li class="aiz-side-nav-item">
                                <a href="<?php echo e(route('seller_sale_report.index')); ?>" class="aiz-side-nav-link <?php echo e(areActiveRoutes(['seller_sale_report.index'])); ?>">
                                    <span class="aiz-side-nav-text"><?php echo e(translate('Seller Products Sale')); ?></span>
                                </a>
                            </li>
                            <li class="aiz-side-nav-item">
                                <a href="<?php echo e(route('stock_report.index')); ?>" class="aiz-side-nav-link <?php echo e(areActiveRoutes(['stock_report.index'])); ?>">
                                    <span class="aiz-side-nav-text"><?php echo e(translate('Products Stock')); ?></span>
                                </a>
                            </li>
                            <li class="aiz-side-nav-item">
                                <a href="<?php echo e(route('wish_report.index')); ?>" class="aiz-side-nav-link <?php echo e(areActiveRoutes(['wish_report.index'])); ?>">
                                    <span class="aiz-side-nav-text"><?php echo e(translate('Products wishlist')); ?></span>
                                </a>
                            </li>
                            <li class="aiz-side-nav-item">
                                <a href="<?php echo e(route('user_search_report.index')); ?>" class="aiz-side-nav-link <?php echo e(areActiveRoutes(['user_search_report.index'])); ?>">
                                    <span class="aiz-side-nav-text"><?php echo e(translate('User Searches')); ?></span>
                                </a>
                            </li>
                            <li class="aiz-side-nav-item">
                                <a href="<?php echo e(route('commission-log.index')); ?>" class="aiz-side-nav-link">
                                    <span class="aiz-side-nav-text"><?php echo e(translate('Commission History')); ?></span>
                                </a>
                            </li>
                            <li class="aiz-side-nav-item">
                                <a href="<?php echo e(route('wallet-history.index')); ?>" class="aiz-side-nav-link">
                                    <span class="aiz-side-nav-text"><?php echo e(translate('Wallet Recharge History')); ?></span>
                                </a>
                            </li>
                        </ul>
                    </li>
                <?php endif; ?>
                <?php if(Auth::user()->user_type == 'admin' || in_array('23', json_decode(Auth::user()->staff->role->permissions))): ?>
                <!--Blog System-->
                    <li class="aiz-side-nav-item">
                        <a href="#" class="aiz-side-nav-link">
                            <i class="las la-bullhorn aiz-side-nav-icon"></i>
                            <span class="aiz-side-nav-text"><?php echo e(translate('Blog System')); ?></span>
                            <span class="aiz-side-nav-arrow"></span>
                        </a>
                        <ul class="aiz-side-nav-list level-2">
                            <li class="aiz-side-nav-item">
                                <a href="<?php echo e(route('blog.index')); ?>" class="aiz-side-nav-link <?php echo e(areActiveRoutes(['blog.create', 'blog.edit'])); ?>">
                                    <span class="aiz-side-nav-text"><?php echo e(translate('All Posts')); ?></span>
                                </a>
                            </li>
                            <li class="aiz-side-nav-item">
                                <a href="<?php echo e(route('blog-category.index')); ?>" class="aiz-side-nav-link <?php echo e(areActiveRoutes(['blog-category.create', 'blog-category.edit'])); ?>">
                                    <span class="aiz-side-nav-text"><?php echo e(translate('Categories')); ?></span>
                                </a>
                            </li>
                        </ul>
                    </li>
                <?php endif; ?>

            <!-- marketing -->
                <?php if(Auth::user()->user_type == 'admin' || in_array('11', json_decode(Auth::user()->staff->role->permissions))): ?>
                    <li class="aiz-side-nav-item">
                        <a href="#" class="aiz-side-nav-link">
                            <i class="las la-bullhorn aiz-side-nav-icon"></i>
                            <span class="aiz-side-nav-text"><?php echo e(translate('Marketing')); ?></span>
                            <span class="aiz-side-nav-arrow"></span>
                        </a>
                        <ul class="aiz-side-nav-list level-2">
                            <?php if(Auth::user()->user_type == 'admin' || in_array('2', json_decode(Auth::user()->staff->role->permissions))): ?>
                                <li class="aiz-side-nav-item">
                                    <a href="<?php echo e(route('flash_deals.index')); ?>" class="aiz-side-nav-link <?php echo e(areActiveRoutes(['flash_deals.index', 'flash_deals.create', 'flash_deals.edit'])); ?>">
                                        <span class="aiz-side-nav-text"><?php echo e(translate('Flash deals')); ?></span>
                                    </a>
                                </li>
                            <?php endif; ?>
                            <?php if(Auth::user()->user_type == 'admin' || in_array('7', json_decode(Auth::user()->staff->role->permissions))): ?>
                                <li class="aiz-side-nav-item">
                                    <a href="<?php echo e(route('newsletters.index')); ?>" class="aiz-side-nav-link">
                                        <span class="aiz-side-nav-text"><?php echo e(translate('Newsletters')); ?></span>
                                    </a>
                                </li>
                                <?php if(addon_is_activated('otp_system')): ?>
                                    <li class="aiz-side-nav-item">
                                        <a href="<?php echo e(route('sms.index')); ?>" class="aiz-side-nav-link">
                                            <span class="aiz-side-nav-text"><?php echo e(translate('Bulk SMS')); ?></span>
                                            <?php if(env("DEMO_MODE") == "On"): ?>
                                                <span class="badge badge-inline badge-danger">Addon</span>
                                            <?php endif; ?>
                                        </a>
                                    </li>
                                <?php endif; ?>
                            <?php endif; ?>
                            <li class="aiz-side-nav-item">
                                <a href="<?php echo e(route('subscribers.index')); ?>" class="aiz-side-nav-link">
                                    <span class="aiz-side-nav-text"><?php echo e(translate('Subscribers')); ?></span>
                                </a>
                            </li>
                            <li class="aiz-side-nav-item">
                                <a href="<?php echo e(route('coupon.index')); ?>" class="aiz-side-nav-link <?php echo e(areActiveRoutes(['coupon.index','coupon.create','coupon.edit'])); ?>">
                                    <span class="aiz-side-nav-text"><?php echo e(translate('Coupon')); ?></span>
                                </a>
                            </li>
                        </ul>
                    </li>
                <?php endif; ?>

            <!-- Support -->
                <?php if(Auth::user()->user_type == 'admin' || in_array('12', json_decode(Auth::user()->staff->role->permissions))): ?>
                    <li class="aiz-side-nav-item">
                        <a href="#" class="aiz-side-nav-link">
                            <i class="las la-link aiz-side-nav-icon"></i>
                            <span class="aiz-side-nav-text"><?php echo e(translate('Support')); ?></span>
                            <span class="aiz-side-nav-arrow"></span>
                        </a>
                        <ul class="aiz-side-nav-list level-2">
                            <?php if(Auth::user()->user_type == 'admin' || in_array('12', json_decode(Auth::user()->staff->role->permissions))): ?>
                                <?php
                                    $support_ticket = DB::table('tickets')
                                                ->where('viewed', 0)
                                                ->select('id')
                                                ->count();
                                ?>
                                <li class="aiz-side-nav-item">
                                    <a href="<?php echo e(route('support_ticket.admin_index')); ?>" class="aiz-side-nav-link <?php echo e(areActiveRoutes(['support_ticket.admin_index', 'support_ticket.admin_show'])); ?>">
                                        <span class="aiz-side-nav-text"><?php echo e(translate('Ticket')); ?></span>
                                        <?php if($support_ticket > 0): ?><span class="badge badge-info"><?php echo e($support_ticket); ?></span><?php endif; ?>
                                    </a>
                                </li>
                            <?php endif; ?>

                            <?php
                                $conversation = \App\Models\Conversation::where('receiver_id', Auth::user()->id)->where('receiver_viewed', '1')->get();
                            ?>
                            <?php if(Auth::user()->user_type == 'admin' || in_array('12', json_decode(Auth::user()->staff->role->permissions))): ?>
                                <li class="aiz-side-nav-item">
                                    <a href="<?php echo e(route('conversations.admin_index')); ?>" class="aiz-side-nav-link <?php echo e(areActiveRoutes(['conversations.admin_index', 'conversations.admin_show'])); ?>">
                                        <span class="aiz-side-nav-text"><?php echo e(translate('Product Queries')); ?></span>
                                        <?php if(count($conversation) > 0): ?>
                                            <span class="badge badge-info"><?php echo e(count($conversation)); ?></span>
                                        <?php endif; ?>
                                    </a>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </li>
                <?php endif; ?>

            <!-- Affiliate Addon -->
                <?php if(addon_is_activated('affiliate_system')): ?>
                    <?php if(Auth::user()->user_type == 'admin' || in_array('15', json_decode(Auth::user()->staff->role->permissions))): ?>
                        <li class="aiz-side-nav-item">
                            <a href="#" class="aiz-side-nav-link">
                                <i class="las la-link aiz-side-nav-icon"></i>
                                <span class="aiz-side-nav-text"><?php echo e(translate('Affiliate System')); ?></span>
                                <?php if(env("DEMO_MODE") == "On"): ?>
                                    <span class="badge badge-inline badge-danger">Addon</span>
                                <?php endif; ?>
                                <span class="aiz-side-nav-arrow"></span>
                            </a>
                            <ul class="aiz-side-nav-list level-2">
                                <li class="aiz-side-nav-item">
                                    <a href="<?php echo e(route('affiliate.configs')); ?>" class="aiz-side-nav-link">
                                        <span class="aiz-side-nav-text"><?php echo e(translate('Affiliate Registration Form')); ?></span>
                                    </a>
                                </li>
                                <li class="aiz-side-nav-item">
                                    <a href="<?php echo e(route('affiliate.index')); ?>" class="aiz-side-nav-link">
                                        <span class="aiz-side-nav-text"><?php echo e(translate('Affiliate Configurations')); ?></span>
                                    </a>
                                </li>
                                <li class="aiz-side-nav-item">
                                    <a href="<?php echo e(route('affiliate.users')); ?>" class="aiz-side-nav-link <?php echo e(areActiveRoutes(['affiliate.users', 'affiliate_users.show_verification_request', 'affiliate_user.payment_history'])); ?>">
                                        <span class="aiz-side-nav-text"><?php echo e(translate('Affiliate Users')); ?></span>
                                    </a>
                                </li>
                                <li class="aiz-side-nav-item">
                                    <a href="<?php echo e(route('refferals.users')); ?>" class="aiz-side-nav-link">
                                        <span class="aiz-side-nav-text"><?php echo e(translate('Referral Users')); ?></span>
                                    </a>
                                </li>
                                <li class="aiz-side-nav-item">
                                    <a href="<?php echo e(route('affiliate.withdraw_requests')); ?>" class="aiz-side-nav-link">
                                        <span class="aiz-side-nav-text"><?php echo e(translate('Affiliate Withdraw Requests')); ?></span>
                                    </a>
                                </li>
                                <li class="aiz-side-nav-item">
                                    <a href="<?php echo e(route('affiliate.logs.admin')); ?>" class="aiz-side-nav-link">
                                        <span class="aiz-side-nav-text"><?php echo e(translate('Affiliate Logs')); ?></span>
                                    </a>
                                </li>
                            </ul>
                        </li>
                    <?php endif; ?>
                <?php endif; ?>

            <!-- Offline Payment Addon-->
                <?php if(addon_is_activated('offline_payment')): ?>
                    <?php if(Auth::user()->user_type == 'admin' || in_array('16', json_decode(Auth::user()->staff->role->permissions))): ?>
                        <li class="aiz-side-nav-item">
                            <a href="#" class="aiz-side-nav-link">
                                <i class="las la-money-check-alt aiz-side-nav-icon"></i>
                                <span class="aiz-side-nav-text"><?php echo e(translate('Offline Payment System')); ?></span>
                                <?php if(env("DEMO_MODE") == "On"): ?>
                                    <span class="badge badge-inline badge-danger">Addon</span>
                                <?php endif; ?>
                                <span class="aiz-side-nav-arrow"></span>
                            </a>
                            <ul class="aiz-side-nav-list level-2">
                                <li class="aiz-side-nav-item">
                                    <a href="<?php echo e(route('manual_payment_methods.index')); ?>" class="aiz-side-nav-link <?php echo e(areActiveRoutes(['manual_payment_methods.index', 'manual_payment_methods.create', 'manual_payment_methods.edit'])); ?>">
                                        <span class="aiz-side-nav-text"><?php echo e(translate('Manual Payment Methods')); ?></span>
                                    </a>
                                </li>
                                <li class="aiz-side-nav-item">
                                    <a href="<?php echo e(route('offline_wallet_recharge_request.index')); ?>" class="aiz-side-nav-link">
                                        <span class="aiz-side-nav-text"><?php echo e(translate('Offline Wallet Recharge')); ?></span>
                                    </a>
                                </li>
                                <?php if(get_setting('classified_product') == 1): ?>
                                    <li class="aiz-side-nav-item">
                                        <a href="<?php echo e(route('offline_customer_package_payment_request.index')); ?>" class="aiz-side-nav-link">
                                            <span class="aiz-side-nav-text"><?php echo e(translate('Offline Customer Package Payments')); ?></span>
                                        </a>
                                    </li>
                                <?php endif; ?>
                                <?php if(addon_is_activated('seller_subscription')): ?>
                                    <li class="aiz-side-nav-item">
                                        <a href="<?php echo e(route('offline_seller_package_payment_request.index')); ?>" class="aiz-side-nav-link">
                                            <span class="aiz-side-nav-text"><?php echo e(translate('Offline Seller Package Payments')); ?></span>
                                            <?php if(env("DEMO_MODE") == "On"): ?>
                                                <span class="badge badge-inline badge-danger">Addon</span>
                                            <?php endif; ?>
                                        </a>
                                    </li>
                                <?php endif; ?>
                            </ul>
                        </li>
                    <?php endif; ?>
                <?php endif; ?>

            <!-- Paytm Addon -->
                <?php if(addon_is_activated('paytm')): ?>
                    <?php if(Auth::user()->user_type == 'admin' || in_array('17', json_decode(Auth::user()->staff->role->permissions))): ?>
                        <li class="aiz-side-nav-item">
                            <a href="#" class="aiz-side-nav-link">
                                <i class="las la-mobile-alt aiz-side-nav-icon"></i>
                                <span class="aiz-side-nav-text"><?php echo e(translate('Paytm Payment Gateway')); ?></span>
                                <?php if(env("DEMO_MODE") == "On"): ?>
                                    <span class="badge badge-inline badge-danger">Addon</span>
                                <?php endif; ?>
                                <span class="aiz-side-nav-arrow"></span>
                            </a>
                            <ul class="aiz-side-nav-list level-2">
                                <li class="aiz-side-nav-item">
                                    <a href="<?php echo e(route('paytm.index')); ?>" class="aiz-side-nav-link">
                                        <span class="aiz-side-nav-text"><?php echo e(translate('Set Paytm Credentials')); ?></span>
                                    </a>
                                </li>
                            </ul>
                        </li>
                    <?php endif; ?>
                <?php endif; ?>

            <!-- Club Point Addon-->
                <?php if(addon_is_activated('club_point')): ?>
                    <?php if(Auth::user()->user_type == 'admin' || in_array('18', json_decode(Auth::user()->staff->role->permissions))): ?>
                        <li class="aiz-side-nav-item">
                            <a href="#" class="aiz-side-nav-link">
                                <i class="lab la-btc aiz-side-nav-icon"></i>
                                <span class="aiz-side-nav-text"><?php echo e(translate('Club Point System')); ?></span>
                                <?php if(env("DEMO_MODE") == "On"): ?>
                                    <span class="badge badge-inline badge-danger">Addon</span>
                                <?php endif; ?>
                                <span class="aiz-side-nav-arrow"></span>
                            </a>
                            <ul class="aiz-side-nav-list level-2">
                                <li class="aiz-side-nav-item">
                                    <a href="<?php echo e(route('club_points.configs')); ?>" class="aiz-side-nav-link">
                                        <span class="aiz-side-nav-text"><?php echo e(translate('Club Point Configurations')); ?></span>
                                    </a>
                                </li>
                                <li class="aiz-side-nav-item">
                                    <a href="<?php echo e(route('set_product_points')); ?>" class="aiz-side-nav-link <?php echo e(areActiveRoutes(['set_product_points', 'product_club_point.edit'])); ?>">
                                        <span class="aiz-side-nav-text"><?php echo e(translate('Set Product Point')); ?></span>
                                    </a>
                                </li>
                                <li class="aiz-side-nav-item">
                                    <a href="<?php echo e(route('club_points.index')); ?>" class="aiz-side-nav-link <?php echo e(areActiveRoutes(['club_points.index', 'club_point.details'])); ?>">
                                        <span class="aiz-side-nav-text"><?php echo e(translate('User Points')); ?></span>
                                    </a>
                                </li>
                            </ul>
                        </li>
                    <?php endif; ?>
                <?php endif; ?>

            <!--OTP addon -->
                <?php if(addon_is_activated('otp_system')): ?>
                    <?php if(Auth::user()->user_type == 'admin' || in_array('19', json_decode(Auth::user()->staff->role->permissions))): ?>
                        <li class="aiz-side-nav-item">
                            <a href="#" class="aiz-side-nav-link">
                                <i class="las la-phone aiz-side-nav-icon"></i>
                                <span class="aiz-side-nav-text"><?php echo e(translate('OTP System')); ?></span>
                                <?php if(env("DEMO_MODE") == "On"): ?>
                                    <span class="badge badge-inline badge-danger">Addon</span>
                                <?php endif; ?>
                                <span class="aiz-side-nav-arrow"></span>
                            </a>
                            <ul class="aiz-side-nav-list level-2">
                                <li class="aiz-side-nav-item">
                                    <a href="<?php echo e(route('otp.configconfiguration')); ?>" class="aiz-side-nav-link">
                                        <span class="aiz-side-nav-text"><?php echo e(translate('OTP Configurations')); ?></span>
                                    </a>
                                </li>
                                <li class="aiz-side-nav-item">
                                    <a href="<?php echo e(route('sms-templates.index')); ?>" class="aiz-side-nav-link">
                                        <span class="aiz-side-nav-text"><?php echo e(translate('SMS Templates')); ?></span>
                                    </a>
                                </li>
                                <li class="aiz-side-nav-item">
                                    <a href="<?php echo e(route('otp_credentials.index')); ?>" class="aiz-side-nav-link">
                                        <span class="aiz-side-nav-text"><?php echo e(translate('Set OTP Credentials')); ?></span>
                                    </a>
                                </li>
                            </ul>
                        </li>
                    <?php endif; ?>
                <?php endif; ?>

                <?php if(addon_is_activated('african_pg')): ?>
                    <?php if(Auth::user()->user_type == 'admin' || in_array('19', json_decode(Auth::user()->staff->role->permissions))): ?>
                        <li class="aiz-side-nav-item">
                            <a href="#" class="aiz-side-nav-link">
                                <i class="las la-phone aiz-side-nav-icon"></i>
                                <span class="aiz-side-nav-text"><?php echo e(translate('African Payment Gateway Addon')); ?></span>
                                <?php if(env("DEMO_MODE") == "On"): ?>
                                    <span class="badge badge-inline badge-danger">Addon</span>
                                <?php endif; ?>
                                <span class="aiz-side-nav-arrow"></span>
                            </a>
                            <ul class="aiz-side-nav-list level-2">
                                <li class="aiz-side-nav-item">
                                    <a href="<?php echo e(route('african.configuration')); ?>" class="aiz-side-nav-link">
                                        <span class="aiz-side-nav-text"><?php echo e(translate('African PG Configurations')); ?></span>
                                    </a>
                                </li>
                                <li class="aiz-side-nav-item">
                                    <a href="<?php echo e(route('african_credentials.index')); ?>" class="aiz-side-nav-link">
                                        <span class="aiz-side-nav-text"><?php echo e(translate('Set African PG Credentials')); ?></span>
                                    </a>
                                </li>
                            </ul>
                        </li>
                    <?php endif; ?>
                <?php endif; ?>

            <!-- Website Setup -->
                <?php if(Auth::user()->user_type == 'admin' || in_array('13', json_decode(Auth::user()->staff->role->permissions))): ?>
                    <li class="aiz-side-nav-item">
                        <a href="#" class="aiz-side-nav-link <?php echo e(areActiveRoutes(['website.footer', 'website.header', 'website.appearance', 'website.pages', 'sliders.admin.index'])); ?>" >
                            <i class="las la-desktop aiz-side-nav-icon"></i>
                            <span class="aiz-side-nav-text"><?php echo e(translate('Website Setup')); ?></span>
                            <span class="aiz-side-nav-arrow"></span>
                        </a>
                        <ul class="aiz-side-nav-list level-2">
                            <li class="aiz-side-nav-item">
                                <a href="<?php echo e(route('website.header')); ?>" class="aiz-side-nav-link">
                                    <span class="aiz-side-nav-text"><?php echo e(translate('Header')); ?></span>
                                </a>
                            </li>
                            <li class="aiz-side-nav-item">
                                <a href="<?php echo e(route('website.footer', ['lang'=>  App::getLocale()] )); ?>" class="aiz-side-nav-link <?php echo e(areActiveRoutes(['website.footer'])); ?>">
                                    <span class="aiz-side-nav-text"><?php echo e(translate('Footer')); ?></span>
                                </a>
                            </li>
                            <li class="aiz-side-nav-item">
                                <a href="<?php echo e(route('website.pages')); ?>" class="aiz-side-nav-link <?php echo e(areActiveRoutes(['website.pages', 'custom-pages.create' ,'custom-pages.edit'])); ?>">
                                    <span class="aiz-side-nav-text"><?php echo e(translate('Pages')); ?></span>
                                </a>
                            </li>
                            <li class="aiz-side-nav-item">
                                <a href="<?php echo e(route('website.appearance')); ?>" class="aiz-side-nav-link">
                                    <span class="aiz-side-nav-text"><?php echo e(translate('Appearance')); ?></span>
                                </a>
                            </li>
                            <li class="aiz-side-nav-item">
                                <a href="<?php echo e(route('sliders.admin.index')); ?>" class="aiz-side-nav-link <?php echo e(areActiveRoutes(['sliders.admin.index'])); ?>">
                                    <span class="aiz-side-nav-text"><?php echo e(translate('Sliders')); ?></span>
                                </a>
                            </li>
                        </ul>

                    </li>
                <?php endif; ?>

            <!-- Setup & Configurations -->
                <?php if(Auth::user()->user_type == 'admin' || in_array('14', json_decode(Auth::user()->staff->role->permissions))): ?>
                    <li class="aiz-side-nav-item">
                        <a href="#" class="aiz-side-nav-link">
                            <i class="las la-dharmachakra aiz-side-nav-icon"></i>
                            <span class="aiz-side-nav-text"><?php echo e(translate('Setup & Configurations')); ?></span>
                            <span class="aiz-side-nav-arrow"></span>
                        </a>
                        <ul class="aiz-side-nav-list level-2">
                            <li class="aiz-side-nav-item">
                                <a href="<?php echo e(route('general_setting.index')); ?>" class="aiz-side-nav-link">
                                    <span class="aiz-side-nav-text"><?php echo e(translate('General Settings')); ?></span>
                                </a>
                            </li>

                            <li class="aiz-side-nav-item">
                                <a href="<?php echo e(route('storefront_info.index')); ?>" class="aiz-side-nav-link">
                                    <span class="aiz-side-nav-text"><?php echo e(translate('Storefront Info')); ?></span>
                                </a>
                            </li>

                            <li class="aiz-side-nav-item">
                                <a href="<?php echo e(route('delivery_info.index')); ?>" class="aiz-side-nav-link">
                                    <i class="las la-truck aiz-side-nav-icon"></i>
                                    <span class="aiz-side-nav-text"><?php echo e(translate('Delivery Info')); ?></span>
                                </a>
                            </li>

                            <li class="aiz-side-nav-item">
                                <a href="<?php echo e(route('activation.index')); ?>" class="aiz-side-nav-link">
                                    <span class="aiz-side-nav-text"><?php echo e(translate('Features activation')); ?></span>
                                </a>
                            </li>
                            <li class="aiz-side-nav-item">
                                <a href="<?php echo e(route('languages.index')); ?>" class="aiz-side-nav-link <?php echo e(areActiveRoutes(['languages.index', 'languages.create', 'languages.store', 'languages.show', 'languages.edit'])); ?>">
                                    <span class="aiz-side-nav-text"><?php echo e(translate('Languages')); ?></span>
                                </a>
                            </li>

                            <li class="aiz-side-nav-item">
                                <a href="<?php echo e(route('currency.index')); ?>" class="aiz-side-nav-link">
                                    <span class="aiz-side-nav-text"><?php echo e(translate('Currency')); ?></span>
                                </a>
                            </li>
                            <li class="aiz-side-nav-item">
                                <a href="<?php echo e(route('tax.index')); ?>" class="aiz-side-nav-link <?php echo e(areActiveRoutes(['tax.index', 'tax.create', 'tax.store', 'tax.show', 'tax.edit'])); ?>">
                                    <span class="aiz-side-nav-text"><?php echo e(translate('Vat & TAX')); ?></span>
                                </a>
                            </li>
                            <li class="aiz-side-nav-item">
                                <a href="<?php echo e(route('pick_up_points.index')); ?>" class="aiz-side-nav-link <?php echo e(areActiveRoutes(['pick_up_points.index','pick_up_points.create','pick_up_points.edit'])); ?>">
                                    <span class="aiz-side-nav-text"><?php echo e(translate('Pickup point')); ?></span>
                                </a>
                            </li>
                            <li class="aiz-side-nav-item">
                                <a href="<?php echo e(route('smtp_settings.index')); ?>" class="aiz-side-nav-link">
                                    <span class="aiz-side-nav-text"><?php echo e(translate('SMTP Settings')); ?></span>
                                </a>
                            </li>
                            <li class="aiz-side-nav-item">
                                <a href="<?php echo e(route('payment_method.index')); ?>" class="aiz-side-nav-link">
                                    <span class="aiz-side-nav-text"><?php echo e(translate('Payment Methods')); ?></span>
                                </a>
                            </li>
                            <li class="aiz-side-nav-item">
                                <a href="<?php echo e(route('file_system.index')); ?>" class="aiz-side-nav-link">
                                    <span class="aiz-side-nav-text"><?php echo e(translate('File System & Cache Configuration')); ?></span>
                                </a>
                            </li>
                            <li class="aiz-side-nav-item">
                                <a href="<?php echo e(route('social_login.index')); ?>" class="aiz-side-nav-link">
                                    <span class="aiz-side-nav-text"><?php echo e(translate('Social media Logins')); ?></span>
                                </a>
                            </li>

                            <li class="aiz-side-nav-item">
                                <a href="javascript:void(0);" class="aiz-side-nav-link">
                                    <span class="aiz-side-nav-text"><?php echo e(translate('Facebook')); ?></span>
                                    <span class="aiz-side-nav-arrow"></span>
                                </a>
                                <ul class="aiz-side-nav-list level-3">
                                    <li class="aiz-side-nav-item">
                                        <a href="<?php echo e(route('facebook_chat.index')); ?>" class="aiz-side-nav-link">
                                            <span class="aiz-side-nav-text"><?php echo e(translate('Facebook Chat')); ?></span>
                                        </a>
                                    </li>
                                    <li class="aiz-side-nav-item">
                                        <a href="<?php echo e(route('facebook-comment')); ?>" class="aiz-side-nav-link">
                                            <span class="aiz-side-nav-text"><?php echo e(translate('Facebook Comment')); ?></span>
                                        </a>
                                    </li>
                                </ul>
                            </li>

                            <li class="aiz-side-nav-item">
                                <a href="javascript:void(0);" class="aiz-side-nav-link">
                                    <span class="aiz-side-nav-text"><?php echo e(translate('Google')); ?></span>
                                    <span class="aiz-side-nav-arrow"></span>
                                </a>
                                <ul class="aiz-side-nav-list level-3">
                                    <li class="aiz-side-nav-item">
                                        <a href="<?php echo e(route('google_analytics.index')); ?>" class="aiz-side-nav-link">
                                            <span class="aiz-side-nav-text"><?php echo e(translate('Analytics Tools')); ?></span>
                                        </a>
                                    </li>
                                    <li class="aiz-side-nav-item">
                                        <a href="<?php echo e(route('google_recaptcha.index')); ?>" class="aiz-side-nav-link">
                                            <span class="aiz-side-nav-text"><?php echo e(translate('Google reCAPTCHA')); ?></span>
                                        </a>
                                    </li>
                                    <li class="aiz-side-nav-item">
                                        <a href="<?php echo e(route('google-map.index')); ?>" class="aiz-side-nav-link">
                                            <span class="aiz-side-nav-text"><?php echo e(translate('Google Map')); ?></span>
                                        </a>
                                    </li>
                                    <li class="aiz-side-nav-item">
                                        <a href="<?php echo e(route('google-firebase.index')); ?>" class="aiz-side-nav-link">
                                            <span class="aiz-side-nav-text"><?php echo e(translate('Google Firebase')); ?></span>
                                        </a>
                                    </li>
                                </ul>
                            </li>




                            <li class="aiz-side-nav-item">
                                <a href="javascript:void(0);" class="aiz-side-nav-link">
                                    <span class="aiz-side-nav-text"><?php echo e(translate('Shipping')); ?></span>
                                    <span class="aiz-side-nav-arrow"></span>
                                </a>
                                <ul class="aiz-side-nav-list level-3">
                                    <li class="aiz-side-nav-item">
                                        <a href="<?php echo e(route('shipping_configuration.index')); ?>" class="aiz-side-nav-link <?php echo e(areActiveRoutes(['shipping_configuration.index','shipping_configuration.edit','shipping_configuration.update'])); ?>">
                                            <span class="aiz-side-nav-text"><?php echo e(translate('Shipping Configuration')); ?></span>
                                        </a>
                                    </li>
                                    <li class="aiz-side-nav-item">
                                        <a href="<?php echo e(route('countries.index')); ?>" class="aiz-side-nav-link <?php echo e(areActiveRoutes(['countries.index','countries.edit','countries.update'])); ?>">
                                            <span class="aiz-side-nav-text"><?php echo e(translate('Shipping Countries')); ?></span>
                                        </a>
                                    </li>
                                    <li class="aiz-side-nav-item">
                                        <a href="<?php echo e(route('states.index')); ?>" class="aiz-side-nav-link <?php echo e(areActiveRoutes(['states.index','states.edit','states.update'])); ?>">
                                            <span class="aiz-side-nav-text"><?php echo e(translate('Shipping States')); ?></span>
                                        </a>
                                    </li>
                                    <li class="aiz-side-nav-item">
                                        <a href="<?php echo e(route('cities.index')); ?>" class="aiz-side-nav-link <?php echo e(areActiveRoutes(['cities.index','cities.edit','cities.update'])); ?>">
                                            <span class="aiz-side-nav-text"><?php echo e(translate('Shipping Cities')); ?></span>
                                        </a>
                                    </li>
                                </ul>
                            </li>

                        </ul>
                    </li>
                <?php endif; ?>


            <!-- Staffs -->
                <?php if(Auth::user()->user_type == 'admin' || in_array('20', json_decode(Auth::user()->staff->role->permissions))): ?>
                    <li class="aiz-side-nav-item">
                        <a href="#" class="aiz-side-nav-link">
                            <i class="las la-user-tie aiz-side-nav-icon"></i>
                            <span class="aiz-side-nav-text"><?php echo e(translate('Staffs')); ?></span>
                            <span class="aiz-side-nav-arrow"></span>
                        </a>
                        <ul class="aiz-side-nav-list level-2">
                            <li class="aiz-side-nav-item">
                                <a href="<?php echo e(route('staffs.index')); ?>" class="aiz-side-nav-link <?php echo e(areActiveRoutes(['staffs.index', 'staffs.create', 'staffs.edit'])); ?>">
                                    <span class="aiz-side-nav-text"><?php echo e(translate('All staffs')); ?></span>
                                </a>
                            </li>
                            <li class="aiz-side-nav-item">
                                <a href="<?php echo e(route('roles.index')); ?>" class="aiz-side-nav-link <?php echo e(areActiveRoutes(['roles.index', 'roles.create', 'roles.edit'])); ?>">
                                    <span class="aiz-side-nav-text"><?php echo e(translate('Staff permissions')); ?></span>
                                </a>
                            </li>
                        </ul>
                    </li>
                <?php endif; ?>
                <!--<?php if(Auth::user()->user_type == 'admin' || in_array('24', json_decode(Auth::user()->staff->role->permissions))): ?>-->
                <!--    <li class="aiz-side-nav-item">-->
                <!--        <a href="#" class="aiz-side-nav-link">-->
                <!--            <i class="las la-user-tie aiz-side-nav-icon"></i>-->
                <!--            <span class="aiz-side-nav-text"><?php echo e(translate('System')); ?></span>-->
                <!--            <span class="aiz-side-nav-arrow"></span>-->
                <!--        </a>-->
                <!--        <ul class="aiz-side-nav-list level-2">-->
                <!--            <li class="aiz-side-nav-item">-->
                <!--                <a href="<?php echo e(route('system_update')); ?>" class="aiz-side-nav-link">-->
                <!--                    <span class="aiz-side-nav-text"><?php echo e(translate('Update')); ?></span>-->
                <!--                </a>-->
                <!--            </li>-->
                <!--            <li class="aiz-side-nav-item">-->
                <!--                <a href="<?php echo e(route('system_server')); ?>" class="aiz-side-nav-link">-->
                <!--                    <span class="aiz-side-nav-text"><?php echo e(translate('Server status')); ?></span>-->
                <!--                </a>-->
                <!--            </li>-->
                <!--        </ul>-->
                <!--    </li>-->
                <!--<?php endif; ?>-->

             <!--Addon Manager -->
                <!--<?php if(Auth::user()->user_type == 'admin' || in_array('21', json_decode(Auth::user()->staff->role->permissions))): ?>-->
                <!--    <li class="aiz-side-nav-item">-->
                <!--        <a href="<?php echo e(route('addons.index')); ?>" class="aiz-side-nav-link <?php echo e(areActiveRoutes(['addons.index', 'addons.create'])); ?>">-->
                <!--            <i class="las la-wrench aiz-side-nav-icon"></i>-->
                <!--            <span class="aiz-side-nav-text"><?php echo e(translate('Addon Manager')); ?></span>-->
                <!--        </a>-->
                <!--    </li>-->
                <!--<?php endif; ?>-->
            </ul><!-- .aiz-side-nav -->
        </div><!-- .aiz-side-nav-wrap -->
    </div><!-- .aiz-sidebar -->
    <div class="aiz-sidebar-overlay"></div>
</div><!-- .aiz-sidebar -->
<?php /**PATH D:\laragon\www\kachermart\backend\resources\views/backend/inc/admin_sidenav.blade.php ENDPATH**/ ?>