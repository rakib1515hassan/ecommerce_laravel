@extends('layouts.back-end.app')
@section('title', translate('Mail Config'))
@push('css_or_js')

@endpush

@section('content')
    <div class="content container-fluid">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a
                        href="{{route('admin.dashboard')}}">{{translate('Dashboard')}}</a>
                </li>
                <li class="breadcrumb-item" aria-current="page">{{translate('mail_config')}}</li>
            </ol>
        </nav>

        <div class="row"
             style="padding-bottom: 20px; text-align: {{Session::get('direction') === "rtl" ? 'right' : 'left'}};">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body" style="padding: 20px">

                        @php($config=\App\Models\BusinessSetting::where(['type'=>'mail_config'])->first())
                        @php($data=json_decode($config['value'],true))
                        <form action="{{route('admin.business-settings.mail.update')}}"
                              method="post">
                            @csrf
                            @if(isset($config))
                                <div class="form-group mb-2 text-center">
                                    <label class="control-label">{{translate('smtp_mail_config')}}</label>
                                </div>
                                <div class="form-group mb-2">
                                    <label style="padding-left: 10px">{{translate('mailer_name')}}</label><br>
                                    <input type="text" placeholder="ex : Alex" class="form-control" name="name"
                                           value="{{$data['name']}}">
                                </div>

                                <div class="form-group mb-2">
                                    <label style="padding-left: 10px">{{translate('Host')}}</label><br>
                                    <input type="text" class="form-control" name="host"
                                           value="{{$data['host']}}">
                                </div>
                                <div class="form-group mb-2">
                                    <label style="padding-left: 10px">{{translate('Driver')}}</label><br>
                                    <input type="text" class="form-control" name="driver"
                                           value="{{$data['driver']}}">
                                </div>
                                <div class="form-group mb-2">
                                    <label style="padding-left: 10px">{{translate('port')}}</label><br>
                                    <input type="text" class="form-control" name="port"
                                           value="{{$data['port']}}">
                                </div>

                                <div class="form-group mb-2">
                                    <label
                                        style="padding-left: 10px">{{translate('Username')}}</label><br>
                                    <input type="text" placeholder="ex : ex@yahoo.com" class="form-control"
                                           name="username" value="{{$data['username']}}">
                                </div>

                                <div class="form-group mb-2">
                                    <label
                                        style="padding-left: 10px">{{translate('email_id')}}</label><br>
                                    <input type="text" placeholder="ex : ex@yahoo.com" class="form-control" name="email"
                                           value="{{$data['email_id']}}">
                                </div>

                                <div class="form-group mb-2">
                                    <label
                                        style="padding-left: 10px">{{translate('Encryption')}}</label><br>
                                    <input type="text" placeholder="ex : tls" class="form-control" name="encryption"
                                           value="{{$data['encryption']}}">
                                </div>

                                <div class="form-group mb-2">
                                    <label
                                        style="padding-left: 10px">{{translate('password')}}</label><br>
                                    <input type="text" class="form-control" name="password"
                                           value="{{$data['password']}}">
                                </div>
                                <button type="submit"
                                        class="btn btn-primary mb-2">{{translate('save')}}</button>
                            @else
                                <button type="submit"
                                        class="btn btn-primary mb-2">{{translate('Configure')}}</button>
                            @endif
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')

@endpush
