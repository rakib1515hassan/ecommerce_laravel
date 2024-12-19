@extends('layouts.back-end.app')
@section('title', translate('Seller Add'))

@push('css_or_js')

    <link href="{{asset('assets/back-end/css/croppie.css')}}" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- Custom styles for this page -->
    <link href="{{asset('assets/back-end')}}/vendor/datatables/dataTables.bootstrap4.min.css" rel="stylesheet">
@endpush

@section('content')
    <div class="content container-fluid">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a
                            href="{{route('admin.dashboard')}}">{{translate('Dashboard')}}</a></li>
                <li class="breadcrumb-item" aria-current="page">{{translate('seller')}}</li>
            </ol>
        </nav>

        <!-- Content Row -->
        <div class="row">
            <div class="col-lg-12">
                <div class="p-5">
                    <div class="text-center mb-2 ">
                        <h3 class=""> {{ translate('Shop') }} {{ translate('Application') }}</h3>
                        <hr>
                    </div>
                    <form class="user" action="{{ route('admin.sellers.store') }}" method="post"
                        enctype="multipart/form-data">
                        @csrf
                        <h5 class="black">{{ translate('Seller') }} {{ translate('Info') }} </h5>
                        <div class="form-group row">
                            <div class="col-sm-6 mb-3 mb-sm-0">
                                <input type="text" class="form-control form-control-user" id="exampleFirstName"
                                    name="f_name" value="{{ old('f_name') }}"
                                    placeholder="{{ translate('first_name') }}" required>
                            </div>
                            <div class="col-sm-6">
                                <input type="text" class="form-control form-control-user" id="exampleLastName"
                                    name="l_name" value="{{ old('l_name') }}"
                                    placeholder="{{ translate('last_name') }}" required>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-sm-6 mb-3 mb-sm-0 mt-4">
                                <input type="email" class="form-control form-control-user" id="exampleInputEmail"
                                    name="email" value="{{ old('email') }}"
                                    placeholder="{{ translate('email_address') }}" required>
                            </div>
                            <div class="col-sm-6">
                                <input type="number" class="form-control form-control-user" id="exampleInputPhone"
                                    name="phone" value="{{ old('phone') }}"
                                    placeholder="{{ translate('phone_number') }}" required>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-sm-6 mb-3 mb-sm-0">
                                <input type="password" class="form-control form-control-user" minlength="6"
                                    id="exampleInputPassword" name="password"
                                    placeholder="{{ translate('password') }}" required>
                            </div>
                            <div class="col-sm-6">
                                <input type="password" class="form-control form-control-user" minlength="6"
                                    id="exampleRepeatPassword" name="password_confirmation"
                                    placeholder="{{ translate('repeat_password') }}" required>
                                <div class="pass invalid-feedback">{{ translate('Repeat') }}
                                    {{ translate('password') }} {{ translate('not match') }}
                                    .
                                </div>
                            </div>
                        </div>
                        <div class="">
                            <div class="pb-1">
                                <center>
                                    <img style="width: auto;border: 1px solid; border-radius: 10px; max-height:200px;"
                                        id="viewer" src="{{ asset('assets\back-end\img\400x400\img2.jpg') }}"
                                        alt="banner image" />
                                </center>
                            </div>

                            <div class="form-group">
                                <div class="custom-file" style="text-align: left">
                                    <input type="file" name="image" id="customFileUpload"
                                        class="custom-file-input"
                                        accept=".jpg, .png, .jpeg, .gif, .bmp, .tif, .tiff|image/*">
                                    <label class="custom-file-label"
                                        for="customFileUpload">{{ translate('Upload') }}
                                        {{ translate('image') }}</label>
                                </div>
                            </div>
                        </div>


                        <h5 class="black">{{ translate('Shop') }} {{ translate('Info') }}</h5>
                        <div class="form-group row">
                            <div class="col-sm-6 mb-3   ">
                                <input type="text" class="form-control form-control-user" id="shop_name"
                                    name="shop_name" placeholder="{{ translate('shop_name') }}"
                                    value="{{ old('shop_name') }}" required>
                            </div>
                            <div class="col-sm-6 mb-3  ">
                                <textarea name="shop_address" class="form-control" id="shop_address" rows="1"
                                    placeholder="{{ translate('shop_address') }}">{{ old('shop_address') }}</textarea>
                            </div>

                            <div class="col-sm-6 mb-3  ">
                                <select name="district_id" class="form-control" id="district_id">
                                    <option value="">{{ translate('Select District') }}</option>
                                    @foreach ($districts as $district)
                                        <option value="{{ $district->id }}">{{ $district->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-sm-6 mb-3  ">
                                <select name="area_id" class="form-control" id="area_id">
                                    <option value="">{{ translate('Select Area') }}</option>
                                </select>
                            </div>
                        </div>
                        <div class="">
                            <div class="pb-1">
                                <center>
                                    <img style="width: auto;border: 1px solid; border-radius: 10px; max-height:200px;"
                                        id="viewerLogo" src="{{ asset('assets\back-end\img\400x400\img2.jpg') }}"
                                        alt="banner image" />
                                </center>
                            </div>

                            <div class="form-group">
                                <div class="custom-file" style="text-align: left">
                                    <input type="file" name="logo" id="LogoUpload"
                                        class="custom-file-input"
                                        accept=".jpg, .png, .jpeg, .gif, .bmp, .tif, .tiff|image/*">
                                    <label class="custom-file-label" for="LogoUpload">{{ translate('Upload') }}
                                        {{ translate('logo') }}</label>
                                </div>
                            </div>
                        </div>
                        <div class="">
                            <div class="pb-1">
                                <center>
                                    <img style="width: auto;border: 1px solid; border-radius: 10px; max-height:200px;"
                                        id="viewerBanner"
                                        src="{{ asset('assets\back-end\img\400x400\img2.jpg') }}"
                                        alt="banner image" />
                                </center>
                            </div>

                            <div class="form-group">
                                <div class="custom-file" style="text-align: left">
                                    <input type="file" name="banner" id="BannerUpload"
                                        class="custom-file-input"
                                        accept=".jpg, .png, .jpeg, .gif, .bmp, .tif, .tiff|image/*"
                                        style="overflow: hidden; padding: 2%">
                                    <label class="custom-file-label" for="BannerUpload">{{ translate('Upload') }}
                                        {{ translate('Banner') }}</label>
                                </div>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary btn-user btn-block"
                            id="apply">{{ translate('Apply') }} {{ translate('Shop') }} </button>
                    </form>
                    <hr>
                    <div class="text-center">
                        <a class="small"
                            href="{{ route('seller.auth.login') }}">{{ translate('already_have_an_account?_login.') }}</a>
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
        $('#apply').on('click', function() {

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

        function Validate(file) {
            var x;
            var le = file.length;
            var poin = file.lastIndexOf(".");
            var accu1 = file.substring(poin, le);
            var accu = accu1.toLowerCase();
            if ((accu != '.png') && (accu != '.jpg') && (accu != '.jpeg')) {
                x = 1;
                return x;
            } else {
                x = 0;
                return x;
            }
        }

        function readURL(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();

                reader.onload = function(e) {
                    $('#viewer').attr('src', e.target.result);
                }

                reader.readAsDataURL(input.files[0]);
            }
        }

        $("#customFileUpload").change(function() {
            readURL(this);
        });

        function readlogoURL(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();

                reader.onload = function(e) {
                    $('#viewerLogo').attr('src', e.target.result);
                }

                reader.readAsDataURL(input.files[0]);
            }
        }

        function readBannerURL(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();

                reader.onload = function(e) {
                    $('#viewerBanner').attr('src', e.target.result);
                }

                reader.readAsDataURL(input.files[0]);
            }
        }

        $("#LogoUpload").change(function() {
            readlogoURL(this);
        });
        $("#BannerUpload").change(function() {
            readBannerURL(this);
        });


        $('#district_id').on('change', function() {
            var district_id = $('#district_id').val();
            if (district_id) {
                $.ajax({
                    url: "{{ url('/api/v1/address?district_id=') }}" + district_id,
                    type: "GET",
                    dataType: "json",
                    success: function(data) {
                        $('#area_id').empty();

                        var options = '<option value="">{{ translate('Select District') }}</option>';

                        $.each(data, function(key, value) {
                            options += '<option value="' + value.id + '">' + value.name +
                                '</option>';
                        });

                        $('#area_id').html(options);
                    },
                });
            }
        });
    </script>
@endpush
