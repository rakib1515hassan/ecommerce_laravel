@extends('layouts.back-end.app')

@section('title', translate('Order List'))

@push('css_or_js')

@endpush

@section('content')
    <div class="content container-fluid">
        <!-- Page Header -->
        <div class="page-header mb-1">
            <div class="flex-between align-items-center">
                <div>
                    <h1 class="page-header-title">{{translate('Orders')}} <span
                                class="badge badge-soft-dark mx-2">{{$orders->total()}}</span></h1>
                </div>
                <div>
                    <i class="tio-shopping-cart" style="font-size: 30px"></i>
                </div>
            </div>
            <!-- End Row -->

            <!-- Nav Scroller -->
            <div class="js-nav-scroller hs-nav-scroller-horizontal">
            <span class="hs-nav-scroller-arrow-prev" style="display: none;">
              <a class="hs-nav-scroller-arrow-link" href="javascript:;">
                <i class="tio-chevron-left"></i>
              </a>
            </span>

                <span class="hs-nav-scroller-arrow-next" style="display: none;">
              <a class="hs-nav-scroller-arrow-link" href="javascript:;">
                <i class="tio-chevron-right"></i>
              </a>
            </span>

                <!-- Nav -->
                <ul class="nav nav-tabs page-header-tabs">
                    <li class="nav-item">
                        <a class="nav-link active" href="#">{{translate('order_list')}}</a>
                    </li>
                </ul>
                <!-- End Nav -->
            </div>
            <!-- End Nav Scroller -->
        </div>
        <!-- End Page Header -->

        <!-- Card -->
        <div class="card">
            <!-- Header -->
            <div class="card-header">
                <div class="flex-between justify-content-between align-items-center flex-grow-1">
                    <div>
                        <form action="{{ url()->current() }}" method="GET" id="filter_form">
                            <!-- Search -->
                            <div class="input-group input-group-merge input-group-flush">
                                <div class="input-group-prepend">
                                    <div class="input-group-text">
                                        <i class="tio-search"></i>
                                    </div>
                                </div>
                                <input id="datatableSearch_" type="search" name="search" class="form-control"
                                       placeholder="{{translate('Search orders')}}"
                                       aria-label="Search orders" value="{{ $search }}"
                                       required>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fa fa-search" aria-hidden="true"></i>
                                    {{translate('search')}}
                                </button>

                                {{-- <button class="btn btn-info btn-sm ml-2" id="download_csv" 
                                    type="button">
                                    <i class="fa fa-cloud-download" aria-hidden="true"></i>
                                    Download
                                </button> --}}

                            </div>
                            <!-- End Search -->
                        </form>
                    </div>
                    <div>
                        <label> {{translate('inhouse_orders_only')}} : </label>
                        <label class="switch ml-3">
                            <input type="checkbox" class="status"
                                   onclick="filter_order()" {{session()->has('show_inhouse_orders') && session('show_inhouse_orders')==1?'checked':''}}>
                            <span class="slider round"></span>
                        </label>
                    </div>
                </div>
                <!-- End Row -->
            </div>
            <!-- End Header -->

            {{-- Filter --}}
            <div>
                <form 
                    action="{{ route('admin.orders.list', $status) }}" 
                    method="GET" 
                    id="filter_form"
                    >
                    <h4 class="text-center mt-2">Filter By Order Date</h4>
                    <div class="row d-flex justify-content-center mt-4 mb-4">
                        <input type="date" class="form-control col-3 mr-3" 
                            name="from_date" 
                            value="{{ request('from_date') }}" id="from_date">

                        <input type="date" class="form-control col-3 mr-3" 
                            name="to_date" 
                            value="{{ request('to_date') }}" id="to_date">

                        <button class="btn btn-primary btn-sm" type="submit">
                            <i class="fa fa-filter" aria-hidden="true"></i>
                            Filter
                        </button>
                        
                        <button class="btn btn-info btn-sm ml-2" id="download_csv" 
                            type="submit" name=download>
                            <i class="fa fa-cloud-download" aria-hidden="true"></i>
                            Download
                        </button>

                        <button class="btn btn-danger btn-sm ml-2" id="filter_clear" 
                            type="button">
                            <i class="fa fa-times" aria-hidden="true"></i>
                            Clear
                        </button>
                    </div>
                </form>
            </div>

            <!-- Table -->
            <div class="table-responsive datatable-custom">
                <table class="table table-hover table-borderless table-thead-bordered table-nowrap table-align-middle card-table"
                       style="width: 100%; text-align: {{Session::get('direction') === "rtl" ? 'right' : 'left'}}">
                    <thead class="thead-light">
                    <tr>
                        <th class="">
                            {{translate('SL')}}#
                        </th>
                        <th class=" ">{{translate('Order')}}</th>
                        <th>{{translate('Date')}}</th>
                        <th>{{translate('customer_name')}}</th>
                        <th>{{translate('Status')}}</th>
                        <th>{{translate('Total')}}</th>
                        <th>{{translate('Order')}} {{translate('Status')}} </th>
                        <th>{{translate('Action')}}</th>
                    </tr>
                    </thead>

                    <tbody>
                    @foreach($orders as $key=>$order)

                        <tr class="status-{{$order['order_status']}} class-all">
                            <td class="">
                                {{$orders->firstItem()+$key}}
                            </td>
                            <td class="table-column-pl-0">
                                <a href="{{route('admin.orders.details',['id'=>$order['id']])}}">{{$order['id']}}</a>
                            </td>
                            <td>{{date('d M Y',strtotime($order['created_at']))}}</td>
                            <td>
                                @if($order->customer)
                                    <a class="text-body text-capitalize"
                                       href="{{route('admin.orders.details',['id'=>$order['id']])}}">{{$order->customer['f_name'].' '.$order->customer['l_name']}}</a>
                                @else
                                    <label class="badge badge-danger">{{translate('invalid_customer_data')}}</label>
                                @endif
                            </td>
                            <td>
                                @if($order->payment_status=='paid')
                                    <span class="badge badge-soft-success">
                                      <span class="legend-indicator bg-success"
                                            style="{{Session::get('direction') === "rtl" ? 'margin-right: 0;margin-left: .4375rem;' : 'margin-left: 0;margin-right: .4375rem;'}}"></span>{{translate('paid')}}
                                    </span>
                                @else
                                    <span class="badge badge-soft-danger">
                                      <span class="legend-indicator bg-danger"
                                            style="{{Session::get('direction') === "rtl" ? 'margin-right: 0;margin-left: .4375rem;' : 'margin-left: 0;margin-right: .4375rem;'}}"></span>{{translate('unpaid')}}
                                    </span>
                                @endif
                            </td>
                            <td> {{\App\Services\BackEndHelper::set_symbol(\App\Services\BackEndHelper::usd_to_currency($order->order_amount))}}</td>
                            <td class="text-capitalize">
                                @if($order['order_status']=='pending')
                                    <span class="badge badge-soft-info ml-2 ml-sm-3">
                                        <span class="legend-indicator bg-info"
                                              style="{{Session::get('direction') === "rtl" ? 'margin-right: 0;margin-left: .4375rem;' : 'margin-left: 0;margin-right: .4375rem;'}}"></span>{{translate($order['order_status'])}}
                                      </span>

                                @elseif($order['order_status']=='processing' || $order['order_status']=='out_for_delivery')
                                    <span class="badge badge-soft-warning ml-2 ml-sm-3">
                                        <span class="legend-indicator bg-warning"
                                              style="{{Session::get('direction') === "rtl" ? 'margin-right: 0;margin-left: .4375rem;' : 'margin-left: 0;margin-right: .4375rem;'}}"></span>{{translate($order['order_status'])}}
                                      </span>
                                @elseif($order['order_status']=='confirmed')
                                    <span class="badge badge-soft-success ml-2 ml-sm-3">
                                        <span class="legend-indicator bg-success"
                                              style="{{Session::get('direction') === "rtl" ? 'margin-right: 0;margin-left: .4375rem;' : 'margin-left: 0;margin-right: .4375rem;'}}"></span>{{translate($order['order_status'])}}
                                      </span>
                                @elseif($order['order_status']=='failed')
                                    <span class="badge badge-danger ml-2 ml-sm-3">
                                        <span class="legend-indicator bg-warning"
                                              style="{{Session::get('direction') === "rtl" ? 'margin-right: 0;margin-left: .4375rem;' : 'margin-left: 0;margin-right: .4375rem;'}}"></span>{{translate($order['order_status'])}}
                                      </span>
                                @elseif($order['order_status']=='delivered')
                                    <span class="badge badge-soft-success ml-2 ml-sm-3">
                                        <span class="legend-indicator bg-success"
                                              style="{{Session::get('direction') === "rtl" ? 'margin-right: 0;margin-left: .4375rem;' : 'margin-left: 0;margin-right: .4375rem;'}}"></span>{{translate($order['order_status'])}}
                                      </span>
                                @else
                                    <span class="badge badge-soft-danger ml-2 ml-sm-3">
                                        <span class="legend-indicator bg-danger"
                                              style="{{Session::get('direction') === "rtl" ? 'margin-right: 0;margin-left: .4375rem;' : 'margin-left: 0;margin-right: .4375rem;'}}"></span>{{translate($order['order_status'])}}
                                      </span>
                                @endif
                            </td>
                            <td>
                                <div class="dropdown">
                                    <button class="btn btn-outline-secondary dropdown-toggle" type="button"
                                            id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true"
                                            aria-expanded="false">
                                        <i class="tio-settings"></i>
                                    </button>
                                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                        <a class="dropdown-item"
                                           href="{{route('admin.orders.details',['id'=>$order['id']])}}"><i
                                                    class="tio-visible"></i> {{translate('view')}}</a>
                                        <a class="dropdown-item" target="_blank"
                                           href="{{route('admin.orders.generate-invoice',[$order['id']])}}"><i
                                                    class="tio-download"></i> {{translate('invoice')}}</a>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
            <!-- End Table -->

            <!-- Footer -->
            <div class="card-footer">
                <!-- Pagination -->
                <div class="row justify-content-center justify-content-sm-between align-items-sm-center">
                    <div class="col-sm-auto">
                        <div class="d-flex justify-content-center justify-content-sm-end">
                            <!-- Pagination -->
                            {!! $orders->links() !!}
                        </div>
                    </div>
                </div>
                <!-- End Pagination -->
            </div>
            <!-- End Footer -->
        </div>
        <!-- End Card -->
    </div>
