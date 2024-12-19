@extends('layouts.back-end.app-reseller')

@section('title',translate('Order Details'))

@push('css_or_js')
@endpush

@section('content')
<!-- Page Heading -->
<div class="content container-fluid">

    <div class="page-header d-print-none p-3" style="background: white">
        <div class="row align-items-center">
            <div class="col-sm mb-2 mb-sm-0">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb breadcrumb-no-gutter">
                        <li class="breadcrumb-item"><a class="breadcrumb-link"
                                href="{{route('seller.orders.list',['all'])}}">{{translate('Orders')}}</a>
                        </li>
                        <li class="breadcrumb-item active" aria-current="page">{{translate('Order details')}}</li>
                    </ol>
                </nav>

                <div class="d-sm-flex align-items-sm-center">
                    <h1 class="page-header-title">{{translate('Order')}} #{{$order['id']}}</h1>
                    {{-- {{ $order}} --}}

                    @if($order['payment_status']=='paid')
                    <span class="badge badge-soft-success {{Session::get('direction') === " rtl" ? 'mr-sm-3' : 'ml-sm-3'
                        }}">
                        <span class="legend-indicator bg-success" style="{{Session::get('direction') === " rtl"
                            ? 'margin-right: 0;margin-left: .4375rem;' : 'margin-left: 0;margin-right: .4375rem;'
                            }}"></span>{{translate('Paid')}}
                    </span>
                    @else
                    <span class="badge badge-soft-danger {{Session::get('direction') === " rtl" ? 'mr-sm-3' : 'ml-sm-3'
                        }}">
                        <span class="legend-indicator bg-danger" style="{{Session::get('direction') === " rtl"
                            ? 'margin-right: 0;margin-left: .4375rem;' : 'margin-left: 0;margin-right: .4375rem;'
                            }}"></span>{{translate('Unpaid')}}
                    </span>
                    @endif

                    @if($order['order_status']=='pending')
                    <span class="badge badge-soft-info {{Session::get('direction') === " rtl" ? 'mr-2 mr-sm-3'
                        : 'ml-2 ml-sm-3' }} text-capitalize">
                        <span class="legend-indicator bg-info text" style="{{Session::get('direction') === " rtl"
                            ? 'margin-right: 0;margin-left: .4375rem;' : 'margin-left: 0;margin-right: .4375rem;'
                            }}"></span>{{str_replace('_',' ',$order['order_status'])}}
                    </span>
                    @elseif($order['order_status']=='failed')
                    <span class="badge badge-danger {{Session::get('direction') === " rtl" ? 'mr-2 mr-sm-3'
                        : 'ml-2 ml-sm-3' }} text-capitalize">
                        <span class="legend-indicator bg-info" style="{{Session::get('direction') === " rtl"
                            ? 'margin-right: 0;margin-left: .4375rem;' : 'margin-left: 0;margin-right: .4375rem;'
                            }}"></span>{{str_replace('_',' ',$order['order_status'])}}
                    </span>
                    @elseif($order['order_status']=='processing' || $order['order_status']=='out_for_delivery')
                    <span class="badge badge-soft-warning ml-2 ml-sm-3 text-capitalize">
                        <span class="legend-indicator bg-warning"></span>{{str_replace('_',' ',$order['order_status'])}}
                    </span>

                    @elseif($order['order_status']=='delivered' || $order['order_status']=='confirmed')
                    <span class="badge badge-soft-success ml-2 ml-sm-3 text-capitalize">
                        <span class="legend-indicator bg-success"></span>{{str_replace('_',' ',$order['order_status'])}}
                    </span>
                    @else
                    <span class="badge badge-soft-danger {{Session::get('direction') === " rtl" ? 'mr-2 mr-sm-3'
                        : 'ml-2 ml-sm-3' }} text-capitalize">
                        <span class="legend-indicator bg-danger" style="{{Session::get('direction') === " rtl"
                            ? 'margin-right: 0;margin-left: .4375rem;' : 'margin-left: 0;margin-right: .4375rem;'
                            }}"></span>{{str_replace('_',' ',$order['order_status'])}}
                    </span>
                    @endif
                    <span class="{{Session::get('direction') === " rtl" ? 'mr-2 mr-sm-3' : 'ml-2 ml-sm-3' }}">
                        <i class="tio-date-range"></i> {{date('d M Y H:i:s',strtotime($order['created_at']))}}
                    </span>
                </div>
            </div>
        </div>
    </div>

    <div class="row" id="printableArea">
        <div class="col-lg-8 mb-3  mb-lg-0">
            <!-- Card -->
            <div class="card mb-3  mb-lg-5" style="text-align: {{Session::get('direction') === " rtl" ? 'right' : 'left'
                }};">
                <!-- Header -->
                <div class="card-header" style="display: block!important;">
                    <div class="row">
                        <div class="col-12 pb-2 border-bottom">
                            <h4 class="card-header-title">
                                {{translate('Order details')}}
                                <span class="badge badge-soft-dark rounded-circle {{Session::get('direction') === " rtl"
                                    ? 'mr-1' : 'ml-1' }}">{{$order->details->count()}}</span>
                            </h4>
                        </div>

                        <div class="col-6 pt-2">
                            @if ($order->order_note !=null)
                            <span class="font-weight-bold text-capitalize">
                                {{translate('order_note')}} :
                            </span>
                            <p class="pl-1">
                                {{$order->order_note}}
                            </p>
                            @endif
                        </div>
                        <div class="col-6 pt-2">
                            <div class="flex-end">
                                <h6 class="text-capitalize" style="color: #8a8a8a;">{{translate('payment_method')}} :
                                </h6>
                                <h6 class="mx-1" style="color: #8a8a8a;">{{str_replace('_','
                                    ',$order['payment_method'])}}</h6>
                            </div>
                            <div class="flex-end">
                                <h6 style="color: #8a8a8a;">{{translate('Payment')}} {{translate('reference')}}
                                    :</h6>
                                <h6 class="mx-1" style="color: #8a8a8a;">{{str_replace('_','
                                    ',$order['transaction_ref'])}}</h6>
                            </div>
                            <div class="flex-end">
                                <h6 style="color: #8a8a8a;">{{translate('shipping')}} {{translate('method')}}
                                    :</h6>
                                <h6 class="mx-1" style="color: #8a8a8a;">{{$order->shipping->title??""}}</h6>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- End Header -->

                <!-- Body -->
                <div class="card-body">
                    <div class="media">
                        <div class="avatar avatar-xl {{Session::get('direction') === " rtl" ? 'ml-3' : 'mr-3' }}">
                            <p>{{translate('image')}}</p>
                        </div>

                        <div class="media-body">
                            <div class="row">
                                <div class="col-md-4 product-name">
                                    <p> {{translate('Name')}}</p>
                                </div>

                                <div class="col col-md-2 align-self-center p-0 ">
                                    <p> {{translate('price')}}</p>
                                </div>

                                <div class="col col-md-1 align-self-center">
                                    <p>{{translate('Q')}}</p>
                                </div>
                                <div class="col col-md-1 align-self-center  p-0 product-name">
                                    <p> {{translate('TAX')}}</p>
                                </div>
                                <div class="col col-md-2 align-self-center  p-0 product-name">
                                    <p> {{translate('Discount')}}</p>
                                </div>
                                <div class="col col-md-2 align-self-center text-{{Session::get('direction') === " rtl"
                                    ? 'left' : 'right' }}">
                                    <p> {{translate('Subtotal')}}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    @php($subtotal=0)
                    @php($total=0)
                    @php($shipping=0)
                    @php($discount=0)
                    @php($tax=0)

                    @foreach($order->details as $detail)
                    @if($detail->product)

                    <!-- Media -->
                    <div class="media">
                        <div class="avatar avatar-xl {{Session::get('direction') === " rtl" ? 'ml-3' : 'mr-3' }}">
                            <img class="img-fluid"
                                onerror="this.src='{{asset('assets/front-end/img/image-place-holder.png')}}'"
                                src="{{\App\Services\ProductManager::product_image_path('thumbnail')}}/{{$detail->product['thumbnail']}}"
                                alt="Image Description">
                        </div>

                        <div class="media-body">
                            <div class="row">
                                <div class="col-md-4 mb-3 mb-md-0 product-name">
                                    <p>
                                        {{substr($detail->product['name'],0,10)}}{{strlen($detail->product['name'])>10?'...':''}}
                                    </p>
                                    <strong><u>{{translate('variation')}} : </u></strong>

                                    <div class="font-size-sm text-body">
                                        <span class="font-weight-bold">{{$detail['variant']}}</span>
                                    </div>
                                </div>

                                <div class="col col-md-2 align-self-center p-0 ">
                                    <h6>{{\App\Services\BackEndHelper::set_symbol(\App\Services\BackEndHelper::usd_to_currency($detail['price']))}}
                                    </h6>
                                </div>

                                <div class="col col-md-1 align-self-center">

                                    <h5>{{$detail->qty}}</h5>
                                </div>
                                <div class="col col-md-1 align-self-center  p-0 product-name">

                                    <h5>{{\App\Services\BackEndHelper::set_symbol(\App\Services\BackEndHelper::usd_to_currency($detail['tax']))}}
                                    </h5>
                                </div>
                                <div class="col col-md-2 align-self-center  p-0 product-name">
                                    <h5>{{\App\Services\BackEndHelper::set_symbol(\App\Services\BackEndHelper::usd_to_currency($detail['discount']))}}
                                    </h5>
                                </div>


                                <div class="col col-md-2 align-self-center text-{{Session::get('direction') === " rtl"
                                    ? 'left' : 'right' }}">
                                    @php($subtotal=$detail['price']*$detail->qty+$detail['tax']-$detail['discount'])

                                    <h5>{{\App\Services\BackEndHelper::set_symbol(\App\Services\BackEndHelper::usd_to_currency($subtotal))}}
                                    </h5>
                                </div>
                            </div>
                        </div>
                    </div>

                    @php($discount+=$detail['discount'])
                    @php($tax+=$detail['tax'])
                    @php($shipping+=$detail->shipping ? $detail->shipping->cost :0)

                    @php($total+=$subtotal)
                    <!-- End Media -->
                    <hr>
                    @endif
                    @endforeach
                    @php($shipping=$order['shipping_cost'])
                    @php($coupon_discount=$order['discount_amount'])
                    <div class="row justify-content-md-end mb-3">
                        <div class="col-md-9 col-lg-8">
                            <dl class="row text-sm-right">
                                <dt class="col-sm-6">{{translate('Shipping')}}</dt>
                                <dd class="col-sm-6 border-bottom">
                                    <strong>{{\App\Services\BackEndHelper::set_symbol(\App\Services\BackEndHelper::usd_to_currency($shipping))}}</strong>
                                </dd>

                                <dt class="col-sm-6">{{translate('coupon_discount')}}</dt>
                                <dd class="col-sm-6 border-bottom">
                                    <strong>-
                                        {{\App\Services\BackEndHelper::set_symbol(\App\Services\BackEndHelper::usd_to_currency($coupon_discount))}}</strong>
                                </dd>

                                <dt class="col-sm-6">{{translate('Total')}}</dt>
                                <dd class="col-sm-6">
                                    <strong>{{\App\Services\BackEndHelper::set_symbol(\App\Services\BackEndHelper::usd_to_currency($total+$shipping-$coupon_discount))}}</strong>
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
            <!-- Start::Tracking -->
            @if($tracking)
            <div class="px-1 mx-auto">
                <div class="card p-5">
                    <div class="row d-flex justify-content-between px-3 pb-3 top">
                        <div class="d-flex">
                            <h5>TRACKING ID <span
                                    class="text-primary font-weight-bold">#{{$tracking['tracking_id']}}</span></h5>
                        </div>
                    </div>
                    <!-- Add class 'active' to progress -->
                    <div class="row d-flex justify-content-center">
                        <div class="col-12">
                            <ol class="list-group list-group-light list-group-numbered font-boldest">
                                @foreach ($tracking['steps'] as $step)
                                <li class="list-group-item">
                                    <div class="d-flex">
                                        <div class="mr-3">
                                            <span class="p-2 rounded-circle border font-boltest">STEP -
                                                {{$loop->iteration}}</span>
                                        </div>
                                        <div>
                                            <p>{{$step['message_en']}}</p>
                                            <p>{{$step['message_bn']}}</p>
                                            <p>{{\Carbon\Carbon::parse($step['time'])->diffForHumans()}}</p>
                                        </div>
                                    </div>
                                </li>
                                @endforeach
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
            @endif
            <!-- End::Tracking -->
        </div>

        <div class="col-lg-4">
            <!-- Card -->
            <div class="card">
                <!-- Header -->
                <div class="card-header">
                    <h4 class="card-header-title">{{translate('Customer')}}</h4>
                </div>
                <!-- End Header -->

                <!-- Body -->
                @if($order->customer)

                <div class="card-body" style="text-align: {{Session::get('direction') === " rtl" ? 'right' : 'left'
                    }};">
                    <div class="media align-items-center" href="javascript:">
                        <div class="avatar avatar-circle {{Session::get('direction') === " rtl" ? 'ml-3' : 'mr-3' }}">
                            <img class="avatar-img"
                                onerror="this.src='{{asset('assets/front-end/img/image-place-holder.png')}}'"
                                src="{{asset('storage/profile/'.$order->customer->image)}}" alt="Image Description">
                        </div>
                        <div class="media-body">
                            <span class="text-body text-hover-primary">{{$order->customer['f_name'].'
                                '.$order->customer['l_name']}}</span>
                        </div>
                        <div class="media-body text-right">
                            {{--<i class="tio-chevron-right text-body"></i>--}}
                        </div>
                    </div>

                    <hr>

                    <div class="media align-items-center" href="javascript:">
                        <div class="icon icon-soft-info icon-circle {{Session::get('direction') === " rtl" ? 'ml-3'
                            : 'mr-3' }}">
                            <i class="tio-shopping-basket-outlined"></i>
                        </div>
                        <div class="media-body">
                            <span class="text-body text-hover-primary">
                                {{\App\Models\Order::where('customer_id',$order['customer_id'])->count()}}
                                {{translate('orders')}}</span>
                        </div>
                        <div class="media-body text-right">
                            {{--<i class="tio-chevron-right text-body"></i>--}}
                        </div>
                    </div>

                    <hr>

                    <div class="d-flex justify-content-between align-items-center">
                        <h5>{{translate('Contact info')}}</h5>
                    </div>

                    <div class="flex-start">
                        <div>
                            <i class="tio-online {{Session::get('direction') === " rtl" ? 'ml-2' : 'mr-2' }}"></i>
                        </div>
                        <div class="mx-1"><a class="text-dark"
                                href="mailto: {{$order->customer['email']}}">{{$order->customer['email']}}</a>
                        </div>
                    </div>
                    <div class="flex-start">
                        <div>
                            <i class="tio-android-phone-vs {{Session::get('direction') === " rtl" ? 'ml-2' : 'mr-2'
                                }}"></i>
                        </div>
                        <div class="mx-1"><a class="text-dark"
                                href="tel:{{$order->customer['phone']}}">{{$order->customer['phone']}}</a>
                        </div>
                    </div>

                    <hr>

                    <div class="d-flex justify-content-between align-items-center">
                        <h5>{{translate('shipping_address')}}</h5>
                    </div>

                    @if($order->shippingAddress)
                    @php($shipping_address=$order->shippingAddress)
                    @else
                    @php($shipping_address=json_decode($order['shipping_address_data']))
                    @endif

                    <span class="d-block">{{translate('Name')}} :
                        <strong>{{$shipping_address? $shipping_address->contact_person_name :
                            translate('empty')}}</strong><br>
                        {{translate('Country')}}:
                        <strong>{{$shipping_address ? $shipping_address->country : translate('empty')}}</strong><br>
                        {{translate('City')}}:
                        <strong>{{$shipping_address ? $shipping_address->city : translate('empty')}}</strong><br>
                        {{translate('zip_code')}} :
                        <strong>{{$shipping_address ? $shipping_address->zip : translate('empty')}}</strong><br>
                        {{translate('address')}} :
                        <strong>{{$shipping_address ? $shipping_address->address : translate('empty')}}</strong><br>
                        {{translate('Phone')}}:
                        <strong>{{$shipping_address ? $shipping_address->phone : translate('empty')}}</strong>
                    </span>

                    <hr>

                    <div class="d-flex justify-content-between align-items-center">
                        <h5>{{translate('billing_address')}}</h5>
                    </div>

                    @if($order->billingAddress)
                    @php($billing=$order->billingAddress)
                    @else
                    @php($billing=json_decode($order['billing_address_data']))
                    @endif

                    <span class="d-block">{{translate('Name')}} :
                        <strong>{{$billing? $billing->contact_person_name : translate('empty')}}</strong><br>
                        {{translate('Country')}}:
                        <strong>{{$billing ? $billing->country : translate('empty')}}</strong><br>
                        {{translate('City')}}:
                        <strong>{{$billing ? $billing->city : translate('empty')}}</strong><br>
                        {{translate('zip_code')}} :
                        <strong>{{$billing ? $billing->zip : translate('empty')}}</strong><br>
                        {{translate('address')}} :
                        <strong>{{$billing ? $billing->address : translate('empty')}}</strong><br>
                        {{translate('Phone')}}:
                        <strong>{{$billing ? $billing->phone : translate('empty')}}</strong>
                    </span>
                </div>
                @endif
                <!-- End Body -->
            </div>
            <!-- End Card -->
        </div>
    </div>
</div>
@endsection
