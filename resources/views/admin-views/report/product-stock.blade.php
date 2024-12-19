@extends('layouts.back-end.app')

@section('title', translate('Product stock Report'))

@push('css_or_js')
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endpush

@section('content')
    <div class="content container-fluid">
        <!-- Page Header -->
        <div class="page-header">
            <!-- Nav -->
            <div class="js-nav-scroller hs-nav-scroller-horizontal">
                <ul class="nav nav-tabs page-header-tabs" id="projectsTab" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active"
                           href="javascript:">{{translate('Product stock report')}}</a>
                    </li>
                </ul>
            </div>
            <!-- End Nav -->
        </div>
        <!-- End Page Header -->

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <form style="width: 100%;" 
                            action="{{route('admin.stock.product-stock')}}" 
                            id="filter_form">
                            <div class="row text-{{Session::get('direction') === "rtl" ? 'right' : 'left'}}">
                                <div class="col-2">
                                    <div class="form-group">
                                        <label for="exampleInputEmail1">{{translate('Seller')}}</label>
                                    </div>
                                </div>
                                <div class="col-8">
                                    <div class="form-group">
                                        <select class="js-select2-custom form-control"
                                                name="seller_id">
                                            <option value="all" selected>{{translate('All')}}</option>
                                            <option
                                                value="in_house" {{$seller_is=='in_house'?'selected':''}}>{{translate('In-House')}}
                                            </option>
                                            {{-- @foreach(\App\Models\Seller::where(['status'=>'approved'])->get() as $seller)
                                                <option
                                                    value="{{$seller['id']}}" {{$seller_is==$seller['id']?'selected':''}}>
                                                    {{$seller['f_name']}} {{$seller['l_name']}}
                                                </option>
                                            @endforeach --}}
                                        </select>
                                    </div>
                                </div>

                                <div class="col-6 col-md-2 d-flex justify-content-center">
                                    <button type="submit" class="btn btn-primary btn-block">
                                        {{translate('Filter')}}
                                    </button>

                                    <button class="btn btn-info btn-sm ml-2" id="download_csv" 
                                        type="button">
                                        <i class="fa fa-cloud-download" aria-hidden="true"></i>
                                        Download
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="card-body" id="products-table"
                         style="text-align: {{Session::get('direction') === "rtl" ? 'right' : 'left'}};">
                        <table class="table">
                            <thead>
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">
                                    {{translate('Product Name')}} <label class="badge badge-success ml-3"
                                                                                       style="cursor: pointer">{{translate('ASE/DESC')}}</label>
                                </th>
                                <th scope="col">
                                    {{translate('Total Stock')}} <label class="badge badge-success ml-3"
                                                                                      style="cursor: pointer">{{translate('ASE/DESC')}}</label>
                                </th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($products as $key=>$data)
                                <tr>
                                    <th scope="row">{{$products->firstItem()+$key}}</th>
                                    <td>{{$data['name']}}</td>
                                    <td>{{$data['current_stock']}}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                        <table>
                            <tfoot>
                            {!! $products->links() !!}
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <!-- End Stats -->
    </div>
@endsection

@push('script')

@endpush

@push('script_2')
<script>
    document.getElementById('download_csv').addEventListener('click', function() {
        // Add hidden input to trigger CSV download
        var form = document.getElementById('filter_form');
        var input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'download';
        input.value = 'csv';
        form.appendChild(input);

        // Submit the form
        form.submit();

        form.removeChild(input);
    });
</script>
@endpush
