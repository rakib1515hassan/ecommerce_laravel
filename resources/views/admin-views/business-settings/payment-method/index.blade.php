@extends('layouts.back-end.app')

@section('title', translate('Payment Method'))

@push('css_or_js')

@endpush

@section('content')
    <div class="content container-fluid">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a
                        href="{{route('admin.dashboard')}}">{{translate('Dashboard')}}</a></li>
                <li class="breadcrumb-item" aria-current="page">{{translate('payment_method')}}</li>
            </ol>
        </nav>

        <div class="row" style="padding-bottom: 20px">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body" style="padding: 20px">
                        <h5 class="text-center">{{translate('system_default')}} {{translate('payment_method')}}</h5>
                        @php($config=\App\Services\AdditionalServices::get_business_settings('cash_on_delivery'))
                        <form action="{{route('admin.business-settings.payment-method.update',['cash_on_delivery'])}}"
                              style="text-align: {{Session::get('direction') === "rtl" ? 'right' : 'left'}};"
                              method="post">
                            @csrf
                            @if(isset($config))
                                <div class="form-group mb-2">
                                    <label class="control-label">{{translate('cash_on_delivery')}}</label>
                                </div>
                                <div class="form-group mb-2 mt-2">
                                    <input type="radio" name="status" value="1" {{$config['status']==1?'checked':''}}>
                                    <label
                                        style="padding-{{Session::get('direction') === "rtl" ? 'right' : 'left'}}: 10px">{{translate('Active')}}</label>
                                    <br>
                                </div>
                                <div class="form-group mb-2">
                                    <input type="radio" name="status" value="0" {{$config['status']==0?'checked':''}}>
                                    <label
                                        style="padding-{{Session::get('direction') === "rtl" ? 'right' : 'left'}}: 10px">{{translate('Inactive')}}</label>
                                    <br>
                                </div>
                                <button type="button"

                                        class="btn btn-primary mb-2">{{translate('save')}}</button>
                            @else
                                <button type="submit"
                                        class="btn btn-primary mb-2">{{translate('Configure')}}</button>
                            @endif
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card">
                    <div class="card-body" style="padding: 20px">
                        <h5 class="text-center">{{translate('digital_payment')}}</h5>
                        @php($config=\App\Services\AdditionalServices::get_business_settings('digital_payment'))
                        <form action="{{route('admin.business-settings.payment-method.update',['digital_payment'])}}"
                              style="text-align: {{Session::get('direction') === "rtl" ? 'right' : 'left'}};"
                              method="post">
                            @csrf
                            @if(isset($config))
                                <div class="form-group mb-2">
                                    <label class="control-label">{{translate('digital_payment')}}</label>
                                </div>
                                <div class="form-group mb-2 mt-2">
                                    <input type="radio" name="status" value="1" {{$config['status']==1?'checked':''}}>
                                    <label
                                        style="padding-{{Session::get('direction') === "rtl" ? 'right' : 'left'}}: 10px">{{translate('Active')}}</label>
                                    <br>
                                </div>
                                <div class="form-group mb-2">
                                    <input type="radio" name="status" value="0" {{$config['status']==0?'checked':''}}>
                                    <label
                                        style="padding-{{Session::get('direction') === "rtl" ? 'right' : 'left'}}: 10px">{{translate('Inactive')}}</label>
                                    <br>
                                </div>
                                <button type="button"

                                        class="btn btn-primary mb-2">{{translate('save')}}</button>
                            @else
                                <button type="submit"
                                        class="btn btn-primary mb-2">{{translate('Configure')}}</button>
                            @endif
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-md-6 mt-4">
                <div class="card">
                    <div class="card-body" style="padding: 20px">
                        <h5 class="text-center">{{translate('SSLCOMMERZ')}}</h5>
                        @php($config=\App\Services\AdditionalServices::get_business_settings('ssl_commerz_payment'))
                        <form
                            action="{{route('admin.business-settings.payment-method.update',['ssl_commerz_payment'])}}"
                            method="post">
                            @csrf
                            @if(isset($config))
                                <div class="form-group mb-2">
                                    <label
                                        class="control-label">{{translate('ssl_commerz_payment')}}</label>
                                </div>
                                <div class="form-group mb-2 mt-2">
                                    <input type="radio" name="status" value="1" {{$config['status']==1?'checked':''}}>
                                    <label style="padding-left: 10px">{{translate('Active')}}</label>
                                    <br>
                                </div>
                                <div class="form-group mb-2">
                                    <input type="radio" name="status" value="0" {{$config['status']==0?'checked':''}}>
                                    <label style="padding-left: 10px">{{translate('Inactive')}}</label>
                                    <br>
                                </div>
                                <div class="form-group mb-2">
                                    <label
                                        style="padding-left: 10px">{{translate('Store')}} {{translate('ID')}} </label><br>
                                    <input type="text" class="form-control" name="store_id"
                                           value="{{$config['store_id']}}">
                                </div>
                                <div class="form-group mb-2">
                                    <label
                                        style="padding-left: 10px">{{translate('Store')}} {{translate('password')}}</label><br>
                                    <input type="text" class="form-control" name="store_password"
                                           value="{{$config['store_password']}}">
                                </div>
                                <button type="button"

                                        class="btn btn-primary mb-2">{{translate('save')}}</button>
                            @else
                                <button type="submit"
                                        class="btn btn-primary mb-2">{{translate('Configure')}}</button>
                            @endif
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-md-6 mt-4">
                <div class="card">
                    <div class="card-body" style="padding: 20px">
                        <h5 class="text-center">{{translate('bkash')}}</h5>
                        @php($config=\App\Services\AdditionalServices::get_business_settings('bkash'))
                        <form
                            action="{{env('APP_MODE')!='dev'?route('admin.business-settings.payment-method.update',['bkash']):'javascript:'}}"
                            method="post">
                            @csrf
                            @if(isset($config))
                                <div class="form-group mb-2">
                                    <label class="control-label">{{translate('bkash')}}</label>
                                </div>

                                <div class="form-group mb-2 mt-2">
                                    <input type="radio" name="status" value="1" {{$config['status']==1?'checked':''}}>
                                    <label style="padding-left: 10px">{{translate('Active')}}</label>
                                    <br>
                                </div>

                                <div class="form-group mb-2">
                                    <input type="radio" name="status" value="0" {{$config['status']==0?'checked':''}}>
                                    <label style="padding-left: 10px">{{translate('Inactive')}} </label>
                                    <br>
                                </div>

                                <div class="form-group mb-2">
                                    <label style="padding-left: 10px">{{translate('api_key')}}</label><br>
                                    <input type="text" class="form-control" name="api_key"
                                           value="{{env('APP_MODE')!='dev'?$config['api_key']:''}}">
                                </div>

                                <div class="form-group mb-2">
                                    <label
                                        style="padding-left: 10px">{{translate('api_secret')}}</label><br>
                                    <input type="text" class="form-control" name="api_secret"
                                           value="{{env('APP_MODE')!='dev'?$config['api_secret']:''}}">
                                </div>

                                <div class="form-group mb-2">
                                    <label
                                        style="padding-left: 10px">{{translate('username')}}</label><br>
                                    <input type="text" class="form-control" name="username"
                                           value="{{env('APP_MODE')!='dev'?$config['username']:''}}">
                                </div>

                                <div class="form-group mb-2">
                                    <label
                                        style="padding-left: 10px">{{translate('password')}}</label><br>
                                    <input type="text" class="form-control" name="password"
                                           value="{{env('APP_MODE')!='dev'?$config['password']:''}}">
                                </div>


                                <button type="button"

                                        class="btn btn-primary mb-2">{{translate('save')}}</button>
                            @else
                                <button type="submit"
                                        class="btn btn-primary mb-2">{{translate('configure')}}</button>
                            @endif
                        </form>
                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection

@push('script')
    <script>
        function copyToClipboard(element) {
            var $temp = $("<input>");
            $("body").append($temp);
            $temp.val($(element).text()).select();
            document.execCommand("copy");
            $temp.remove();
            toastr.success("{{translate('Copied to the clipboard')}}");
        }
    </script>
@endpush
