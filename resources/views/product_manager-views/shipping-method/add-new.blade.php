@extends('layouts.back-end.app-product_manager')

@section('title', translate('Add Shipping'))

@push('css_or_js')

@endpush

@section('content')
    <div class="content container-fluid">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a
                            href="{{route('product_manager.dashboard.index')}}">{{translate('Dashboard')}}</a>
                </li>
                <li class="breadcrumb-item" aria-current="page">{{translate('shipping_method')}}</li>

            </ol>
        </nav>

        <!-- Content Row -->
        <div class="row mt-2">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h1 class="h3 mb-0 text-black-50 text-capitalize">{{translate('shipping_method')}} </h1>
                    </div>
                    <div class="card-body">
                        <form action="{{route('product_manager.business-settings.shipping-method.add')}}" method="post"
                              style="text-align: {{Session::get('direction') === "rtl" ? 'right' : 'left'}};">
                            @csrf
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-6">
                                        <label for="title">{{translate('title')}}</label>
                                        <input type="text" name="title" class="form-control" placeholder="">
                                    </div>

                                    <div class="col-md-3">
                                        <label for="duration">{{translate('duration')}}</label>
                                        <input type="text" name="duration" class="form-control"
                                               placeholder="{{translate('Ex')}} : 4-6 {{translate('days')}}">
                                    </div>

                                    <div class="col-md-3">
                                        <label for="cost">{{translate('cost')}}</label>
                                        <input type="number" min="0" max="1000000" name="cost" class="form-control"
                                               placeholder="{{translate('Ex')}} : 10 $">
                                    </div>
                                </div>
                            </div>

                            <div class="card-footer"
                                 style="padding-{{Session::get('direction') === "rtl" ? 'right' : 'left'}}: 0">
                                <button type="submit"
                                        class="btn btn-primary">{{translate('Submit')}}</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="row" style="margin-top: 20px">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="text-capitalize">{{translate('shipping_method')}} {{translate('table')}}
                            <span style="color: red;">({{ $shipping_methods->total() }})</span></h5>
                    </div>
                    <div class="card-body">
                        <table id="datatable"
                               style="text-align: {{Session::get('direction') === "rtl" ? 'right' : 'left'}};"
                               class="table table-hover table-borderless table-thead-bordered table-nowrap table-align-middle card-table"
                               style="width: 100%">
                            <thead class="thead-light">
                            <tr>
                                <th>{{translate('sl#')}}</th>
                                <th>{{translate('title')}}</th>
                                <th>{{translate('duration')}}</th>
                                <th>{{translate('cost')}}</th>
                                <th>{{translate('status')}}</th>
                                <th scope="col" style="width: 50px">{{translate('action')}}</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($shipping_methods as $k=>$method)
                                <tr>
                                    <th scope="row">{{$shipping_methods->firstItem()+$k}}</th>
                                    <td>{{$method['title']}}</td>
                                    <td>
                                        {{$method['duration']}}
                                    </td>
                                    <td>
                                        {{\App\Services\BackEndHelper::set_symbol(\App\Services\BackEndHelper::usd_to_currency($method['cost']))}}
                                    </td>

                                    <td>
                                        <label class="switch">
                                            <input type="checkbox" class="status"
                                                   id="{{$method['id']}}" {{$method->status == 1?'checked':''}}>
                                            <span class="slider round"></span>
                                        </label>
                                    </td>
                                    <td>
                                        <div class="dropdown float-right">
                                            <button class="btn btn-seconary btn-sm dropdown-toggle" type="button"
                                                    id="dropdownMenuButton" data-toggle="dropdown"
                                                    aria-haspopup="true"
                                                    aria-expanded="false">
                                                <i class="tio-settings"></i>
                                            </button>
                                            <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                                <a class="dropdown-item"
                                                   href="{{route('product_manager.business-settings.shipping-method.edit',[$method['id']])}}">{{translate('Edit')}}</a>
                                                <a class="dropdown-item delete" style="cursor: pointer;"
                                                   id="{{ $method['id'] }}">{{translate('Delete')}}</a>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="card-footer">
                        {!! $shipping_methods->links() !!}
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
                url: "{{route('product_manager.business-settings.shipping-method.status-update')}}",
                method: 'POST',
                data: {
                    id: id,
                    status: status
                },
                success: function () {
                    toastr.success('{{translate('Status updated successfully')}}');
                }
            });
        });
        $(document).on('click', '.delete', function () {
            var id = $(this).attr("id");
            Swal.fire({
                title: '{{translate('Are you sure delete this ?')}}',
                text: "{{translate('You wont be able to revert this!')}}",
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: '{{translate('Yes, delete it!')}}'
            }).then((result) => {
                if (result.value) {
                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                        }
                    });
                    $.ajax({
                        url: "{{route('product_manager.business-settings.shipping-method.delete')}}",
                        method: 'POST',
                        data: {id: id},
                        success: function () {
                            toastr.success('{{translate('Shipping Method deleted successfully')}}');
                            location.reload();
                        }
                    });
                }
            })
        });
    </script>
@endpush
