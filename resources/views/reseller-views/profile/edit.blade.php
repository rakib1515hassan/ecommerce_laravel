@extends('layouts.back-end.app-reseller')

@section('title', translate('Profile Settings'))

@push('css_or_js')
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endpush

@section('content')
    <!-- Content -->
    <div class="content container-fluid">
        <!-- Page Header -->
        <div class="page-header">
            <div class="row align-items-end">
                <div class="col-sm mb-2 mb-sm-0">
                    <h1 class="page-header-title">{{translate('Settings')}}</h1>
                </div>

                <div class="col-sm-auto">
                    <a class="btn btn-primary" href="{{route('reseller.dashboard.index')}}">
                        <i class="tio-home mr-1"></i> {{translate('Dashboard')}}
                    </a>
                </div>
            </div>
            <!-- End Row -->
        </div>
        <!-- End Page Header -->

        <div class="row">
            <div class="col-lg-3">
                <!-- Navbar -->
                <div class="navbar-vertical navbar-expand-lg mb-3 mb-lg-5">
                    <!-- Navbar Toggle -->
                    <button type="button" class="navbar-toggler btn btn-block btn-white mb-3"
                            aria-label="Toggle navigation" aria-expanded="false" aria-controls="navbarVerticalNavMenu"
                            data-toggle="collapse" data-target="#navbarVerticalNavMenu">
                <span class="d-flex justify-content-between align-items-center">
                  <span class="h5 mb-0">{{translate('Nav menu')}}</span>

                  <span class="navbar-toggle-default">
                    <i class="tio-menu-hamburger"></i>
                  </span>

                  <span class="navbar-toggle-toggled">
                    <i class="tio-clear"></i>
                  </span>
                </span>
                    </button>
                    <!-- End Navbar Toggle -->

                    <div id="navbarVerticalNavMenu" class="collapse navbar-collapse">
                        <!-- Navbar Nav -->
                        <ul id="navbarSettings"
                            class="js-sticky-block js-scrollspy navbar-nav navbar-nav-lg nav-tabs card card-navbar-nav">
                            <li class="nav-item">
                                <a class="nav-link active" href="javascript:" id="generalSection" style="color: black">
                                    <i class="tio-user-outlined nav-icon"></i>{{translate('Basic')}} {{translate('information')}}
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="javascript:" id="passwordSection" style="color: black">
                                    <i class="tio-lock-outlined nav-icon"></i> {{translate('Password')}}
                                </a>
                            </li>
                        </ul>
                        <!-- End Navbar Nav -->
                    </div>
                </div>
                <!-- End Navbar -->
            </div>

            <div class="col-lg-9">
                <form action="{{route('reseller.profile.update',[$data->id])}}" method="post"
                      enctype="multipart/form-data" id="reseller-profile-form">
                    @csrf
                    <!-- Card -->
                    <div class="card mb-3 mb-lg-5" id="generalDiv">
                        <!-- Profile Cover -->
                        <div class="profile-cover">
                            <div class="profile-cover-img-wrapper"></div>
                        </div>
                        <!-- End Profile Cover -->

                        <!-- Avatar -->
                        <label
                                class="avatar avatar-xxl avatar-circle avatar-border-lg avatar-uploader profile-cover-avatar"
                                for="avatarUploader">
                            <img id="viewer"
                                 onerror="this.src='{{asset('assets/back-end/img/160x160/img1.jpg')}}'"
                                 class="avatar-img"
                                 src="{{asset('storage/reseller')}}/{{$data->image}}"
                                 alt="Image">
                        </label>
                        <!-- End Avatar -->
                    </div>
                    <!-- End Card -->

                    <!-- Card -->
                    <div class="card mb-3 mb-lg-5">
                        <div class="card-header">
                            <h2 class="card-title h4">{{translate('Basic')}} {{translate('information')}}</h2>
                        </div>

                        <!-- Body -->
                        <div class="card-body">
                            <!-- Form -->
                            <!-- Form Group -->
                            <div class="row form-group">
                                <label for="firstNameLabel"
                                       class="col-sm-3 col-form-label input-label">{{translate('Full')}} {{translate('name')}}
                                    <i
                                            class="tio-help-outlined text-body ml-1" data-toggle="tooltip"
                                            data-placement="top"
                                            title="Display name"></i></label>

                                <div class="col-sm-9 row">
                                    <div class="col-md-6">
                                        <label for="name">{{translate('First')}} {{translate('Name')}}
                                            <span
                                                    class="text-danger">*</span></label>
                                        <input type="text" name="f_name" value="{{$data->f_name}}" class="form-control"
                                               id="name"
                                               required>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="name">{{translate('Last')}} {{translate('Name')}}
                                            <span
                                                    class="text-danger">*</span></label>
                                        <input type="text" name="l_name" value="{{$data->l_name}}" class="form-control"
                                               id="name"
                                               required>
                                    </div>
                                </div>
                            </div>
                            <!-- End Form Group -->

                            <!-- Form Group -->
                            <div class="row form-group">
                                <label for="phoneLabel"
                                       class="col-sm-3 col-form-label input-label mt-3">{{translate('Phone')}}
                                    <span
                                            class="input-label-secondary">({{translate('Optional')}})</span></label>

                                <div class="col-sm-9"><small class="text-danger">(
                                        * {{translate('country_code_is_must')}} {{translate('like_for_BD_880')}}
                                        )</small>
                                    <input type="number" class="js-masked-input form-control" name="phone"
                                           id="phoneLabel"
                                           placeholder="+x(xxx)xxx-xx-xx" aria-label="+(xxx)xx-xxx-xxxxx"
                                           value="{{$data->phone}}"
                                           data-hs-mask-options='{
                                           "template": "+(880)00-000-00000"
                                         }'>
                                </div>
                            </div>
                            <!-- End Form Group -->

                            <div class="row form-group">
                                <label for="newEmailLabel"
                                       class="col-sm-3 col-form-label input-label">{{translate('Email')}}</label>

                                <div class="col-sm-9">
                                    <input type="email" class="form-control" name="email" id="newEmailLabel"
                                           value="{{$data->email}}"
                                           placeholder="{{translate('Enter new email address')}}"
                                           aria-label="Enter new email address">
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-3 col-form-label">
                                </div>
                                <div class="form-group col-md-9" id="select-img">
                                    <div class="custom-file">
                                        <input type="file" name="image" id="customFileUpload" class="custom-file-input"
                                               accept=".jpg, .png, .jpeg, .gif, .bmp, .tif, .tiff|image/*">
                                        <label class="custom-file-label"
                                               for="customFileUpload">{{translate('image')}} {{translate('Upload')}}</label>
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex justify-content-end">
                                <button type="button"
                                        onclick="{{env('APP_MODE')!='dev'?"form_alert('reseller-profile-form','Want to update reseller info ?')":"call_demo()"}}"
                                        class="btn btn-primary">{{translate('Save changes')}}
                                </button>
                            </div>

                            <!-- End Form -->
                        </div>
                        <!-- End Body -->
                    </div>
                    <!-- End Card -->
                </form>

                <!-- Card -->
                <div id="passwordDiv" class="card mb-3 mb-lg-5">
                    <div class="card-header">
                        <h4 class="card-title">{{translate('Change')}} {{translate('your')}} {{translate('password')}}</h4>
                    </div>

                    <!-- Body -->
                    <div class="card-body">
                        <!-- Form -->
                        <form id="changePasswordForm" action="{{route('reseller.profile.settings-password')}}"
                              method="post"
                              enctype="multipart/form-data">
                            @csrf

                            <!-- Form Group -->
                            <div class="row form-group">
                                <label for="newPassword"
                                       class="col-sm-3 col-form-label input-label"> {{translate('New')}}
                                    {{translate('password')}}</label>

                                <div class="col-sm-9">
                                    <input type="password" class="js-pwstrength form-control" name="password"
                                           id="newPassword"
                                           placeholder="{{translate('Enter new password')}}"
                                           aria-label="Enter new password"
                                           data-hs-pwstrength-options='{
                                           "ui": {
                                             "container": "#changePasswordForm",
                                             "viewports": {
                                               "progress": "#passwordStrengthProgress",
                                               "verdict": "#passwordStrengthVerdict"
                                             }
                                           }
                                         }'>

                                    <p id="passwordStrengthVerdict" class="form-text mb-2"></p>

                                    <div id="passwordStrengthProgress"></div>
                                </div>
                            </div>
                            <!-- End Form Group -->

                            <!-- Form Group -->
                            <div class="row form-group">
                                <label for="confirmNewPasswordLabel"
                                       class="col-sm-3 col-form-label input-label"> {{translate('Confirm')}}
                                    {{translate('password')}} </label>

                                <div class="col-sm-9">
                                    <div class="mb-3">
                                        <input type="password" class="form-control" name="confirm_password"
                                               id="confirmNewPasswordLabel"
                                               placeholder="{{translate('Confirm your new password')}}"
                                               aria-label="Confirm your new password">
                                    </div>
                                </div>
                            </div>
                            <!-- End Form Group -->

                            <div class="d-flex justify-content-end">
                                <button type="button"
                                        onclick="{{env('APP_MODE')!='dev'?"form_alert('changePasswordForm','Want to update admin password ?')":"call_demo()"}}"
                                        class="btn btn-primary">{{translate('Save')}} {{translate('changes')}}</button>
                            </div>
                        </form>
                        <!-- End Form -->
                    </div>
                    <!-- End Body -->
                </div>
                <!-- End Card -->

                <!-- Sticky Block End Point -->
                <div id="stickyBlockEndPoint"></div>
            </div>
        </div>
        <!-- End Row -->
    </div>
    <!-- End Content -->
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

        $("#customFileUpload").change(function () {
            readURL(this);
        });
    </script>

    <script>
        $("#generalSection").click(function () {
            $("#passwordSection").removeClass("active");
            $("#generalSection").addClass("active");
            $('html, body').animate({
                scrollTop: $("#generalDiv").offset().top
            }, 2000);
        });

        $("#passwordSection").click(function () {
            $("#generalSection").removeClass("active");
            $("#passwordSection").addClass("active");
            $('html, body').animate({
                scrollTop: $("#passwordDiv").offset().top
            }, 2000);
        });
    </script>
@endpush

@push('script')

@endpush
