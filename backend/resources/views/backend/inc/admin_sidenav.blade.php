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
            <a href="{{ route('admin.dashboard') }}" class="d-block text-left">
                @if(get_setting('system_logo_white') != null)
                    <img class="mw-100" src="{{ uploaded_asset(get_setting('system_logo_white')) }}" class="brand-icon" alt="{{ get_setting('site_name') }}">
                @else
                    <img class="mw-100" src="{{ static_asset('assets/img/logo.png') }}" class="brand-icon" alt="{{ get_setting('site_name') }}">
                @endif
            </a>
        </div>

        <!-- Admin Profile Widget -->
        <div class="aiz-sidebar-profile px-3 py-3 mb-3 mx-3 rounded-xl d-flex align-items-center" style="border-radius: 12px; margin-top: 15px; gap: 12px;">
            <div class="position-relative shrink-0">
                <div class="avatar avatar-sm bg-soft-primary text-primary rounded-circle d-flex align-items-center justify-content-center font-weight-bold" style="width: 38px; height: 38px; font-size: 15px; background: rgba(247, 123, 11, 0.15); color: var(--primary);">
                    @if(Auth::user()->avatar_original != null)
                        <img src="{{ uploaded_asset(Auth::user()->avatar_original) }}" onerror="this.onerror=null;this.src='{{ static_asset('assets/img/avatar-place.png') }}';" class="rounded-circle" style="width: 38px; height: 38px; object-fit: cover;">
                    @else
                        {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                    @endif
                </div>
                <span class="position-absolute bottom-0 right-0 bg-success border border-white rounded-circle" style="width: 10px; height: 10px; border-width: 2px !important; bottom: 2px; right: 2px;"></span>
            </div>
            <div class="overflow-hidden flex-grow-1" style="min-width: 0;">
                <h6 class="text-white mb-0 text-truncate font-weight-bold" style="font-size: 13px; line-height: 1.2;">{{ Auth::user()->name }}</h6>
                <span class="text-muted d-block text-truncate text-uppercase font-weight-bold mt-1" style="font-size: 9px; letter-spacing: 0.8px; opacity: 0.6; color: #a2a3b7 !important;">{{ Auth::user()->user_type }}</span>
            </div>
        </div>

        <div class="aiz-side-nav-wrap">
            <div class="px-20px mb-3">
                <input class="form-control bg-soft-secondary border-0 form-control-sm text-white" type="text" name="" placeholder="{{ translate('Search in menu') }}" id="menu-search" onkeyup="menuSearch()">
            </div>
            <ul class="aiz-side-nav-list" id="search-menu">
            </ul>
            <ul class="aiz-side-nav-list" id="main-menu" data-toggle="aiz-side-menu">
                <li class="aiz-side-nav-item">
                    <a href="{{route('admin.dashboard')}}" class="aiz-side-nav-link">
                        <i class="las la-home aiz-side-nav-icon"></i>
                        <span class="aiz-side-nav-text">{{translate('Dashboard')}}</span>
                    </a>
                </li>

                <!-- POS Addon-->
                @if (addon_is_activated('pos_system'))
                    @if(Auth::user()->user_type == 'admin' || in_array('1', json_decode(Auth::user()->staff->role->permissions)))
                        <li class="aiz-side-nav-item">
                            <a href="#" class="aiz-side-nav-link">
                                <i class="las la-tasks aiz-side-nav-icon"></i>
                                <span class="aiz-side-nav-text">{{translate('POS System')}}</span>
                                @if (env("DEMO_MODE") == "On")
                                    <span class="badge badge-inline badge-danger">Addon</span>
                                @endif
                                <span class="aiz-side-nav-arrow"></span>
                            </a>
                            <ul class="aiz-side-nav-list level-2">
                                <li class="aiz-side-nav-item">
                                    <a href="{{route('poin-of-sales.index')}}" class="aiz-side-nav-link {{ areActiveRoutes(['poin-of-sales.index', 'poin-of-sales.create'])}}">
                                        <span class="aiz-side-nav-text">{{translate('POS Manager')}}</span>
                                    </a>
                                </li>
                                <li class="aiz-side-nav-item">
                                    <a href="{{route('poin-of-sales.activation')}}" class="aiz-side-nav-link">
                                        <span class="aiz-side-nav-text">{{translate('POS Configuration')}}</span>
                                    </a>
                                </li>
                            </ul>
                        </li>
                    @endif
                @endif

            <!-- Product -->
                @if(Auth::user()->user_type == 'admin' || in_array('2', json_decode(Auth::user()->staff->role->permissions)))
                    <li class="aiz-side-nav-item">
                        <a href="#" class="aiz-side-nav-link">
                            <i class="las la-shopping-cart aiz-side-nav-icon"></i>
                            <span class="aiz-side-nav-text">{{translate('Products')}}</span>
                            <span class="aiz-side-nav-arrow"></span>
                        </a>
                        <!--Submenu-->
                        <ul class="aiz-side-nav-list level-2">
                            <li class="aiz-side-nav-item">
                                <a class="aiz-side-nav-link" href="{{route('products.create')}}">
                                    <span class="aiz-side-nav-text">{{translate('Add New product')}}</span>
                                </a>
                            </li>
                            <li class="aiz-side-nav-item">
                                <a href="{{route('products.all')}}" class="aiz-side-nav-link">
                                    <span class="aiz-side-nav-text">{{ translate('All Products') }}</span>
                                </a>
                            </li>
                            <li class="aiz-side-nav-item">
                                <a href="{{route('products.admin')}}" class="aiz-side-nav-link {{ areActiveRoutes(['products.admin', 'products.create', 'products.admin.edit']) }}" >
                                    <span class="aiz-side-nav-text">{{ translate('In House Products') }}</span>
                                </a>
                            </li>
                            @if(get_setting('vendor_system_activation') == 1)
                                <li class="aiz-side-nav-item">
                                    <a href="{{route('products.seller')}}" class="aiz-side-nav-link {{ areActiveRoutes(['products.seller', 'products.seller.edit']) }}">
                                        <span class="aiz-side-nav-text">{{ translate('Seller Products') }}</span>
                                    </a>
                                </li>
                            @endif
                            <li class="aiz-side-nav-item">
                                <a href="{{route('digitalproducts.index')}}" class="aiz-side-nav-link {{ areActiveRoutes(['digitalproducts.index', 'digitalproducts.create', 'digitalproducts.edit']) }}">
                                    <span class="aiz-side-nav-text">{{ translate('Digital Products') }}</span>
                                </a>
                            </li>
                            <li class="aiz-side-nav-item">
                                <a href="{{ route('product_bulk_upload.index') }}" class="aiz-side-nav-link" >
                                    <span class="aiz-side-nav-text">{{ translate('Bulk Import') }}</span>
                                </a>
                            </li>
                            <li class="aiz-side-nav-item">
                                <a href="{{route('product_bulk_export.index')}}" class="aiz-side-nav-link">
                                    <span class="aiz-side-nav-text">{{translate('Bulk Export')}}</span>
                                </a>
                            </li>
                            <li class="aiz-side-nav-item">
                                <a href="{{route('categories.index')}}" class="aiz-side-nav-link {{ areActiveRoutes(['categories.index', 'categories.create', 'categories.edit'])}}">
                                    <span class="aiz-side-nav-text">{{translate('Category')}}</span>
                                </a>
                            </li>
                            <li class="aiz-side-nav-item">
                                <a href="{{route('brands.index')}}" class="aiz-side-nav-link {{ areActiveRoutes(['brands.index', 'brands.create', 'brands.edit'])}}" >
                                    <span class="aiz-side-nav-text">{{translate('Brand')}}</span>
                                </a>
                            </li>
                            <li class="aiz-side-nav-item">
                                <a href="{{route('attributes.index')}}" class="aiz-side-nav-link {{ areActiveRoutes(['attributes.index','attributes.create','attributes.edit'])}}">
                                    <span class="aiz-side-nav-text">{{translate('Attribute')}}</span>
                                </a>
                            </li>
                            <li class="aiz-side-nav-item">
                                <a href="{{route('colors')}}" class="aiz-side-nav-link {{ areActiveRoutes(['attributes.index','attributes.create','attributes.edit'])}}">
                                    <span class="aiz-side-nav-text">{{translate('Colors')}}</span>
                                </a>
                            </li>
                            <li class="aiz-side-nav-item">
                                <a href="{{route('reviews.index')}}" class="aiz-side-nav-link">
                                    <span class="aiz-side-nav-text">{{translate('Product Reviews')}}</span>
                                </a>
                            </li>
                        </ul>
                    </li>
                @endif

                <!-- Auction Product -->
                @if(addon_is_activated('auction'))
                    <li class="aiz-side-nav-item">
                        <a href="#" class="aiz-side-nav-link">
                            <i class="las la-gavel aiz-side-nav-icon"></i>
                            <span class="aiz-side-nav-text">{{translate('Auction Products')}}</span>
                            @if (env("DEMO_MODE") == "On")
                                <span class="badge badge-inline badge-danger">Addon</span>
                            @endif
                            <span class="aiz-side-nav-arrow"></span>
                        </a>
                        <!--Submenu-->
                        <ul class="aiz-side-nav-list level-2">
                            <li class="aiz-side-nav-item">
                                <a class="aiz-side-nav-link" href="{{route('auction_products.create')}}">
                                    <span class="aiz-side-nav-text">{{translate('Add New auction product')}}</span>
                                </a>
                            </li>
                            <li class="aiz-side-nav-item">
                                <a href="{{route('auction.all_products')}}" class="aiz-side-nav-link {{ areActiveRoutes(['auction_products.edit','product_bids.show']) }}">
                                    <span class="aiz-side-nav-text">{{ translate('All Auction Products') }}</span>
                                </a>
                            </li>
                            <li class="aiz-side-nav-item">
                                <a href="{{route('auction.inhouse_products')}}" class="aiz-side-nav-link">
                                    <span class="aiz-side-nav-text">{{ translate('Inhouse Auction Products') }}</span>
                                </a>
                            </li>
                            <li class="aiz-side-nav-item">
                                <a href="{{route('auction.seller_products')}}" class="aiz-side-nav-link">
                                    <span class="aiz-side-nav-text">{{ translate('Seller Auction Products') }}</span>
                                </a>
                            </li>
                            <li class="aiz-side-nav-item">
                                <a href="{{route('auction_products_orders')}}" class="aiz-side-nav-link {{ areActiveRoutes(['auction_products_orders.index']) }}">
                                    <span class="aiz-side-nav-text">{{ translate('Auction Products Orders') }}</span>
                                </a>
                            </li>
                        </ul>
                    </li>
                @endif

                <!-- Wholesale Product -->
                @if(addon_is_activated('wholesale'))
                    <li class="aiz-side-nav-item">
                        <a href="#" class="aiz-side-nav-link">
                            <i class="las la-luggage-cart aiz-side-nav-icon"></i>
                            <span class="aiz-side-nav-text">{{translate('Wholesale Products')}}</span>
                            @if (env("DEMO_MODE") == "On")
                                <span class="badge badge-inline badge-danger">Addon</span>
                            @endif
                            <span class="aiz-side-nav-arrow"></span>
                        </a>
                        <!--Submenu-->
                        <ul class="aiz-side-nav-list level-2">
                            <li class="aiz-side-nav-item">
                                <a class="aiz-side-nav-link" href="{{route('wholesale-products.create')}}">
                                    <span class="aiz-side-nav-text">{{translate('Add new wholesale product')}}</span>
                                </a>
                            </li>
                            <li class="aiz-side-nav-item">
                                <a href="{{route('wholesale-products.index')}}" class="aiz-side-nav-link {{ areActiveRoutes(['wholesale-products.edit','wholesale-products.show']) }}">
                                    <span class="aiz-side-nav-text">{{ translate('All wholesale products') }}</span>
                                </a>
                            </li>
                        </ul>
                    </li>
                @endif

                <!-- Sale -->
                <li class="aiz-side-nav-item">
                    <a href="#" class="aiz-side-nav-link">
                        <i class="las la-money-bill aiz-side-nav-icon"></i>
                        <span class="aiz-side-nav-text">{{translate('Sales')}}</span>
                        <span class="aiz-side-nav-arrow"></span>
                    </a>
                    <!--Submenu-->
                    <ul class="aiz-side-nav-list level-2">
                        @if(Auth::user()->user_type == 'admin' || in_array('3', json_decode(Auth::user()->staff->role->permissions)))
                            <li class="aiz-side-nav-item">
                                <a href="{{ route('all_orders.index') }}" class="aiz-side-nav-link {{ areActiveRoutes(['all_orders.index', 'all_orders.show'])}}">
                                    <span class="aiz-side-nav-text">{{translate('All Orders')}}</span>
                                </a>
                            </li>
                        @endif

                        @if(Auth::user()->user_type == 'admin' || in_array('4', json_decode(Auth::user()->staff->role->permissions)))
                            <li class="aiz-side-nav-item">
                                <a href="{{ route('inhouse_orders.index') }}" class="aiz-side-nav-link {{ areActiveRoutes(['inhouse_orders.index', 'inhouse_orders.show'])}}" >
                                    <span class="aiz-side-nav-text">{{translate('Inhouse orders')}}</span>
                                </a>
                            </li>
                        @endif
                        @if(Auth::user()->user_type == 'admin' || in_array('5', json_decode(Auth::user()->staff->role->permissions)))
                            <li class="aiz-side-nav-item">
                                <a href="{{ route('seller_orders.index') }}" class="aiz-side-nav-link {{ areActiveRoutes(['seller_orders.index', 'seller_orders.show'])}}">
                                    <span class="aiz-side-nav-text">{{translate('Seller Orders')}}</span>
                                </a>
                            </li>
                        @endif
                        @if(Auth::user()->user_type == 'admin' || in_array('6', json_decode(Auth::user()->staff->role->permissions)))
                            <li class="aiz-side-nav-item">
                                <a href="{{ route('pick_up_point.order_index') }}" class="aiz-side-nav-link {{ areActiveRoutes(['pick_up_point.order_index','pick_up_point.order_show'])}}">
                                    <span class="aiz-side-nav-text">{{translate('Pick-up Point Order')}}</span>
                                </a>
                            </li>
                        @endif
                    </ul>
                </li>

                <!-- Deliver Boy Addon-->
                @if (addon_is_activated('delivery_boy'))
                    @if(Auth::user()->user_type == 'admin' || in_array('1', json_decode(Auth::user()->staff->role->permissions)))
                        <li class="aiz-side-nav-item">
                            <a href="#" class="aiz-side-nav-link">
                                <i class="las la-truck aiz-side-nav-icon"></i>
                                <span class="aiz-side-nav-text">{{translate('Delivery Boy')}}</span>
                                @if (env("DEMO_MODE") == "On")
                                    <span class="badge badge-inline badge-danger">Addon</span>
                                @endif
                                <span class="aiz-side-nav-arrow"></span>
                            </a>
                            <ul class="aiz-side-nav-list level-2">
                                <li class="aiz-side-nav-item">
                                    <a href="{{route('delivery-boys.index')}}" class="aiz-side-nav-link">
                                        <span class="aiz-side-nav-text">{{translate('All Delivery Boy')}}</span>
                                    </a>
                                </li>
                                <li class="aiz-side-nav-item">
                                    <a href="{{route('delivery-boys.create')}}" class="aiz-side-nav-link">
                                        <span class="aiz-side-nav-text">{{translate('Add Delivery Boy')}}</span>
                                    </a>
                                </li>
                                <li class="aiz-side-nav-item">
                                    <a href="{{route('delivery-boys-payment-histories')}}" class="aiz-side-nav-link">
                                        <span class="aiz-side-nav-text">{{translate('Payment Histories')}}</span>
                                    </a>
                                </li>
                                <li class="aiz-side-nav-item">
                                    <a href="{{route('delivery-boys-collection-histories')}}" class="aiz-side-nav-link">
                                        <span class="aiz-side-nav-text">{{translate('Collected Histories')}}</span>
                                    </a>
                                </li>
                                <li class="aiz-side-nav-item">
                                    <a href="{{route('delivery-boy.cancel-request')}}" class="aiz-side-nav-link">
                                        <span class="aiz-side-nav-text">{{translate('Cancel Request')}}</span>
                                    </a>
                                </li>
                                <li class="aiz-side-nav-item">
                                    <a href="{{route('delivery-boy-configuration')}}" class="aiz-side-nav-link">
                                        <span class="aiz-side-nav-text">{{translate('Configuration')}}</span>
                                    </a>
                                </li>
                            </ul>
                        </li>
                    @endif
                @endif

            <!-- Refund addon -->
                @if (addon_is_activated('refund_request'))
                    @if(Auth::user()->user_type == 'admin' || in_array('7', json_decode(Auth::user()->staff->role->permissions)))
                        <li class="aiz-side-nav-item">
                            <a href="#" class="aiz-side-nav-link">
                                <i class="las la-backward aiz-side-nav-icon"></i>
                                <span class="aiz-side-nav-text">{{ translate('Refunds') }}</span>
                                @if (env("DEMO_MODE") == "On")
                                    <span class="badge badge-inline badge-danger">Addon</span>
                                @endif
                                <span class="aiz-side-nav-arrow"></span>
                            </a>
                            <ul class="aiz-side-nav-list level-2">
                                <li class="aiz-side-nav-item">
                                    <a href="{{route('refund_requests_all')}}" class="aiz-side-nav-link {{ areActiveRoutes(['refund_requests_all', 'reason_show'])}}">
                                        <span class="aiz-side-nav-text">{{translate('Refund Requests')}}</span>
                                    </a>
                                </li>
                                <li class="aiz-side-nav-item">
                                    <a href="{{route('paid_refund')}}" class="aiz-side-nav-link">
                                        <span class="aiz-side-nav-text">{{translate('Approved Refunds')}}</span>
                                    </a>
                                </li>
                                <li class="aiz-side-nav-item">
                                    <a href="{{route('rejected_refund')}}" class="aiz-side-nav-link">
                                        <span class="aiz-side-nav-text">{{translate('rejected Refunds')}}</span>
                                    </a>
                                </li>
                                <li class="aiz-side-nav-item">
                                    <a href="{{route('refund_time_config')}}" class="aiz-side-nav-link">
                                        <span class="aiz-side-nav-text">{{translate('Refund Configuration')}}</span>
                                    </a>
                                </li>
                            </ul>
                        </li>
                    @endif
                @endif


            <!-- Customers -->
                @if(Auth::user()->user_type == 'admin' || in_array('8', json_decode(Auth::user()->staff->role->permissions)))
                    <li class="aiz-side-nav-item">
                        <a href="#" class="aiz-side-nav-link">
                            <i class="las la-user-friends aiz-side-nav-icon"></i>
                            <span class="aiz-side-nav-text">{{ translate('Customers') }}</span>
                            <span class="aiz-side-nav-arrow"></span>
                        </a>
                        <ul class="aiz-side-nav-list level-2">
                            <li class="aiz-side-nav-item">
                                <a href="{{ route('customers.index') }}" class="aiz-side-nav-link">
                                    <span class="aiz-side-nav-text">{{ translate('Customer list') }}</span>
                                </a>
                            </li>
                            @if(get_setting('classified_product') == 1)
                                <li class="aiz-side-nav-item">
                                    <a href="{{route('classified_products')}}" class="aiz-side-nav-link">
                                        <span class="aiz-side-nav-text">{{translate('Classified Products')}}</span>
                                    </a>
                                </li>
                                <li class="aiz-side-nav-item">
                                    <a href="{{ route('customer_packages.index') }}" class="aiz-side-nav-link {{ areActiveRoutes(['customer_packages.index', 'customer_packages.create', 'customer_packages.edit'])}}">
                                        <span class="aiz-side-nav-text">{{ translate('Classified Packages') }}</span>
                                    </a>
                                </li>
                            @endif
                        </ul>
                    </li>
                @endif

            <!-- Sellers -->
                @if((Auth::user()->user_type == 'admin' || in_array('9', json_decode(Auth::user()->staff->role->permissions))) && get_setting('vendor_system_activation') == 1)
                    <li class="aiz-side-nav-item">
                        <a href="#" class="aiz-side-nav-link">
                            <i class="las la-user aiz-side-nav-icon"></i>
                            <span class="aiz-side-nav-text">{{ translate('Sellers') }}</span>
                            <span class="aiz-side-nav-arrow"></span>
                        </a>
                        <ul class="aiz-side-nav-list level-2">
                            <li class="aiz-side-nav-item">
                                @php
                                    $sellers = \App\Models\Seller::where('verification_status', 0)->where('verification_info', '!=', null)->count();
                                @endphp
                                <a href="{{ route('sellers.index') }}" class="aiz-side-nav-link {{ areActiveRoutes(['sellers.index', 'sellers.create', 'sellers.edit', 'sellers.payment_history','sellers.approved','sellers.profile_modal','sellers.show_verification_request'])}}">
                                    <span class="aiz-side-nav-text">{{ translate('All Seller') }}</span>
                                    @if($sellers > 0)<span class="badge badge-info">{{ $sellers }}</span> @endif
                                </a>
                            </li>
                            <li class="aiz-side-nav-item">
                                <a href="{{ route('sellers.payment_histories') }}" class="aiz-side-nav-link">
                                    <span class="aiz-side-nav-text">{{ translate('Payouts') }}</span>
                                </a>
                            </li>
                            <li class="aiz-side-nav-item">
                                <a href="{{ route('withdraw_requests_all') }}" class="aiz-side-nav-link">
                                    <span class="aiz-side-nav-text">{{ translate('Payout Requests') }}</span>
                                </a>
                            </li>
                            <li class="aiz-side-nav-item">
                                <a href="{{ route('business_settings.vendor_commission') }}" class="aiz-side-nav-link">
                                    <span class="aiz-side-nav-text">{{ translate('Seller Commission') }}</span>
                                </a>
                            </li>

                            @if (addon_is_activated('seller_subscription'))
                                <li class="aiz-side-nav-item">
                                    <a href="{{ route('seller_packages.index') }}" class="aiz-side-nav-link {{ areActiveRoutes(['seller_packages.index', 'seller_packages.create', 'seller_packages.edit'])}}">
                                        <span class="aiz-side-nav-text">{{ translate('Seller Packages') }}</span>
                                        @if (env("DEMO_MODE") == "On")
                                            <span class="badge badge-inline badge-danger">Addon</span>
                                        @endif
                                    </a>
                                </li>
                            @endif
                            <li class="aiz-side-nav-item">
                                <a href="{{ route('seller_verification_form.index') }}" class="aiz-side-nav-link">
                                    <span class="aiz-side-nav-text">{{ translate('Seller Verification Form') }}</span>
                                </a>
                            </li>
                        </ul>
                    </li>
                @endif
                @if(Auth::user()->user_type == 'admin' || in_array('22', json_decode(Auth::user()->staff->role->permissions)))
                    <li class="aiz-side-nav-item">
                        <a href="{{ route('uploaded-files.index') }}" class="aiz-side-nav-link {{ areActiveRoutes(['uploaded-files.create'])}}">
                            <i class="las la-folder-open aiz-side-nav-icon"></i>
                            <span class="aiz-side-nav-text">{{ translate('Uploaded Files') }}</span>
                        </a>
                    </li>
                @endif
            <!-- Reports -->
                @if(Auth::user()->user_type == 'admin' || in_array('10', json_decode(Auth::user()->staff->role->permissions)))
                    <li class="aiz-side-nav-item">
                        <a href="#" class="aiz-side-nav-link">
                            <i class="las la-file-alt aiz-side-nav-icon"></i>
                            <span class="aiz-side-nav-text">{{ translate('Reports') }}</span>
                            <span class="aiz-side-nav-arrow"></span>
                        </a>
                        <ul class="aiz-side-nav-list level-2">
                            <li class="aiz-side-nav-item">
                                <a href="{{ route('in_house_sale_report.index') }}" class="aiz-side-nav-link {{ areActiveRoutes(['in_house_sale_report.index'])}}">
                                    <span class="aiz-side-nav-text">{{ translate('In House Product Sale') }}</span>
                                </a>
                            </li>
                            <li class="aiz-side-nav-item">
                                <a href="{{ route('seller_sale_report.index') }}" class="aiz-side-nav-link {{ areActiveRoutes(['seller_sale_report.index'])}}">
                                    <span class="aiz-side-nav-text">{{ translate('Seller Products Sale') }}</span>
                                </a>
                            </li>
                            <li class="aiz-side-nav-item">
                                <a href="{{ route('stock_report.index') }}" class="aiz-side-nav-link {{ areActiveRoutes(['stock_report.index'])}}">
                                    <span class="aiz-side-nav-text">{{ translate('Products Stock') }}</span>
                                </a>
                            </li>
                            <li class="aiz-side-nav-item">
                                <a href="{{ route('wish_report.index') }}" class="aiz-side-nav-link {{ areActiveRoutes(['wish_report.index'])}}">
                                    <span class="aiz-side-nav-text">{{ translate('Products wishlist') }}</span>
                                </a>
                            </li>
                            <li class="aiz-side-nav-item">
                                <a href="{{ route('user_search_report.index') }}" class="aiz-side-nav-link {{ areActiveRoutes(['user_search_report.index'])}}">
                                    <span class="aiz-side-nav-text">{{ translate('User Searches') }}</span>
                                </a>
                            </li>
                            <li class="aiz-side-nav-item">
                                <a href="{{ route('commission-log.index') }}" class="aiz-side-nav-link">
                                    <span class="aiz-side-nav-text">{{ translate('Commission History') }}</span>
                                </a>
                            </li>
                            <li class="aiz-side-nav-item">
                                <a href="{{ route('wallet-history.index') }}" class="aiz-side-nav-link">
                                    <span class="aiz-side-nav-text">{{ translate('Wallet Recharge History') }}</span>
                                </a>
                            </li>
                        </ul>
                    </li>
                @endif
                @if(Auth::user()->user_type == 'admin' || in_array('23', json_decode(Auth::user()->staff->role->permissions)))
                <!--Blog System-->
                    <li class="aiz-side-nav-item">
                        <a href="#" class="aiz-side-nav-link">
                            <i class="las la-bullhorn aiz-side-nav-icon"></i>
                            <span class="aiz-side-nav-text">{{ translate('Blog System') }}</span>
                            <span class="aiz-side-nav-arrow"></span>
                        </a>
                        <ul class="aiz-side-nav-list level-2">
                            <li class="aiz-side-nav-item">
                                <a href="{{ route('blog.index') }}" class="aiz-side-nav-link {{ areActiveRoutes(['blog.create', 'blog.edit'])}}">
                                    <span class="aiz-side-nav-text">{{ translate('All Posts') }}</span>
                                </a>
                            </li>
                            <li class="aiz-side-nav-item">
                                <a href="{{ route('blog-category.index') }}" class="aiz-side-nav-link {{ areActiveRoutes(['blog-category.create', 'blog-category.edit'])}}">
                                    <span class="aiz-side-nav-text">{{ translate('Categories') }}</span>
                                </a>
                            </li>
                        </ul>
                    </li>
                @endif

            <!-- marketing -->
                @if(Auth::user()->user_type == 'admin' || in_array('11', json_decode(Auth::user()->staff->role->permissions)))
                    <li class="aiz-side-nav-item">
                        <a href="#" class="aiz-side-nav-link">
                            <i class="las la-bullhorn aiz-side-nav-icon"></i>
                            <span class="aiz-side-nav-text">{{ translate('Marketing') }}</span>
                            <span class="aiz-side-nav-arrow"></span>
                        </a>
                        <ul class="aiz-side-nav-list level-2">
                            @if(Auth::user()->user_type == 'admin' || in_array('2', json_decode(Auth::user()->staff->role->permissions)))
                                <li class="aiz-side-nav-item">
                                    <a href="{{ route('flash_deals.index') }}" class="aiz-side-nav-link {{ areActiveRoutes(['flash_deals.index', 'flash_deals.create', 'flash_deals.edit'])}}">
                                        <span class="aiz-side-nav-text">{{ translate('Flash deals') }}</span>
                                    </a>
                                </li>
                            @endif
                            @if(Auth::user()->user_type == 'admin' || in_array('7', json_decode(Auth::user()->staff->role->permissions)))
                                <li class="aiz-side-nav-item">
                                    <a href="{{route('newsletters.index')}}" class="aiz-side-nav-link">
                                        <span class="aiz-side-nav-text">{{ translate('Newsletters') }}</span>
                                    </a>
                                </li>
                                @if (addon_is_activated('otp_system'))
                                    <li class="aiz-side-nav-item">
                                        <a href="{{route('sms.index')}}" class="aiz-side-nav-link">
                                            <span class="aiz-side-nav-text">{{ translate('Bulk SMS') }}</span>
                                            @if (env("DEMO_MODE") == "On")
                                                <span class="badge badge-inline badge-danger">Addon</span>
                                            @endif
                                        </a>
                                    </li>
                                @endif
                            @endif
                            <li class="aiz-side-nav-item">
                                <a href="{{ route('subscribers.index') }}" class="aiz-side-nav-link">
                                    <span class="aiz-side-nav-text">{{ translate('Subscribers') }}</span>
                                </a>
                            </li>
                            <li class="aiz-side-nav-item">
                                <a href="{{route('coupon.index')}}" class="aiz-side-nav-link {{ areActiveRoutes(['coupon.index','coupon.create','coupon.edit'])}}">
                                    <span class="aiz-side-nav-text">{{ translate('Coupon') }}</span>
                                </a>
                            </li>
                        </ul>
                    </li>
                @endif

            <!-- Support -->
                @if(Auth::user()->user_type == 'admin' || in_array('12', json_decode(Auth::user()->staff->role->permissions)))
                    <li class="aiz-side-nav-item">
                        <a href="#" class="aiz-side-nav-link">
                            <i class="las la-link aiz-side-nav-icon"></i>
                            <span class="aiz-side-nav-text">{{translate('Support')}}</span>
                            <span class="aiz-side-nav-arrow"></span>
                        </a>
                        <ul class="aiz-side-nav-list level-2">
                            @if(Auth::user()->user_type == 'admin' || in_array('12', json_decode(Auth::user()->staff->role->permissions)))
                                @php
                                    $support_ticket = DB::table('tickets')
                                                ->where('viewed', 0)
                                                ->select('id')
                                                ->count();
                                @endphp
                                <li class="aiz-side-nav-item">
                                    <a href="{{ route('support_ticket.admin_index') }}" class="aiz-side-nav-link {{ areActiveRoutes(['support_ticket.admin_index', 'support_ticket.admin_show'])}}">
                                        <span class="aiz-side-nav-text">{{translate('Ticket')}}</span>
                                        @if($support_ticket > 0)<span class="badge badge-info">{{ $support_ticket }}</span>@endif
                                    </a>
                                </li>
                            @endif

                            @php
                                $conversation = \App\Models\Conversation::where('receiver_id', Auth::user()->id)->where('receiver_viewed', '1')->get();
                            @endphp
                            @if(Auth::user()->user_type == 'admin' || in_array('12', json_decode(Auth::user()->staff->role->permissions)))
                                <li class="aiz-side-nav-item">
                                    <a href="{{ route('conversations.admin_index') }}" class="aiz-side-nav-link {{ areActiveRoutes(['conversations.admin_index', 'conversations.admin_show'])}}">
                                        <span class="aiz-side-nav-text">{{translate('Product Queries')}}</span>
                                        @if (count($conversation) > 0)
                                            <span class="badge badge-info">{{ count($conversation) }}</span>
                                        @endif
                                    </a>
                                </li>
                            @endif
                        </ul>
                    </li>
                @endif

            <!-- Affiliate Addon -->
                @if (addon_is_activated('affiliate_system'))
                    @if(Auth::user()->user_type == 'admin' || in_array('15', json_decode(Auth::user()->staff->role->permissions)))
                        <li class="aiz-side-nav-item">
                            <a href="#" class="aiz-side-nav-link">
                                <i class="las la-link aiz-side-nav-icon"></i>
                                <span class="aiz-side-nav-text">{{translate('Affiliate System')}}</span>
                                @if (env("DEMO_MODE") == "On")
                                    <span class="badge badge-inline badge-danger">Addon</span>
                                @endif
                                <span class="aiz-side-nav-arrow"></span>
                            </a>
                            <ul class="aiz-side-nav-list level-2">
                                <li class="aiz-side-nav-item">
                                    <a href="{{route('affiliate.configs')}}" class="aiz-side-nav-link">
                                        <span class="aiz-side-nav-text">{{translate('Affiliate Registration Form')}}</span>
                                    </a>
                                </li>
                                <li class="aiz-side-nav-item">
                                    <a href="{{route('affiliate.index')}}" class="aiz-side-nav-link">
                                        <span class="aiz-side-nav-text">{{translate('Affiliate Configurations')}}</span>
                                    </a>
                                </li>
                                <li class="aiz-side-nav-item">
                                    <a href="{{route('affiliate.users')}}" class="aiz-side-nav-link {{ areActiveRoutes(['affiliate.users', 'affiliate_users.show_verification_request', 'affiliate_user.payment_history'])}}">
                                        <span class="aiz-side-nav-text">{{translate('Affiliate Users')}}</span>
                                    </a>
                                </li>
                                <li class="aiz-side-nav-item">
                                    <a href="{{route('refferals.users')}}" class="aiz-side-nav-link">
                                        <span class="aiz-side-nav-text">{{translate('Referral Users')}}</span>
                                    </a>
                                </li>
                                <li class="aiz-side-nav-item">
                                    <a href="{{route('affiliate.withdraw_requests')}}" class="aiz-side-nav-link">
                                        <span class="aiz-side-nav-text">{{translate('Affiliate Withdraw Requests')}}</span>
                                    </a>
                                </li>
                                <li class="aiz-side-nav-item">
                                    <a href="{{route('affiliate.logs.admin')}}" class="aiz-side-nav-link">
                                        <span class="aiz-side-nav-text">{{translate('Affiliate Logs')}}</span>
                                    </a>
                                </li>
                            </ul>
                        </li>
                    @endif
                @endif

            <!-- Offline Payment Addon-->
                @if (addon_is_activated('offline_payment'))
                    @if(Auth::user()->user_type == 'admin' || in_array('16', json_decode(Auth::user()->staff->role->permissions)))
                        <li class="aiz-side-nav-item">
                            <a href="#" class="aiz-side-nav-link">
                                <i class="las la-money-check-alt aiz-side-nav-icon"></i>
                                <span class="aiz-side-nav-text">{{translate('Offline Payment System')}}</span>
                                @if (env("DEMO_MODE") == "On")
                                    <span class="badge badge-inline badge-danger">Addon</span>
                                @endif
                                <span class="aiz-side-nav-arrow"></span>
                            </a>
                            <ul class="aiz-side-nav-list level-2">
                                <li class="aiz-side-nav-item">
                                    <a href="{{ route('manual_payment_methods.index') }}" class="aiz-side-nav-link {{ areActiveRoutes(['manual_payment_methods.index', 'manual_payment_methods.create', 'manual_payment_methods.edit'])}}">
                                        <span class="aiz-side-nav-text">{{translate('Manual Payment Methods')}}</span>
                                    </a>
                                </li>
                                <li class="aiz-side-nav-item">
                                    <a href="{{ route('offline_wallet_recharge_request.index') }}" class="aiz-side-nav-link">
                                        <span class="aiz-side-nav-text">{{translate('Offline Wallet Recharge')}}</span>
                                    </a>
                                </li>
                                @if(get_setting('classified_product') == 1)
                                    <li class="aiz-side-nav-item">
                                        <a href="{{ route('offline_customer_package_payment_request.index') }}" class="aiz-side-nav-link">
                                            <span class="aiz-side-nav-text">{{translate('Offline Customer Package Payments')}}</span>
                                        </a>
                                    </li>
                                @endif
                                @if (addon_is_activated('seller_subscription'))
                                    <li class="aiz-side-nav-item">
                                        <a href="{{ route('offline_seller_package_payment_request.index') }}" class="aiz-side-nav-link">
                                            <span class="aiz-side-nav-text">{{translate('Offline Seller Package Payments')}}</span>
                                            @if (env("DEMO_MODE") == "On")
                                                <span class="badge badge-inline badge-danger">Addon</span>
                                            @endif
                                        </a>
                                    </li>
                                @endif
                            </ul>
                        </li>
                    @endif
                @endif

            <!-- Paytm Addon -->
                @if (addon_is_activated('paytm'))
                    @if(Auth::user()->user_type == 'admin' || in_array('17', json_decode(Auth::user()->staff->role->permissions)))
                        <li class="aiz-side-nav-item">
                            <a href="#" class="aiz-side-nav-link">
                                <i class="las la-mobile-alt aiz-side-nav-icon"></i>
                                <span class="aiz-side-nav-text">{{translate('Paytm Payment Gateway')}}</span>
                                @if (env("DEMO_MODE") == "On")
                                    <span class="badge badge-inline badge-danger">Addon</span>
                                @endif
                                <span class="aiz-side-nav-arrow"></span>
                            </a>
                            <ul class="aiz-side-nav-list level-2">
                                <li class="aiz-side-nav-item">
                                    <a href="{{ route('paytm.index') }}" class="aiz-side-nav-link">
                                        <span class="aiz-side-nav-text">{{translate('Set Paytm Credentials')}}</span>
                                    </a>
                                </li>
                            </ul>
                        </li>
                    @endif
                @endif

            <!-- Club Point Addon-->
                @if (addon_is_activated('club_point'))
                    @if(Auth::user()->user_type == 'admin' || in_array('18', json_decode(Auth::user()->staff->role->permissions)))
                        <li class="aiz-side-nav-item">
                            <a href="#" class="aiz-side-nav-link">
                                <i class="lab la-btc aiz-side-nav-icon"></i>
                                <span class="aiz-side-nav-text">{{translate('Club Point System')}}</span>
                                @if (env("DEMO_MODE") == "On")
                                    <span class="badge badge-inline badge-danger">Addon</span>
                                @endif
                                <span class="aiz-side-nav-arrow"></span>
                            </a>
                            <ul class="aiz-side-nav-list level-2">
                                <li class="aiz-side-nav-item">
                                    <a href="{{ route('club_points.configs') }}" class="aiz-side-nav-link">
                                        <span class="aiz-side-nav-text">{{translate('Club Point Configurations')}}</span>
                                    </a>
                                </li>
                                <li class="aiz-side-nav-item">
                                    <a href="{{route('set_product_points')}}" class="aiz-side-nav-link {{ areActiveRoutes(['set_product_points', 'product_club_point.edit'])}}">
                                        <span class="aiz-side-nav-text">{{translate('Set Product Point')}}</span>
                                    </a>
                                </li>
                                <li class="aiz-side-nav-item">
                                    <a href="{{route('club_points.index')}}" class="aiz-side-nav-link {{ areActiveRoutes(['club_points.index', 'club_point.details'])}}">
                                        <span class="aiz-side-nav-text">{{translate('User Points')}}</span>
                                    </a>
                                </li>
                            </ul>
                        </li>
                    @endif
                @endif

            <!--OTP addon -->
                @if (addon_is_activated('otp_system'))
                    @if(Auth::user()->user_type == 'admin' || in_array('19', json_decode(Auth::user()->staff->role->permissions)))
                        <li class="aiz-side-nav-item">
                            <a href="#" class="aiz-side-nav-link">
                                <i class="las la-phone aiz-side-nav-icon"></i>
                                <span class="aiz-side-nav-text">{{translate('OTP System')}}</span>
                                @if (env("DEMO_MODE") == "On")
                                    <span class="badge badge-inline badge-danger">Addon</span>
                                @endif
                                <span class="aiz-side-nav-arrow"></span>
                            </a>
                            <ul class="aiz-side-nav-list level-2">
                                <li class="aiz-side-nav-item">
                                    <a href="{{ route('otp.configconfiguration') }}" class="aiz-side-nav-link">
                                        <span class="aiz-side-nav-text">{{translate('OTP Configurations')}}</span>
                                    </a>
                                </li>
                                <li class="aiz-side-nav-item">
                                    <a href="{{route('sms-templates.index')}}" class="aiz-side-nav-link">
                                        <span class="aiz-side-nav-text">{{translate('SMS Templates')}}</span>
                                    </a>
                                </li>
                                <li class="aiz-side-nav-item">
                                    <a href="{{route('otp_credentials.index')}}" class="aiz-side-nav-link">
                                        <span class="aiz-side-nav-text">{{translate('Set OTP Credentials')}}</span>
                                    </a>
                                </li>
                            </ul>
                        </li>
                    @endif
                @endif

                @if(addon_is_activated('african_pg'))
                    @if(Auth::user()->user_type == 'admin' || in_array('19', json_decode(Auth::user()->staff->role->permissions)))
                        <li class="aiz-side-nav-item">
                            <a href="#" class="aiz-side-nav-link">
                                <i class="las la-phone aiz-side-nav-icon"></i>
                                <span class="aiz-side-nav-text">{{translate('African Payment Gateway Addon')}}</span>
                                @if (env("DEMO_MODE") == "On")
                                    <span class="badge badge-inline badge-danger">Addon</span>
                                @endif
                                <span class="aiz-side-nav-arrow"></span>
                            </a>
                            <ul class="aiz-side-nav-list level-2">
                                <li class="aiz-side-nav-item">
                                    <a href="{{ route('african.configuration') }}" class="aiz-side-nav-link">
                                        <span class="aiz-side-nav-text">{{translate('African PG Configurations')}}</span>
                                    </a>
                                </li>
                                <li class="aiz-side-nav-item">
                                    <a href="{{route('african_credentials.index')}}" class="aiz-side-nav-link">
                                        <span class="aiz-side-nav-text">{{translate('Set African PG Credentials')}}</span>
                                    </a>
                                </li>
                            </ul>
                        </li>
                    @endif
                @endif

            <!-- Website Setup -->
                @if(Auth::user()->user_type == 'admin' || in_array('13', json_decode(Auth::user()->staff->role->permissions)))
                    <li class="aiz-side-nav-item">
                        <a href="#" class="aiz-side-nav-link {{ areActiveRoutes(['website.footer', 'website.header', 'website.appearance', 'website.pages', 'sliders.admin.index'])}}" >
                            <i class="las la-desktop aiz-side-nav-icon"></i>
                            <span class="aiz-side-nav-text">{{translate('Website Setup')}}</span>
                            <span class="aiz-side-nav-arrow"></span>
                        </a>
                        <ul class="aiz-side-nav-list level-2">
                            <li class="aiz-side-nav-item">
                                <a href="{{ route('website.header') }}" class="aiz-side-nav-link">
                                    <span class="aiz-side-nav-text">{{translate('Header')}}</span>
                                </a>
                            </li>
                            <li class="aiz-side-nav-item">
                                <a href="{{ route('website.footer', ['lang'=>  App::getLocale()] ) }}" class="aiz-side-nav-link {{ areActiveRoutes(['website.footer'])}}">
                                    <span class="aiz-side-nav-text">{{translate('Footer')}}</span>
                                </a>
                            </li>
                            <li class="aiz-side-nav-item">
                                <a href="{{ route('website.pages') }}" class="aiz-side-nav-link {{ areActiveRoutes(['website.pages', 'custom-pages.create' ,'custom-pages.edit'])}}">
                                    <span class="aiz-side-nav-text">{{translate('Pages')}}</span>
                                </a>
                            </li>
                            <li class="aiz-side-nav-item">
                                <a href="{{ route('website.appearance') }}" class="aiz-side-nav-link">
                                    <span class="aiz-side-nav-text">{{translate('Appearance')}}</span>
                                </a>
                            </li>
                            <li class="aiz-side-nav-item">
                                <a href="{{ route('sliders.admin.index') }}" class="aiz-side-nav-link {{ areActiveRoutes(['sliders.admin.index']) }}">
                                    <span class="aiz-side-nav-text">{{translate('Sliders')}}</span>
                                </a>
                            </li>
                        </ul>

                    </li>
                @endif

            <!-- Setup & Configurations -->
                @if(Auth::user()->user_type == 'admin' || in_array('14', json_decode(Auth::user()->staff->role->permissions)))
                    <li class="aiz-side-nav-item">
                        <a href="#" class="aiz-side-nav-link">
                            <i class="las la-dharmachakra aiz-side-nav-icon"></i>
                            <span class="aiz-side-nav-text">{{translate('Setup & Configurations')}}</span>
                            <span class="aiz-side-nav-arrow"></span>
                        </a>
                        <ul class="aiz-side-nav-list level-2">
                            <li class="aiz-side-nav-item">
                                <a href="{{route('general_setting.index')}}" class="aiz-side-nav-link">
                                    <span class="aiz-side-nav-text">{{translate('General Settings')}}</span>
                                </a>
                            </li>

                            <li class="aiz-side-nav-item">
                                <a href="{{route('storefront_info.index')}}" class="aiz-side-nav-link">
                                    <span class="aiz-side-nav-text">{{translate('Storefront Info')}}</span>
                                </a>
                            </li>

                            <li class="aiz-side-nav-item">
                                <a href="{{route('delivery_info.index')}}" class="aiz-side-nav-link">
                                    <i class="las la-truck aiz-side-nav-icon"></i>
                                    <span class="aiz-side-nav-text">{{translate('Delivery Info')}}</span>
                                </a>
                            </li>

                            <li class="aiz-side-nav-item">
                                <a href="{{route('activation.index')}}" class="aiz-side-nav-link">
                                    <span class="aiz-side-nav-text">{{translate('Features activation')}}</span>
                                </a>
                            </li>
                            <li class="aiz-side-nav-item">
                                <a href="{{route('languages.index')}}" class="aiz-side-nav-link {{ areActiveRoutes(['languages.index', 'languages.create', 'languages.store', 'languages.show', 'languages.edit'])}}">
                                    <span class="aiz-side-nav-text">{{translate('Languages')}}</span>
                                </a>
                            </li>

                            <li class="aiz-side-nav-item">
                                <a href="{{route('currency.index')}}" class="aiz-side-nav-link">
                                    <span class="aiz-side-nav-text">{{translate('Currency')}}</span>
                                </a>
                            </li>
                            <li class="aiz-side-nav-item">
                                <a href="{{route('tax.index')}}" class="aiz-side-nav-link {{ areActiveRoutes(['tax.index', 'tax.create', 'tax.store', 'tax.show', 'tax.edit'])}}">
                                    <span class="aiz-side-nav-text">{{translate('Vat & TAX')}}</span>
                                </a>
                            </li>
                            <li class="aiz-side-nav-item">
                                <a href="{{route('pick_up_points.index')}}" class="aiz-side-nav-link {{ areActiveRoutes(['pick_up_points.index','pick_up_points.create','pick_up_points.edit'])}}">
                                    <span class="aiz-side-nav-text">{{translate('Pickup point')}}</span>
                                </a>
                            </li>
                            <li class="aiz-side-nav-item">
                                <a href="{{ route('smtp_settings.index') }}" class="aiz-side-nav-link">
                                    <span class="aiz-side-nav-text">{{translate('SMTP Settings')}}</span>
                                </a>
                            </li>
                            <li class="aiz-side-nav-item">
                                <a href="{{ route('payment_method.index') }}" class="aiz-side-nav-link">
                                    <span class="aiz-side-nav-text">{{translate('Payment Methods')}}</span>
                                </a>
                            </li>
                            <li class="aiz-side-nav-item">
                                <a href="{{ route('file_system.index') }}" class="aiz-side-nav-link">
                                    <span class="aiz-side-nav-text">{{translate('File System & Cache Configuration')}}</span>
                                </a>
                            </li>
                            <li class="aiz-side-nav-item">
                                <a href="{{ route('social_login.index') }}" class="aiz-side-nav-link">
                                    <span class="aiz-side-nav-text">{{translate('Social media Logins')}}</span>
                                </a>
                            </li>

                            <li class="aiz-side-nav-item">
                                <a href="javascript:void(0);" class="aiz-side-nav-link">
                                    <span class="aiz-side-nav-text">{{translate('Facebook')}}</span>
                                    <span class="aiz-side-nav-arrow"></span>
                                </a>
                                <ul class="aiz-side-nav-list level-3">
                                    <li class="aiz-side-nav-item">
                                        <a href="{{ route('facebook_chat.index') }}" class="aiz-side-nav-link">
                                            <span class="aiz-side-nav-text">{{translate('Facebook Chat')}}</span>
                                        </a>
                                    </li>
                                    <li class="aiz-side-nav-item">
                                        <a href="{{ route('facebook-comment') }}" class="aiz-side-nav-link">
                                            <span class="aiz-side-nav-text">{{translate('Facebook Comment')}}</span>
                                        </a>
                                    </li>
                                </ul>
                            </li>

                            <li class="aiz-side-nav-item">
                                <a href="javascript:void(0);" class="aiz-side-nav-link">
                                    <span class="aiz-side-nav-text">{{translate('Google')}}</span>
                                    <span class="aiz-side-nav-arrow"></span>
                                </a>
                                <ul class="aiz-side-nav-list level-3">
                                    <li class="aiz-side-nav-item">
                                        <a href="{{ route('google_analytics.index') }}" class="aiz-side-nav-link">
                                            <span class="aiz-side-nav-text">{{translate('Analytics Tools')}}</span>
                                        </a>
                                    </li>
                                    <li class="aiz-side-nav-item">
                                        <a href="{{ route('google_recaptcha.index') }}" class="aiz-side-nav-link">
                                            <span class="aiz-side-nav-text">{{translate('Google reCAPTCHA')}}</span>
                                        </a>
                                    </li>
                                    <li class="aiz-side-nav-item">
                                        <a href="{{ route('google-map.index') }}" class="aiz-side-nav-link">
                                            <span class="aiz-side-nav-text">{{translate('Google Map')}}</span>
                                        </a>
                                    </li>
                                    <li class="aiz-side-nav-item">
                                        <a href="{{ route('google-firebase.index') }}" class="aiz-side-nav-link">
                                            <span class="aiz-side-nav-text">{{translate('Google Firebase')}}</span>
                                        </a>
                                    </li>
                                </ul>
                            </li>




                            <li class="aiz-side-nav-item">
                                <a href="javascript:void(0);" class="aiz-side-nav-link">
                                    <span class="aiz-side-nav-text">{{translate('Shipping')}}</span>
                                    <span class="aiz-side-nav-arrow"></span>
                                </a>
                                <ul class="aiz-side-nav-list level-3">
                                    <li class="aiz-side-nav-item">
                                        <a href="{{route('shipping_configuration.index')}}" class="aiz-side-nav-link {{ areActiveRoutes(['shipping_configuration.index','shipping_configuration.edit','shipping_configuration.update'])}}">
                                            <span class="aiz-side-nav-text">{{translate('Shipping Configuration')}}</span>
                                        </a>
                                    </li>
                                    <li class="aiz-side-nav-item">
                                        <a href="{{route('countries.index')}}" class="aiz-side-nav-link {{ areActiveRoutes(['countries.index','countries.edit','countries.update'])}}">
                                            <span class="aiz-side-nav-text">{{translate('Shipping Countries')}}</span>
                                        </a>
                                    </li>
                                    <li class="aiz-side-nav-item">
                                        <a href="{{route('states.index')}}" class="aiz-side-nav-link {{ areActiveRoutes(['states.index','states.edit','states.update'])}}">
                                            <span class="aiz-side-nav-text">{{translate('Shipping States')}}</span>
                                        </a>
                                    </li>
                                    <li class="aiz-side-nav-item">
                                        <a href="{{route('cities.index')}}" class="aiz-side-nav-link {{ areActiveRoutes(['cities.index','cities.edit','cities.update'])}}">
                                            <span class="aiz-side-nav-text">{{translate('Shipping Cities')}}</span>
                                        </a>
                                    </li>
                                </ul>
                            </li>

                        </ul>
                    </li>
                @endif


            <!-- Staffs -->
                @if(Auth::user()->user_type == 'admin' || in_array('20', json_decode(Auth::user()->staff->role->permissions)))
                    <li class="aiz-side-nav-item">
                        <a href="#" class="aiz-side-nav-link">
                            <i class="las la-user-tie aiz-side-nav-icon"></i>
                            <span class="aiz-side-nav-text">{{translate('Staffs')}}</span>
                            <span class="aiz-side-nav-arrow"></span>
                        </a>
                        <ul class="aiz-side-nav-list level-2">
                            <li class="aiz-side-nav-item">
                                <a href="{{ route('staffs.index') }}" class="aiz-side-nav-link {{ areActiveRoutes(['staffs.index', 'staffs.create', 'staffs.edit'])}}">
                                    <span class="aiz-side-nav-text">{{translate('All staffs')}}</span>
                                </a>
                            </li>
                            <li class="aiz-side-nav-item">
                                <a href="{{route('roles.index')}}" class="aiz-side-nav-link {{ areActiveRoutes(['roles.index', 'roles.create', 'roles.edit'])}}">
                                    <span class="aiz-side-nav-text">{{translate('Staff permissions')}}</span>
                                </a>
                            </li>
                        </ul>
                    </li>
                @endif
                <!--@if(Auth::user()->user_type == 'admin' || in_array('24', json_decode(Auth::user()->staff->role->permissions)))-->
                <!--    <li class="aiz-side-nav-item">-->
                <!--        <a href="#" class="aiz-side-nav-link">-->
                <!--            <i class="las la-user-tie aiz-side-nav-icon"></i>-->
                <!--            <span class="aiz-side-nav-text">{{translate('System')}}</span>-->
                <!--            <span class="aiz-side-nav-arrow"></span>-->
                <!--        </a>-->
                <!--        <ul class="aiz-side-nav-list level-2">-->
                <!--            <li class="aiz-side-nav-item">-->
                <!--                <a href="{{ route('system_update') }}" class="aiz-side-nav-link">-->
                <!--                    <span class="aiz-side-nav-text">{{translate('Update')}}</span>-->
                <!--                </a>-->
                <!--            </li>-->
                <!--            <li class="aiz-side-nav-item">-->
                <!--                <a href="{{route('system_server')}}" class="aiz-side-nav-link">-->
                <!--                    <span class="aiz-side-nav-text">{{translate('Server status')}}</span>-->
                <!--                </a>-->
                <!--            </li>-->
                <!--        </ul>-->
                <!--    </li>-->
                <!--@endif-->

             <!--Addon Manager -->
                <!--@if(Auth::user()->user_type == 'admin' || in_array('21', json_decode(Auth::user()->staff->role->permissions)))-->
                <!--    <li class="aiz-side-nav-item">-->
                <!--        <a href="{{route('addons.index')}}" class="aiz-side-nav-link {{ areActiveRoutes(['addons.index', 'addons.create'])}}">-->
                <!--            <i class="las la-wrench aiz-side-nav-icon"></i>-->
                <!--            <span class="aiz-side-nav-text">{{translate('Addon Manager')}}</span>-->
                <!--        </a>-->
                <!--    </li>-->
                <!--@endif-->
            </ul><!-- .aiz-side-nav -->
        </div><!-- .aiz-side-nav-wrap -->
    </div><!-- .aiz-sidebar -->
    <div class="aiz-sidebar-overlay"></div>
</div><!-- .aiz-sidebar -->
