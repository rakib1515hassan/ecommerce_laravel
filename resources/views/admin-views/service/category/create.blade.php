@php use App\Models\Color; @endphp
@extends('layouts.back-end.app')

@section('title', translate('Service Category Add'))

@push('css_or_js')
    <link href="{{ asset('assets/back-end/css/tags-input.min.css') }}" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endpush

@section('content')
    <div class="content container-fluid">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="{{ route('admin.dashboard') }}">{{ translate('Dashboard') }}</a>
                </li>
                <li class="breadcrumb-item" aria-current="page">
                    <a href="{{ route('admin.service.category_list') }}">{{ translate('Service Category') }}</a>
                </li>
                <li class="breadcrumb-item">{{ translate('Add') }} {{ translate('New') }} </li>
            </ol>
        </nav>

        <!-- Content Row -->
        <div class="row">
            <div class="col-md-12">
                <form class="product-form" action="{{ route('admin.service.category_create_store') }}" method="POST"
                    style="text-align: {{ Session::get('direction') === 'rtl' ? 'right' : 'left' }};"
                    enctype="multipart/form-data">
                    @csrf

                    <div class="card">

                        <div class="card-body">


                            <div>
                                {{-- Service Name --}}
                                <div class="form-group col-md-6">
                                    <label class="input-label" for="name">{{ translate('name') }}</label>
                                    <input type="text" name="name" id="name" class="form-control"
                                        placeholder="New Service Name" required>
                                </div>
                            </div>

                            <div>
                                {{-- Service Type --}}
                                <div class="form-group col-md-6">
                                    <label class="input-label" for="category_type">
                                        {{ translate('category_type') }}
                                    </label>
                                    <select class="form-control" name="category_type" id="category_type" required>
                                        <option value="service" selected>Service</option>
                                        <option value="car">Car</option>
                                        <option value="property">Property</option>
                                    </select>
                                </div>

                            </div>

                            <!-- <div>
                                    {{-- Service Loge --}}
                                    <div class="form-group col-md-6">
                                        <label class="input-label" for="name">{{ translate('logo') }}</label>
                                        <input type="file" name="logo" id="logo" class="form-control"
                                            required>
                                    </div>
                                </div> -->

                            <div class="col-md-6">
                                <label class="input-label" for="name">
                                    {{ translate('logo') }}
                                    <small style="color: red">* ( {{ translate('ratio') }} 1:1)</small>
                                </label>
                                <div style="max-width:200px;">
                                    <div class="row" id="logo_img"></div>
                                </div>
                            </div>

                        </div>
                    </div>

                    <div class="card card-footer">
                        <div class="row">
                            <div class="col-md-12" style="padding-top: 20px">
                                <button type="submit" class="btn btn-primary">{{ translate('Submit') }}</button>
                            </div>
                        </div>
                    </div>

                </form>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script src="{{ asset('assets/back-end') }}/js/tags-input.min.js"></script>
    <script src="{{ asset('assets/back-end/js/spartan-multi-image-picker.js') }}"></script>



    {{-- Logo Add --}}
    <script>
        $(function() {
            $("#logo_img").spartanMultiImagePicker({
                fieldName: 'logo',
                maxCount: 1,
                rowHeight: 'auto',
                groupClassName: 'col-12',
                maxFileSize: '',
                placeholderImage: {
                    image: '{{ asset('assets/back-end/img/400x400/img2.jpg') }}',
                    width: '100%',
                },
                dropFileLabel: "Drop Here",
                onAddRow: function(index, file) {

                },
                onRenderedPreview: function(index) {

                },
                onRemoveRow: function(index) {

                },
                onExtensionErr: function(index, file) {
                    toastr.error('{{ translate('Please only input png or jpg type file') }}', {
                        CloseButton: true,
                        ProgressBar: true
                    });
                },
                onSizeErr: function(index, file) {
                    toastr.error('{{ translate('File size too big') }}', {
                        CloseButton: true,
                        ProgressBar: true
                    });
                }
            });

        });

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


        $(".js-example-theme-single").select2({
            theme: "classic"
        });

        $(".js-example-responsive").select2({
            // dir: "rtl",
            width: 'resolve'
        });
    </script>





    {{-- <script>
        function check() {
            // Display a confirmation dialog
            Swal.fire({
                title: '{{translate('Are you sure')}}?',
                text: '',
                type: 'warning',
                showCancelButton: true,
                cancelButtonColor: 'default',
                confirmButtonColor: '#377dff',
                cancelButtonText: 'No',
                confirmButtonText: 'Yes',
                reverseButtons: true
            }).then((result) => {
                // Update CKEditor instances
                for (instance in CKEDITOR.instances) {
                    CKEDITOR.instances[instance].updateElement();
                }
    
                // Gather feature names into an array
                var featureNames = [];
                $(".featureRow input[name='feature_name[]']").each(function() {
                    featureNames.push($(this).val());
                });
    
                // Collect selected images from file input element
                var selectedImages = [];
                var files = document.getElementById('image_input').files; 
                for (var i = 0; i < files.length; i++) {
                    selectedImages.push(files[i]);
                }
    
                console.log("Feature Names:", featureNames);
                console.log("Selected Images:", selectedImages);
    
                var formData = {
                    name: $("#name").val(),
                    description: $("#description").val(),
                    purchase_price: $("#purchase_price").val(),
                    features: featureNames,
                    images: selectedImages 
                };
    
                // Log formData to console
                console.log("Add Form Data =",formData);
    
                // Continue with form submission if user confirms
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                $.post({
                    url: '{{route('admin.service.store')}}',
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function (data) {
                        // Handle success or errors
                        if (data.errors) {
                            for (var i = 0; i < data.errors.length; i++) {
                                toastr.error(data.errors[i].message, {
                                    CloseButton: true,
                                    ProgressBar: true
                                });
                            }
                        } else {
                            toastr.success('{{translate('product added successfully')}}!', {
                                CloseButton: true,
                                ProgressBar: true
                            });
                            // Clear form fields
                            $('#product_form')[0].reset();
                        }
                    }
                });
            })
        };
    </script> --}}



    {{-- ck editor --}}
    <script src="{{ asset('/') }}vendor/ckeditor/ckeditor/ckeditor.js"></script>
    <script src="{{ asset('/') }}vendor/ckeditor/ckeditor/adapters/jquery.js"></script>
    <script>
        $('.textarea').ckeditor({
            contentsLangDirection: '{{ Session::get('direction') }}',
        });
    </script>
    {{-- ck editor --}}
@endpush
