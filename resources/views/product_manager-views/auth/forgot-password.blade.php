<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Required Meta Tags Always Come First -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <!-- Title -->
    <title>{{translate('forgot_password')}}</title>

    <!-- Favicon -->
    <link rel="shortcut icon" href="favicon.ico">

    <!-- Font -->
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600&amp;display=swap" rel="stylesheet">
    <!-- CSS Implementing Plugins -->
    <link rel="stylesheet" href="{{asset('assets/back-end')}}/css/vendor.min.css">
    <link rel="stylesheet" href="{{asset('assets/back-end')}}/vendor/icon-set/style.css">
    <!-- CSS Front Template -->
    <link rel="stylesheet" href="{{asset('assets/back-end')}}/css/theme.minc619.css?v=1.0">
    <link rel="stylesheet" href="{{asset('assets/back-end')}}/css/toastr.css">
</head>

<body>
<!-- ========== MAIN CONTENT ========== -->
<main id="content" role="main" class="main">
    <div class="position-fixed top-0 right-0 left-0 bg-img-hero"
         style="height: 32rem; background-image: url({{asset('assets/admin')}}/svg/components/abstract-bg-4.svg);">
        <!-- SVG Bottom Shape -->
        <figure class="position-absolute right-0 bottom-0 left-0">
            <svg preserveAspectRatio="none" xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" viewBox="0 0 1921 273">
                <polygon fill="#fff" points="0,273 1921,273 1921,0 "/>
            </svg>
        </figure>
        <!-- End SVG Bottom Shape -->
    </div>

    <!-- Content -->
    <div class="container py-5 py-sm-7">
        @php($e_commerce_logo=\App\Models\BusinessSetting::where(['type'=>'company_web_logo'])->first()->value)
        <a class="d-flex justify-content-center mb-5" href="javascript:">
            <img class="z-index-2" src="{{asset("storage/company/".$e_commerce_logo)}}" alt="Logo"
                 onerror="this.src='{{asset('assets/back-end/img/400x400/img2.jpg')}}'"
                 style="width: 8rem;">
        </a>

        <div class="row justify-content-center">
            <div class="col-lg-8 col-md-10">
                <h2 class="h3 mb-4">{{translate('forgot_password?')}}</h2>
                <p class="font-size-md">{{translate('follow_steps')}}</p>
                <ol class="list-unstyled font-size-md">
                    <li>
                        <span
                            class="text-primary mr-2">1.</span>{{translate('Fill in your email address below')}}
                        .
                    </li>
                    <li>
                        <span
                            class="text-primary mr-2">2.</span>{{translate('We will send email you a temporary code')}}
                        .
                    </li>
                    <li><span class="text-primary mr-2">3.</span>{{translate('Use the code to change your password on our secure
                        website')}}.
                    </li>
                </ol>
                <div class="card py-2 mt-4">
                    <form class="card-body needs-validation" action="{{route('product_manager.auth.forgot-password')}}"
                          method="post">
                        @csrf
                        <div class="form-group">
                            <label for="recover-email">{{translate('Enter your email address')}}</label>
                            <input class="form-control" type="email" name="email" id="recover-email" required>
                            <div
                                class="invalid-feedback">{{translate('Please provide valid email address')}}
                                .
                            </div>
                        </div>
                        <button class="btn btn-primary"
                                type="submit">{{translate('Get new password')}}</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- End Content -->
</main>
<!-- ========== END MAIN CONTENT ========== -->


<!-- JS Implementing Plugins -->
<script src="{{asset('assets/back-end')}}/js/vendor.min.js"></script>

<!-- JS Front -->
<script src="{{asset('assets/back-end')}}/js/theme.min.js"></script>
<script src="{{asset('assets/back-end')}}/js/toastr.js"></script>
{!! Toastr::message() !!}

@if ($errors->any())
    <script>
        @foreach($errors->all() as $error)
        toastr.error('{{$error}}', Error, {
            CloseButton: true,
            ProgressBar: true
        });
        @endforeach
    </script>
@endif

<!-- JS Plugins Init. -->
<script>
    $(document).on('ready', function () {
        // INITIALIZATION OF SHOW PASSWORD
        // =======================================================
        $('.js-toggle-password').each(function () {
            new HSTogglePassword(this).init()
        });

        // INITIALIZATION OF FORM VALIDATION
        // =======================================================
        $('.js-validate').each(function () {
            $.HSCore.components.HSValidation.init($(this));
        });
    });
</script>

<!-- IE Support -->
<script>
    if (/MSIE \d|Trident.*rv:/.test(navigator.userAgent)) document.write('<script src="{{asset('assets/admin')}}/vendor/babel-polyfill/polyfill.min.js"><\/script>');
</script>
</body>
</html>

