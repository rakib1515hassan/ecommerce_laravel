@extends('layouts.back-end.app')
@section('title', translate('Create Role'))
@push('css_or_js')
    <!-- Custom styles for this page -->
    <link href="{{asset('assets/back-end')}}/vendor/datatables/dataTables.bootstrap4.min.css" rel="stylesheet">
@endpush

@section('content')
    <div class="content container-fluid">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a
                            href="{{route('admin.dashboard')}}">{{translate('Dashboard')}}</a>
                </li>
                <li class="breadcrumb-item" aria-current="page">{{translate('custom_role')}}</li>
            </ol>
        </nav>

        <!-- Content Row -->
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        {{translate('role_form')}}
                    </div>
                    <div class="card-body">
                        <form action="{{route('admin.custom-role.create')}}" method="post"
                              style="text-align: {{Session::get('direction') === "rtl" ? 'right' : 'left'}};">
                            @csrf
                            <div class="form-group">
                                <label for="name">{{translate('role_name')}}</label>
                                <input type="text" name="name" class="form-control" id="name"
                                       aria-describedby="emailHelp"
                                       placeholder="{{translate('Ex')}} : {{translate('Store')}}"
                                       required>
                            </div>

                            <label for="name">{{translate('module_permission')}} : </label>
                            <hr>
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group form-check">
                                        <input type="checkbox" name="modules[]" value="order_management"
                                               class="form-check-input"
                                               id="order">
                                        <label class="form-check-label"
                                               style="{{Session::get('direction') === "rtl" ? 'margin-right: 1.25rem;' : ''}};"
                                               for="order">{{translate('Order_Management')}}</label>
                                    </div>
                                </div>
                                <!--order end-->

                                <div class="col-md-3">
                                    <div class="form-group form-check">
                                        <input type="checkbox" name="modules[]" value="product_management"
                                               class="form-check-input"
                                               id="product">
                                        <label class="form-check-label"
                                               style="{{Session::get('direction') === "rtl" ? 'margin-right: 1.25rem;' : ''}};"
                                               for="product">{{translate('Product_Management')}}</label>
                                    </div>
                                </div>
                                <!--product-->

                                <div class="col-md-3">
                                    <div class="form-group form-check">
                                        <input type="checkbox" name="modules[]" value="marketing_section"
                                               class="form-check-input"
                                               id="marketing">
                                        <label class="form-check-label"
                                               style="{{Session::get('direction') === "rtl" ? 'margin-right: 1.25rem;' : ''}};"
                                               for="marketing">{{translate('Marketing_Section')}}</label>
                                    </div>
                                </div>
                                <!--marketing-->

                                <div class="col-md-3">
                                    <div class="form-group form-check">
                                        <input type="checkbox" name="modules[]" value="business_section"
                                               class="form-check-input"
                                               id="business_section">
                                        <label class="form-check-label"
                                               style="{{Session::get('direction') === "rtl" ? 'margin-right: 1.25rem;' : ''}};"
                                               for="business_section">{{translate('Business_Section')}}</label>
                                    </div>
                                </div>
                                <!--business_settings-->
                            </div>

                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group form-check">
                                        <input type="checkbox" name="modules[]" value="user_section"
                                               class="form-check-input"
                                               id="user_section">
                                        <label class="form-check-label"
                                               style="{{Session::get('direction') === "rtl" ? 'margin-right: 1.25rem;' : ''}};"
                                               for="user_section">{{translate('user_Section')}}</label>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group form-check">
                                        <input type="checkbox" name="modules[]" value="support_section"
                                               class="form-check-input"
                                               id="support_section">
                                        <label class="form-check-label"
                                               style="{{Session::get('direction') === "rtl" ? 'margin-right: 1.25rem;' : ''}};"
                                               for="support_section">{{translate('Support_Section')}}</label>
                                    </div>
                                </div>
                                {{--                                <div class="col-md-3">--}}
                                {{--                                    <div class="form-group form-check">--}}
                                {{--                                        <input type="checkbox" name="modules[]" value="business_settings"--}}
                                {{--                                               class="form-check-input"--}}
                                {{--                                               id="business_settings">--}}
                                {{--                                        <label class="form-check-label"--}}
                                {{--                                               style="{{Session::get('direction') === "rtl" ? 'margin-right: 1.25rem;' : ''}};"--}}
                                {{--                                               for="business_settings">{{translate('Business_Settings')}}</label>--}}
                                {{--                                    </div>--}}
                                {{--                                </div>--}}
                                {{--                                <div class="col-md-3">--}}
                                {{--                                    <div class="form-group form-check">--}}
                                {{--                                        <input type="checkbox" name="modules[]" value="web_&_app_settings"--}}
                                {{--                                               class="form-check-input"--}}
                                {{--                                               id="web_&_app_settings">--}}
                                {{--                                        <label class="form-check-label"--}}
                                {{--                                               style="{{Session::get('direction') === "rtl" ? 'margin-right: 1.25rem;' : ''}};"--}}
                                {{--                                               for="web_&_app_settings">{{translate('Web_&_App_Settings')}}</label>--}}
                                {{--                                    </div>--}}
                                {{--                                </div>--}}

                                <div class="col-md-3">
                                    <div class="form-group form-check">
                                        <input type="checkbox" name="modules[]" value="report" class="form-check-input"
                                               id="report">
                                        <label class="form-check-label"
                                               style="{{Session::get('direction') === "rtl" ? 'margin-right: 1.25rem;' : ''}};"
                                               for="report">{{translate('Report_&_Analytics')}}</label>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group form-check">
                                        <input type="checkbox" name="modules[]" value="employee_section"
                                               class="form-check-input"
                                               id="employee_section">
                                        <label class="form-check-label"
                                               style="{{Session::get('direction') === "rtl" ? 'margin-right: 1.25rem;' : ''}};"
                                               for="employee_section">{{translate('Employee_Section')}}</label>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group form-check">
                                        <input type="checkbox" name="modules[]" value="blog_management"
                                               class="form-check-input"
                                               id="blog_management">
                                        <label class="form-check-label"
                                               style="{{Session::get('direction') === "rtl" ? 'margin-right: 1.25rem;' : ''}};"
                                               for="blog_management">{{translate('blog_management')}}</label>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group form-check">
                                        <input type="checkbox" name="modules[]" value="dashboard"
                                               class="form-check-input"
                                               id="dashboard">
                                        <label class="form-check-label"
                                               style="{{Session::get('direction') === "rtl" ? 'margin-right: 1.25rem;' : ''}};"
                                               for="dashboard">{{translate('Dashboard')}}</label>
                                    </div>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary">{{translate('Submit')}}</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="row" style="margin-top: 20px">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h5>{{translate('roles_table')}}</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0"
                                   style="text-align: {{Session::get('direction') === "rtl" ? 'right' : 'left'}};">
                                <thead>
                                <tr>
                                    <th scope="col">{{translate('SL')}}#</th>
                                    <th scope="col">{{translate('role_name')}}</th>
                                    <th scope="col">{{translate('modules')}}</th>
                                    <th scope="col">{{translate('created_at')}}</th>
                                    <th scope="col">{{translate('status')}}</th>
                                    <th scope="col" style="width: 50px">{{translate('action')}}</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($rl as $k=>$r)
                                    <tr>
                                        <th scope="row">{{$k+1}}</th>
                                        <td>{{$r['name']}}</td>
                                        <td class="text-capitalize">
                                            @if($r['module_access']!=null)
                                                @foreach((array)json_decode($r['module_access']) as $m)
                                                    {{str_replace('_',' ',$m)}} <br>
                                                @endforeach
                                            @endif
                                        </td>
                                        <td>{{date('d-M-y',strtotime($r['created_at']))}}</td>
                                        <td>{{\App\Services\AdditionalServices::status($r['status'])}}</td>
                                        <td>
                                            <a href="{{route('admin.custom-role.update',[$r['id']])}}"
                                               class="btn btn-primary btn-sm">
                                                {{translate('Edit') }}
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <!-- Page level plugins -->
    <script src="{{asset('assets/back-end')}}/vendor/datatables/jquery.dataTables.min.js"></script>
    <script src="{{asset('assets/back-end')}}/vendor/datatables/dataTables.bootstrap4.min.js"></script>
    <!-- Page level custom scripts -->
    <script>
        $(document).ready(function () {
            $('#dataTable').DataTable();
        });
    </script>
@endpush
