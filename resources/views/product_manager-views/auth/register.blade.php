@extends('layouts.front-end.app')

@section('title', translate('Product Manager Apply'))

@push('css_or_js')
    <link href="{{ asset('assets/back-end/css/croppie.css') }}" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endpush



@section('content')

    <div class="container main-card rtl"
         style="text-align: {{ Session::get('direction') === 'rtl' ? 'right' : 'left' }};">

        <div class="card o-hidden border-0 shadow-lg my-4">
            <div class="card-body ">
                <!-- Nested Row within Card Body -->
                <div class="row">
                    <div class="col-lg-12">
                        <div class="p-5">
                            <div class="text-center mb-2 ">
                                <h3 class=""> {{ translate('Product Manager') }} {{ translate('Application') }}</h3>
                                <hr>
                            </div>
                            <form id="myForm" class="user" action="{{ route('product_manager.auth.apply') }}"
                                  method="post"
                                  enctype="multipart/form-data">
                                @csrf
                                <h5 class="black">{{ translate('Product Manager') }} {{ translate('Info') }} </h5>

                                <div class="row form-group">
                                    <div class="col-sm-6 mb-3 mb-sm-0">
                                        <label class="input-label"
                                               for="exampleFormControlInput1">{{ translate('first') }}
                                            {{ translate('name') }}</label>
                                        <input type="text" name="f_name" class="form-control"
                                               placeholder="{{ translate('first') }} {{ translate('name') }}" required>
                                    </div>
                                    <div class="col-sm-6 mb-3 mb-sm-0">
                                        <label class="input-label"
                                               for="exampleFormControlInput1">{{ translate('last') }}
                                            {{ translate('name') }}</label>
                                        <input type="text" name="l_name" class="form-control"
                                               placeholder="{{ translate('last') }} {{ translate('name') }}" required>
                                    </div>
                                </div>

                                <div class="row form-group">
                                    <div class="col-sm-6 mb-3 mb-sm-0">
                                        <label class="input-label"
                                               for="exampleFormControlInput1">{{ translate('email') }}</label>
                                        <input type="email" name="email" class="form-control"
                                               placeholder="Ex : ex@example.com" required>
                                    </div>
                                    <div class="col-sm-6 mb-3 mb-sm-0">
                                        <label class="input-label"
                                               for="exampleFormControlInput1">{{ translate('phone') }}</label>
                                        <input type="text" name="phone" class="form-control"
                                               placeholder="Ex : 017********" required>
                                    </div>
                                </div>

                                <div class="row form-group">
                                    <div class="col-sm-6 mb-3 mb-sm-0">
                                        <label class="input-label"
                                               for="exampleFormControlInput1">{{ translate('identity') }}
                                            {{ translate('type') }}</label>
                                        <select name="identity_type" class="form-control">
                                            <option value="passport">{{ translate('passport') }}</option>
                                            <option value="driving_license">{{ translate('driving') }}
                                                {{ translate('license') }}</option>
                                            <option value="nid">{{ translate('nid') }}</option>
                                            <option value="company_id">{{ translate('company') }} {{ translate('id') }}
                                            </option>
                                        </select>
                                    </div>
                                    <div class="col-sm-6 mb-3 mb-sm-0">
                                        <label class="input-label"
                                               for="exampleFormControlInput1">{{ translate('identity') }}
                                            {{ translate('number') }}</label>
                                        <input type="text" name="identity_number" class="form-control"
                                               placeholder="Ex : DH-23434-LS" required>
                                    </div>
                                </div>

                                <div class="row form-group">
                                    <div class="col-sm-6 mb-3 mb-sm-0">
                                        <label>{{ translate('Product Manager') }} {{ translate('image') }}</label><small
                                                style="color: red">* ( {{ translate('ratio') }} 1:1 )</small>
                                        <div class="custom-file">
                                            <input type="file" class="form-control" name="image" id="image"
                                                   placeholder="Image">
                                        </div>
                                    </div>
                                    {{-- <div class="col-sm-6 mb-3 mb-sm-0">
                                        <label for="name">{{ translate('Seller') }}</label>
                                        <select
                                            class="js-example-basic-multiple js-states js-example-responsive form-control"
                                            name="seller_id[]" id="seller_id" multiple>
                                            <option value="{{ null }}" selected disabled>
                                                ---{{ translate('Select') }}---
                                            </option>
                                            @foreach ($sellers as $seller)
                                                <option value="{{ $seller['id'] }}">{{ $seller['f_name'] }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div> --}}
                                    <div class="col-sm-6 mb-3 mb-sm-0">
                                        <label for="name">{{ translate('Seller') }}</label>
                                        <select
                                                class="js-example-basic-multiple js-states js-example-responsive form-control select2"
                                                data-placeholder="{{ translate('Choose Seller') }}"
                                                name="seller_id" id="seller_id" required>
                                            @foreach ($sellers as $seller)
                                                <option value="{{ $seller['id'] }}">{{ $seller['f_name']  }} {{ $seller['l_name'] }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="row form-group">
                                    <div class="col-sm-6 mb-3 mb-sm-0">
                                        <label class="input-label"
                                               for="exampleFormControlInput1">{{ translate('password') }}</label>
                                        <input type="password" class="form-control form-control-user" minlength="6"
                                               id="exampleInputPassword" name="password"
                                               placeholder="{{ translate('password') }}" required>
                                    </div>
                                    <div class="col-sm-6 mb-3 mb-sm-0">
                                        <label class="input-label"
                                               for="exampleFormControlInput1">{{ translate('Confirm_password') }}</label>
                                        <input type="password" class="form-control form-control-user" minlength="6"
                                               id="exampleRepeatPassword" name="password_confirmation"
                                               placeholder="{{ translate('repeat_password') }}" required>
                                        <div class="pass invalid-feedback">{{ translate('Repeat') }}
                                            {{ translate('password') }} {{ translate('not match') }}
                                            .
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <button type="submit" class="btn btn-primary btn-user btn-block"
                                            id="apply">{{ translate('Apply') }} {{ translate('Product Manager') }}
                                    </button>
                                </div>
                            </form>
                            <hr>
                            <div class="text-center">
                                <a class="small"
                                   href="{{ route('product_manager.auth.login') }}">{{ translate('already_have_an_account?_login.') }}</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
    @if ($errors->any())
        <script>
            @foreach ($errors->all() as $error)
            toastr.error('{{ $error }}', Error, {
                CloseButton: true,
                ProgressBar: true
            });
            @endforeach
        </script>
    @endif
    <script>
        $('#apply').on('click', function () {

            var pass = $("#exampleInputPassword").val();
            var passRepeat = $("#exampleRepeatPassword").val();
            if (pass !== passRepeat) {
                // $('.pass').show();
                // return false;
                // $('#passwordError').text('Passwords do not match');
                console.log('Passwords do not match');
            }
        });
    </script>

    <script>
        $(document).ready(function () {
            $('#seller_id').select2({
                placeholder: "Select Seller",
                style: 'height: 100px',
            });
        });
    </script>
@endpush
