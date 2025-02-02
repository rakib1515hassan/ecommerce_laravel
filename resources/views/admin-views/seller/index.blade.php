@extends('layouts.back-end.app')

@section('title', translate('Seller List'))

@push('css_or_js')

@endpush

@section('content')
    <div class="content container-fluid">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a
                            href="{{route('admin.dashboard.index')}}">{{translate('Dashboard')}}</a>
                </li>
                <li class="breadcrumb-item" aria-current="page">{{translate('Sellers')}}</li>
            </ol>
        </nav>

        <div class="row mt-4">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row row justify-content-between align-items-center flex-grow-1 mx-1">
                            <div class="flex-between">
                                <div><h5>{{translate('seller_table')}}</h5></div>
                                <div class="mx-1"><h5 style="color: red;">({{ $sellers->total() }})</h5></div>
                            </div>
                            <div style="width: 40vw">
                                <!-- Search -->
                                <form action="{{ url()->current() }}" method="GET">
                                    <div class="input-group input-group-merge input-group-flush">
                                        <div class="input-group-prepend">
                                            <div class="input-group-text">
                                                <i class="tio-search"></i>
                                            </div>
                                        </div>
                                        <input id="datatableSearch_" type="search" name="search" class="form-control"
                                               placeholder="{{translate('Search by Name or Phone or Email')}}"
                                               aria-label="Search orders" value="{{ $search }}" required>
                                        <button type="submit"
                                                class="btn btn-primary">{{translate('search')}}</button>
                                    </div>
                                </form>
                                <!-- End Search -->
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table
                                    style="text-align: {{Session::get('direction') === "rtl" ? 'right' : 'left'}};"
                                    class="table table-borderless table-thead-bordered table-nowrap table-align-middle card-table">
                                <thead class="thead-light">
                                <tr>
                                    <th scope="col">{{translate('SL#')}}</th>
                                    <th scope="col">{{translate('name')}}</th>
                                    <th scope="col">{{translate('Phone')}}</th>
                                    <th scope="col">{{translate('Email')}}</th>
                                    <th scope="col">{{translate('status')}}</th>
                                    <th scope="col">{{translate('orders')}}</th>
                                    <th scope="col">{{translate('Products')}}</th>
                                    <th scope="col">{{translate('Admin Manage')}}</th>
                                    <th scope="col" style="width: 50px">{{translate('action')}}</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($sellers as $key=>$seller)
                                    <tr>
                                        <td scope="col">{{$sellers->firstItem()+$key}}</td>
                                        <td scope="col">{{$seller->f_name}} {{$seller->l_name}}</td>
                                        <td scope="col">{{$seller->phone}}</td>
                                        <td scope="col">{{$seller->email}}</td>
                                        <td scope="col">
                                            {!! $seller->status=='approved'?'<label class="badge badge-success">Active</label>':'<label class="badge badge-danger">In-Active</label>' !!}
                                        </td>
                                        <td scope="col">
                                            <a href="{{route('admin.sellers.order-list',[$seller['id']])}}"
                                               class="btn btn-outline-primary btn-block">
                                                <i class="tio-shopping-cart-outlined"></i>( {{$seller->orders->count()}}
                                                )
                                            </a>
                                        </td>
                                        <td scope="col">
                                            <a href="{{route('admin.sellers.product-list',[$seller['id']])}}"
                                               class="btn btn-outline-primary btn-block">
                                                <i class="tio-premium-outlined mr-1"></i>( {{$seller->product->count()}}
                                                )
                                            </a>
                                        </td>

                                        <td scope="col">
                                            <label class="switch">
                                                <input type="checkbox" class="status"
                                                       id="{{$seller['id']}}" {{$seller->admin_manage == 1?'checked':''}}>
                                                <span class="slider round"></span>
                                            </label>
                                        </td>


                                        <td>
                                            <a class="btn btn-primary"
                                               href="{{route('admin.sellers.view',$seller->id)}}">
                                                {{translate('View')}}
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="card-footer">
                        {!! $sellers->links() !!}
                    </div>
                    @if(count($sellers)==0)
                        <div class="text-center p-4">
                            <img class="mb-3" src="{{asset('assets/back-end')}}/svg/illustrations/sorry.svg"
                                 alt="Image Description" style="width: 7rem;">
                            <p class="mb-0">{{translate('No data to show')}}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script>
        $(document).on('change', '.status', function () {
            var id = $(this).attr("id");
            if ($(this).prop("checked") == true) {
                var status = 1;
            } else if ($(this).prop("checked") == false) {
                var status = 0;
            }
            $.ajax({
                type: "POST",
                url: "{{route('admin.sellers.admin_manage_status')}}",
                data: {
                    'admin_manage': status,
                    'id': id,
                    '_token': '{{ csrf_token() }}'
                },
                success: function (data) {
                    toastr.success("{{translate('Status Updated Successfully')}}");
                }
            });
        });
    </script>
@endpush
