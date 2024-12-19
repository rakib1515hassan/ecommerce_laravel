@extends('layouts.back-end.app-seller')

@section('title', translate('Add new Product-Manager'))

@push('css_or_js')
@endpush

@section('content')
    <div class="content container-fluid">
        <!-- Page Header -->
        <div class="page-header">
            <div class="row align-items-center">
                <div class="col-sm mb-2 mb-sm-0">
                    <h1 class="page-header-title"><i class="tio-add-circle-outlined"></i> {{ translate('add') }}
                        {{ translate('new') }} {{ translate('Product-Manager') }}
                    </h1>
                </div>
            </div>
        </div>
        <!-- End Page Header -->
        <div class="row gx-2 gx-lg-3">
            <div class="col-sm-12 col-lg-12 mb-3 mb-lg-2">
                <div class="card">
                    <div class="card-body">
                        <form action="{{ route('seller.product_manager.store') }}" method="post"
                              enctype="multipart/form-data">
                            @csrf
                            <div class="row">
                                <div class="col-md-6 col-12">
                                    <div class="form-group">
                                        <label class="input-label"
                                               for="exampleFormControlInput1">{{ translate('first') }}
                                            {{ translate('name') }}</label>
                                        <input type="text" name="f_name" class="form-control"
                                               placeholder="{{ translate('first') }} {{ translate('name') }}" required>
                                    </div>
                                </div>
                                <div class="col-md-6 col-12">
                                    <div class="form-group">
                                        <label class="input-label"
                                               for="exampleFormControlInput1">{{ translate('last') }}
                                            {{ translate('name') }}</label>
                                        <input type="text" name="l_name" class="form-control"
                                               placeholder="{{ translate('last') }} {{ translate('name') }}" required>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 col-12">
                                    <div class="form-group">
                                        <label class="input-label"
                                               for="exampleFormControlInput1">{{ translate('email') }}</label>
                                        <input type="email" name="email" class="form-control"
                                               placeholder="Ex : ex@example.com" required>
                                    </div>
                                </div>
                                <div class="col-md-6 col-12">
                                    <div class="form-group">
                                        <label class="input-label"
                                               for="exampleFormControlInput1">{{ translate('phone') }}</label>
                                        <input type="text" name="phone" class="form-control"
                                               placeholder="Ex : 017********" required>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 col-12">
                                    <div class="form-group">
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
                                </div>
                                <div class="col-md-6 col-12">
                                    <div class="form-group">
                                        <label class="input-label"
                                               for="exampleFormControlInput1">{{ translate('identity') }}
                                            {{ translate('number') }}</label>
                                        <input type="text" name="identity_number" class="form-control"
                                               placeholder="Ex : DH-23434-LS" required>
                                    </div>
                                </div>

                            </div>

                            <div class="row form-group">
                                <div class="col-md-6 col-12">
                                    <label class="input-label"
                                           for="exampleFormControlInput1">{{ translate('password') }}</label>
                                    <input type="password" class="form-control form-control-user" minlength="6"
                                           id="exampleInputPassword" name="password"
                                           placeholder="{{ translate('password') }}"
                                           required>
                                </div>
                                <div class="col-md-6 col-12">
                                    <label class="input-label"
                                           for="exampleFormControlInput1">{{ translate('password') }}</label>
                                    <input type="password" class="form-control form-control-user" minlength="6"
                                           id="exampleRepeatPassword" name="password_confirmation"
                                           placeholder="{{ translate('repeat_password') }}" required>
                                    <div class="pass invalid-feedback">{{ translate('Repeat') }}
                                        {{ translate('password') }} {{ translate('not match') }}
                                        .
                                    </div>
                                </div>
                            </div>


                            <div class="form-group">
                                <label>{{ translate('Product-Manager') }} {{ translate('image') }}</label><small
                                    style="color: red">* ( {{ translate('ratio') }} 1:1 )</small>
                                <div class="custom-file">
                                    <input type="file" name="image" id="customFileEg1" class="custom-file-input"
                                           accept=".jpg, .png, .jpeg, .gif, .bmp, .tif, .tiff|image/*" required>
                                    <label class="custom-file-label" for="customFileEg1">{{ translate('choose') }}
                                        {{ translate('file') }}</label>
                                </div>
                                <hr>
                                <center>
                                    <img style="height: 200px;border: 1px solid; border-radius: 10px;" id="viewer"
                                         src="{{ asset('assets/back-end/img/900x400/img1.jpg') }}"
                                         alt="product_manager image"/>
                                </center>
                            </div>
                            <hr>
                            <button type="submit" id="apply" class="btn btn-primary">{{ translate('submit') }}</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('script_2')
    <script>
        function readURL(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();

                reader.onload = function (e) {
                    $('#viewer').attr('src', e.target.result);
                }

                reader.readAsDataURL(input.files[0]);
            }
        }

        $("#customFileEg1").change(function () {
            readURL(this);
        });
    </script>


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

            var image = $("#image-set").val();
            if (image == "") {
                $('.image').show();
                return false;
            }
            var pass = $("#exampleInputPassword").val();
            var passRepeat = $("#exampleRepeatPassword").val();
            if (pass != passRepeat) {
                $('.pass').show();
                return false;
            }
        });
    </script>

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
@endpush
