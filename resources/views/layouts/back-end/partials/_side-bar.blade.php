<style>
    .navbar-vertical .nav-link {
        color: #ffffff;
        font-weight: bold;
    }

    .navbar .nav-link:hover {
        color: #C6FFC1;
    }

    .navbar .active>.nav-link,
    .navbar .nav-link.active,
    .navbar .nav-link.show,
    .navbar .show>.nav-link {
        color: #C6FFC1;
    }

    .navbar-vertical .active .nav-indicator-icon,
    .navbar-vertical .nav-link:hover .nav-indicator-icon,
    .navbar-vertical .show>.nav-link>.nav-indicator-icon {
        color: #C6FFC1;
    }

    .nav-subtitle {
        display: block;
        color: #fffbdf91;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: .03125rem;
    }

    .side-logo {
        background-color: #F7F8FA;
    }

    .nav-sub {
        background-color: #182c2f !important;
    }

    .nav-indicator-icon {
        margin-left: {{ Session::get('direction') === 'rtl' ? '6px' : '' }};
    }
</style>

<div id="sidebarMain" class="d-none">
    <aside
        style="background: #182c2f!important; text-align: {{ Session::get('direction') === 'rtl' ? 'right' : 'left' }};"
        class="js-navbar-vertical-aside navbar navbar-vertical-aside navbar-vertical navbar-vertical-fixed navbar-expand-xl navbar-bordered  ">
        <div class="navbar-vertical-container">
            <div class="navbar-vertical-footer-offset pb-0">
                <div class="navbar-brand-wrapper justify-content-between side-logo">
                    <!-- Logo -->
                    @php($e_commerce_logo = \App\Models\BusinessSetting::where(['type' => 'company_web_logo'])->first()->value)
                    <a class="navbar-brand" href="{{ route('admin.dashboard.index') }}" aria-label="Front">
                        <img style="max-height: 38px"
                            onerror="this.src='{{ asset('assets/back-end/img/1920x400/logo-width.png') }}'"
                            class="navbar-brand-logo-mini for-web-logo"
                            src="{{ asset("storage/company/$e_commerce_logo") }}" alt="Logo" />
                    </a>
                    <!-- Navbar Vertical Toggle -->
                    <button type="button"
                        class="js-navbar-vertical-aside-toggle-invoker navbar-vertical-aside-toggle btn btn-icon btn-xs btn-ghost-dark">
                        <i class="tio-clear tio-lg"></i>
                    </button>
                    <!-- End Navbar Vertical Toggle -->
                </div>

                <!-- Content -->
                <div class="navbar-vertical-content mt-2">
                    <ul class="navbar-nav navbar-nav-lg nav-tabs">
                        <!-- Dashboards -->

                        <li class="navbar-vertical-aside-has-menu {{ Request::is('admin') ? 'show' : '' }}">
                            <a class="js-navbar-vertical-aside-menu-link nav-link"
                                href="{{ route('admin.dashboard.index') }}">
                                <i class="tio-home-vs-1-outlined nav-icon"></i>
                                <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                    {{ translate('Dashboard') }}
                                </span>
                            </a>
                        </li>

                        <!-- End Dashboards -->
                        <!-- POS -->
                        <!--                        <li class="nav-item">
                            <small
                                class="nav-subtitle">{{ translate('pos') }} {{ translate('system') }}</small>
                            <small class="tio-more-horizontal nav-subtitle-replacer"></small>
                        </li>
                        <li class="navbar-vertical-aside-has-menu {{ Request::is('admin/pos/*') ? 'active' : '' }}">
                            <a class="js-navbar-vertical-aside-menu-link nav-link nav-link-toggle" href="javascript:">
                                <i class="tio-shopping nav-icon"></i>
                                <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">{{ translate('POS') }}</span>
                            </a>
                            <ul class="js-navbar-vertical-aside-submenu nav nav-sub"
                                style="display: {{ Request::is('admin/pos/*') ? 'block' : 'none' }}">
                                <li class="nav-item {{ Request::is('admin/pos/') ? 'active' : '' }}">
                                    <a class="nav-link " href="{{ route('admin.pos.index') }}"
                                       title="{{ translate('pos') }}">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span
                                            class="text-truncate">{{ translate('pos') }}</span>
                                    </a>
                                </li>
                                <li class="nav-item {{ Request::is('admin/pos/orders') ? 'active' : '' }}">
                                    <a class="nav-link " href="{{ route('admin.pos.orders') }}" title="{{ translate('orders') }}">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">{{ translate('orders') }}
                        <span class="badge badge-info badge-pill ml-1">
{{-- {{\App\Models\Order::where('branch_id', auth()->id())->Pos()->count()}} --}}
                        </span>
                    </span>
                    </a>
                </li> &#45;&#45;}}
                            </ul>
                        </li>-->

                        <!-- End POS -->
                        @if (permission_check('order_management'))
                            <li class="nav-item {{ Request::is('admin/orders*') ? 'scroll-here' : '' }}">
                                <small class="nav-subtitle" title="">{{ translate('order_management') }}</small>
                                <small class="tio-more-horizontal nav-subtitle-replacer"></small>
                            </li>
                            <!-- Order -->
                            <li
                                class="navbar-vertical-aside-has-menu {{ Request::is('admin/orders*') ? 'active' : '' }}">
                                <a class="js-navbar-vertical-aside-menu-link nav-link nav-link-toggle"
                                    href="javascript:">
                                    <i class="tio-shopping-cart-outlined nav-icon"></i>
                                    <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                        {{ translate('orders') }}
                                    </span>
                                </a>
                                <ul class="js-navbar-vertical-aside-submenu nav nav-sub"
                                    style="display: {{ Request::is('admin/order*') ? 'block' : 'none' }}">
                                    <li class="nav-item {{ Request::is('admin/orders/list/all') ? 'active' : '' }}">
                                        <a class="nav-link" href="{{ route('admin.orders.list', ['all']) }}"
                                            title="">
                                            <span class="tio-circle nav-indicator-icon"></span>
                                            <span class="text-truncate">
                                                {{ translate('All') }}
                                                <span class="badge badge-info badge-pill ml-1">
                                                    {{ \App\Models\Order::count() }}
                                                </span>
                                            </span>
                                        </a>
                                    </li>
                                    <li
                                        class="nav-item {{ Request::is('admin/orders/list/pending') ? 'active' : '' }}">
                                        <a class="nav-link " href="{{ route('admin.orders.list', ['pending']) }}"
                                            title="">
                                            <span class="tio-circle nav-indicator-icon"></span>
                                            <span class="text-truncate">
                                                {{ translate('pending') }}
                                                <span class="badge badge-soft-info badge-pill ml-1">
                                                    {{ \App\Models\Order::where(['order_status' => 'pending'])->count() }}
                                                </span>
                                            </span>
                                        </a>
                                    </li>
                                    <li
                                        class="nav-item {{ Request::is('admin/orders/list/confirmed') ? 'active' : '' }}">
                                        <a class="nav-link " href="{{ route('admin.orders.list', ['confirmed']) }}"
                                            title="">
                                            <span class="tio-circle nav-indicator-icon"></span>
                                            <span class="text-truncate">
                                                {{ translate('confirmed') }}
                                                <span class="badge badge-soft-success badge-pill ml-1">
                                                    {{ \App\Models\Order::where(['order_status' => 'confirmed'])->count() }}
                                                </span>
                                            </span>
                                        </a>
                                    </li>
                                    <li
                                        class="nav-item {{ Request::is('admin/orders/list/processing') ? 'active' : '' }}">
                                        <a class="nav-link " href="{{ route('admin.orders.list', ['processing']) }}"
                                            title="">
                                            <span class="tio-circle nav-indicator-icon"></span>
                                            <span class="text-truncate">
                                                {{ translate('Processing') }}
                                                <span class="badge badge-warning badge-pill ml-1">
                                                    {{ \App\Models\Order::where(['order_status' => 'processing'])->count() }}
                                                </span>
                                            </span>
                                        </a>
                                    </li>
                                    <li
                                        class="nav-item {{ Request::is('admin/orders/list/out_for_delivery') ? 'active' : '' }}">
                                        <a class="nav-link "
                                            href="{{ route('admin.orders.list', ['out_for_delivery']) }}"
                                            title="">
                                            <span class="tio-circle nav-indicator-icon"></span>
                                            <span class="text-truncate">
                                                {{ translate('out_for_delivery') }}
                                                <span class="badge badge-warning badge-pill ml-1">
                                                    {{ \App\Models\Order::where(['order_status' => 'out_for_delivery'])->count() }}
                                                </span>
                                            </span>
                                        </a>
                                    </li>
                                    <li
                                        class="nav-item {{ Request::is('admin/orders/list/delivered') ? 'active' : '' }}">
                                        <a class="nav-link " href="{{ route('admin.orders.list', ['delivered']) }}"
                                            title="">
                                            <span class="tio-circle nav-indicator-icon"></span>
                                            <span class="text-truncate">
                                                {{ translate('delivered') }}
                                                <span class="badge badge-success badge-pill ml-1">
                                                    {{ \App\Models\Order::where(['order_status' => 'delivered'])->count() }}
                                                </span>
                                            </span>
                                        </a>
                                    </li>
                                    <li
                                        class="nav-item {{ Request::is('admin/orders/list/returned') ? 'active' : '' }}">
                                        <a class="nav-link " href="{{ route('admin.orders.list', ['returned']) }}"
                                            title="">
                                            <span class="tio-circle nav-indicator-icon"></span>
                                            <span class="text-truncate">
                                                {{ translate('returned') }}
                                                <span class="badge badge-soft-danger badge-pill ml-1">
                                                    {{ \App\Models\Order::where(['order_status' => 'returned'])->count() }}
                                                </span>
                                            </span>
                                        </a>
                                    </li>
                                    <li
                                        class="nav-item {{ Request::is('admin/orders/list/failed') ? 'active' : '' }}">
                                        <a class="nav-link " href="{{ route('admin.orders.list', ['failed']) }}"
                                            title="">
                                            <span class="tio-circle nav-indicator-icon"></span>
                                            <span class="text-truncate">
                                                {{ translate('failed') }}
                                                <span class="badge badge-danger badge-pill ml-1">
                                                    {{ \App\Models\Order::where(['order_status' => 'failed'])->count() }}
                                                </span>
                                            </span>
                                        </a>
                                    </li>

                                    <li
                                        class="nav-item {{ Request::is('admin/orders/list/canceled') ? 'active' : '' }}">
                                        <a class="nav-link " href="{{ route('admin.orders.list', ['canceled']) }}"
                                            title="">
                                            <span class="tio-circle nav-indicator-icon"></span>
                                            <span class="text-truncate">
                                                {{ translate('canceled') }}
                                                <span class="badge badge-danger badge-pill ml-1">
                                                    {{ \App\Models\Order::where(['order_status' => 'canceled'])->count() }}
                                                </span>
                                            </span>
                                        </a>
                                    </li>
                                </ul>
                            </li>
                        @endif
                        <!--order management ends-->

                        @if (permission_check('blog_management'))
                            <li class="nav-item {{ Request::is('admin/blog*') ? 'scroll-here' : '' }}">
                                <small class="nav-subtitle" title="">{{ translate('blog_management') }}</small>
                                <small class="tio-more-horizontal nav-subtitle-replacer"></small>
                            </li>
                            <!-- Pages -->
                            <li class="nav-item {{ Request::is('admin/customer/list') ? 'active' : '' }}">
                                <a class="nav-link " href="{{ route('admin.post.view') }}">
                                    <span class="tio-poi-user nav-icon"></span>
                                    <span class="text-truncate">Post</span>
                                </a>
                            </li>

                            {{-- <li class="nav-item {{ Request::is('admin/customer/list') ? 'active' : '' }}">
                                <a class="nav-link " href="{{ route('admin.video_post.view') }}">
                                    <span class="tio-poi-user nav-icon"></span>
                                    <span class="text-truncate">Video Post</span>
                                </a>
                            </li> --}}

                            <li
                                class="navbar-vertical-aside-has-menu {{ Request::is('admin/blog_category*') ? 'active' : '' }}">
                                <a class="js-navbar-vertical-aside-menu-link nav-link nav-link-toggle"
                                    href="javascript:">
                                    <i class="tio-apple-outlined nav-icon"></i>
                                    <span
                                        class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">{{ translate('Blog Category') }}</span>
                                </a>
                                <ul class="js-navbar-vertical-aside-submenu nav nav-sub"
                                    style="display: {{ Request::is('admin/blog_category/view*') ? 'block' : 'none' }}">
                                    <li
                                        class="nav-item {{ Request::is('admin/blog_category/view') ? 'active' : '' }}">
                                        <a class="nav-link " href="{{ route('admin.blog_category.view') }}">
                                            <span class="tio-circle nav-indicator-icon"></span>
                                            <span class="text-truncate">{{ translate('Manage') }}</span>
                                        </a>
                                    </li>
                                </ul>
                            </li>

                            <li
                                class="navbar-vertical-aside-has-menu {{ Request::is('admin/blog_post*') ? 'active' : '' }}">
                                <a class="js-navbar-vertical-aside-menu-link nav-link nav-link-toggle"
                                    href="javascript:">
                                    <i class="tio-apple-outlined nav-icon"></i>
                                    <span
                                        class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">{{ translate('Blog Posts') }}</span>
                                </a>
                                <ul class="js-navbar-vertical-aside-submenu nav nav-sub"
                                    style="display: {{ Request::is('admin/blog_post/view*') ? 'block' : 'none' }}">
                                    <li class="nav-item {{ Request::is('admin/blog_post/view') ? 'active' : '' }}">
                                        <a class="nav-link " href="{{ route('admin.blog_post.view') }}">
                                            <span class="tio-circle nav-indicator-icon"></span>
                                            <span class="text-truncate">{{ translate('Manage') }}</span>
                                        </a>
                                    </li>
                                </ul>
                            </li>
                        @endif



                        @if (permission_check('product_management'))
                            <li
                                class="nav-item {{ Request::is('admin/brand*') || Request::is('admin/category*') || Request::is('admin/sub*') || Request::is('admin/attribute*') || Request::is('admin/product*') ? 'scroll-here' : '' }}">
                                <small class="nav-subtitle"
                                    title="">{{ translate('product_management') }}</small>
                                <small class="tio-more-horizontal nav-subtitle-replacer"></small>
                            </li>

                            <!-- Pages -->
                            <li
                                class="navbar-vertical-aside-has-menu {{ Request::is('admin/brand*') ? 'active' : '' }}">
                                <a class="js-navbar-vertical-aside-menu-link nav-link nav-link-toggle"
                                    href="javascript:">
                                    <i class="tio-apple-outlined nav-icon"></i>
                                    <span
                                        class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">{{ translate('brands') }}</span>
                                </a>
                                <ul class="js-navbar-vertical-aside-submenu nav nav-sub"
                                    style="display: {{ Request::is('admin/brand*') ? 'block' : 'none' }}">
                                    <li class="nav-item {{ Request::is('admin/brand/add-new') ? 'active' : '' }}">
                                        <a class="nav-link " href="{{ route('admin.brand.add-new') }}">
                                            <span class="tio-circle nav-indicator-icon"></span>
                                            <span class="text-truncate">{{ translate('add_new') }}</span>
                                        </a>
                                    </li>
                                    <li class="nav-item {{ Request::is('admin/brand/list') ? 'active' : '' }}">
                                        <a class="nav-link " href="{{ route('admin.brand.list') }}">
                                            <span class="tio-circle nav-indicator-icon"></span>
                                            <span class="text-truncate">{{ translate('List') }}</span>
                                        </a>
                                    </li>
                                </ul>
                            </li>

                            <li
                                class="navbar-vertical-aside-has-menu {{ Request::is('admin/category*') || Request::is('admin/sub*') ? 'active' : '' }}">
                                <a class="js-navbar-vertical-aside-menu-link nav-link nav-link-toggle"
                                    href="javascript:">
                                    <i class="tio-filter-list nav-icon"></i>
                                    <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                        {{ translate('categories') }}
                                    </span>
                                </a>
                                <ul class="js-navbar-vertical-aside-submenu nav nav-sub"
                                    style="display: {{ Request::is('admin/category*') || Request::is('admin/sub*') ? 'block' : '' }}">
                                    <li class="nav-item {{ Request::is('admin/category/view') ? 'active' : '' }}">
                                        <a class="nav-link " href="{{ route('admin.category.view') }}">
                                            <span class="tio-circle nav-indicator-icon"></span>
                                            <span class="text-truncate">{{ translate('category') }}</span>
                                        </a>

                                    </li>
                                    <li class="nav-item {{ Request::is('admin/sub-category/view') ? 'active' : '' }}">
                                        <a class="nav-link " href="{{ route('admin.sub-category.view') }}">
                                            <span class="tio-circle nav-indicator-icon"></span>
                                            <span class="text-truncate">{{ translate('sub_category') }}</span>
                                        </a>
                                    </li>
                                    <li
                                        class="nav-item {{ Request::is('admin/sub-sub-category/view') ? 'active' : '' }}">
                                        <a class="nav-link " href="{{ route('admin.sub-sub-category.view') }}">
                                            <span class="tio-circle nav-indicator-icon"></span>
                                            <span class="text-truncate">{{ translate('sub_sub_category') }}</span>
                                        </a>
                                    </li>
                                </ul>
                            </li>

                            <li
                                class="navbar-vertical-aside-has-menu {{ Request::is('admin/attribute*') ? 'active' : '' }}">
                                <a class="js-navbar-vertical-aside-menu-link nav-link"
                                    href="{{ route('admin.attribute.view') }}">
                                    <i class="tio-category-outlined nav-icon"></i>
                                    <span
                                        class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">{{ translate('Attribute') }}</span>
                                </a>
                            </li>

                            <li
                                class="navbar-vertical-aside-has-menu {{ Request::is('admin/product/list/in_house') || Request::is('admin/product/bulk-import') ? 'active' : '' }}">
                                <a class="js-navbar-vertical-aside-menu-link nav-link nav-link-toggle"
                                    href="javascript:">
                                    <i class="tio-shop nav-icon"></i>
                                    <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                        <span class="text-truncate">{{ translate('InHouse Products') }}</span>
                                    </span>
                                </a>
                                <ul class="js-navbar-vertical-aside-submenu nav nav-sub"
                                    style="display: {{ Request::is('admin/product/list/in_house') || Request::is('admin/product/stock-limit-list/in_house') || Request::is('admin/product/bulk-import') ? 'block' : '' }}">
                                    <li
                                        class="nav-item {{ Request::is('admin/product/list/in_house') ? 'active' : '' }}">
                                        <a class="nav-link "
                                            href="{{ route('admin.product.list', ['in_house', '']) }}">
                                            <span class="tio-circle nav-indicator-icon"></span>
                                            <span class="text-truncate">{{ translate('Products') }}</span>
                                        </a>
                                    </li>
                                    <li
                                        class="nav-item {{ Request::is('admin/product/stock-limit-list/in_house') ? 'active' : '' }}">
                                        <a class="nav-link "
                                            href="{{ route('admin.product.stock-limit-list', ['in_house', '']) }}">
                                            <span class="tio-circle nav-indicator-icon"></span>
                                            <span
                                                class="text-truncate">{{ translate('stock_limit_products') }}</span>
                                        </a>
                                    </li>
                                    <li
                                        class="nav-item {{ Request::is('admin/product/bulk-import') ? 'active' : '' }}">
                                        <a class="nav-link " href="{{ route('admin.product.bulk-import') }}">
                                            <span class="tio-circle nav-indicator-icon"></span>
                                            <span class="text-truncate">{{ translate('bulk_import') }}</span>
                                        </a>
                                    </li>
                                    <li
                                        class="nav-item {{ Request::is('admin/product/bulk-export') ? 'active' : '' }}">
                                        <a class="nav-link " href="{{ route('admin.product.bulk-export') }}">
                                            <span class="tio-circle nav-indicator-icon"></span>
                                            <span class="text-truncate">{{ translate('bulk_export') }}</span>
                                        </a>
                                    </li>
                                </ul>
                            </li>

                            <li
                                class="navbar-vertical-aside-has-menu {{ Request::is('admin/product/list/seller*') ? 'active' : '' }}">
                                <a class="js-navbar-vertical-aside-menu-link nav-link nav-link-toggle"
                                    href="javascript:">
                                    <i class="tio-airdrop nav-icon"></i>
                                    <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                        {{ translate('Seller') }} {{ translate('Products') }}
                                    </span>
                                </a>
                                <ul class="js-navbar-vertical-aside-submenu nav nav-sub"
                                    style="display: {{ Request::is('admin/product/list/seller*') ? 'block' : '' }}">
                                    <li
                                        class="nav-item {{ Request::is('admin/product/list/seller/0') ? 'active' : '' }}">
                                        <a class="nav-link "
                                            href="{{ route('admin.product.list', ['seller', 'status' => '0']) }}">
                                            <span class="tio-circle nav-indicator-icon"></span>
                                            <span class="text-truncate">{{ translate('New') }}
                                                {{ translate('Products') }}</span>
                                        </a>
                                    </li>
                                    <li
                                        class="nav-item {{ Request::is('admin/product/list/seller/1') ? 'active' : '' }}">
                                        <a class="nav-link "
                                            href="{{ route('admin.product.list', ['seller', 'status' => '1']) }}">
                                            <span class="tio-circle nav-indicator-icon"></span>
                                            <span class="text-truncate">{{ translate('Approved') }}
                                                {{ translate('Products') }}</span>
                                        </a>
                                    </li>
                                    <li
                                        class="nav-item {{ Request::is('admin/product/list/seller/2') ? 'active' : '' }}">
                                        <a class="nav-link "
                                            href="{{ route('admin.product.list', ['seller', 'status' => '2']) }}">
                                            <span class="tio-circle nav-indicator-icon"></span>
                                            <span class="text-truncate">{{ translate('Denied') }}
                                                {{ translate('Products') }}</span>
                                        </a>
                                    </li>
                                </ul>
                            </li>

                            

                            <li
                                class="nav-item {{ Request::is('admin/brand*') || Request::is('admin/category*') || Request::is('admin/sub*') || Request::is('admin/attribute*') || Request::is('admin/product*') ? 'scroll-here' : '' }}">
                                <small class="nav-subtitle"
                                    title="">{{ translate('service_management') }}</small>
                                <small class="tio-more-horizontal nav-subtitle-replacer"></small>
                            </li>

                            <li class="nav-item {{ Request::is('admin/service/category_list') ? 'active' : '' }}">
                                <a class="nav-link " href="{{ route('admin.service.category_list') }}">
                                    <i class="fa fa-arrows mr-2" aria-hidden="true"></i>
                                    <span class="text-truncate">{{ translate('Category') }} </span>
                                </a>
                            </li>

                            <li class="nav-item {{ Request::is('admin/service/banner_list') ? 'active' : '' }}">
                                <a class="nav-link " href="{{ route('admin.service.banner_list') }}">
                                    <i class="fa fa-picture-o mr-2" aria-hidden="true"></i>
                                    <span class="text-truncate">{{ translate('Banner') }} </span>
                                </a>
                            </li>

                            <li class="nav-item {{ Request::is('admin/service/list') ? 'active' : '' }}">
                                <a class="nav-link " href="{{ route('admin.service.list') }}">
                                    <i class="fa fa-address-card mr-2" aria-hidden="true"></i>
                                    <span class="text-truncate">{{ translate('Service') }} </span>
                                </a>
                            </li>

                            <li class="nav-item {{ Request::is('admin/car/list') ? 'active' : '' }}">
                                <a class="nav-link " href="{{ route('admin.car.list') }}">
                                    <i class="fa fa-car mr-2" aria-hidden="true"></i>
                                    <span class="text-truncate">{{ translate('Car') }} </span>
                                </a>
                            </li>

                            <li class="nav-item {{ Request::is('admin/property/list') ? 'active' : '' }}">
                                <a class="nav-link " href="{{ route('admin.property.list') }}">
                                    <i class="fa fa-home mr-2" aria-hidden="true"></i>
                                    <span class="text-truncate">{{ translate('property') }} </span>
                                </a>
                            </li>

                            <li class="nav-item {{ Request::is('admin/ambulance/show_update_page') ? 'active' : '' }}">
                                <a class="nav-link " href="{{ route('admin.ambulance.show_update_page') }}">
                                    <i class="fa fa-ambulance mr-2" aria-hidden="true"></i>
                                    <span class="text-truncate">{{ translate('ambulance') }} </span>
                                </a>
                            </li>
                            
                            <li
                                class="nav-item {{ Request::is('admin/service/apply_service_list') ? 'active' : '' }}">
                                <a class="nav-link " href="{{ route('admin.service.apply_service_list') }}">
                                    <i class="fa fa-handshake-o mr-2" aria-hidden="true"></i>
                                    <span class="text-truncate">{{ translate('Apply Service List') }} </span>
                                </a>
                            </li>
                        @endif
                        <!--product management ends-->




                        @if (permission_check('marketing_section'))
                            <li
                                class="nav-item {{ Request::is('admin/banner*') || Request::is('admin/coupon*') || Request::is('admin/notification*') || Request::is('admin/deal*') ? 'scroll-here' : '' }}">
                                <small class="nav-subtitle"
                                    title="">{{ translate('Marketing_Section') }}</small>
                                <small class="tio-more-horizontal nav-subtitle-replacer"></small>
                            </li>
                            <li
                                class="navbar-vertical-aside-has-menu {{ Request::is('admin/banner*') ? 'active' : '' }}">
                                <a class="js-navbar-vertical-aside-menu-link nav-link"
                                    href="{{ route('admin.banner.list') }}">
                                    <i class="tio-photo-square-outlined nav-icon"></i>
                                    <span
                                        class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">{{ translate('banners') }}</span>
                                </a>
                            </li>
                            <li
                                class="navbar-vertical-aside-has-menu {{ Request::is('admin/coupon*') ? 'active' : '' }}">
                                <a class="js-navbar-vertical-aside-menu-link nav-link"
                                    href="{{ route('admin.coupon.add-new') }}">
                                    <i class="tio-credit-cards nav-icon"></i>
                                    <span
                                        class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">{{ translate('coupons') }}</span>
                                </a>
                            </li>
                            <li
                                class="navbar-vertical-aside-has-menu {{ Request::is('admin/notification*') ? 'active' : '' }}">
                                <a class="js-navbar-vertical-aside-menu-link nav-link"
                                    href="{{ route('admin.notification.add-new') }}" title="">
                                    <i class="tio-notifications-on-outlined nav-icon"></i>
                                    <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                        {{ translate('push_notification') }}
                                    </span>
                                </a>
                            </li>
                            <li
                                class="navbar-vertical-aside-has-menu {{ Request::is('admin/deal/flash') ? 'active' : '' }}">
                                <a class="js-navbar-vertical-aside-menu-link nav-link"
                                    href="{{ route('admin.deal.flash') }}">
                                    <i class="tio-flash nav-icon"></i>
                                    <span
                                        class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">{{ translate('flash_deals') }}</span>
                                </a>
                            </li>

                            {{-- <li
                                class="navbar-vertical-aside-has-menu {{ Request::is('admin/deal/day') ? 'active' : '' }}">
                                <a class="js-navbar-vertical-aside-menu-link nav-link"
                                    href="{{ route('admin.deal.day') }}">
                                    <i class="tio-crown-outlined nav-icon"></i>
                                    <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                        {{ translate('deal_of_the_day') }}
                                    </span>
                                </a>
                            </li> --}}

                            <li
                                class="navbar-vertical-aside-has-menu {{ Request::is('admin/deal/feature') ? 'active' : '' }}">
                                <a class="js-navbar-vertical-aside-menu-link nav-link"
                                    href="{{ route('admin.deal.feature') }}">
                                    <i class="tio-flag-outlined nav-icon"></i>
                                    <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                        {{ translate('featured_deal') }}
                                    </span>
                                </a>
                            </li>
                        @endif
                        <!--marketing section ends here-->

                        @if (permission_check('business_section'))
                            <li
                                class="nav-item {{ Request::is('admin/report/product-in-wishlist') || Request::is('admin/reviews*') || Request::is('admin/sellers/withdraw_list') || Request::is('admin/report/product-stock') ? 'scroll-here' : '' }}">
                                <small class="nav-subtitle"
                                    title="">{{ translate('business_section') }}</small>
                                <small class="tio-more-horizontal nav-subtitle-replacer"></small>
                            </li>

                            {{-- seller withdraw --}}
                            <li
                                class="navbar-vertical-aside-has-menu {{ Request::is('admin/stock/product-stock') ? 'active' : '' }}">
                                <a class="js-navbar-vertical-aside-menu-link nav-link"
                                    href="{{ route('admin.stock.product-stock') }}">
                                    <i class="tio-fullscreen-1-1 nav-icon"></i>
                                    <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                        {{ translate('product') }} {{ translate('stock') }}
                                    </span>
                                </a>
                            </li>
                            <li
                                class="navbar-vertical-aside-has-menu {{ Request::is('admin/reviews*') ? 'active' : '' }}">
                                <a class="js-navbar-vertical-aside-menu-link nav-link"
                                    href="{{ route('admin.reviews.list') }}">
                                    <i class="tio-star nav-icon"></i>
                                    <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                        {{ translate('Customer') }} {{ translate('Reviews') }}
                                    </span>
                                </a>
                            </li>
                            <li
                                class="navbar-vertical-aside-has-menu {{ Request::is('admin/stock/product-in-wishlist') ? 'active' : '' }}">
                                <a class="js-navbar-vertical-aside-menu-link nav-link"
                                    href="{{ route('admin.stock.product-in-wishlist') }}">
                                    <i class="tio-heart-outlined nav-icon"></i>
                                    <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                        {{ translate('product') }} {{ translate('in') }}
                                        {{ translate('wish_list') }}
                                    </span>
                                </a>
                            </li>
                            <li
                                class="navbar-vertical-aside-has-menu {{ Request::is('admin/transaction/list') ? 'active' : '' }}">
                                <a class="js-navbar-vertical-aside-menu-link nav-link"
                                    href="{{ route('admin.transaction.list') }}">
                                    <i class="tio-money nav-icon"></i>
                                    <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                        {{ translate('Transactions') }}
                                    </span>
                                </a>
                            </li>
                        @endif
                        <!--business section ends here-->

                        @if (permission_check('user_section'))
                            <li class="nav-item">
                                <a class="nav-link " href="{{ route('admin.withdraw.index') }}">
                                    <span class="tio-credit-card nav-icon"></span>
                                    <span class="text-truncate">{{ translate('Withdraw') }} </span>
                                </a>
                            </li>

                            <li
                                class="nav-item {{ Request::is('admin/customer/list') || Request::is('admin/sellers/seller-list') ? 'scroll-here' : '' }}">
                                <small class="nav-subtitle" title="">{{ translate('user_section') }}</small>
                                <small class="tio-more-horizontal nav-subtitle-replacer"></small>
                            </li>
                            <li class="nav-item {{ Request::is('admin/customer/list') ? 'active' : '' }}">
                                <a class="nav-link " href="{{ route('admin.product.product-manager') }}">
                                    <span class="tio-poi-user nav-icon"></span>
                                    <span class="text-truncate">{{ translate('product_manager') }} </span>
                                </a>
                            </li>
                            <li class="nav-item {{ Request::is('admin/customer/list') ? 'active' : '' }}">
                                <a class="nav-link " href="{{ route('admin.sellers.reseller-list') }}">
                                    <span class="tio-poi-user nav-icon"></span>
                                    <span class="text-truncate">{{ translate('reseller') }} </span>
                                </a>
                            </li>
                            <li
                                class="navbar-vertical-aside-has-menu {{ Request::is('admin/seller*') ? 'active' : '' }}">
                                <a class="js-navbar-vertical-aside-menu-link nav-link nav-link-toggle"
                                    href="javascript:">
                                    <i class="tio-users-switch nav-icon"></i>
                                    <span
                                        class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">{{ translate('Seller') }}</span>
                                </a>
                                <ul class="js-navbar-vertical-aside-submenu nav nav-sub"
                                    style="display: {{ Request::is('admin/seller*') ? 'block' : 'none' }}">
                                    <li
                                        class="nav-item {{ Request::is('admin/sellers/seller-list') ? 'active' : '' }}">
                                        <a class="nav-link" href="{{ route('admin.sellers.seller-list') }}">
                                            <span class="tio-circle nav-indicator-icon"></span>
                                            <span class="text-truncate">
                                                {{ translate('list') }}
                                            </span>
                                        </a>
                                    </li>
                                    <li
                                        class="nav-item {{ Request::is('admin/sellers/withdraw_list') ? 'active' : '' }}">
                                        <a class="nav-link " href="{{ route('admin.sellers.withdraw_list') }}">
                                            <span class="tio-circle nav-indicator-icon"></span>
                                            <span class="text-truncate">{{ translate('withdraws') }}</span>
                                        </a>
                                    </li>
                                </ul>
                            </li>


                            <li class="nav-item {{ Request::is('admin/customer/list') ? 'active' : '' }}">
                                <a class="nav-link " href="{{ route('admin.customer.list') }}">
                                    <span class="tio-poi-user nav-icon"></span>
                                    <span class="text-truncate">{{ translate('customers') }} </span>
                                </a>
                            </li>

                            <li class="nav-item {{ Request::is('admin/membership/list') ? 'active' : '' }}">
                                <a class="nav-link " href="{{ route('admin.membership.list') }}">
                                    <span class="tio-poi-user nav-icon"></span>
                                    <span class="text-truncate">{{ translate('Membership') }} </span>
                                </a>
                            </li>
                        @endif
                        <!--user section ends here-->

                        @if (permission_check('support_section'))
                            <li
                                class="nav-item {{ Request::is('admin/support-ticket*') || Request::is('admin/contact*') ? 'scroll-here' : '' }}">
                                <small class="nav-subtitle"
                                    title="">{{ translate('support_section') }}</small>
                                <small class="tio-more-horizontal nav-subtitle-replacer"></small>
                            </li>

                            <li
                                class="navbar-vertical-aside-has-menu {{ Request::is('admin/contact*') ? 'active' : '' }}">
                                <a class="js-navbar-vertical-aside-menu-link nav-link"
                                    href="{{ route('admin.contact.list') }}">
                                    <i class="tio-messages nav-icon"></i>
                                    <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                        {{ translate('messages') }}
                                    </span>
                                </a>
                            </li>
                            <li
                                class="navbar-vertical-aside-has-menu {{ Request::is('admin/support-ticket*') ? 'active' : '' }}">
                                <a class="js-navbar-vertical-aside-menu-link nav-link"
                                    href="{{ route('admin.support-ticket.view') }}">
                                    <i class="tio-chat nav-icon"></i>
                                    <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                        {{ translate('support_ticket') }}
                                    </span>
                                </a>
                            </li>
                        @endif
                        <!--support section ends here-->


                        <li
                            class="navbar-vertical-aside-has-menu {{ Request::is('admin/business-settings/terms-condition') || Request::is('admin/business-settings/privacy-policy') || Request::is('admin/business-settings/about-us') || Request::is('admin/business-settings/return_and_refunds') || Request::is('admin/helpTopic/list') ? 'active' : '' }}">
                            <a class="js-navbar-vertical-aside-menu-link nav-link nav-link-toggle" href="javascript:">
                                <i class="tio-pages-outlined nav-icon"></i>
                                <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                    {{ translate('page_setup') }}
                                </span>
                            </a>
                            <ul class="js-navbar-vertical-aside-submenu nav nav-sub"
                                style="display: {{ Request::is('admin/business-settings/terms-condition') || Request::is('admin/business-settings/privacy-policy') || Request::is('admin/business-settings/about-us') || Request::is('admin/helpTopic/list') ? 'block' : 'none' }}">
                                <li
                                    class="nav-item {{ Request::is('admin/business-settings/terms-condition') ? 'active' : '' }}">
                                    <a class="nav-link"
                                        href="{{ route('admin.business-settings.terms-condition') }}">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">
                                            {{ translate('terms_and_condition') }}
                                        </span>
                                    </a>
                                </li>
                                <li
                                    class="nav-item {{ Request::is('admin/business-settings/privacy-policy') ? 'active' : '' }}">
                                    <a class="nav-link"
                                        href="{{ route('admin.business-settings.privacy-policy') }}">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">
                                            {{ translate('privacy_policy') }}
                                        </span>
                                    </a>
                                </li>
                                <li
                                    class="nav-item {{ Request::is('admin/business-settings/about-us') ? 'active' : '' }}">
                                    <a class="nav-link" href="{{ route('admin.business-settings.about-us') }}">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">
                                            {{ translate('about_us') }}
                                        </span>
                                    </a>
                                </li>

                                <li
                                    class="nav-item {{ Request::is('admin/business-settings/future_plan') ? 'active' : '' }}">
                                    <a class="nav-link" href="{{ route('admin.business-settings.future_plans') }}">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">
                                            {{ translate('future_plan') }}
                                        </span>
                                    </a>
                                </li>

                                <li
                                    class="nav-item {{ Request::is('admin/business-settings/contact-us') ? 'active' : '' }}">
                                    <a class="nav-link" href="{{ route('admin.business-settings.contact-us') }}">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">
                                            {{ translate('contact_us') }}
                                        </span>
                                    </a>
                                </li>

                                <li
                                    class="nav-item {{ Request::is('admin/business-settings/return-and-refunds') ? 'active' : '' }}">
                                    <a class="nav-link"
                                        href="{{ route('admin.business-settings.return-and-refunds') }}">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">
                                            {{ translate('return_and_refunds') }}
                                        </span>
                                    </a>
                                </li>

                                <li class="nav-item {{ Request::is('admin/helpTopic/list') ? 'active' : '' }}">
                                    <a class="nav-link" href="{{ route('admin.helpTopic.list') }}">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">
                                            {{ translate('faq') }}
                                        </span>
                                    </a>
                                </li>
                            </ul>
                        </li>
                        @if (permission_check('business_settings'))
                            <li
                                class="nav-item {{ Request::is('admin/currency/view') || Request::is('admin/business-settings/language*') || Request::is('admin/business-settings/shipping-method*') || Request::is('admin/business-settings/payment-method') || Request::is('admin/business-settings/seller-settings*') ? 'scroll-here' : '' }}">
                                <small class="nav-subtitle"
                                    title="">{{ translate('business_settings') }}</small>
                                <small class="tio-more-horizontal nav-subtitle-replacer"></small>
                            </li>
                            <li
                                class="navbar-vertical-aside-has-menu {{ Request::is('admin/business-settings/seller-settings*') ? 'active' : '' }}">
                                <a class="js-navbar-vertical-aside-menu-link nav-link"
                                    href="{{ route('admin.business-settings.seller-settings.index') }}">
                                    <i class="tio-user-big-outlined nav-icon"></i>
                                    <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                        {{ translate('seller_settings') }}
                                    </span>
                                </a>
                            </li>

                            <li
                                class="navbar-vertical-aside-has-menu {{ Request::is('admin/business-settings/payment-method') ? 'active' : '' }}">
                                <a class="js-navbar-vertical-aside-menu-link nav-link"
                                    href="{{ route('admin.business-settings.payment-method.index') }}">
                                    <i class="tio-money-vs nav-icon"></i>
                                    <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                        {{ translate('payment_method') }}
                                    </span>
                                </a>
                            </li>

                            <li
                                class="navbar-vertical-aside-has-menu {{ Request::is('admin/business-settings/sms-module') ? 'active' : '' }}">
                                <a class="nav-link " href="{{ route('admin.business-settings.sms-module') }}"
                                    title="{{ translate('sms') }} {{ translate('module') }}">
                                    <i class="tio-sms-active-outlined nav-icon"></i>
                                    <span class="text-truncate">{{ translate('sms') }}
                                        {{ translate('module') }}</span>
                                </a>
                            </li>

                            <li
                                class="navbar-vertical-aside-has-menu {{ Request::is('admin/business-settings/shipping-method*') ? 'active' : '' }}">
                                <a class="js-navbar-vertical-aside-menu-link nav-link nav-link-toggle"
                                    href="javascript:">
                                    <i class="tio-car nav-icon"></i>
                                    <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                        {{ translate('shipping_method') }}
                                    </span>
                                </a>
                                <ul class="js-navbar-vertical-aside-submenu nav nav-sub"
                                    style="display: {{ Request::is('admin/business-settings/shipping-method*') ? 'block' : 'none' }}">
                                    <li
                                        class="nav-item {{ Request::is('admin/business-settings/shipping-method/by/admin') ? 'active' : '' }}">
                                        <a class="nav-link"
                                            href="{{ route('admin.business-settings.shipping-method.by.admin') }}">
                                            <span class="tio-circle nav-indicator-icon"></span>
                                            <span class="text-truncate">
                                                {{ translate('by_admin') }}
                                            </span>
                                        </a>
                                    </li>
                                    {{--                                    <li class="nav-item {{Request::is('admin/business-settings/shipping-method/by/seller')?'active':''}}"> --}}
                                    {{--                                        <a class="nav-link" --}}
                                    {{--                                           href="{{route('admin.business-settings.shipping-method.by.seller')}}"> --}}
                                    {{--                                            <span class="tio-circle nav-indicator-icon"></span> --}}
                                    {{--                                            <span class="text-truncate"> --}}
                                    {{--                                                                                   {{translate('by_seller')}} --}}
                                    {{--                                                                                </span> --}}
                                    {{--                                        </a> --}}
                                    {{--                                    </li> --}}
                                    <li
                                        class="nav-item {{ Request::is('admin/business-settings/shipping-method/setting') ? 'active' : '' }}">
                                        <a class="nav-link"
                                            href="{{ route('admin.business-settings.shipping-method.setting') }}">
                                            <span class="tio-circle nav-indicator-icon"></span>
                                            <span class="text-truncate">
                                                {{ translate('Settings') }}
                                            </span>
                                        </a>
                                    </li>
                                </ul>
                            </li>

                            {{--                            <li class="navbar-vertical-aside-has-menu {{Request::is('admin/business-settings/language*')?'active':''}}"> --}}
                            {{--                                <a class="nav-link " href="{{route('admin.business-settings.language.index')}}" --}}
                            {{--                                   title="{{translate('languages')}}"> --}}
                            {{--                                    <i class="tio-book-opened nav-icon"></i> --}}
                            {{--                                    <span class="text-truncate">{{translate('languages')}}</span> --}}
                            {{--                                </a> --}}
                            {{--                            </li> --}}

                            {{--                            <li class="navbar-vertical-aside-has-menu {{Request::is('admin/business-settings/language*')?'active':''}}"> --}}
                            {{--                                <a class="js-navbar-vertical-aside-menu-link nav-link nav-link-toggle" --}}
                            {{--                                   href="javascript:"> --}}
                            {{--                                    <i class="tio-book-opened nav-icon"></i> --}}
                            {{--                                    <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate"> --}}
                            {{--                                                                    {{translate('languages')}} --}}
                            {{--                                                                </span> --}}
                            {{--                                </a> --}}
                            {{--                                <ul class="js-navbar-vertical-aside-submenu nav nav-sub" --}}
                            {{--                                    style="display: {{Request::is('admin/business-settings/language*')?'block':'none'}}"> --}}
                            {{--                                    <li class="nav-item {{Request::is('admin/business-settings/language-app')?'active':''}}"> --}}
                            {{--                                        <a class="nav-link" --}}
                            {{--                                           href="{{route('admin.business-settings.language.index-app')}}"> --}}
                            {{--                                            <span class="tio-circle nav-indicator-icon"></span> --}}
                            {{--                                            <span class="text-truncate"> --}}
                            {{--                                                                                                              {{translate('for_data_entry')}} --}}
                            {{--                                                                                                            </span> --}}
                            {{--                                        </a> --}}
                            {{--                                    </li> --}}
                            {{--                                    <li class="nav-item {{Request::is('admin/business-settings/language')?'active':''}}"> --}}
                            {{--                                        <a class="nav-link" --}}
                            {{--                                           href="{{route('admin.business-settings.language.index')}}"> --}}
                            {{--                                            <span class="tio-circle nav-indicator-icon"></span> --}}
                            {{--                                            <span class="text-truncate"> --}}
                            {{--                                                                                                               {{translate('for_website')}} --}}
                            {{--                                                                                                            </span> --}}
                            {{--                                        </a> --}}
                            {{--                                    </li> --}}
                            {{--                                </ul> --}}
                            {{--                            </li> --}}
                            <li
                                class="navbar-vertical-aside-has-menu {{ Request::is('admin/social-login/view') ? 'active' : '' }}">
                                <a class="js-navbar-vertical-aside-menu-link nav-link"
                                    href="{{ route('admin.social-login.view') }}">
                                    <i class="tio-top-security-outlined nav-icon"></i>
                                    <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                        {{ translate('social_login') }}
                                    </span>
                                </a>
                            </li>
                            <li
                                class="navbar-vertical-aside-has-menu {{ Request::is('admin/currency/view') ? 'active' : '' }}">
                                <a class="js-navbar-vertical-aside-menu-link nav-link"
                                    href="{{ route('admin.currency.view') }}">
                                    <i class="tio-dollar-outlined nav-icon"></i>
                                    <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                        {{ translate('currencies') }}
                                    </span>
                                </a>
                            </li>
                        @endif
                        <!--business settings ends here-->

                        @if (permission_check('web_&_app_settings'))
                            <li
                                class="nav-item {{ Request::is('admin/business-settings/social-media') || Request::is('admin/business-settings/terms-condition') || Request::is('admin/business-settings/privacy-policy') || Request::is('admin/business-settings/about-us') || Request::is('admin/helpTopic/list') || Request::is('admin/business-settings/fcm-index') || Request::is('admin/business-settings/mail') || Request::is('admin/business-settings/web-config') ? 'scroll-here' : '' }}">
                                <small class="nav-subtitle"
                                    title="">{{ translate('web_&_app_settings') }}</small>
                                <small class="tio-more-horizontal nav-subtitle-replacer"></small>
                            </li>

                            <li
                                class="navbar-vertical-aside-has-menu {{ Request::is('admin/business-settings/web-config') ? 'active' : '' }}">
                                <a class="js-navbar-vertical-aside-menu-link nav-link"
                                    href="{{ route('admin.business-settings.web-config.index') }}">
                                    <i class="tio-globe nav-icon"></i>
                                    <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                        {{ translate('web_config') }}
                                    </span>
                                </a>
                            </li>
                            <li
                                class="navbar-vertical-aside-has-menu {{ Request::is('admin/business-settings/captcha') ? 'active' : '' }}">
                                <a class="js-navbar-vertical-aside-menu-link nav-link"
                                    href="{{ route('admin.business-settings.captcha') }}">
                                    <i class="tio-panorama-image nav-icon"></i>
                                    <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                        {{ translate('recaptcha') }}
                                    </span>
                                </a>
                            </li>
                            <li
                                class="navbar-vertical-aside-has-menu {{ Request::is('admin/business-settings/mail') ? 'active' : '' }}">
                                <a class="js-navbar-vertical-aside-menu-link nav-link"
                                    href="{{ route('admin.business-settings.mail.index') }}">
                                    <i class="tio-email nav-icon"></i>
                                    <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                        {{ translate('mail_config') }}
                                    </span>
                                </a>
                            </li>
                            <li
                                class="navbar-vertical-aside-has-menu {{ Request::is('admin/business-settings/fcm-index') ? 'active' : '' }}">
                                <a class="js-navbar-vertical-aside-menu-link nav-link"
                                    href="{{ route('admin.business-settings.fcm-index') }}">
                                    <i class="tio-notifications-alert nav-icon"></i>
                                    <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                        {{ translate('notification') }}
                                    </span>
                                </a>
                            </li>



                            <li
                                class="navbar-vertical-aside-has-menu {{ Request::is('admin/business-settings/social-media') ? 'active' : '' }}">
                                <a class="js-navbar-vertical-aside-menu-link nav-link"
                                    href="{{ route('admin.business-settings.social-media') }}">
                                    <i class="tio-twitter nav-icon"></i>
                                    <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                        {{ translate('social_media') }}
                                    </span>
                                </a>
                            </li>
                            <li
                                class="navbar-vertical-aside-has-menu {{ Request::is('admin/business-settings/map-api*') ? 'active' : '' }}">
                                <a class="nav-link " href="{{ route('admin.business-settings.map-api') }}"
                                    title="{{ translate('third_party_apis') }}">
                                    <span class="tio-key nav-icon"></span>
                                    <span class="text-truncate">{{ translate('third_party_apis') }}</span>
                                </a>
                            </li>
                            <li
                                class="navbar-vertical-aside-has-menu {{ Request::is('admin/file-manager*') ? 'active' : '' }}">
                                <a class="js-navbar-vertical-aside-menu-link nav-link"
                                    href="{{ route('admin.file-manager.index') }}">
                                    <i class="tio-album nav-icon"></i>
                                    <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                        {{ translate('gallery') }}
                                    </span>
                                </a>
                            </li>
                        @endif
                        <!--web & app settings ends here-->

                        @if (permission_check('report'))
                            <li
                                class="nav-item {{ Request::is('admin/report/inhoue-product-sale') || Request::is('admin/report/seller-product-sale') || Request::is('admin/report/order') || Request::is('admin/report/earning') ? 'scroll-here' : '' }}">
                                <small class="nav-subtitle" title="">
                                    {{ translate('Report') }}& {{ translate('Analytics') }}
                                </small>
                                <small class="tio-more-horizontal nav-subtitle-replacer"></small>
                            </li>

                            <li
                                class="navbar-vertical-aside-has-menu {{ Request::is('admin/report/earning') ? 'active' : '' }}">
                                <a class="js-navbar-vertical-aside-menu-link nav-link"
                                    href="{{ route('admin.report.earning') }}">
                                    <i class="tio-chart-pie-1 nav-icon"></i>
                                    <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                        {{ translate('Earning') }} {{ translate('Report') }}
                                    </span>
                                </a>
                            </li>
                            <li
                                class="navbar-vertical-aside-has-menu {{ Request::is('admin/report/order') ? 'active' : '' }}">
                                <a class="js-navbar-vertical-aside-menu-link nav-link"
                                    href="{{ route('admin.report.order') }}">
                                    <i class="tio-chart-bar-1 nav-icon"></i>
                                    <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                        {{ translate('Order') }} {{ translate('Report') }}
                                    </span>
                                </a>
                            </li>
                            <li
                                class="navbar-vertical-aside-has-menu {{ Request::is('admin/report/inhoue-product-sale') || Request::is('admin/report/seller-product-sale') ? 'active' : '' }}">
                                <a class="js-navbar-vertical-aside-menu-link nav-link nav-link-toggle"
                                    href="javascript:">
                                    <i class="tio-chart-bar-4 nav-icon"></i>
                                    <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                        {{ translate('sale_report') }}
                                    </span>
                                </a>
                                <ul class="js-navbar-vertical-aside-submenu nav nav-sub"
                                    style="display: {{ Request::is('admin/report/inhoue-product-sale') || Request::is('admin/report/seller-product-sale') ? 'block' : 'none' }}">
                                    <li
                                        class="nav-item {{ Request::is('admin/report/inhoue-product-sale') ? 'active' : '' }}">
                                        <a class="nav-link" href="{{ route('admin.report.inhoue-product-sale') }}">
                                            <span class="tio-circle nav-indicator-icon"></span>
                                            <span class="text-truncate">
                                                {{ translate('inhouse') }} {{ translate('sale') }}
                                            </span>
                                        </a>
                                    </li>
                                    <li
                                        class="nav-item {{ Request::is('admin/report/seller-product-sale') ? 'active' : '' }}">
                                        <a class="nav-link" href="{{ route('admin.report.seller-product-sale') }}">
                                            <span class="tio-circle nav-indicator-icon"></span>
                                            <span class="text-truncate text-capitalize">
                                                {{ translate('seller') }} {{ translate('sale') }}
                                            </span>
                                        </a>
                                    </li>
                                </ul>
                            </li>
                        @endif
                        <!--reporting and analysis ends here-->

                        @if (permission_check('employee_section'))
                            <li
                                class="nav-item {{ Request::is('admin/employee*') || Request::is('admin/custom-role*') ? 'scroll-here' : '' }}">
                                <small class="nav-subtitle">{{ translate('employee_section') }}</small>
                                <small class="tio-more-horizontal nav-subtitle-replacer"></small>
                            </li>

                            <li
                                class="navbar-vertical-aside-has-menu {{ Request::is('admin/custom-role*') ? 'active' : '' }}">
                                <a class="js-navbar-vertical-aside-menu-link nav-link"
                                    href="{{ route('admin.custom-role.create') }}">
                                    <i class="tio-incognito nav-icon"></i>
                                    <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                        {{ translate('employee_role') }}</span>
                                </a>
                            </li>
                            <li
                                class="navbar-vertical-aside-has-menu {{ Request::is('admin/employee*') ? 'active' : '' }}">
                                <a class="js-navbar-vertical-aside-menu-link nav-link nav-link-toggle"
                                    href="javascript:">
                                    <i class="tio-user nav-icon"></i>
                                    <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                        {{ translate('employees') }}
                                    </span>
                                </a>
                                <ul class="js-navbar-vertical-aside-submenu nav nav-sub"
                                    style="display: {{ Request::is('admin/employee*') ? 'block' : 'none' }}">
                                    <li class="nav-item {{ Request::is('admin/employee/add-new') ? 'active' : '' }}">
                                        <a class="nav-link " href="{{ route('admin.employee.add-new') }}">
                                            <span class="tio-circle nav-indicator-icon"></span>
                                            <span class="text-truncate">{{ translate('add_new') }}</span>
                                        </a>
                                    </li>
                                    <li class="nav-item {{ Request::is('admin/employee/list') ? 'active' : '' }}">
                                        <a class="nav-link" href="{{ route('admin.employee.list') }}">
                                            <span class="tio-circle nav-indicator-icon"></span>
                                            <span class="text-truncate">{{ translate('List') }}</span>
                                        </a>
                                    </li>
                                </ul>
                            </li>
                        @endif

                        @if (permission_check('delivery_man_management'))
                            <li class="nav-item {{ Request::is('admin/delivery-man*') ? 'scroll-here' : '' }}">
                                <small class="nav-subtitle">{{ translate('delivery_man_management') }}</small>
                                <small class="tio-more-horizontal nav-subtitle-replacer"></small>
                            </li>

                            <li
                                class="navbar-vertical-aside-has-menu {{ Request::is('admin/delivery-man*') ? 'active' : '' }}">
                                <a class="js-navbar-vertical-aside-menu-link nav-link nav-link-toggle"
                                    href="javascript:">
                                    <i class="tio-user nav-icon"></i>
                                    <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                        {{ translate('delivery-man') }}
                                    </span>
                                </a>
                                <ul class="js-navbar-vertical-aside-submenu nav nav-sub"
                                    style="display: {{ Request::is('admin/delivery-man*') ? 'block' : 'none' }}">
                                    <li class="nav-item {{ Request::is('admin/delivery-man/add') ? 'active' : '' }}">
                                        <a class="nav-link " href="{{ route('admin.delivery-man.add') }}">
                                            <span class="tio-circle nav-indicator-icon"></span>
                                            <span class="text-truncate">{{ translate('add_new') }}</span>
                                        </a>
                                    </li>
                                    <li
                                        class="nav-item {{ Request::is('admin/delivery-man/list') ? 'active' : '' }}">
                                        <a class="nav-link" href="{{ route('admin.delivery-man.list') }}">
                                            <span class="tio-circle nav-indicator-icon"></span>
                                            <span class="text-truncate">{{ translate('List') }}</span>
                                        </a>
                                    </li>
                                </ul>
                            </li>
                        @endif

                        <li class="nav-item" style="padding-top: 50px">
                            <div class="nav-divider"></div>
                        </li>
                    </ul>
                </div>
                <!-- End Content -->
            </div>
        </div>
    </aside>
</div>
