@extends('layouts.back-end.app')

@section('title', translate('Order Details'))

@push('css_or_js')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        .sellerName {
            height: fit-content;
            margin-top: 10px;
            margin-left: 10px;
            font-size: 16px;
            border-radius: 25px;
            text-align: center;
            padding-top: 10px;
        }

        .stepper {
            .line {
                width: 2px;
                background-color: lightgrey !important;
            }

            .line:last-child {
                display: none;
            }

            .lead {
                font-size: 1.1rem;
            }
        }
    </style>
@endpush



@section('content')
    <div class="content container-fluid">
        <!-- Page Header -->
        <div class="page-header d-print-none p-3" style="background: white">
            <div class="row align-items-center">
                <div class="col-sm mb-2 mb-sm-0">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb breadcrumb-no-gutter">
                            <li class="breadcrumb-item"><a class="breadcrumb-link"
                                    href="{{ route('admin.orders.list', ['status' => 'all']) }}">{{ translate('Orders') }}</a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">{{ translate('Order') }}
                                {{ translate('details') }} </li>
                        </ol>
                    </nav>

                    <div class="d-sm-flex align-items-sm-center">
                        <h1 class="page-header-title">{{ translate('Order') }} #{{ $order['id'] }}</h1>

                        @if ($order['payment_status'] == 'paid')
                            <span class="badge badge-soft-success ml-sm-3">
                                <span class="legend-indicator bg-success"></span>{{ translate('Paid') }}
                            </span>
                        @else
                            <span class="badge badge-soft-danger ml-sm-3">
                                <span class="legend-indicator bg-danger"></span>{{ translate('Unpaid') }}
                            </span>
                        @endif

                        @if ($order['order_status'] == 'pending')
                            <span class="badge badge-soft-info ml-2 ml-sm-3 text-capitalize">
                                <span
                                    class="legend-indicator bg-info text"></span>{{ str_replace('_', ' ', $order['order_status']) }}
                            </span>
                        @elseif($order['order_status'] == 'failed')
                            <span class="badge badge-danger ml-2 ml-sm-3 text-capitalize">
                                <span
                                    class="legend-indicator bg-info"></span>{{ str_replace('_', ' ', $order['order_status']) }}
                            </span>
                        @elseif($order['order_status'] == 'processing' || $order['order_status'] == 'out_for_delivery')
                            <span class="badge badge-soft-warning ml-2 ml-sm-3 text-capitalize">
                                <span
                                    class="legend-indicator bg-warning"></span>{{ str_replace('_', ' ', $order['order_status']) }}
                            </span>
                        @elseif($order['order_status'] == 'delivered' || $order['order_status'] == 'confirmed')
                            <span class="badge badge-soft-success ml-2 ml-sm-3 text-capitalize">
                                <span
                                    class="legend-indicator bg-success"></span>{{ str_replace('_', ' ', $order['order_status']) }}
                            </span>
                        @else
                            <span class="badge badge-soft-danger ml-2 ml-sm-3 text-capitalize">
                                <span
                                    class="legend-indicator bg-danger"></span>{{ str_replace('_', ' ', $order['order_status']) }}
                            </span>
                        @endif
                        <span class="ml-2 ml-sm-3">
                            <i class="tio-date-range"></i> {{ date('d M Y H:i:s', strtotime($order['created_at'])) }}
                        </span>

                        @if (\App\Services\AdditionalServices::get_business_settings('order_verification'))
                            <span class="ml-2 ml-sm-3">
                                <b>
                                    {{ translate('order_verification_code') }} : {{ $order['verification_code'] }}
                                </b>
                            </span>
                        @endif
                    </div>
                    <div class="col-md-6 mt-2">
                        <a class="text-body mr-3" target="_blank"
                            href={{ route('admin.orders.generate-invoice', [$order['id']]) }}>
                            <i class="tio-print mr-1"></i> {{ translate('Print') }} {{ translate('invoice') }}
                        </a>

                        <button class="btn btn-xs btn-warning"><i class="tio-map"></i>
                            {{ translate('shipping_address_has_been_given_below') }}
                        </button>
                    </div>

                    <div class="row">
                        <div class="col-6 mt-4">
                            <label class="badge badge-info">{{ translate('linked_orders') }}
                                : {{ $linked_orders->count() }}</label><br>
                            @foreach ($linked_orders as $linked)
                                <a href="{{ route('admin.orders.details', [$linked['id']]) }}"
                                    class="btn btn-secondary">{{ translate('ID') }}
                                    :{{ $linked['id'] }}</a>
                            @endforeach
                        </div>

                        <div class="col-6">
                            <div class="hs-unfold float-right">
                                <div class="dropdown">


                                    <select name="order_status" onchange="order_status(this.value)"
                                        class="status form-control" data-id="{{ $order['id'] }}">

                                        <option value="">Select Status</option>

                                        <?php
                                            $status_arr = [
                                                'pending', 
                                                'confirmed', 
                                                'processing', 
                                                // 'shipped', 
                                                'out_for_delivery', 
                                                'delivered', 
                                                'returned', 
                                                'failed', 
                                                'canceled'
                                            ];
                                            $index_of_current_status = array_search($order->order_status, $status_arr);
                                        
                                            for ($i = $index_of_current_status + 1; $i < count($status_arr); $i++) {
                                                $is_selected = $order->order_status == $status_arr[$i] ? 'selected' : '';
                                                echo "<option value='{$status_arr[$i]}' {$is_selected}>{$status_arr[$i]}</option>";
                                            }
                                        ?>
                                    </select>
                                </div>
                            </div>


                            <div class="hs-unfold float-right pr-2">
                                <div class="dropdown">
                                    <select name="payment_status" class="payment_status form-control"
                                        data-id="{{ $order['id'] }}">

                                        <option
                                            onclick="route_alert('{{ route('admin.orders.payment-status', ['id' => $order['id'], 'payment_status' => 'paid']) }}','Change status to paid ?')"
                                            href="javascript:" value="paid"
                                            {{ $order->payment_status == 'paid' ? 'selected' : '' }}>
                                            {{ translate('Paid') }}
                                        </option>
                                        <option value="unpaid" {{ $order->payment_status == 'unpaid' ? 'selected' : '' }}>
                                            {{ translate('Unpaid') }}
                                        </option>

                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- End Unfold -->
                </div>
            </div>
        </div>

        <!-- End Page Header -->
        <div class="row" id="printableArea">
            <div class="col-lg-8 mb-3 mb-lg-0">
                <!-- Card -->
                <div class="card mb-3 mb-lg-5">
                    <!-- Header -->
                    <div class="card-header" style="display: block!important;">
                        <div class="row">
                            <div class="col-12 pb-2 border-bottom">
                                <h4 class="card-header-title">
                                    {{ translate('Order') }} {{ translate('details') }}
                                    <span
                                        class="badge badge-soft-dark rounded-circle ml-1">{{ $order->details->count() }}</span>
                                </h4>
                            </div>

                            <div class="col-6 pt-2">
                                @if ($order->order_note != null)
                                    <span class="font-weight-bold text-capitalize">
                                        {{ translate('order_note') }} :
                                    </span>
                                    <p class="pl-1">
                                        {{ $order->order_note }}
                                    </p>
                                @endif
                            </div>
                            <div class="col-6 pt-2">
                                <div class="text-right">
                                    <h6 class="" style="color: #8a8a8a;">
                                        {{ translate('Payment') }} {{ translate('Method') }}
                                        : {{ str_replace('_', ' ', $order['payment_method']) }}
                                    </h6>
                                    <h6 class="" style="color: #8a8a8a;">
                                        {{ translate('Payment') }} {{ translate('reference') }}
                                        : {{ str_replace('_', ' ', $order['transaction_ref']) }}
                                    </h6>
                                    <h6 class="" style="color: #8a8a8a;">
                                        {{ translate('shipping') }} {{ translate('method') }}
                                        : {{ $order->shipping ? $order->shipping->title : 'No shipping method selected' }}
                                    </h6>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- End Header -->

                    <!-- Body -->
                    <div class="card-body">
                        <div class="media">
                            <div class="avatar avatar-xl mr-3">
                                <p>{{ translate('image') }}</p>
                            </div>

                            <div class="media-body">
                                <div class="row">
                                    <div class="col-md-4 product-name">
                                        <p> {{ translate('Name') }}</p>
                                    </div>

                                    <div class="col col-md-2 align-self-center p-0 ">
                                        <p> {{ translate('price') }}</p>
                                    </div>

                                    <div class="col col-md-1 align-self-center">
                                        <p>Q</p>
                                    </div>
                                    <div class="col col-md-1 align-self-center  p-0 product-name">
                                        <p> {{ translate('TAX') }}</p>
                                    </div>
                                    <div class="col col-md-2 align-self-center  p-0 product-name">
                                        <p> {{ translate('Discount') }}</p>
                                    </div>

                                    <div class="col col-md-2 align-self-center text-right  ">
                                        <p> {{ translate('Subtotal') }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @php($subtotal = 0)
                        @php($total = 0)
                        @php($shipping = 0)
                        @php($discount = 0)
                        @php($tax = 0)
                        @foreach ($order->details as $key => $detail)

                            @if ($detail->product)
                                @if ($key == 0)
                                    @if ($detail->product->added_by == 'admin')
                                        <div class="row">
                                            <img class="avatar-img" style="width: 55px;height: 55px; border-radius: 50%;"
                                                onerror="this.src='{{ asset('assets/front-end/img/image-place-holder.png') }}'"
                                                src="{{ asset('storage/company/' . \App\Services\AdditionalServices::get_business_settings('company_footer_logo')) }}"
                                                alt="Image">
                                            <p class="sellerName">
                                                <a style="color: black;" href="javascript:">
                                                    {{ \App\Services\AdditionalServices::get_business_settings('company_name') }}
                                                </a>
                                            </p>
                                        </div>
                                    @else
                                        <div class="row">
                                            <img class="avatar-img" style="width: 55px;height: 55px; border-radius: 50%;"
                                                onerror="this.src='{{ asset('assets/front-end/img/image-place-holder.png') }}'"
                                                src="{{ asset('storage/order/' . \App\Models\Shop::where('seller_id', '=', $detail->seller_id)->first()->image) }}"
                                                alt="Image">
                                            <p class="sellerName">
                                                <a style="color: black;"
                                                    href="{{ route('admin.sellers.view', $detail->seller_id) }}">{{ \App\Models\Shop::where('seller_id', '=', $detail->seller_id)->first()->name }}</a>
                                                <i class="tio tio-info-outined ml-4" data-toggle="collapse"
                                                    style="font-size: 20px; cursor: pointer"
                                                    data-target="#sellerInfoCollapse-{{ $detail->id }}"
                                                    aria-expanded="false"></i>
                                            </p>
                                        </div>

                                        @php($seller = App\Models\Seller::with('orders')->find($detail->seller_id))
                                        <div class="collapse" id="sellerInfoCollapse-{{ $detail->id }}">
                                            <div class="row card-body mb-3">
                                                <div class="col-6">
                                                    <h4>
                                                        {{ translate('Status') }}
                                                        : {!! $seller->status == 'approved'
                                                            ? '<label class="badge badge-success">Active</label>'
                                                            : '<label class="badge badge-danger">In-Active</label>' !!}
                                                    </h4>
                                                    <h5>{{ translate('Email') }} : <a class="text-dark"
                                                            href="mailto:{{ $seller->email }}">{{ $seller->email }}</a>
                                                    </h5>
                                                </div>
                                                <div class="col-6">
                                                    <h5>{{ translate('name') }} : <a class="text-dark"
                                                            href="{{ route('admin.sellers.view', [$seller['id']]) }}">{{ $seller['name'] }}</a>
                                                    </h5>
                                                    <h5>{{ translate('Phone') }} : <a class="text-dark"
                                                            href="tel:{{ $seller->phone }}">{{ $seller->phone }}</a>
                                                    </h5>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                @endif
                                <!-- Media -->
                                <div class="media">
                                    <div class="avatar avatar-xl mr-3">
                                        <img class="img-fluid"
                                            onerror="this.src='{{ asset('assets/back-end/img/160x160/img2.jpg') }}'"
                                            src="{{ \App\Services\ProductManager::product_image_path('thumbnail') }}/{{ $detail->product['thumbnail'] }}"
                                            alt="Image Description">
                                    </div>

                                    <div class="media-body">
                                        <div class="row">
                                            <div class="col-md-4 mb-3 mb-md-0 product-name">
                                                <p>
                                                    {{ substr($detail->product['name'], 0, 30) }}{{ strlen($detail->product['name']) > 10 ? '...' : '' }}
                                                </p>
                                                <strong><u>{{ translate('Variation') }} : </u></strong>

                                                <div class="font-size-sm text-body">

                                                    <span class="font-weight-bold">{{ $detail['variant'] }}</span>
                                                </div>
                                            </div>

                                            <div class="col col-md-2 align-self-center p-0 ">
                                                <h6>{{ \App\Services\BackEndHelper::set_symbol(\App\Services\BackEndHelper::usd_to_currency($detail['price'])) }}
                                                </h6>
                                            </div>

                                            <div class="col col-md-1 align-self-center">

                                                <h5>{{ $detail->qty }}</h5>
                                            </div>
                                            <div class="col col-md-1 align-self-center  p-0 product-name">

                                                <h5>{{ \App\Services\BackEndHelper::set_symbol(\App\Services\BackEndHelper::usd_to_currency($detail['tax'])) }}
                                                </h5>
                                            </div>
                                            <div class="col col-md-2 align-self-center  p-0 product-name">

                                                <h5>
                                                    {{ \App\Services\BackEndHelper::set_symbol(\App\Services\BackEndHelper::usd_to_currency($detail['discount'])) }}
                                                </h5>
                                            </div>

                                            <div class="col col-md-2 align-self-center text-right  ">
                                                @php($subtotal = $detail['price'] * $detail->qty + $detail['tax'] - $detail['discount'])

                                                <h5 style="font-size: 12px">
                                                    {{ \App\Services\BackEndHelper::set_symbol(\App\Services\BackEndHelper::usd_to_currency($subtotal)) }}
                                                </h5>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                {{-- seller info old --}}

                                @php($discount += $detail['discount'])
                                @php($tax += $detail['tax'])
                                @php($total += $subtotal)
                                <!-- End Media -->
                                <hr>
                            @endif
                            @php($sellerId = $detail->seller_id)
                        @endforeach
                        @php($shipping = $order['shipping_cost'])
                        @php($coupon_discount = $order['discount_amount'])
                        {{-- <div>

                        </div> --}}
                        <div class="row justify-content-md-end mb-3">
                            <div class="col-md-9 col-lg-8">
                                <dl class="row text-sm-right">
                                    <dt class="col-sm-6">{{ translate('Shipping') }}</dt>
                                    <dd class="col-sm-6 border-bottom">
                                        <strong>{{ \App\Services\BackEndHelper::set_symbol(\App\Services\BackEndHelper::usd_to_currency($shipping)) }}</strong>
                                    </dd>

                                    <dt class="col-sm-6">{{ translate('coupon_discount') }}</dt>
                                    <dd class="col-sm-6 border-bottom">
                                        <strong>-
                                            {{ \App\Services\BackEndHelper::set_symbol(\App\Services\BackEndHelper::usd_to_currency($coupon_discount)) }}</strong>
                                    </dd>

                                    <dt class="col-sm-6">{{ translate('Total') }}</dt>
                                    <dd class="col-sm-6">
                                        <strong>{{ \App\Services\BackEndHelper::set_symbol(\App\Services\BackEndHelper::usd_to_currency($total + $shipping - $coupon_discount)) }}</strong>
                                    </dd>
                                </dl>
                                <!-- End Row -->
                            </div>
                        </div>
                        <!-- End Row -->
                    </div>
                    <!-- End Body -->
                </div>
                <!-- End Card -->
            </div>

            <div class="col-lg-4">
                <!-- Card -->
                <div class="card">
                    <!-- Header -->
                    <div class="card-header">
                        <h4 class="card-header-title">{{ translate('Customer') }}</h4>
                    </div>
                    <!-- End Header -->

                    <!-- Body -->
                    @if ($order->customer)
                        <div class="card-body">
                            <div class="media align-items-center" href="javascript:">
                                <div class="avatar avatar-circle mr-3">
                                    <img class="avatar-img" style="width: 75px;height: 42px"
                                        onerror="this.src='{{ asset('assets/front-end/img/image-place-holder.png') }}'"
                                        src="{{ asset('storage/profile/' . $order->customer->image) }}" alt="Image">
                                </div>
                                <div class="media-body">
                                    <span
                                        class="text-body text-hover-primary">{{ $order->customer['f_name'] . ' ' . $order->customer['l_name'] }}</span>
                                </div>
                                <div class="media-body text-right">
                                    {{--                                    <i class="tio-chevron-right text-body"></i> --}}
                                </div>
                            </div>

                            <hr>

                            <div class="media align-items-center" href="javascript:">
                                <div class="icon icon-soft-info icon-circle mr-3">
                                    <i class="tio-shopping-basket-outlined"></i>
                                </div>
                                <div class="media-body">
                                    <span class="text-body text-hover-primary">
                                        {{ \App\Models\Order::where('customer_id', $order['customer_id'])->count() }}
                                        {{ translate('orders') }}</span>
                                </div>
                                <div class="media-body text-right">
                                    {{-- <i class="tio-chevron-right text-body"></i> --}}
                                </div>
                            </div>

                            <hr>

                            <div class="d-flex justify-content-between align-items-center">
                                <h5>{{ translate('Contact') }} {{ translate('info') }} </h5>
                            </div>

                            <ul class="list-unstyled list-unstyled-py-2">
                                <li>
                                    <i class="tio-online mr-2"></i>
                                    {{ $order->customer['email'] }}
                                </li>
                                <li>
                                    <i class="tio-android-phone-vs mr-2"></i>
                                    {{ $order->customer['phone'] }}
                                </li>
                            </ul>

                            <hr>

                            <div class="d-flex justify-content-between align-items-center">
                                <h5>{{ translate('shipping_address') }}</h5>
                            </div>

                            @if ($order->shippingAddress)
                                @php($shipping_address = $order->shippingAddress)
                            @else
                                @php($shipping_address = json_decode($order['shipping_address_data']))
                            @endif

                            <span class="d-block">{{ translate('Name') }} :
                                <strong>{{ $shipping_address ? $shipping_address->contact_person_name : translate('empty') }}</strong><br>
                                {{--                                 {{translate('Country')}}: --}}
                                {{--                                <strong>{{$shipping_address ? $shipping_address->country : translate('empty')}}</strong><br> --}}
                                {{--                                {{translate('City')}}: --}}
                                {{--                                <strong>{{$shipping_address ? $shipping_address->city : translate('empty')}}</strong><br> --}}
                                {{--                                {{translate('zip_code')}} : --}}
                                {{--                                <strong>{{$shipping_address ? $shipping_address->zip  : translate('empty')}}</strong><br> --}}
                                {{ translate('address') }} :
                                <strong>{{ $shipping_address ? $shipping_address->address : translate('empty') }}</strong><br>
                                {{ translate('Phone') }}:
                                <strong>{{ $shipping_address ? $shipping_address->phone : translate('empty') }}</strong>
                            </span>

                            <hr>

                            <div class="d-flex justify-content-between align-items-center">
                                <h5>{{ translate('billing_address') }}</h5>
                            </div>

                            @if ($order->billingAddress)
                                @php($billing = $order->billingAddress)
                            @else
                                @php($billing = json_decode($order['billing_address_data']))
                            @endif

                            <span class="d-block">{{ translate('Name') }} :
                                <strong>{{ $billing ? $billing->contact_person_name : translate('empty') }}</strong><br>
                                {{--                                 {{translate('Country')}}: --}}
                                {{--                                <strong>{{$billing ? $billing->country : translate('empty')}}</strong><br> --}}
                                {{--                                {{translate('City')}}: --}}
                                {{--                                <strong>{{$billing ? $billing->city : translate('empty')}}</strong><br> --}}
                                {{--                                {{translate('zip_code')}} : --}}
                                {{--                                <strong>{{$billing ? $billing->zip  : translate('empty')}}</strong><br> --}}
                                {{ translate('address') }} :
                                <strong>{{ $billing ? $billing->address : translate('empty') }}</strong><br>
                                {{ translate('Phone') }}:
                                <strong>{{ $billing ? $billing->phone : translate('empty') }}</strong>
                            </span>
                        </div>
                    @endif
                    <!-- End Body -->
                </div>
                <!-- End Card -->
            </div>

            <div class="col-lg-12">
                <!-- Card -->
                <div class="card">
                    <!-- Header -->
                    <div class="card-header">
                        <h4 class="card-header-title">{{ translate('Order') }} {{ translate('Tracking') }}</h4>

                        <div class="card-header-action">
                            <button class="btn btn-xs btn-warning" id="trackOrder" data-order_id="{{ $order['id'] }}">
                                <i class="tio-map"></i> {{ translate('Check Again') }}
                            </button>
                        </div>
                    </div>
                    <!-- End Header -->

                    <!-- Body -->
                    <div class="card-body">
                        <div class="stepper d-flex flex-column mt-5 ml-2">

                            {{--                            <div class="d-flex mb-1"> --}}
                            {{--                                <div class="d-flex flex-column pr-4 align-items-center"> --}}
                            {{--                                    <div class="rounded-circle py-2 px-3 bg-primary text-white mb-1">1</div> --}}
                            {{--                                    <div class="line h-100"></div> --}}
                            {{--                                </div> --}}
                            {{--                                <div> --}}
                            {{--                                    <h5 class="text-dark"> --}}
                            {{--                                        Create your application respository --}}
                            {{--                                    </h5> --}}
                            {{--                                    <p class="lead text-muted pb-3">Choose your website name & create repository</p> --}}
                            {{--                                </div> --}}
                            {{--                            </div> --}}


                        </div>
                    </div>
                    <!-- End Body -->
                </div>
                <!-- End Card -->
            </div>
            <!-- End Row -->
        </div>
    </div>
@endsection

@push('script_2')
    <script>
        $(document).on('change', '.payment_status', function() {
            var id = $(this).attr("data-id");
            var value = $(this).val();
            Swal.fire({
                title: '{{ translate('Are you sure Change this') }}?',
                text: "{{ translate('You will not be able to revert this') }}!",
                showCancelButton: true,
                confirmButtonColor: '#377dff',
                cancelButtonColor: 'secondary',
                confirmButtonText: '{{ translate('Yes, Change it') }}!'
            }).then((result) => {
                if (result.value) {
                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                        }
                    });
                    $.ajax({
                        url: "{{ route('admin.orders.payment-status') }}",
                        method: 'POST',
                        data: {
                            "id": id,
                            "payment_status": value
                        },
                        success: function(data) {
                            toastr.success('{{ translate('Status Change successfully') }}');
                            location.reload();
                        }
                    });
                }
            })
        });

        function order_status(status) {
            if (status == "") {
                Swal.fire({
                    title: 'Select a valid status to change!',
                    text: "Please see the dropdown menu"
                });
            }

            @if ($order['order_status'] == 'delivered')
                Swal.fire({
                    title: '{{ translate('Order is already delivered, and transaction amount has been disbursed, changing status can be the reason of miscalculation') }}!',
                    text: "{{ translate('Think before you proceed') }}.",
                    showCancelButton: true,
                    confirmButtonColor: '#377dff',
                    cancelButtonColor: 'secondary',
                    confirmButtonText: '{{ translate('Yes, Change it') }}!'
                }).then((result) => {
                    if (result.value) {
                        $.ajaxSetup({
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                            }
                        });
                        $.ajax({
                            url: "{{ route('admin.orders.status') }}",
                            method: 'POST',
                            data: {
                                "id": '{{ $order['id'] }}',
                                "order_status": status
                            },
                            success: function(data) {
                                if (data.success == 0) {
                                    toastr.success(
                                        '{{ translate('Order is already delivered, You can not change it') }} !!'
                                        );
                                    location.reload();
                                } else {
                                    toastr.success('{{ translate('Status Change successfully') }}!');
                                    location.reload();
                                }

                            }
                        });
                    }
                })
            @else
                Swal.fire({
                    title: '{{ translate('Are you sure Change this') }}?',
                    text: "{{ translate('You will not be able to revert this') }}!",
                    showCancelButton: true,
                    confirmButtonColor: '#377dff',
                    cancelButtonColor: 'secondary',
                    confirmButtonText: '{{ translate('Yes, Change it') }}!'
                }).then((result) => {
                    if (result.value) {
                        $.ajaxSetup({
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                            }
                        });
                        $.ajax({
                            url: "{{ route('admin.orders.status') }}",
                            method: 'POST',
                            data: {
                                "id": '{{ $order['id'] }}',
                                "order_status": status
                            },
                            success: function(data) {
                                if (data.success == 0) {
                                    toastr.success(
                                        '{{ translate('Order is already delivered, You can not change it') }} !!'
                                        );
                                    location.reload();
                                } else {
                                    toastr.success('{{ translate('Status Change successfully') }}!');
                                    location.reload();
                                }

                            }
                        });
                    }
                })
            @endif
        }


        function addDeliveryMan(id) {
            $.ajax({
                type: "GET",
                url: '{{ admin_url() }}/orders/add-delivery-man/{{ $order['id'] }}/' + id,
                data: {
                    'order_id': '{{ $order['id'] }}',
                    'delivery_man_id': id
                },
                success: function(data) {
                    if (data.status == true) {
                        toastr.success('Delivery man successfully assigned/changed', {
                            CloseButton: true,
                            ProgressBar: true
                        });
                    } else {
                        toastr.error('Deliveryman man can not assign/change in that status', {
                            CloseButton: true,
                            ProgressBar: true
                        });
                    }
                },
                error: function() {
                    toastr.error('Add valid data', {
                        CloseButton: true,
                        ProgressBar: true
                    });
                }
            });
        }

        function last_location_view() {
            toastr.warning('Only available when order is out for delivery!', {
                CloseButton: true,
                ProgressBar: true
            });
        }

        $(function() {
            $('[data-toggle="tooltip"]').tooltip()
        })

        function waiting_for_location() {
            toastr.warning('{{ translate('waiting_for_location') }}', {
                CloseButton: true,
                ProgressBar: true
            });
        }

        function render_tracking_stepper() {
            var trackingKey = 'tracking-{{ $order->id }}';

            if (localStorage.getItem(trackingKey) == null) {
                return;
            }


            var tracking = JSON.parse(localStorage.getItem('tracking-{{ $order->id }}'))['tracking']

            console.log("render_tracking_stepper", tracking);

            if (tracking != null) {
                var html = '';
                for (var i = 1; i <= tracking.length; i++) {
                    var d = Date.parse(tracking[i - 1].time);
                    var date = new Date(d);
                    var time = date.toLocaleTimeString("en-US", {
                        year: 'numeric',
                        month: 'short',
                        day: 'numeric',
                        hour: '2-digit',
                        minute: '2-digit',
                    });

                    html += `<div class="d-flex mb-1">
                                <div class="d-flex flex-column pr-4 align-items-center">
                                    <div class="rounded-circle py-2 px-3 bg-primary text-white mb-1">${i}</div>
                                    <div class="line h-100"></div>
                                </div>
                                <div>
                                    <h5 class="text-dark">${tracking[i - 1].message_bn}</h5>
                                    <p class="lead text-muted pb-3">${time}</p>
                                </div>
                            </div>`
                }
                $('.stepper').html(html);
            }

        }


        function fetch_tracking_data() {
            $.ajax({
                type: "GET",
                url: '{{ route('admin.orders.track-order', $order->id) }}',
                success: function(data) {
                    if (data.status == true) {
                        localStorage.setItem('tracking-{{ $order->id }}', JSON.stringify(data['data']));
                        render_tracking_stepper();
                    } else {
                        toastr.error('Failed to update tracking', {
                            CloseButton: true,
                            ProgressBar: true
                        });
                    }
                },
                error: function() {
                    toastr.error('Add valid data', {
                        CloseButton: true,
                        ProgressBar: true
                    });
                }
            });
        }


        $(document).on('click', '#trackOrder', function() {
            fetch_tracking_data();
        });

        if (localStorage.getItem('tracking-{{ $order->id }}') !== null) {
            fetch_tracking_data();
        }
    </script>
@endpush
