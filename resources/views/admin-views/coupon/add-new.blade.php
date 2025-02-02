@extends('layouts.back-end.app')

@section('title', translate('Coupon Add'))

@push('css_or_js')

@endpush

@section('content')
    <div class="content container-fluid">
        <!-- Page Header -->
        <div class="page-header">
            <div class="row align-items-center">
                <div class="col-sm mb-2 mb-sm-0">
                    <h1 class="page-header-title"><i
                                class="tio-add-circle-outlined"></i> {{translate('Add')}} {{translate('New')}} {{translate('Coupon')}}
                    </h1>
                </div>
            </div>
        </div>
        <!-- End Page Header -->

        <!-- Content Row -->
        <div class="row gx-2 gx-lg-3">
            <div class="col-sm-12 col-lg-12 mb-3 mb-lg-2">
                <div class="card">
                    {{-- <div class="card-header">
                        {{translate('coupon_form')}}
                    </div> --}}
                    <div class="card-body">
                        <form action="{{route('admin.coupon.add-new')}}" method="post">
                            @csrf

                            <div class="row">
                                <div class="col-4">
                                    <div class="form-group">
                                        <label for="name">{{translate('Type')}}</label>
                                        <select class="form-control" name="coupon_type"
                                                style="width: 100%" required>
                                            {{--<option value="delivery_charge_free">Delivery Charge Free</option>--}}
                                            <option value="discount_on_purchase">{{translate('Discount_on_Purchase')}}</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="form-group">
                                        <label for="name">{{translate('Title')}}</label>
                                        <input type="text" name="title" class="form-control" id="title"
                                               placeholder="{{translate('Title')}}" required>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="form-group">
                                        <label for="name">{{translate('Code')}}</label>
                                        <input type="text" name="code" value="{{\Illuminate\Support\Str::random(10)}}"
                                               class="form-control" id="code"
                                               placeholder="" required>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-3 col-6">
                                    <div class="form-group">
                                        <label for="name">{{translate('start_date')}}</label>
                                        <input type="date" name="start_date" class="form-control" id="start date"
                                               placeholder="{{translate('start date')}}" required>
                                    </div>
                                </div>
                                <div class="col-md-3 col-6">
                                    <div class="form-group">
                                        <label for="name">{{translate('expire_date')}}</label>
                                        <input type="date" name="expire_date" class="form-control" id="expire date"
                                               placeholder="{{translate('expire date')}}" required>
                                    </div>
                                </div>
                                <div class="col-md-3 col-6">
                                    <div class="form-group">
                                        <label
                                                for="exampleFormControlInput1">{{translate('limit')}} {{translate('for')}} {{translate('same')}} {{translate('user')}}</label>
                                        <input type="number" name="limit" id="coupon_limit" class="form-control"
                                               placeholder="{{translate('EX')}}: {{translate('10')}}">
                                    </div>
                                </div>
                                <div class="col-md-3 col-6">
                                    <div class="form-group">
                                        <label for="name">{{translate('discount_type')}}</label>
                                        <select class="form-control" name="discount_type"
                                                onchange="checkDiscountType(this.value)"
                                                style="width: 100%">
                                            <option value="amount">{{translate('Amount')}}</option>
                                            <option value="percentage">{{translate('percentage')}}</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-3 col-6">
                                    <div class="form-group">
                                        <label for="name">{{translate('Discount')}}</label>
                                        <input type="number" min="1" max="1000000" name="discount" class="form-control"
                                               id="discount"
                                               placeholder="{{translate('discount')}}" required>
                                    </div>
                                </div>
                                <div class="col-md-3 col-6">
                                    <label for="name">{{translate('minimum_purchase')}}</label>
                                    <input type="number" min="1" max="1000000" name="min_purchase" class="form-control"
                                           id="minimum purchase"
                                           placeholder="{{translate('minimum purchase')}}" required>
                                </div>
                                <div class="col-md-3 col-6">
                                    <div class="form-group">
                                        <label for="name">{{translate('maximum_discount')}}</label>
                                        <input type="number" min="1" max="1000000" name="max_discount"
                                               class="form-control" id="maximum discount"
                                               placeholder="{{translate('maximum discount')}}" required>
                                    </div>
                                </div>
                            </div>

                            <div class="card-footer">
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
                        <div class="row justify-content-between align-items-center flex-grow-1">
                            <div class="col-lg-3 mb-3 mb-lg-0">
                                <h5>{{translate('coupons_table')}} <span style="color: red;">({{ $cou->total() }})</span>
                                </h5>
                            </div>
                            <div class="col-lg-6">
                                <!-- Search -->
                                <form action="{{ url()->current() }}" method="GET">
                                    <div class="input-group input-group-merge input-group-flush">
                                        <div class="input-group-prepend">
                                            <div class="input-group-text">
                                                <i class="tio-search"></i>
                                            </div>
                                        </div>
                                        <input id="datatableSearch_" type="search" name="search" class="form-control"
                                               placeholder="{{translate('Search by Title or Code or Discount Type')}}"
                                               value="{{ $search }}" aria-label="Search orders" required>
                                        <button type="submit"
                                                class="btn btn-primary">{{translate('search')}}</button>
                                    </div>
                                </form>
                                <!-- End Search -->
                            </div>
                        </div>
                    </div>
                    <div class="card-body" style="padding: 0">
                        <div class="table-responsive">
                            <table id="datatable"
                                   class="table table-hover table-borderless table-thead-bordered table-nowrap table-align-middle card-table"
                                   style="width: 100%">
                                <thead class="thead-light">
                                <tr>
                                    <th>{{translate('SL')}}#</th>
                                    <th>{{translate('coupon_type')}}</th>
                                    <th>{{translate('Title')}}</th>
                                    <th>{{translate('Code')}}</th>
                                    <th>{{ translate('user') }} {{ translate('limit') }}</th>
                                    <th>{{translate('minimum_purchase')}}</th>
                                    <th>{{translate('maximum_discount')}}</th>
                                    <th>{{translate('Discount')}}</th>
                                    <th>{{translate('discount_type')}}</th>
                                    <th>{{translate('start_date')}}</th>
                                    <th>{{translate('expire_date')}}</th>
                                    <th>{{translate('Status')}}</th>
                                    <th>{{translate('Action')}}</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($cou as $k=>$c)
                                    <tr>
                                        <th scope="row">{{$cou->firstItem() + $k}}</th>
                                        <td style="text-transform: capitalize">{{str_replace('_',' ',$c['coupon_type'])}}</td>
                                        <td class="text-capitalize">
                                            {{substr($c['title'],0,20)}}
                                        </td>
                                        <td>{{$c['code']}}</td>
                                        <td>{{ $c['limit'] }}</td>
                                        <td>{{\App\Services\BackEndHelper::set_symbol(\App\Services\BackEndHelper::usd_to_currency($c['min_purchase']))}}</td>
                                        <td>{{\App\Services\BackEndHelper::set_symbol(\App\Services\BackEndHelper::usd_to_currency($c['max_discount']))}}</td>
                                        <td>{{$c['discount_type']=='amount'?\App\Services\BackEndHelper::set_symbol(\App\Services\BackEndHelper::usd_to_currency($c['discount'])):$c['discount']}}</td>
                                        <td>{{$c['discount_type']}}</td>
                                        <td>{{date('d-M-y',strtotime($c['start_date']))}}</td>
                                        <td>{{date('d-M-y',strtotime($c['expire_date']))}}</td>
                                        <td>
                                            <label class="toggle-switch toggle-switch-sm">
                                                <input type="checkbox" class="toggle-switch-input"
                                                       onclick="location.href='{{route('admin.coupon.status',[$c['id'],$c->status?0:1])}}'"
                                                       class="toggle-switch-input" {{$c->status?'checked':''}}>
                                                <span class="toggle-switch-label">
                                            <span class="toggle-switch-indicator"></span>
                                            </span>
                                            </label>
                                        </td>
                                        <td>
                                            <a href="{{route('admin.coupon.update',[$c['id']])}}"
                                               class="btn btn-primary btn-sm">
                                                {{translate('Update')}}
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="card-footer">
                        {{$cou->links()}}
                    </div>
                    @if(count($cou)==0)
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
    {{-- <script>
         $(document).ready(function() {
            $('#dataTable').DataTable();
        });
        function checkDiscountType(val) {
            if (val == 'amount') {
                $('#max-discount').hide()
            } else if (val == 'percentage') {
                $('#max-discount').show()
            }
        }
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
                url: "#",
                method: 'POST',
                data: {
                    id: id,
                    status: status
                },
                success: function (data) {
                    if (data == 1) {
                        toastr.success('Coupon published successfully');
                    } else {
                        toastr.success('Coupon unpublished successfully');
                    }
                }
            });
        });

    </script> --}}

    <script>
        $(".js-example-theme-single").select2({
            theme: "classic"
        });

        $(".js-example-responsive").select2({
            width: 'resolve'
        });
    </script>

    <!-- Page level plugins -->
    <script src="{{asset('assets/back-end')}}/vendor/datatables/jquery.dataTables.min.js"></script>
    <script src="{{asset('assets/back-end')}}/vendor/datatables/dataTables.bootstrap4.min.js"></script>

    <!-- Page level custom scripts -->
    <script src="{{asset('assets/back-end')}}/js/demo/datatables-demo.js"></script>
@endpush
