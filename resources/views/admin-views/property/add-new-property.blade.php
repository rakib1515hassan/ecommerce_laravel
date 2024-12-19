@php use App\Models\Color; @endphp
@extends('layouts.back-end.app')

@section('title', translate('Property Add'))

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
                    <a href="{{ route('admin.property.list') }}">{{ translate('property') }}</a>
                </li>
                <li class="breadcrumb-item">{{ translate('Add') }} {{ translate('New') }} </li>
            </ol>
        </nav>

        <!-- Content Row -->
        <div class="row">
            <div class="col-md-12">
                <form class="product-form" action="{{ route('admin.property.store') }}" method="POST"
                    style="text-align: {{ Session::get('direction') === 'rtl' ? 'right' : 'left' }};"
                    enctype="multipart/form-data">
                    @csrf

                    <div class="card">

                        <div class="card-body">
                            <!-- Service Category -->
                            <div class="form-group ">
                                <label class="input-label" for="">{{ translate('property_category') }}</label>
                                <select name="category_id" id="" class="form-control col-md-6" required>
                                    <option value="">{{ translate('Select category') }}</option>
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                {{-- Service Name --}}
                                <div class="form-group">
                                    <label class="input-label" for="name">{{ translate('Title') }}</label>
                                    <input type="text" name="name" id="name" class="form-control"
                                        placeholder="Write a title" required>
                                </div>

                                {{-- Service Short Description --}}
                                <div class="form-group pt-4">
                                    <label class="input-label"
                                        for="s_description">{{ translate('short Description') }}</label>
                                    <textarea name="s_description" id="s_description" class="form-control" cols="30" rows="5"
                                        placeholder="write short description" required>{{ old('details') }}</textarea>
                                </div>

                                {{-- Service Description --}}
                                <div class="form-group pt-4">
                                    <label class="input-label" for="description">{{ translate('description') }}</label>
                                    <textarea name="description" id="description" class="editor textarea" cols="30" rows="10" required>{{ old('details') }}</textarea>
                                </div>
                            </div>

                        </div>
                    </div>

                    {{-- Service price --}}
                    <div class="card mt-2 rest-part">
                        <div class="card-header">
                            <h4>{{ translate('Service price') }}</h4>
                        </div>
                        <div class="card-body">
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-6">
                                        <label class="control-label">{{ translate('Purchase price') }}</label>
                                        <input type="number" min="0" step="0.01"
                                            placeholder="{{ translate('Service price') }}"
                                            value="{{ old('purchase_price') }}" name="purchase_price" class="form-control"
                                            id="purchase_price">
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>


                    {{-- Feature Add Section --}}
                    {{-- <div class="card mt-2 mb-2 rest-part">
                        <div class="card-header">
                            <h4>{{translate('Feature Add Section')}}</h4>
                        </div>
                        <div class="card-body">
                            <div id="featureContainer">
                                <div class="row featureRow">
                                    <div class="col-md-8">
                                        <label class="control-label">{{translate('Feature Name')}}</label>
                                        <input type="text" name="feature_name[]" placeholder="" class="form-control" required>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-8 mt-4">
                                    <button class="btn btn-primary" type="button" id="addFeature" style="min-width: 300px;">Add More</button>
                                </div>
                            </div>
                        </div>
                    </div> --}}


                    {{-- Image Add Section --}}
                    <div class="card mt-2 mb-2 rest-part">
                        <div class="card-header">
                            <h4>{{ translate('Image Add Section') }}</h4>
                        </div>
                        <div class="card-body">
                            <div class="col-md-12">
                                <div class="row">
                                    <div class="col-md-8">
                                        <label class="control-label">{{ translate('Service Image') }}</label>
                                        <!-- <input type="file" name="images[]" multiple class="form-control" accept="" id="image_input"> -->
                                    </div>
                                </div>

                                <div class="p-2 border border-dashed mt-4">
                                    <div class="row" id="coba"></div>
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

    {{-- Multiple Features Add --}}
    <script>
        $(document).ready(function() {
            // Add feature input field
            $('#addFeature').click(function() {
                var newFeatureField = '<div class="row featureRow">' +
                    '<div class="col-md-8">' +
                    '<label class="control-label">{{ translate('Feature Name') }}</label>' +
                    '<input type="text" name="feature_name[]" placeholder="" class="form-control">' +
                    '</div>' +
                    '<div class="col-md-4" style="margin-top: 30px;">' +
                    '<button class="btn btn-danger removeFeature" type="button">Remove</button>' +
                    '</div>' +
                    '</div>';
                $('#featureContainer').append(newFeatureField);
            });

            // Remove feature input field
            $(document).on('click', '.removeFeature', function() {
                $(this).closest('.featureRow').remove();
            });
        });
    </script>


    {{-- Multiple Images Add --}}
    <script>
        $(function() {
            $("#coba").spartanMultiImagePicker({
                fieldName: 'images[]',
                maxCount: 4,
                rowHeight: 'auto',
                groupClassName: 'col-4',
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
