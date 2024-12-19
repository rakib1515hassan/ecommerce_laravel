<style>
    .navbar-vertical .nav-link {
        color: #ffffff;
        font-weight: bold;
    }

    .navbar .nav-link:hover {
        color: #C6FFC1;
    }

    .navbar .active > .nav-link,
    .navbar .nav-link.active,
    .navbar .nav-link.show,
    .navbar .show > .nav-link {
        color: #C6FFC1;
    }

    .navbar-vertical .active .nav-indicator-icon,
    .navbar-vertical .nav-link:hover .nav-indicator-icon,
    .navbar-vertical .show > .nav-link > .nav-indicator-icon {
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
        margin-left: {
    {
        Session:: get('direction') = = = "rtl" ? '6px': ''
    }
    };
    }
</style>
<div id="sidebarMain" class="d-none">
    <aside
        style="background: #182c2f!important; text-align: {{ Session::get('direction') === ' rtl' ? 'right' : 'left' }};"
        class="js-navbar-vertical-aside navbar navbar-vertical-aside navbar-vertical navbar-vertical-fixed navbar-expand-xl navbar-bordered  ">
        <div class="navbar-vertical-container">
            <div class="navbar-vertical-footer-offset" style="padding-bottom: 0">
                <div class="navbar-brand-wrapper justify-content-between side-logo">
                    <a class="navbar-brand" href="{{ route('reseller.dashboard.index') }}" aria-label="Front">
                        <img onerror="this.src='{{ asset('assets/back-end/img/900x400/img1.jpg') }}'"
                             class="navbar-brand-logo-mini for-seller-logo"
                             src="{{ asset("assets/back-end/img/logo.png") }}" alt="Logo">
                    </a>
                    <!-- End Logo -->

                    <!-- Navbar Vertical Toggle -->
                    <button type="button"
                            class="js-navbar-vertical-aside-toggle-invoker navbar-vertical-aside-toggle btn btn-icon btn-xs btn-ghost-dark">
                        <i class="tio-clear tio-lg"></i>
                    </button>
                    <!-- End Navbar Vertical Toggle -->
                </div>

                <!-- Content -->
                <div class="navbar-vertical-content">
                    <ul class="navbar-nav navbar-nav-lg nav-tabs">
                        <!-- Dashboards -->
                        <li class="navbar-vertical-aside-has-menu {{ Request::is('reseller') ? 'show' : '' }}">
                            <a class="js-navbar-vertical-aside-menu-link nav-link"
                               href="{{ route('reseller.dashboard.index') }}">
                                <i class="tio-home-vs-1-outlined nav-icon"></i>
                                <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                    {{ translate('Dashboard') }}
                                </span>
                            </a>
                        </li>
                        <!-- End Dashboards -->
                        {{--                        <li class="nav-item">--}}
                        {{--                            <small class="nav-subtitle">{{ translate('product_management') }}</small>--}}
                        {{--                            <small class="tio-more-horizontal nav-subtitle-replacer"></small>--}}
                        {{--                        </li>--}}

                        <li
                                class="navbar-vertical-aside-has-menu {{ Request::is('product_manager/product*') ? 'active' : '' }}">
                            <a class="js-navbar-vertical-aside-menu-link nav-link"
                               href="{{ route('reseller.sale-product.index') }}"
                            >
                                <i class="tio-premium-outlined nav-icon"></i>
                                <span
                                        class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate text-capitalize">
                                    {{ translate('Sale Products') }}
                                </span>
                            </a>
                        </li>

                        {{-- <li
                            class="navbar-vertical-aside-has-menu {{ Request::is('seller/reviews/list*') ? 'active' : '' }}">
                            <a class="js-navbar-vertical-aside-menu-link nav-link"
                                href="{{ route('seller.reviews.list') }}">
                                <i class="tio-star nav-icon"></i>
                                <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                    {{ translate('Product') }} {{ translate('Reviews') }}
                                </span>
                            </a>
                        </li>


                        <li class="navbar-vertical-aside-has-menu {{ Request::is('seller/messages*') ? 'active' : '' }}">
                            <a class="js-navbar-vertical-aside-menu-link nav-link"
                                href="{{ route('seller.messages.chat') }}">
                                <i class="tio-email nav-icon"></i>
                                <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                    {{ translate('messages') }}
                                </span>
                            </a>
                        </li> --}}


                        <li class="navbar-vertical-aside-has-menu {{ Request::is('reseller/profile*') ? 'active' : '' }}">
                            <a class="js-navbar-vertical-aside-menu-link nav-link"
                               href="{{ route('reseller.profile.view') }}">
                                <i class="tio-shop nav-icon"></i>
                                <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                    {{ translate('my_bank_info') }}
                                </span>
                            </a>
                        </li>


                        <li class="navbar-vertical-aside-has-menu {{ Request::is('reseller/order*') ? 'active' : '' }}">
                            <a class="js-navbar-vertical-aside-menu-link nav-link"
                               href="{{ route('reseller.orders.list', 'all') }}">
                                <i class="tio-home nav-icon"></i>
                                <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                    {{ translate('My Order') }}
                                </span>
                            </a>
                        </li>

                        {{--

                        <li class="nav-item {{ Request::is('seller/reseller*') ? 'scroll-here' : '' }}">
                            <small class="nav-subtitle">{{ translate('reseller_management') }}</small>
                            <small class="tio-more-horizontal nav-subtitle-replacer"></small>
                        </li>
                        <li
                            class="navbar-vertical-aside-has-menu {{ Request::is('seller/reseller*') ? 'active' : '' }}">
                            <a class="js-navbar-vertical-aside-menu-link nav-link nav-link-toggle" href="javascript:">
                                <i class="tio-user nav-icon"></i>
                                <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                    {{ translate('reseller') }}
                                </span>
                            </a>
                            <ul class="js-navbar-vertical-aside-submenu nav nav-sub"
                                style="display: {{ Request::is('seller/reseller*') ? 'block' : 'none' }}">
                                <li class="nav-item {{ Request::is('seller/reseller/add') ? 'active' : '' }}">
                                    <a class="nav-link " href="{{ route('seller.reseller.add') }}">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">{{ translate('add_new') }}</span>
                                    </a>
                                </li>
                                <li class="nav-item {{ Request::is('seller/reseller/list') ? 'active' : '' }}">
                                    <a class="nav-link" href="{{ route('seller.reseller.list') }}">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">{{ translate('List') }}</span>
                                    </a>
                                </li>
                            </ul>
                        </li>


                        <li class="nav-item">
                            <small class="nav-subtitle">{{ translate('marketing') }}</small>
                            <small class="tio-more-horizontal nav-subtitle-replacer"></small>
                        </li>

                        <li class="navbar-vertical-aside-has-menu {{ Request::is('seller/deal/flash') ? 'active' : '' }}">
                            <a class="js-navbar-vertical-aside-menu-link nav-link"
                                href="{{ route('seller.deal.flash') }}">
                                <i class="tio-flash nav-icon"></i>
                                <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                    {{ translate('Flash Deal Manage') }}
                                </span>
                            </a>
                        </li>

                        <li
                            class="navbar-vertical-aside-has-menu {{ Request::is('seller/deal/feature') ? 'active' : '' }}">
                            <a class="js-navbar-vertical-aside-menu-link nav-link"
                                href="{{ route('seller.deal.feature.index') }}">
                                <i class="tio-flag-outlined nav-icon"></i>
                                <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                    {{ translate('Feature-Deal Manage') }}
                                </span>
                            </a>
                        </li> --}}
                        <!-- End Pages -->


                        {{-- Business Section Start--}}
                        <li class="nav-item {{ Request::is('reseller/business-settings*') ? 'scroll-here' : '' }}">
                            <small class="nav-subtitle" title="">{{ translate('business_section') }}</small>
                            <small class="tio-more-horizontal nav-subtitle-replacer"></small>
                        </li>

                        @php($shippingMethod = \App\Services\AdditionalServices::get_business_settings('shipping_method'))
                        @if ($shippingMethod == 'resellerwise_shipping')
                            <li
                                    class="navbar-vertical-aside-has-menu {{ Request::is('reseller/business-settings/shipping-method*') ? 'active' : '' }}">
                                <a class="js-navbar-vertical-aside-menu-link nav-link"
                                   href="{{ route('reseller.business-settings.shipping-method.add') }}">
                                    <i class="tio-settings nav-icon"></i>
                                    <span
                                            class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate text-capitalize">
                                        {{ translate('shipping_method') }}
                                    </span>
                                </a>
                            </li>
                        @endif


                        <li
                                class="navbar-vertical-aside-has-menu {{ Request::is('reseller/business-settings/withdraw*') ? 'active' : '' }}">
                            <a class="js-navbar-vertical-aside-menu-link nav-link"
                               href="{{ route('reseller.business-settings.withdraw.list') }}"
                            >
                                <i class="tio-wallet-outlined nav-icon"></i>
                                <span
                                        class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate text-capitalize">
                                    {{ translate('withdraws') }}
                                </span>
                            </a>
                        </li>
                    </ul>
                </div>
                <!-- End Content -->
            </div>
        </div>
    </aside>
</div>
