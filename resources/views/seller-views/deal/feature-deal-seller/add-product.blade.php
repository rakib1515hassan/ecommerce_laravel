@extends('layouts.back-end.app-seller')
@section('title', translate('Deal Product'))
@push('css_or_js')

    <link href="{{ asset('assets/back-end/css/custom.css')}}" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endpush

@section('content')
    <div class="content container-fluid">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a
                        href="{{route('seller.dashboard.index')}}">{{ translate('Dashboard')}}</a></li>
                <li class="breadcrumb-item" aria-current="page">{{ translate('feature_deal')}}</li>
                <li class="breadcrumb-item">{{ translate('Add new product')}}</li>
            </ol>
        </nav>

        <!-- Content Row -->
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h1 class="h3 mb-0 text-black-50">Manage product for {{$deal['title']}}</h1>
                        <h4 class="text-warning">Request for featured deal</h4>
                    </div>
                    <div class="card-body">
                        <form action="{{route('seller.deal.feature.add-product',[$deal['id']])}}" method="post">
                            @csrf
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-12">
                                        <label for="name">{{ translate('Add new product')}}</label>
                                        <select
                                            class="js-example-basic-multiple js-states js-example-responsive form-control"
                                            name="product_id">
                                            @foreach (\App\Models\Product::where('added_by','seller')->where('user_id',auth('seller')->id())->orderBy('name', 'asc')->get() as $key => $product)
                                                <option value="{{ $product->id }}">
                                                    {{$product['name']}}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="card-footer">
                                <button type="submit"
                                        class="btn btn-primary float-right">{{ translate('add')}}</button>
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
                        <h5>{{ translate('Product')}} {{ translate('Table')}}</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered" width="100%" cellspacing="0">
                                <thead>
                                <tr>
                                    <th scope="col">{{ translate('sl')}}</th>
                                    <th scope="col">{{ translate('name')}}</th>
                                    <th scope="col">{{ translate('price')}}</th>
                                    <th scope="col" style="width: 50px">{{ translate('action')}}</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($feature_deal_products as $k=>$item)
                                    @php($de_p= $item->product)
                                    <tr>
                                        <th scope="row">{{$feature_deal_products->firstitem()+$k}}</th>
                                        <td>{{$de_p['name']}}</td>
                                        <td>{{\App\Services\BackEndHelper::usd_to_currency($de_p['unit_price'])}}</td>
                                        <td>
                                            <a href="{{route('seller.deal.feature.delete-product',[$item['id']])}}"
                                               class="btn btn-danger btn-sm">
                                                {{ trans ('Delete')}}
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                            <table>
                                <tfoot>
                                {!! $feature_deal_products->links() !!}
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')

    <script>
        $(".js-example-theme-single").select2({
            theme: "classic"
        });

        $(".js-example-responsive").select2({
            width: 'resolve'
        });

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
                url: "{{route('seller.deal.flash')}}",
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

        $(document).on('change', '.flash-product-discount-type', function () {
            var discount_type = $(this).val();
            var discount = $(this).parent().parent().parent().find('.flash-product-discount-input').find('input').val();
            var product_id = $(this).parent().parent().parent().find('.flash-product-discount-input').find('input').attr('data-id');


            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                }
            });
            $.ajax({
                url: "{{route('seller.deal.add-product',[$deal['id']])}}",
                method: 'POST',
                data: {
                    discount_type: discount_type,
                    discount: discount,
                    product_id: product_id
                },
                success: function () {
                    toastr.success('{{translate('Discount type updated successfully')}}');
                }
            });
        });

        $(document).on('change', '.flash-product-discount-input', function () {

            var id = $(this).find('input').attr('data-id');
            var discount = $(this).find('input').val();
            var discount_type = $(this).parent().parent().find('.flash-product-discount-type').val();

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                }
            });
            $.ajax({
                url: "{{route('seller.deal.add-product',[$deal['id']])}}",
                method: 'POST',
                data: {
                    product_id: id,
                    discount_type: discount_type,
                    discount: discount
                },
                success: function () {
                    toastr.success('{{translate('Discount updated successfully')}}');
                }
            });
        });

    </script>
@endpush
