@extends('layouts.back-end.app-seller')

@section('title',translate('Product Manager List'))

@push('css_or_js')
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endpush

@section('content')
    <div class="content container-fluid">
        <!-- Page Header -->
        <div class="page-header">
            <div class="row align-items-center">
                <div class="col-sm mb-2 mb-sm-0">
                    <h1 class="page-header-title"><i class="tio-filter-list"></i>
                        {{translate('Product Manager')}} {{translate('list')}}
                        ( {{ $product_managers->total() }} )
                    </h1>
                </div>
            </div>
        </div>
        <!-- End Page Header -->
        <div class="row gx-2 gx-lg-3">
            <div class="col-sm-12 col-lg-12 mb-3 mb-lg-2">
                <!-- Card -->
                <div class="card">
                    <!-- Header -->
                    <div class="card-header">
                        <div class="flex-start">
                            <div>
                                <form action="{{url()->current()}}" method="GET">
                                    <!-- Search -->
                                    <div class="input-group input-group-merge input-group-flush">
                                        <div class="input-group-prepend">
                                            <div class="input-group-text">
                                                <i class="tio-search"></i>
                                            </div>
                                        </div>
                                        <input id="datatableSearch_" type="search" name="search" class="form-control"
                                               placeholder="Search" aria-label="Search" value="{{$search}}" required>
                                        <button type="submit" class="btn btn-primary">search</button>

                                    </div>
                                    <!-- End Search -->
                                </form>
                            </div>
                        </div>
                        <a href="{{route('seller.product_manager.add')}}" class="btn btn-primary pull-right"><i
                                    class="tio-add-circle"></i> {{translate('add')}} {{translate('Product-Manager')}}
                        </a>
                    </div>
                    <!-- End Header -->

                    <!-- Table -->
                    <div class="table-responsive datatable-custom">
                        <table
                                class="table table-borderless table-thead-bordered table-nowrap table-align-middle card-table">
                            <thead class="thead-light">
                            <tr>
                                <th>{{translate('#')}}</th>
                                <th style="width: 30%">{{translate('name')}}</th>
                                <th style="width: 25%">{{translate('image')}}</th>
                                <th>{{translate('email')}}</th>
                                <th>{{translate('phone')}}</th>
                                <th>{{translate('status')}}</th>
                                <th>{{translate('action')}}</th>
                            </tr>
                            </thead>

                            <tbody id="set-rows">
                            @foreach($product_managers as $key=>$dm)
                                <tr>
                                    <td>{{$product_managers->firstitem()+$key}}</td>
                                    <td>
                                        <span class="d-block font-size-sm text-body">
                                            {{$dm['f_name'].' '.$dm['l_name']}}
                                        </span>
                                    </td>
                                    <td>
                                        <div style="overflow-x: hidden;overflow-y: hidden">
                                            <img width="60" style="border-radius: 50%;height: 60px; width: 60px;"
                                                 onerror="this.src='{{asset('assets/back-end/img/160x160/img1.jpg')}}'"
                                                 src="{{asset('storage/product_manager')}}/{{$dm['image']}}">
                                        </div>
                                    </td>
                                    <td>
                                        {{$dm['email']}}
                                    </td>
                                    <td>
                                        {{$dm['phone']}}
                                    </td>
                                    <td>
                                        <label class="switch switch-status">
                                            <input type="checkbox" class="status"
                                                   id="{{$dm['id']}}" {{$dm->is_active == 1?'checked':''}}>
                                            <span class="slider round"></span>
                                        </label>
                                    </td>
                                    <td>
                                        <!-- Dropdown -->
                                        <div class="dropdown">
                                            <button class="btn btn-secondary dropdown-toggle" type="button"
                                                    id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true"
                                                    aria-expanded="false">
                                                <i class="tio-settings"></i>
                                            </button>
                                            <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                                <a class="dropdown-item"
                                                   href="{{route('seller.product_manager.edit',[$dm['id']])}}">{{translate('edit')}}</a>
                                                <a class="dropdown-item" href="javascript:"
                                                   onclick="form_alert('product_manager-{{$dm['id']}}','Want to remove this information ?')">{{translate('delete')}}</a>
                                                <form action="{{route('seller.product_manager.delete',[$dm['id']])}}"
                                                      method="post" id="product_manager-{{$dm['id']}}">
                                                    @csrf @method('delete')
                                                </form>
                                            </div>
                                        </div>
                                        <!-- End Dropdown -->
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                        <hr>

                        <div class="page-area">
                            <table>
                                <tfoot>
                                {!! $product_managers->links() !!}
                                </tfoot>
                            </table>
                        </div>

                    </div>
                    <!-- End Table -->
                </div>
                <!-- End Card -->
            </div>
        </div>
    </div>

@endsection

@push('script_2')
    <script>
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
                url: "{{route('seller.product_manager.status-update')}}",
                method: 'POST',
                data: {
                    id: id,
                    status: status
                },
                success: function (data) {
                    toastr.success('{{translate('Status updated successfully')}}');
                }
            });
        });
    </script>
@endpush
