@extends('layouts.back-end.app-product_manager')

@section('title', translate('Dashboard'))

@push('css_or_js')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        .grid-card {
            border: 2px solid #00000012;
            border-radius: 10px;
            padding: 10px;
        }

        .label_1 {
            /*position: absolute;*/
            font-size: 10px;
            background: #FF4C29;
            color: #ffffff;
            width: 80px;
            padding: 2px;
            font-weight: bold;
            border-radius: 6px;
            text-align: center;
        }

        .center-div {
            text-align: center;
            border-radius: 6px;
            padding: 6px;
            border: 2px solid #8080805e;
        }
    </style>
@endpush

@section('content')

    <div class="content container-fluid">
        <!-- Page Heading -->
        <div class="page-header pb-0" style="border-bottom: 0!important">
            <div class="flex-between row align-items-center mx-1">
                <h1 class="page-header-title">{{translate('Dashboard')}}</h1>

                <div>
                    <a class="btn btn-primary" href="{{route('product_manager.product.list')}}">
                        <i class="tio-premium-outlined mr-1"></i> {{translate('Products')}}
                    </a>
                </div>
            </div>
        </div>


        <div class="card mb-3">
            <div class="card-body">
                <div class="flex-between row gx-2 gx-lg-3 mb-2">
                    <div style="{{Session::get('direction') === "rtl" ? 'margin-right:2px' : ''}};">
                        <h4><i style="font-size: 30px"
                               class="tio-chart-bar-4"></i>{{translate('dashboard_order_statistics')}}
                        </h4>
                    </div>
                    <div style="width: 20vw">
                        <select class="custom-select" name="statistics_type" onchange="order_stats_update(this.value)">
                            <option
                                value="overall" {{session()->has('statistics_type') && session('statistics_type') == 'overall'?'selected':''}}>
                                {{translate('Overall Statistics')}}
                            </option>
                            <option
                                value="today" {{session()->has('statistics_type') && session('statistics_type') == 'today'?'selected':''}}>
                                {{translate('Todays Statistics')}}
                            </option>
                            <option
                                value="this_month" {{session()->has('statistics_type') && session('statistics_type') == 'this_month'?'selected':''}}>
                                {{translate('This Months Statistics')}}
                            </option>
                        </select>
                    </div>
                </div>
{{--                <div class="row gx-2 gx-lg-3" id="order_stats">--}}
{{--                    @include('product_manager-views.partials._dashboard-order-stats',['data'=>$data])--}}
{{--                </div>--}}
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-body">
                <div class="flex-between gx-2 gx-lg-3 mb-2">
                    <div>
                        <h4><i style="font-size: 30px"
                               class="tio-wallet"></i>{{translate('product_manager_wallet')}}</h4>
                    </div>
                </div>
                <div class="row gx-2 gx-lg-3" id="order_stats">
                    @include('product_manager-views.partials._dashboard-wallet-stats',['data'=>$data])
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
                                            class="for-card-count">{{\App\Services\BackEndHelper::set_symbol(\App\Services\BackEndHelper::usd_to_currency($data['total_earning']))}}</div>
                                    </div>
                                    <div>
                                        <a href="javascript:" style="background: #3A6351!important;"
                                           class="btn btn-primary"
                                           data-toggle="modal" data-target="#balance-modal">
                                            <i class="tio-wallet-outlined"></i> {{translate('Withdraw')}}
                                        </a>
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
                                            class="for-card-count">{{\App\Services\BackEndHelper::set_symbol(\App\Services\BackEndHelper::usd_to_currency($data['withdrawn']))}}</div>
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
        <!-- End Stats -->

        <div class="modal fade" id="balance-modal" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content"
                     style="text-align: {{Session::get('direction') === "rtl" ? 'right' : 'left'}};">
                    <div class="modal-header">
                        <h5 class="modal-title"
                            id="exampleModalLabel">{{translate('Withdraw Request')}}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form action="{{route('product_manager.withdraw.request')}}" method="post">
                        <div class="modal-body">
                            @csrf
                            <div class="form-group">
                                <label for="recipient-name" class="col-form-label">{{translate('Amount')}}
                                    :</label>
                                <input type="number" name="amount" step=".01"
                                       value="{{\App\Services\BackEndHelper::usd_to_currency($data['total_earning'])}}"
                                       class="form-control" id="">
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary"
                                    data-dismiss="modal">{{translate('Close')}}</button>
                            @if(auth('product_manager')->user()->account_no==null || auth('product_manager')->user()->bank_name==null)
                                <button type="button" class="btn btn-primary" onclick="call_duty()">
                                    {{translate('Incomplete bank info')}}
                                </button>
                            @else
                                <button type="submit"
                                        class="btn btn-primary">{{translate('Request')}}</button>
                            @endif
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="row gx-2 gx-lg-3">
            <div class="col-lg-12 mb-3 mb-lg-12">
                <!-- Card -->
                <div class="card h-100">
                    <!-- Body -->
                    <div class="card-body">
                        <div class="row mb-4">
                            <div class="col-12 mb-3 border-bottom">
                                <h5 class="card-header-title float-left mb-2">
                                    <i style="font-size: 30px" class="tio-chart-pie-1"></i>
                                    {{translate('Earning statistics for business analytics')}}
                                </h5>
                                <!-- Legend Indicators -->
                                <h5 class="card-header-title float-right mb-2">{{translate('This Year Earning')}}
                                    <i style="font-size: 30px" class="tio-chart-bar-2"></i>
                                </h5>
                                <!-- End Legend Indicators -->
                            </div>
                            <div class="col-6 graph-border-1">
                                <div class="mt-2 center-div">
                                      <span class="h6 mb-0">
                                          <i class="legend-indicator bg-success"
                                             style="background-color: #B6C867!important;"></i>
                                         {{translate('Your Earnings')}} : {{\App\Services\BackEndHelper::usd_to_currency(array_sum($product_manager_data))." ".\App\Services\BackEndHelper::currency_symbol()}}
                                    </span>
                                </div>
                            </div>
                            <div class="col-6 graph-border-1">
                                <div class="mt-2 center-div">
                                      <span class="h6 mb-0">
                                          <i class="legend-indicator bg-danger"
                                             style="background-color: #01937C!important;"></i>
                                        {{translate('Commission Given')}} : {{\App\Services\BackEndHelper::usd_to_currency(array_sum($commission_data))." ".\App\Services\BackEndHelper::currency_symbol()}}
                                    </span>
                                </div>
                            </div>
                        </div>
                        <!-- End Row -->

                        <!-- Bar Chart -->
                        <div class="chartjs-custom">
                            <canvas id="updatingData" style="height: 20rem;"
                                    data-hs-chartjs-options='{
                            "type": "bar",
                            "data": {
                              "labels": ["Jan","Feb","Mar","April","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec"],
                              "datasets": [{
                                "data": [{{$product_manager_data[1]}},{{$product_manager_data[2]}},{{$product_manager_data[3]}},{{$product_manager_data[4]}},{{$product_manager_data[5]}},{{$product_manager_data[6]}},{{$product_manager_data[7]}},{{$product_manager_data[8]}},{{$product_manager_data[9]}},{{$product_manager_data[10]}},{{$product_manager_data[11]}},{{$product_manager_data[12]}}],
                                "backgroundColor": "#B6C867",
                                "borderColor": "#B6C867"
                              },
                              {
                                "data": [{{$commission_data[1]}},{{$commission_data[2]}},{{$commission_data[3]}},{{$commission_data[4]}},{{$commission_data[5]}},{{$commission_data[6]}},{{$commission_data[7]}},{{$commission_data[8]}},{{$commission_data[9]}},{{$commission_data[10]}},{{$commission_data[11]}},{{$commission_data[12]}}],
                                "backgroundColor": "#01937C",
                                "borderColor": "#01937C"
                              }]
                            },
                            "options": {
                              "scales": {
                                "yAxes": [{
                                  "gridLines": {
                                    "color": "#e7eaf3",
                                    "drawBorder": false,
                                    "zeroLineColor": "#e7eaf3"
                                  },
                                  "ticks": {
                                    "beginAtZero": true,
                                    "stepSize": 50000,
                                    "fontSize": 12,
                                    "fontColor": "#97a4af",
                                    "fontFamily": "Open Sans, sans-serif",
                                    "padding": 10,
                                    "postfix": " {{\App\Services\BackEndHelper::currency_symbol()}}"
                                  }
                                }],
                                "xAxes": [{
                                  "gridLines": {
                                    "display": false,
                                    "drawBorder": false
                                  },
                                  "ticks": {
                                    "fontSize": 12,
                                    "fontColor": "#97a4af",
                                    "fontFamily": "Open Sans, sans-serif",
                                    "padding": 5
                                  },
                                  "categoryPercentage": 0.5,
                                  "maxBarThickness": "10"
                                }]
                              },
                              "cornerRadius": 2,
                              "tooltips": {
                                "prefix": " ",
                                "hasIndicator": true,
                                "mode": "index",
                                "intersect": false
                              },
                              "hover": {
                                "mode": "nearest",
                                "intersect": true
                              }
                            }
                          }'></canvas>
                        </div>
                        <!-- End Bar Chart -->
                    </div>
                    <!-- End Body -->
                </div>
                <!-- End Card -->
            </div>

            <div class="col-lg-6 mt-3">
                <!-- Card -->
                <div class="card h-100">
                    @include('product_manager-views.partials._top-selling-products',['top_sell'=>$data['top_sell']])
                </div>
                <!-- End Card -->
            </div>

            <div class="col-lg-6 mt-3">
                <!-- Card -->
                <div class="card h-100">
                    @include('product_manager-views.partials._most-rated-products',['most_rated_products'=>$data['most_rated_products']])
                </div>
                <!-- End Card -->
            </div>
        </div>
    </div>