@endsection

@push('script_2')
    <script>
        function filter_order() {
            $.get({
                url: '{{route('admin.orders.inhouse-order-filter')}}',
                contentType: false,
                processData: false,
                beforeSend: function () {
                    $('#loading').show();
                },
                success: function (data) {
                    toastr.success('{{translate('order_filter_success')}}');
                    location.reload();
                },
                complete: function () {
                    $('#loading').hide();
                },
            });
        };
    </script>

    <script>
        // function getStatusFromUrl() {

        //     var currentUrl = window.location.href;
            
        //     // var urlParts = currentUrl.split('/');
        //     // var url = urlParts[urlParts.length - 1];
        //     // console.log("Url is =",url);

        //     var path = currentUrl.split('?')[0];

        //     var urlParts = path.split('/');

        //     var status = urlParts[urlParts.length - 1];
            
        //     // console.log("Status is =", status);
            
        //     return url
        // }

        document.getElementById('filter_clear').addEventListener('click', function() {
            console.log("Clear is called");
            document.getElementById('from_date').value = '';
            document.getElementById('to_date').value = '';

            window.location.href = '{{ route('admin.orders.list', $status) }}';

        });

        // document.getElementById('download_csv').addEventListener('click', function() {
        //     // Add hidden input to trigger CSV download
        //     var form = document.getElementById('filter_form');
        //     var input = document.createElement('input');
        //     input.type = 'hidden';
        //     input.name = 'download';
        //     input.value = 'csv';
        //     form.appendChild(input);

        //     // Submit the form
        //     form.submit();

        //     form.removeChild(input);
        // });

        // document.getElementById('download_csv').addEventListener('click', function() {
        //     var fromDate = document.getElementById('from_date').value;
        //     var toDate = document.getElementById('to_date').value;

        //     var form = document.getElementById('filter_form');
        //     var input = document.createElement('input');
        //     input.type = 'hidden';
        //     input.name = 'download';
        //     input.value = 'csv';
        //     form.appendChild(input);

        //     var action = '{{ route('admin.orders.list', $status) }}';
        //     action += `?from_date=${fromDate}&to_date=${toDate}&download=csv`;
        //     form.action = action;

        //     form.submit();
        //     form.removeChild(input);
        // });


        // document.getElementById('download_csv').addEventListener('click', function() {
        //     var form = document.getElementById('filter_form');
        //     var input = document.createElement('input');
        //     input.type = 'hidden';
        //     input.name = 'download';
        //     input.value = 'csv';
        //     form.appendChild(input);

        //     var status = getStatusFromUrl();
        //     var formData = new FormData(form);
        //     var queryString = new URLSearchParams(formData).toString();

        //     // Modify form action to include the current status and query parameters
        //     form.action = '/orders/list/' + status + '?' + queryString;

        //     // Submit the form
        //     form.submit();

        //     // Remove the input element after form submission
        //     form.removeChild(input);
        // });

        // function getStatusFromUrl() {
        //     var currentUrl = window.location.href;
        //     var path = currentUrl.split('?')[0];
        //     var urlParts = path.split('/');
        //     var status = urlParts[urlParts.length - 1];
        //     return status.split('?')[0]; 
        // }
    </script>
@endpush
