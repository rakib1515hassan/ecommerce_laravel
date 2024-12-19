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
        margin-left: {
                {
                Session: :get('direction')==="rtl"? '6px': ''
            }
        }

        ;
    }
</style>
<div id="sidebarMain" class="d-none">
    <aside style="background: #182c2f!important; text-align: {{Session::get('direction') === " rtl" ? 'right' : 'left'
        }};"
        class="js-navbar-vertical-aside navbar navbar-vertical-aside navbar-vertical navbar-vertical-fixed navbar-expand-xl navbar-bordered  ">
        <div class="navbar-vertical-container">
            <div class="navbar-vertical-footer-offset" style="padding-bottom: 0">
                <div class="navbar-brand-wrapper justify-content-between side-logo">
                    <!-- Logo -->
                    @php($seller_logo=\App\Models\Shop::where(['seller_id'=>auth('seller')->id()])->first()->image)
                    <a class="navbar-brand" href="{{route('seller.dashboard.index')}}" aria-label="Front">
                        <img onerror="this.src='{{asset('assets/back-end/img/900x400/img1.jpg')}}'"
                            class="navbar-brand-logo-mini for-seller-logo" src="{{asset(" storage/order/$seller_logo")}}"
                            alt="Logo">
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
                        <li class="navbar-vertical-aside-has-menu {{Request::is('seller')?'show':''}}">
                            <a class="js-navbar-vertical-aside-menu-link nav-link"
                                href="{{route('seller.dashboard.index')}}">
                                <i class="tio-home-vs-1-outlined nav-icon"></i>
                                <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                    {{translate('Dashboard')}}
                                </span>
                            </a>
                        </li>
                        <!-- End Dashboards -->


                        <li class="nav-item">
                            <small class="nav-subtitle">{{translate('order_management')}}</small>
                            <small class="tio-more-horizontal nav-subtitle-replacer"></small>
                        </li>

                        <!-- Pages -->
                        <li class="navbar-vertical-aside-has-menu {{Request::is('seller/orders*')?'active':''}}">
                            <a class="js-navbar-vertical-aside-menu-link nav-link nav-link-toggle" href="javascript:">
                                <i class="tio-shopping-cart nav-icon"></i>
                                <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                    {{translate('orders')}}
                                </span>
                            </a>
                            @php($sellerId = auth('seller')->id())
                            <ul class="js-navbar-vertical-aside-submenu nav nav-sub"
                                style="display: {{Request::is('seller/order*')?'block':'none'}}">

                                <li class="nav-item {{Request::is('seller/orders/list/all')?'active':''}}">
                                    <a class="nav-link " href="{{route('seller.orders.list',['all'])}}" title="">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">{{translate('All')}}</span>
                                        <span class="badge badge-info badge-pill {{Session::get('direction') === " rtl"
                                            ? 'mr-1' : 'ml-1' }}">
                                            {{
                                            \App\Models\Order::where(['seller_is'=>'seller'])->where(['seller_id'=>$sellerId])->count()}}
                                        </span>
                                    </a>
                                </li>
                                <li class="nav-item {{Request::is('seller/orders/list/pending')?'active':''}}">
                                    <a class="nav-link " href="{{route('seller.orders.list',['pending'])}}" title="">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">{{translate('Pending')}}</span>
                                        <span class="badge badge-soft-info badge-pill {{Session::get('direction') === "
                                            rtl" ? 'mr-1' : 'ml-1' }}">
                                            {{
                                            \App\Models\Order::where(['seller_is'=>'seller'])->where(['seller_id'=>$sellerId])->where(['order_status'=>'pending'])->count()}}
                                        </span>
                                    </a>
                                </li>
                                <li class="nav-item {{Request::is('seller/orders/list/confirmed')?'active':''}}">
                                    <a class="nav-link " href="{{route('seller.orders.list',['confirmed'])}}" title="">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">{{translate('confirmed')}}</span>
                                        <span class="badge badge-soft-info badge-pill {{Session::get('direction') === "
                                            rtl" ? 'mr-1' : 'ml-1' }}">
                                            {{
                                            \App\Models\Order::where(['seller_is'=>'seller'])->where(['seller_id'=>$sellerId])->where(['order_status'=>'confirmed'])->count()}}
                                        </span>
                                    </a>
                                </li>

                                <li class="nav-item {{Request::is('seller/orders/list/processing')?'active':''}}">
                                    <a class="nav-link " href="{{route('seller.orders.list',['processing'])}}" title="">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">{{translate('Processing')}}</span>
                                        <span class="badge badge-warning badge-pill {{Session::get('direction') === "
                                            rtl" ? 'mr-1' : 'ml-1' }}">
                                            {{
                                            \App\Models\Order::where(['seller_is'=>'seller'])->where(['seller_id'=>$sellerId])->where(['order_status'=>'processing'])->count()}}
                                        </span>
                                    </a>
                                </li>

                                <li class="nav-item {{Request::is('seller/orders/list/out_for_delivery')?'active':''}}">
                                    <a class="nav-link " href="{{route('seller.orders.list',['out_for_delivery'])}}"
                                        title="">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">{{translate('out_for_delivery')}}</span>
                                        <span class="badge badge-warning badge-pill {{Session::get('direction') === "
                                            rtl" ? 'mr-1' : 'ml-1' }}">
                                            {{
                                            \App\Models\Order::where(['seller_is'=>'seller'])->where(['seller_id'=>$sellerId])->where(['order_status'=>'out_for_delivery'])->count()}}
                                        </span>
                                    </a>
                                </li>

                                <li class="nav-item {{Request::is('seller/orders/list/delivered')?'active':''}}">
                                    <a class="nav-link " href="{{route('seller.orders.list',['delivered'])}}" title="">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">{{translate('Delivered')}}</span>
                                        <span class="badge badge-success badge-pill {{Session::get('direction') === "
                                            rtl" ? 'mr-1' : 'ml-1' }}">
                                            {{
                                            \App\Models\Order::where(['seller_is'=>'seller'])->where(['seller_id'=>$sellerId])->where(['order_status'=>'delivered'])->count()}}
                                        </span>
                                    </a>
                                </li>
                                <li class="nav-item {{Request::is('seller/orders/list/returned')?'active':''}}">
                                    <a class="nav-link " href="{{route('seller.orders.list',['returned'])}}" title="">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">{{translate('Returned')}}</span>
                                        <span
                                            class="badge badge-soft-danger badge-pill {{Session::get('direction') === "
                                            rtl" ? 'mr-1' : 'ml-1' }}">
                                            {{
                                            \App\Models\Order::where(['seller_is'=>'seller'])->where(['seller_id'=>$sellerId])->where(['order_status'=>'returned'])->count()}}
                                        </span>
                                    </a>
                                </li>
                                <li class="nav-item {{Request::is('seller/orders/list/failed')?'active':''}}">
                                    <a class="nav-link " href="{{route('seller.orders.list',['failed'])}}" title="">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">{{translate('Failed')}}</span>
                                        <span class="badge badge-danger badge-pill {{Session::get('direction') === "
                                            rtl" ? 'mr-1' : 'ml-1' }}">
                                            {{
                                            \App\Models\Order::where(['seller_is'=>'seller'])->where(['seller_id'=>$sellerId])->where(['order_status'=>'failed'])->count()}}
                                        </span>
                                    </a>
                                </li>
                                <li class="nav-item {{Request::is('seller/orders/list/canceled')?'active':''}}">
                                    <a class="nav-link " href="{{route('seller.orders.list',['canceled'])}}" title="">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">{{translate('canceled')}}</span>
                                        <span class="badge badge-danger badge-pill {{Session::get('direction') === "
                                            rtl" ? 'mr-1' : 'ml-1' }}">
                                            {{
                                            \App\Models\Order::where(['seller_is'=>'seller'])->where(['seller_id'=>$sellerId])->where(['order_status'=>'canceled'])->count()}}
                                        </span>
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <!-- End Pages -->

                        <li class="nav-item">
                            <small class="nav-subtitle">{{translate('product_management')}}</small>
                            <small class="tio-more-horizontal nav-subtitle-replacer"></small>
                        </li>
                        <li>
                            <a class="js-navbar-vertical-aside-menu-link nav-link" href="{{route('seller.product_manager.list')}}">
                                <i class="tio-premium-outlined nav-icon"></i>
                                <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                    {{translate('Product_Manager')}}
                                </span>
                            </a>
                        </li>


                        <li class="navbar-vertical-aside-has-menu {{(Request::is('seller/product*'))?'active':''}}">
                            <a class="js-navbar-vertical-aside-menu-link nav-link nav-link-toggle" href="javascript:">
                                <i class="tio-premium-outlined nav-icon"></i>
                                <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                    {{translate('Products')}}
                                </span>
                            </a>

                            <ul class="js-navbar-vertical-aside-submenu nav nav-sub"
                                style="display: {{(Request::is('seller/product*'))?'block':''}}">
                                <li class="nav-item {{Request::is('seller/product/list')?'active':''}}">
                                    <a class="nav-link " href="{{route('seller.product.list')}}">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">{{translate('Products')}}</span>
                                    </a>
                                </li>
                                <li
                                    class="nav-item {{Request::is('seller/product/stock-limit-list/in_house')?'active':''}}">
                                    <a class="nav-link "
                                        href="{{route('seller.product.stock-limit-list',['in_house', ''])}}">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">{{translate('stock_limit_products')}}</span>
                                    </a>
                                </li>

                                <li class="nav-item {{Request::is('seller/product/bulk-import')?'active':''}}">
                                    <a class="nav-link " href="{{route('seller.product.bulk-import')}}">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">{{translate('bulk_import')}}</span>
                                    </a>
                                </li>

                                <li class="nav-item {{Request::is('seller/product/bulk-export')?'active':''}}">
                                    <a class="nav-link " href="{{route('seller.product.bulk-export')}}">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">{{translate('bulk_export')}}</span>
                                    </a>
                                </li>
                            </ul>
                        </li>

                        <li class="navbar-vertical-aside-has-menu {{Request::is('seller/reviews/list*')?'active':''}}">
                            <a class="js-navbar-vertical-aside-menu-link nav-link"
                                href="{{route('seller.reviews.list')}}">
                                <i class="tio-star nav-icon"></i>
                                <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                    {{translate('Product')}} {{translate('Reviews')}}
                                </span>
                            </a>
                        </li>


                        <li class="navbar-vertical-aside-has-menu {{Request::is('seller/messages*')?'active':''}}">
                            <a class="js-navbar-vertical-aside-menu-link nav-link"
                                href="{{route('seller.messages.chat')}}">
                                <i class="tio-email nav-icon"></i>
                                <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                    {{translate('messages')}}
                                </span>
                            </a>
                        </li>


                        <li class="navbar-vertical-aside-has-menu {{Request::is('seller/profile*')?'active':''}}">
                            <a class="js-navbar-vertical-aside-menu-link nav-link"
                                href="{{route('seller.profile.view')}}">
                                <i class="tio-shop nav-icon"></i>
                                <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                    {{translate('my_bank_info')}}
                                </span>
                            </a>
                        </li>


                        <li class="navbar-vertical-aside-has-menu {{Request::is('seller/order*')?'active':''}}">
                            <a class="js-navbar-vertical-aside-menu-link nav-link" href="{{route('seller.order.view')}}">
                                <i class="tio-home nav-icon"></i>
                                <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                    {{translate('my_shop')}}
                                </span>
                            </a>
                        </li>


                        <li class="nav-item">
                            <small class="nav-subtitle">{{translate('marketing')}}</small>
                            <small class="tio-more-horizontal nav-subtitle-replacer"></small>
                        </li>

                        <li class="navbar-vertical-aside-has-menu {{Request::is('seller/deal/flash')?'active':''}}">
                            <a class="js-navbar-vertical-aside-menu-link nav-link"
                                href="{{route('seller.deal.flash')}}">
                                <i class="tio-flash nav-icon"></i>
                                <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                    {{translate('Flash Deal Manage')}}
                                </span>
                            </a>
                        </li>

                        <li class="navbar-vertical-aside-has-menu {{Request::is('seller/deal/feature')?'active':''}}">
                            <a class="js-navbar-vertical-aside-menu-link nav-link"
                                href="{{route('seller.deal.feature.index')}}">
                                <i class="tio-flag-outlined nav-icon"></i>
                                <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                    {{translate('Feature-Deal Manage')}}
                                </span>
                            </a>
                        </li>

                        <!-- End Pages -->
                        <li class="nav-item {{( Request::is('seller/business-settings*'))?'scroll-here':''}}">
                            <small class="nav-subtitle" title="">{{translate('business_section')}}</small>
                            <small class="tio-more-horizontal nav-subtitle-replacer"></small>
                        </li>

                        @php($shippingMethod =
                        \App\Services\AdditionalServices::get_business_settings('shipping_method'))
                        @if($shippingMethod=='sellerwise_shipping')
                        <li
                            class="navbar-vertical-aside-has-menu {{Request::is('seller/business-settings/shipping-method*')?'active':''}}">
                            <a class="js-navbar-vertical-aside-menu-link nav-link"
                                href="{{route('seller.business-settings.shipping-method.add')}}">
                                <i class="tio-settings nav-icon"></i>
                                <span
                                    class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate text-capitalize">
                                    {{translate('shipping_method')}}
                                </span>
                            </a>
                        </li>
                        @endif

                        <li
                            class="navbar-vertical-aside-has-menu {{Request::is('seller/business-settings/withdraw*')?'active':''}}">
                            <a class="js-navbar-vertical-aside-menu-link nav-link"
                                href="{{route('seller.business-settings.withdraw.list')}}">
                                <i class="tio-wallet-outlined nav-icon"></i>
                                <span
                                    class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate text-capitalize">
                                    {{translate('withdraws')}}
                                </span>
                            </a>
                        </li>

                        <li
                            class="navbar-vertical-aside-has-menu {{Request::is('seller/business-settings/qa*')?'active':''}}">
                            <a class="js-navbar-vertical-aside-menu-link nav-link"
                                href="{{route('seller.business-settings.qa.index')}}">
                                <i class="tio-wallet-outlined nav-icon"></i>
                                <span
                                    class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate text-capitalize">
                                    {{translate('Question and Answering')}}
                                </span>
                            </a>
                        </li>

                        <li class="navbar-vertical-aside-has-menu {{Request::is('seller/redx*')?'active':''}}">
                            <a class="js-navbar-vertical-aside-menu-link nav-link"
                                href="{{route('seller.business-settings.redx.profile')}}">
                                <i class="tio-wallet-outlined nav-icon"></i>
                                <span
                                    class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate text-capitalize">
                                    {{translate('RedX Information')}}
                                </span>
                            </a>
                        </li>

                        @php( $shipping_method =
                        \App\Services\AdditionalServices::get_business_settings('shipping_method'))
                        @if($shipping_method=='sellerwise_shipping')
                        <li class="nav-item {{Request::is('seller/delivery-man*')?'scroll-here':''}}">
                            <small class="nav-subtitle">{{translate('delivery_man_management')}}</small>
                            <small class="tio-more-horizontal nav-subtitle-replacer"></small>
                        </li>
                        <li class="navbar-vertical-aside-has-menu {{Request::is('seller/delivery-man*')?'active':''}}">
                            <a class="js-navbar-vertical-aside-menu-link nav-link nav-link-toggle" href="javascript:">
                                <i class="tio-user nav-icon"></i>
                                <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                    {{translate('delivery-man')}}
                                </span>
                            </a>
                            <ul class="js-navbar-vertical-aside-submenu nav nav-sub"
                                style="display: {{Request::is('seller/delivery-man*')?'block':'none'}}">
                                <li class="nav-item {{Request::is('seller/delivery-man/add')?'active':''}}">
                                    <a class="nav-link " href="{{route('seller.delivery-man.add')}}">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">{{translate('add_new')}}</span>
                                    </a>
                                </li>
                                <li class="nav-item {{Request::is('seller/delivery-man/list')?'active':''}}">
                                    <a class="nav-link" href="{{route('seller.delivery-man.list')}}">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">{{translate('List')}}</span>
                                    </a>
                                </li>
                            </ul>
                        </li>
                        @endif
                    </ul>
                </div>
                <!-- End Content -->
            </div>
        </div>
    </aside>
</div>
