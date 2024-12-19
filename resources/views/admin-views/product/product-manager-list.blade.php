@extends('layouts.back-end.app')
@section('title', translate('Product Manager List'))
@push('css_or_js')

@endpush

@section('content')
    <div class="content container-fluid">  <!-- Page Heading -->
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a
                        href="{{route('admin.dashboard')}}">{{translate('Dashboard')}}</a></li>
                <li class="breadcrumb-item" aria-current="page">{{translate('Product Managers')}}</li>
            </ol>
        </nav>

        <div class="d-md-flex_ align-items-center justify-content-between mb-0">
            <div class="row text-center">
                <div class="col-12">
                    <h3 class="h3 mt-2 text-black-50">{{translate('product_manager_list')}}</h3>
                </div>
            </div>
        </div>

        <div class="row" style="margin-top: 20px">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h5>{{translate('product_manager_table')}}</h5>
                    </div>
                    <div class="card-body" style="padding: 0">
                        <div class="table-responsive">
                            <table id="datatable"
                                   style="text-align: {{ Session::get('direction') === 'rtl' ? 'right' : 'left' }};"
                                   class="table table-hover table-borderless table-thead-bordered table-nowrap table-align-middle card-table"
                                   style="width: 100%">
                                <thead class="thead-light">
                                <tr>
                                    <th>{{ translate('SL#') }}</th>
                                    <th>{{ translate('First Name') }}</th>
                                    <th>{{ translate('Last Name') }}</th>
                                    <th>{{ translate('Phone') }}</th>
                                    <th>{{ translate('Seller') }}</th>
                                    <th>{{ translate('status') }}</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach ($product_managers as $k => $pm)
                                    <tr>
                                        <th scope="row">{{ $k + 1 }}</th>
                                        <td>{{ $pm->f_name }}</td>
                                        <td>{{ $pm->l_name }}</td>
                                        <td>{{ $pm->phone }}</td>
                                        <td>{{$pm->seller->f_name}} {{$pm->seller->l_name}}</td>

                                        <td>
                                            <label class="switch">
                                                <input type="checkbox" class="status" id="{{ $pm['id'] }}"
                                                    {{ $pm->is_active == 1 ? 'checked' : '' }}>
                                                <span class="slider round"></span>
                                            </label>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="card-footer">
                        {{$product_managers->links()}}
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
                url: "{{route('admin.product.product-manager-status')}}",
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
