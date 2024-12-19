@extends('layouts.back-end.app')

@section('title',$seller->shop ? $seller->shop->name : translate("order name not found"))

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
                    <div class="mt-4">
                        <div class="flex-start">
                            <div class="mx-1"><h4><i class="tio-shop-outlined"></i></h4></div>
                            <div>{{translate('Seller_request_for_open_a_shop.')}}</div>
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
                    <h1 class="page-header-title">{{ $seller->shop ? $seller->shop->name : "Shop Name : Update Please" }}</h1>
                </div>

            </div>
            <!-- Nav Scroller -->
            <div class="js-nav-scroller hs-nav-scroller-horizontal">
                <!-- Nav -->
                <ul class="nav nav-tabs page-header-tabs">
                    <li class="nav-item">
                        <a class="nav-link "
                           href="{{ route('admin.sellers.view',$seller->id) }}">{{translate('Shop')}}</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link"
                           href="{{ route('admin.sellers.view',['id'=>$seller->id, 'tab'=>'order']) }}">{{translate('Order')}}</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active"
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
        <!-- End Page Header-->
        <div class="tab-content">
            <div class="tab-pane fade show active" id="product">
                <div class="row pt-2">
                    <div class="col-md-12">
                        <div class="card h-100">
                            <div class="card-header">
                                <div class="flex-start">
                                    <div class="mx-1"><h3>{{translate('products')}}</h3></div>
                                    <div><h3><span style="color: red;">({{$products->total()}})</span></h3></div>
                                </div>

                                <a href="{{route('admin.product.add-new')}}" class="btn btn-primary pull-right"><i
                                            class="tio-add-circle"></i> {{translate('add')}} {{translate('New')}} {{translate('product')}}
                                </a>
                            </div>
                            <div class="table-responsive datatable-custom">
                                <table id="columnSearchDatatable"
                                       style="text-align: {{Session::get('direction') === "rtl" ? 'right' : 'left'}};"
                                       class="table table-borderless table-thead-bordered table-nowrap table-align-middle card-table"
                                       data-hs-datatables-options='{
                                        "order": [],
                                        "orderCellsTop": true,
                                        "paging": false
                                    }'>
                                    <thead class="thead-light">
                                    <tr>
                                        <th>{{translate('SL#')}}</th>
                                        <th>{{translate('Product Name')}}</th>
                                        <th>{{translate('purchase_price')}}</th>
                                        <th>{{translate('selling_price')}}</th>
                                        <th>{{translate('featured')}}</th>
                                        <th>{{translate('Active')}} {{translate('status')}}</th>
                                        <th style="width: 5px"
                                            class="text-center">{{translate('Action')}}</th>
                                    </tr>
                                    </thead>

                                    <tbody id="set-rows">
                                    @foreach($products as $k=>$p)
                                        <tr>
                                            <th scope="row">{{$products->firstItem()+$k}}</th>
                                            <td>
                                                <a href="{{route('admin.product.view',[$p['id']])}}">
                                                    {{substr($p['name'],0,20)}}{{strlen($p['name'])>20?'...':''}}
                                                </a>
                                            </td>
                                            <td>
                                                {{\App\Services\BackEndHelper::set_symbol(\App\Services\BackEndHelper::usd_to_currency($p['purchase_price']))}}
                                            </td>
                                            <td>
                                                {{\App\Services\BackEndHelper::set_symbol(\App\Services\BackEndHelper::usd_to_currency($p['unit_price']))}}
                                            </td>
                                            <td>
                                                <label class="switch">
                                                    <input type="checkbox"
                                                           onclick="featured_status('{{$p['id']}}')" {{$p->featured == 1?'checked':''}}>
                                                    <span class="slider round"></span>
                                                </label>
                                            </td>
                                            <td>
                                                <label class="switch switch-status">
                                                    <input type="checkbox" class="status"
                                                           id="{{$p['id']}}" {{$p->status == 1?'checked':''}}>
                                                    <span class="slider round"></span>
                                                </label>
                                            </td>
                                            <td>
                                                <a class="btn btn-primary btn-sm"
                                                   href="{{route('admin.product.edit',[$p['id']])}}">
                                                    <i class="tio-edit"></i>{{translate('Edit')}}
                                                </a>
                                                <a class="btn btn-danger btn-sm" href="javascript:"
                                                   onclick="form_alert('product-{{$p['id']}}','Want to delete this item ?')">
                                                    <i class="tio-add-to-trash"></i> {{translate('Delete')}}
                                                </a>
                                                <form action="{{route('admin.product.delete',[$p['id']])}}"
                                                      method="post" id="product-{{$p['id']}}">
                                                    @csrf @method('delete')
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>


                            </div>
                            <div class="card-footer">
                                {{$products->links()}}
                            </div>
                            @if(count($products)==0)
                                <div class="text-center p-4">
                                    <img class="mb-3" src="{{asset('assets/back-end')}}/svg/illustrations/sorry.svg"
                                         alt="Image Description" style="width: 7rem;">
                                    <p class="mb-0">{{translate('No_data_to_show')}}</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script>
        // Call the dataTables jQuery plugin
        $(document).ready(function () {
            $('#dataTable').DataTable();
        });

        $(document).on('change', '.status', function () {
            var id = $(this).attr("id");
            if ($(this).prop("checked") == true) {
                var status = 1;
            } else if ($(this).prop("checked") == false) {
                var status = 0;
            }
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                }
            });
            $.ajax({
                url: "{{route('admin.product.status-update')}}",
                method: 'POST',
                data: {
                    id: id,
                    status: status
                },
                success: function (data) {
                    if (data.success == true) {
                        toastr.success('{{translate('Status updated successfully')}}');
                    } else {
                        toastr.error('{{translate('Status updated failed. Product must be approved')}}');
                        location.reload();
                    }
                }
            });
        });

        function featured_status(id) {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                }
            });
            $.ajax({
                url: "{{route('admin.product.featured-status')}}",
                method: 'POST',
                data: {
                    id: id
                },
                success: function () {
                    toastr.success('{{translate('Featured status updated successfully')}}');
                }
            });
        }
    </script>
@endpush
