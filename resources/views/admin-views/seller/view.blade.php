@extends('layouts.back-end.app')

@section('title', $seller->shop? $seller->shop->name : translate("Shop Name"))

@push('css_or_js')

@endpush

@section('content')
    <div class="content container-fluid">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a
                            href="{{route('admin.dashboard.index')}}">{{translate('Dashboard')}}</a>
                </li>
                <li class="breadcrumb-item" aria-current="page">{{translate('Seller_Details')}}</li>
            </ol>
        </nav>

        <!-- Page Heading -->
        <div class="flex-between d-sm-flex row align-items-center justify-content-between mb-2 mx-1">
            <div>
                <a href="{{route('admin.sellers.seller-list')}}"
                   class="btn btn-primary mt-3 mb-3">{{translate('Back_to_seller_list')}}</a>
            </div>
            <div>
                @if ($seller->status=="pending")
                    <div class="mt-4 pr-2 float-{{Session::get('direction') === "rtl" ? 'left' : 'right'}}">
                        <div class="flex-start">
                            <h4 class="mx-1"><i class="tio-shop-outlined"></i></h4>
                            <div><h4>{{translate('Seller_request_for_open_a_shop.')}}</h4></div>
                        </div>
                        <div class="text-center">
                            <form class="d-inline-block" action="{{route('admin.sellers.updateStatus')}}" method="POST">
                                @csrf
                                <input type="hidden" name="id" value="{{$seller->id}}">
                                <input type="hidden" name="status" value="approved">
                                <button type="submit"
                                        class="btn btn-primary">{{translate('Approve')}}</button>
                            </form>
                            <form class="d-inline-block" action="{{route('admin.sellers.updateStatus')}}" method="POST">
                                @csrf
                                <input type="hidden" name="id" value="{{$seller->id}}">
                                <input type="hidden" name="status" value="rejected">
                                <button type="submit"
                                        class="btn btn-danger">{{translate('reject')}}</button>
                            </form>
                        </div>
                    </div>
                @endif
            </div>
        </div>
        <!-- Page Header -->
        <div class="page-header">
            <div class="flex-between row mx-1">
                <div>
                    <h1 class="page-header-title">{{ $seller->shop? $seller->shop->name : "Shop Name : Update Please" }}</h1>
                </div>
            </div>
            <!-- Nav Scroller -->
            <div class="js-nav-scroller hs-nav-scroller-horizontal">
                <!-- Nav -->
                <ul class="nav nav-tabs page-header-tabs">
                    <li class="nav-item">
                        <a class="nav-link active"
                           href="{{ route('admin.sellers.view',$seller->id) }}">{{translate('Shop')}}</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link"
                           href="{{ route('admin.sellers.view',['id'=>$seller->id, 'tab'=>'order']) }}">{{translate('Order')}}</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link"
                           href="{{ route('admin.sellers.view',['id'=>$seller->id, 'tab'=>'product']) }}">{{translate('Product')}}</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link"
                           href="{{ route('admin.sellers.view',['id'=>$seller->id, 'tab'=>'setting']) }}">{{translate('Setting')}}</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link"
                           href="{{ route('admin.sellers.view',['id'=>$seller->id, 'tab'=>'transaction']) }}">{{translate('Transaction')}}</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link"
                           href="{{ route('admin.sellers.view',['id'=>$seller->id, 'tab'=>'review']) }}">{{translate('Review')}}</a>
                    </li>

                </ul>
                <!-- End Nav -->
            </div>
            <!-- End Nav Scroller -->
        </div>
        <!-- End Page Header -->
        <div class="card mb-3">
            <div class="card-body">
                <div class=" gx-2 gx-lg-3 mb-2">
                    <div>
                        <h4><i style="font-size: 30px"
                               class="tio-wallet"></i>{{translate('seller_wallet')}}</h4>
                    </div>
                    <div class="row gx-2 gx-lg-3" id="order_stats">
                        <div class="flex-between" style="width: 100%">
                            <div class="mb-3 mb-lg-0" style="width: 18%">
                                <div class="card card-body card-hover-shadow h-100 text-white text-center"
                                     style="background-color: #22577A;">
                                    <h1 class="p-2 text-white">{{ $seller->wallet ? \App\Services\BackEndHelper::set_symbol(\App\Services\BackEndHelper::usd_to_currency($seller->wallet->commission_given)) : 0}}</h1>
                                    <div class="text-uppercase">{{translate('commission_given')}}</div>
                                </div>
                            </div>

                            <div class="mb-3 mb-lg-0" style="width: 18%">
                                <div class="card card-body card-hover-shadow h-100 text-white text-center"
                                     style="background-color: #595260;">
                                    <h1 class="p-2 text-white">{{ $seller->wallet ? \App\Services\BackEndHelper::set_symbol(\App\Services\BackEndHelper::usd_to_currency($seller->wallet->pending_withdraw)) : 0}}</h1>
                                    <div class="text-uppercase">{{translate('pending_withdraw')}}</div>
                                </div>
                            </div>

                            <div class="mb-3 mb-lg-0" style="width: 18%">
                                <div class="card card-body card-hover-shadow h-100 text-white text-center"
                                     style="background-color: #a66f2e;">
                                    <h1 class="p-2 text-white">{{ $seller->wallet ? \App\Services\BackEndHelper::set_symbol(\App\Services\BackEndHelper::usd_to_currency($seller->wallet->delivery_charge_earned)) : 0}}</h1>
                                    <div class="text-uppercase">{{translate('delivery_charge_earned')}}</div>
                                </div>
                            </div>

                            <div class="mb-3 mb-lg-0" style="width: 18%">
                                <div class="card card-body card-hover-shadow h-100 text-white text-center"
                                     style="background-color: #6E85B2;">
                                    <h1 class="p-2 text-white">{{ $seller->wallet ? \App\Services\BackEndHelper::set_symbol(\App\Services\BackEndHelper::usd_to_currency($seller->wallet->collected_cash)) : 0}}</h1>
                                    <div class="text-uppercase">{{translate('collected_cash')}}</div>
                                </div>
                            </div>

                            <div class="mb-3 mb-lg-0" style="width: 18%">
                                <div class="card card-body card-hover-shadow h-100 text-white text-center"
                                     style="background-color: #6D9886;">
                                    <h1 class="p-2 text-white">{{ $seller->wallet ? \App\Services\BackEndHelper::set_symbol(\App\Services\BackEndHelper::usd_to_currency($seller->wallet->total_tax_collected)) : 0}}</h1>
                                    <div class="text-uppercase">{{translate('total_collected_tax')}}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <!-- Earnings (Monthly) Card Example -->
                        <div class="col-xl-6 for-card col-md-6 mt-4">
                            <div class="card for-card-body-2 shadow h-100  badge-primary"
                                 style="background: #362222!important;">
                                <div class="card-body text-light">
                                    <div class="flex-between row no-gutters align-items-center">
                                        <div>
                                            <div class="font-weight-bold text-uppercase for-card-text mb-1">
                                                {{translate('Withdrawable_balance')}}
                                            </div>
                                            <div
                                                    class="for-card-count">{{ $seller->wallet ? \App\Services\BackEndHelper::set_symbol(\App\Services\BackEndHelper::usd_to_currency($seller->wallet->total_earning)) : 0 }}</div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Earnings (Monthly) Card Example -->
                        <div class="col-xl-6 for-card col-md-6 mt-4" style="cursor: pointer">
                            <div class="card  shadow h-100 for-card-body-3 badge-info"
                                 style="background: #171010!important;">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div
                                                    class=" font-weight-bold for-card-text text-uppercase mb-1">{{translate('withdrawn')}}</div>
                                            <div
                                                    class="for-card-count">{{$seller->wallet ? \App\Services\BackEndHelper::set_symbol(\App\Services\BackEndHelper::usd_to_currency($seller->wallet->withdrawn)) : 0}}</div>
                                        </div>
                                        <div class="col-auto for-margin">
                                            <i class="tio-money-vs"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header text-capitalize">
                        {{translate('Seller')}} {{translate('Account')}} <br>
                        @if($seller->status=='approved')
                            <form class="d-inline-block" action="{{route('admin.sellers.updateStatus')}}" method="POST">
                                @csrf
                                <input type="hidden" name="id" value="{{$seller->id}}">
                                <input type="hidden" name="status" value="suspended">
                                <button type="submit"
                                        class="btn btn-outline-danger">{{translate('suspend')}}</button>
                            </form>
                        @elseif($seller->status=='rejected' || $seller->status=='suspended')
                            <form class="d-inline-block" action="{{route('admin.sellers.updateStatus')}}" method="POST">
                                @csrf
                                <input type="hidden" name="id" value="{{$seller->id}}">
                                <input type="hidden" name="status" value="approved">
                                <button type="submit"
                                        class="btn btn-outline-success">{{translate('activate')}}</button>
                            </form>
                        @endif
                    </div>
                    <div class="card-body">
                        <div class="card-body"
                             style="text-align: {{Session::get('direction') === "rtl" ? 'right' : 'left'}};">
                            <div class="flex-start">
                                <div><h4>Status : </h4></div>
                                <div class="mx-1">
                                    <h4>{!! $seller->status=='approved'?'<label class="badge badge-success">Active</label>':'<label class="badge badge-danger">In-Active</label>' !!}</h4>
                                </div>
                            </div>
                            <div class="flex-start">
                                <div><h5>{{translate('name')}} : </h5></div>
                                <div class="mx-1"><h5>{{$seller->f_name}} {{$seller->l_name}}</h5></div>
                            </div>
                            <div class="flex-start">
                                <div><h5>{{translate('Email')}} : </h5></div>
                                <div class="mx-1"><h5>{{$seller->email}}</h5></div>
                            </div>
                            <div class="flex-start">
                                <div><h5>{{translate('Phone')}} : </h5></div>
                                <div class="mx-1"><h5>{{$seller->phone}}</h5></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @if($seller->shop)
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            {{translate('Shop')}} {{translate('info')}}
                        </div>
                        <div class="card-body"
                             style="text-align: {{Session::get('direction') === "rtl" ? 'right' : 'left'}};">
                            <div class="flex-start">
                                <div><h5>{{translate('seller')}} : </h5></div>
                                <div class="mx-1"><h5>{{$seller->shop->name}}</h5></div>
                            </div>
                            <div class="flex-start">
                                <div><h5>{{translate('Phone')}} : </h5></div>
                                <div class="mx-1"><h5>{{$seller->shop->contact}}</h5></div>
                            </div>
                            <div class="flex-start">
                                <div><h5>{{translate('address')}} : </h5></div>
                                <div class="mx-1"><h5>{{$seller->shop->address}}</h5></div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <div class="col-md-6 mt-3">
                <div class="card">
                    <div class="card-header">
                        {{translate('bank_info')}}
                    </div>
                    <div class="card-body"
                         style="text-align: {{Session::get('direction') === "rtl" ? 'right' : 'left'}};">
                        <div class="col-md-8 mt-2">
                            <div class="flex-start">
                                <div><h4>{{translate('bank_name')}} : </h4></div>
                                <div class="mx-1"><h4>{{$seller->bank_name ? $seller->bank_name : 'No Data found'}}</h4>
                                </div>
                            </div>
                            <div class="flex-start">
                                <div><h6>{{translate('Branch')}} : </h6></div>
                                <div class="mx-1"><h6>{{$seller->branch ? $seller->branch : 'No Data found'}}</h6></div>
                            </div>
                            <div class="flex-start">
                                <div><h6>{{translate('holder_name')}} : </h6></div>
                                <div class="mx-1">
                                    <h6>{{$seller->holder_name ? $seller->holder_name : 'No Data found'}}</h6></div>
                            </div>
                            <div class="flex-start">
                                <div><h6>{{translate('account_no')}} : </h6></div>
                                <div class="mx-1">
                                    <h6>{{$seller->account_no ? $seller->account_no : 'No Data found'}}</h6></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- <div class="col-md-6 mt-3">
                <form action="{{route('admin.sellers.sales-commission-update',[$seller['id']])}}" method="post">
                    @csrf
                    <div class="card">
                        <div class="card-header">
                            <label> Sales Commission : </label>
                            <label class="switch ml-3">
                                <input type="checkbox" name="status"
                                       class="status"
                                       value="1" {{$seller['sales_commission_percentage']!=null?'checked':''}}>
                                <span class="slider round"></span>
                            </label>
                        </div>
                        <div class="card-body">
                            <small class="badge badge-soft-danger mb-3">
                                If sales commission is disabled here, the system default commission will be applied.
                            </small>
                            <div class="form-group">
                                <label>Commission ( % )</label>
                                <input type="number" value="{{$seller['sales_commission_percentage']}}"
                                       class="form-control" name="commission">
                            </div>
                            <button type="submit" class="btn btn-primary">Update</button>
                        </div>
                    </div>
                </form>
            </div> --}}
        </div>
    </div>
@endsection

@push('script')

@endpush
