@extends('layouts.back-end.app')
@section('title', translate('Edit Role'))
@push('css_or_js')

@endpush

@section('content')
    <div class="content container-fluid">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a
                        href="{{route('admin.dashboard')}}">{{translate('Dashboard')}}</a></li>
                <li class="breadcrumb-item" aria-current="page">{{translate('Role Update')}}</li>
            </ol>
        </nav>

        <!-- Content Row -->
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <form action="{{route('admin.custom-role.update',[$role['id']])}}" method="post"
                              style="text-align: {{Session::get('direction') === "rtl" ? 'right' : 'left'}};">
                            @csrf
                            <div class="form-group">
                                <label for="name">{{translate('role_name')}}</label>
                                <input type="text" name="name" value="{{$role['name']}}" class="form-control" id="name"
                                       aria-describedby="emailHelp"
                                       placeholder="{{translate('Ex')}} : {{translate('Store')}}">
                            </div>

                            <label for="module">{{translate('module_permission')}} : </label>
                            <hr>
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group form-check">
                                        <input type="checkbox" name="modules[]" value="order_management"
                                               class="form-check-input"
                                               id="order" {{in_array('order_management',(array)json_decode($role['module_access']))?'checked':''}}>
                                        <label class="form-check-label"
                                               style="{{Session::get('direction') === "rtl" ? 'margin-right: 1.25rem;' : ''}};"
                                               style="{{Session::get('direction') === "rtl" ? 'margin-right: 1.25rem;' : ''}};"
                                               for="order">{{translate('Order_Management')}}</label>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group form-check">
                                        <input type="checkbox" name="modules[]" value="product_management"
                                               class="form-check-input"
                                               id="product" {{in_array('product_management',(array)json_decode($role['module_access']))?'checked':''}}>
                                        <label class="form-check-label"
                                               style="{{Session::get('direction') === "rtl" ? 'margin-right: 1.25rem;' : ''}};"
                                               for="product">{{translate('Product_Management')}}</label>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group form-check">
                                        <input type="checkbox" name="modules[]" value="marketing_section"
                                               class="form-check-input"
                                               id="marketing" {{in_array('marketing_section',(array)json_decode($role['module_access']))?'checked':''}}>
                                        <label class="form-check-label"
                                               style="{{Session::get('direction') === "rtl" ? 'margin-right: 1.25rem;' : ''}};"
                                               for="marketing">{{translate('Marketing_Section')}}</label>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group form-check">
                                        <input type="checkbox" name="modules[]" value="business_section"
                                               class="form-check-input"
                                               id="business_section" {{in_array('business_section',(array)json_decode($role['module_access']))?'checked':''}}>
                                        <label class="form-check-label"
                                               style="{{Session::get('direction') === "rtl" ? 'margin-right: 1.25rem;' : ''}};"
                                               for="business_section">{{translate('Business_Section')}}</label>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group form-check">
                                        <input type="checkbox" name="modules[]" value="user_section"
                                               class="form-check-input"
                                               id="user_section" {{in_array('user_section',(array)json_decode($role['module_access']))?'checked':''}}>
                                        <label class="form-check-label"
                                               style="{{Session::get('direction') === "rtl" ? 'margin-right: 1.25rem;' : ''}};"
                                               for="user_section">{{translate('user_section')}}</label>
                                    </div>
                                </div>
                                {{--                                <div class="col-md-3">--}}
                                {{--                                    <div class="form-group form-check">--}}
                                {{--                                        <input type="checkbox" name="modules[]" value="support_section"--}}
                                {{--                                               class="form-check-input"--}}
                                {{--                                               id="support_section" {{in_array('support_section',(array)json_decode($role['module_access']))?'checked':''}}>--}}
                                {{--                                        <label class="form-check-label"--}}
                                {{--                                               style="{{Session::get('direction') === "rtl" ? 'margin-right: 1.25rem;' : ''}};"--}}
                                {{--                                               for="support_section">{{translate('Support_Section')}}</label>--}}
                                {{--                                    </div>--}}
                                {{--                                </div>--}}
                                <div class="col-md-3">
                                    <div class="form-group form-check">
                                        <input type="checkbox" name="modules[]" value="business_settings"
                                               class="form-check-input"
                                               id="business_settings" {{in_array('business_settings',(array)json_decode($role['module_access']))?'checked':''}}>
                                        <label class="form-check-label"
                                               style="{{Session::get('direction') === "rtl" ? 'margin-right: 1.25rem;' : ''}};"
                                               for="business_settings">{{translate('Business_Settings')}}</label>
                                    </div>
                                </div>

                                {{--                                <div class="col-md-3">--}}
                                {{--                                    <div class="form-group form-check">--}}
                                {{--                                        <input type="checkbox" name="modules[]" value="web_&_app_settings"--}}
                                {{--                                               class="form-check-input"--}}
                                {{--                                               id="web_&_app_settings" {{in_array('web_&_app_settings',(array)json_decode($role['module_access']))?'checked':''}}>--}}
                                {{--                                        <label class="form-check-label"--}}
                                {{--                                               style="{{Session::get('direction') === "rtl" ? 'margin-right: 1.25rem;' : ''}};"--}}
                                {{--                                               for="web_&_app_settings">{{translate('Web_&_App_Settings')}}</label>--}}
                                {{--                                    </div>--}}
                                {{--                                </div>--}}

                                <div class="col-md-3">
                                    <div class="form-group form-check">
                                        <input type="checkbox" name="modules[]" value="report" class="form-check-input"
                                               id="report" {{in_array('report',(array)json_decode($role['module_access']))?'checked':''}}>
                                        <label class="form-check-label"
                                               style="{{Session::get('direction') === "rtl" ? 'margin-right: 1.25rem;' : ''}};"
                                               for="report">{{translate('Report_&_Analytics')}}</label>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group form-check">
                                        <input type="checkbox" name="modules[]" value="employee_section"
                                               class="form-check-input"
                                               id="employee_section" {{in_array('employee_section',(array)json_decode($role['module_access']))?'checked':''}}>
                                        <label class="form-check-label"
                                               style="{{Session::get('direction') === "rtl" ? 'margin-right: 1.25rem;' : ''}};"
                                               for="employee_section">{{translate('employee_section')}}</label>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group form-check">
                                        <input type="checkbox" name="modules[]" value="blog_management"
                                               class="form-check-input"
                                               id="blog_management" {{in_array('blog_management',(array)json_decode($role['module_access']))?'checked':''}}>
                                        <label class="form-check-label"
                                               style="{{Session::get('direction') === "rtl" ? 'margin-right: 1.25rem;' : ''}};"
                                               for="blog_management">{{translate('blog_management')}}</label>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group form-check">
                                        <input type="checkbox" name="modules[]" value="dashboard"
                                               class="form-check-input"
                                               id="dashboard" {{in_array('dashboard',(array)json_decode($role['module_access']))?'checked':''}}>
                                        <label class="form-check-label"
                                               style="{{Session::get('direction') === "rtl" ? 'margin-right: 1.25rem;' : ''}};"
                                               for="dashboard">{{translate('Dashboard')}}</label>
                                    </div>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary">{{translate('update')}}</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')

@endpush
