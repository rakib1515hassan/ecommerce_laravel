@php use App\Models\Color; @endphp


@extends('layouts.back-end.app')




@section('title', translate('Car Service Update'))

@push('css_or_js')
    <link href="{{asset('assets/back-end/css/tags-input.min.css')}}" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"/>

    <style>
        .delete-icon {
            font-size: 24px; /* Adjust the size as needed */
            color: #e12020; /* Adjust the color as needed */
        }
    </style>
@endpush

@section('content')
    <div class="content container-fluid">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="{{route('admin.dashboard')}}">{{translate('Dashboard')}}</a>
                </li>
                <li class="breadcrumb-item" aria-current="page">
                    <a href="{{route('admin.car.list')}}">{{translate('Car')}}</a>
                </li>
                <li class="breadcrumb-item">{{translate('Information')}} {{translate('Update')}} </li>
            </ol>
        </nav>

        <!-- Content Row -->
        <div class="row">
            <div class="col-md-12">
                <form class="product-form" action="{{route('admin.car.update', ['id' => $service->id])}}" method="POST"
                      style="text-align: {{Session::get('direction') === "rtl" ? 'right' : 'left'}};" enctype="multipart/form-data">
                    @csrf

                    <div class="card">

                        <div class="card-body">
                            <!-- Service Category -->
                            <div class="form-group ">
                                <label class="input-label" for="">{{translate('service_category')}}</label>
                                <select name="category_id" id="" class="form-control col-md-6" required>
                                    <option value="">{{ translate('Select') }}</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" {{ $category->id == $service->category_id ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
    
                            <div>
                                {{-- Service Name --}}
                                <div class="form-group">
                                    <label class="input-label" for="name">{{translate('name')}}</label>
                                    <input type="text" name="name" id="name" class="form-control" placeholder="New Service Name"
                                    value="{{ $service->name }}" required>
                                </div>

                                {{-- Service Short Description --}}
                                <div class="form-group pt-4">
                                    <label class="input-label"
                                        for="s_description">{{ translate('short Description') }}</label>
                                    <textarea name="s_description" id="s_description" class="form-control" cols="30" rows="5" placeholder="write short description" 
                                    required>{{ $service->short_description }}</textarea>
                                </div>
            
                                {{-- Service Description --}}
                                <div class="form-group pt-4">
                                    <label class="input-label" for="description">{{translate('description')}}</label>
                                    <textarea name="description" id="description" class="editor textarea" cols="30"
                                        rows="10" required>{{ $service->description }}</textarea>
                                </div>
                            </div>
                         
                        </div>
                    </div>

                    {{-- Service price --}}
                    <div class="card mt-2 rest-part">
                        <div class="card-header">
                            <h4>{{translate('Service price')}}</h4>
                        </div>
                        <div class="card-body">
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-6">
                                        <label
                                            class="control-label">{{translate('Purchase price')}}</label>
                                        <input type="number" min="0" step="0.01" placeholder="{{translate('Service price')}}"
                                            value="{{ $service->price }}"
                                            name="purchase_price" class="form-control" id="purchase_price">
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>

                    
                    {{-- Feature List Section --}}
                    {{-- <div class="card mt-2 mb-2 rest-part">
                        <div class="card-header">
                            <h4>{{translate('Feature Add Section')}}</h4>
                        </div>
                        <div class="card-body">
                            <div id="featureContainer">
                                @foreach ($service->features as $feature)
                                    <div class="row featureRow">
                                        <div class="col-md-8">
                                            <label class="control-label">{{ translate('Feature Name') }}</label>
                                            <input type="text" name="feature_name[]" value="{{ $feature->feature }}" class="form-control">
                                        </div>
                                        <div class="col-md-4">
                                            <button class="btn btn-danger delete-feature" data-feature-id="{{ $feature->id }}" type="button" style="margin-top: 28px;">{{ translate('Delete') }}</button>
                                        </div>
                                    </div>
                                @endforeach
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
                            <h4>{{translate('Image Section')}}</h4>
                        </div>
                        <div class="card-body">
                            <div class="col-md-12">

                                {{-- Here Show Old Image List --}}
                                <div class="row">
                                    <div class="col-md-8">
                                        <label class="control-label">{{translate('Images')}}</label>
                                        <!-- <input type="file" name="images[]" multiple class="form-control" accept="" id="image_input"> -->
                                    </div>
                                </div>

                                @if ($service->images->isNotEmpty())
                                    <div class="p-2 border border-dashed mt-2">
                                        <div class="row" id="">
                                            @foreach ($service->images as $image)
                                            <div class="col-md-3 mb-3">
                                                <div class="position-relative" style="height: 100%; width:200px;">

                                                    <img src="{{ asset('storage/'. $image->image_path) }}" class="img-fluid flex-grow-1" 
                                                    alt="Image" width="200px">
          
                                                    <button class="btn btn-sm position-absolute top-0 mr-2 deleteImage" type="button"
                                                        data-id="{{ $image->id }}" style="right: -10px;">
                                                        <i class="fa-solid fa-xmark text-danger fa-2x"></i>
                                                    </button>
                                                </div>
                                            </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif

                                <!-- Add New Image -->
                                <div class="row mt-4">
                                    <div class="col-md-8">
                                        <label class="control-label">{{translate('Add New Image')}}</label>
                                        <!-- <input type="file" name="images[]" multiple class="form-control" accept="" id="image_input"> -->
                                    </div>
                                </div>
                                
                                <div class="p-2 border border-dashed mt-2">
                                    <div class="row" id="coba"></div>
                                </div>


                            </div>
                         
                        </div>
                    </div>

                    <div class="card card-footer">
                        <div class="row">
                            <div class="col-md-12" style="padding-top: 20px">
                                <button type="submit" class="btn btn-primary">{{translate('Update')}}</button>
                                <a class="btn btn-secondary ml-2" href="{{route('admin.car.list')}}">{{translate('Cancel')}}</a>
                            </div>
                        </div>
                    </div>

                </form>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script src="{{asset('assets/back-end')}}/js/tags-input.min.js"></script>
    <script src="{{asset('assets/back-end/js/spartan-multi-image-picker.js')}}"></script>

    {{-- Multiple Features Add --}}
    <script>
        $(document).ready(function() {
            // Add feature input field
            $('#addFeature').click(function() {
                var newFeatureField = '<div class="row featureRow">' +
                                        '<div class="col-md-8">' +
                                            '<label class="control-label">{{translate('Feature Name')}}</label>' +
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
        $(function () {
            $("#coba").spartanMultiImagePicker({
                fieldName: 'images[]',
                maxCount: 4,
                rowHeight: 'auto',
                groupClassName: 'col-4',
                maxFileSize: '',
                placeholderImage: {
                    image: '{{asset('assets/back-end/img/400x400/img2.jpg')}}',
                    width: '100%',
                },
                dropFileLabel: "Drop Here",
                onAddRow: function (index, file) {

                },
                onRenderedPreview: function (index) {

                },
                onRemoveRow: function (index) {

                },
                onExtensionErr: function (index, file) {
                    toastr.error('{{translate('Please only input png or jpg type file')}}', {
                        CloseButton: true,
                        ProgressBar: true
                    });
                },
                onSizeErr: function (index, file) {
                    toastr.error('{{translate('File size too big')}}', {
                        CloseButton: true,
                        ProgressBar: true
                    });
                }
            });
        });

    </script>

    <script>
        $(document).ready(function() {
            $('.deleteImage').click(function() {

                console.log("Image deleted"); 
                var imgId = $(this).data('id'); 
                var confirmation = confirm('Are you sure you want to delete this image?');
                if (confirmation) {
                    $.ajax({
                        url: '{{ route("admin.service.image_delete", ["id" => "__img_id__"]) }}'.replace('__img_id__', imgId),
                        type: 'DELETE',
                        data: {
                            _token: '{{ csrf_token() }}',
                            img_id: imgId
                        },
                        success: function(response) {
                            location.reload(); 
                        },
                        error: function(xhr, status, error) {
                            console.error(xhr.responseText);
                        }
                    });
                }
            });
        });
    </script>


    <script>
        $(document).ready(function() {
            $('.delete-feature').click(function() {
                var featureId = $(this).data('feature-id');
                var confirmation = confirm('Are you sure you want to delete this feature?');
                if (confirmation) {
                    $.ajax({
                        url: '{{ route("admin.service.feature_delete", ["id" => "__feature_id__"]) }}'.replace('__feature_id__', featureId),
                        type: 'DELETE',
                        data: {
                            _token: '{{ csrf_token() }}',
                            feature_id: featureId
                        },
                        success: function(response) {
                            location.reload(); 
                        },
                        error: function(xhr, status, error) {
                            console.error(xhr.responseText);
                        }
                    });
                }
            });
        });
    </script>


    {{--ck editor--}}
    <script src="{{asset('/')}}vendor/ckeditor/ckeditor/ckeditor.js"></script>
    <script src="{{asset('/')}}vendor/ckeditor/ckeditor/adapters/jquery.js"></script>
    <script>
        $('.textarea').ckeditor({
            contentsLangDirection: '{{Session::get('direction')}}',
        });
    </script>
    {{--ck editor--}}
@endpush