@endsection

@push('script')
    <script src="{{asset('assets/back-end')}}/vendor/chart.js/dist/Chart.min.js"></script>
    <script src="{{asset('assets/back-end')}}/vendor/chart.js.extensions/chartjs-extensions.js"></script>
    <script
        src="{{asset('assets/back-end')}}/vendor/chartjs-plugin-datalabels/dist/chartjs-plugin-datalabels.min.js"></script>
@endpush

@push('script_2')
    <script>
        // INITIALIZATION OF CHARTJS
        // =======================================================
        Chart.plugins.unregister(ChartDataLabels);

        $('.js-chart').each(function () {
            $.HSCore.components.HSChartJS.init($(this));
        });

        var updatingChart = $.HSCore.components.HSChartJS.init($('#updatingData'));
    </script>

    <script>
        function call_duty() {
            toastr.warning('{{translate('Update your bank info first!')}}', '{{translate('Warning')}}!', {
                CloseButton: true,
                ProgressBar: true
            });
        }
    </script>

    <script>
        {{--function order_stats_update(type) {--}}
        {{--    $.ajaxSetup({--}}
        {{--        headers: {--}}
        {{--            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')--}}
        {{--        }--}}
        {{--    });--}}
        {{--    $.post({--}}
        {{--        url: '{{route('product_manager.dashboard.order-stats')}}',--}}
        {{--        data: {--}}
        {{--            statistics_type: type--}}
        {{--        },--}}
        {{--        beforeSend: function () {--}}
        {{--            $('#loading').show()--}}
        {{--        },--}}
        {{--        success: function (data) {--}}
        {{--            $('#order_stats').html(data.view)--}}
        {{--        },--}}
        {{--        complete: function () {--}}
        {{--            $('#loading').hide()--}}
        {{--        }--}}
        {{--    });--}}
        {{--}--}}

        function business_overview_stats_update(type) {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.post({
                url: '{{route('admin.dashboard.business-overview')}}',
                data: {
                    business_overview: type
                },
                beforeSend: function () {
                    $('#loading').show()
                },
                success: function (data) {
                    console.log(data.view)
                    $('#business-overview-board').html(data.view)
                },
                complete: function () {
                    $('#loading').hide()
                }
            });
        }
    </script>
@endpush
