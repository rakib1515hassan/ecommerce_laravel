@extends('layouts.back-end.app')

@section('title', translate('Banner Edit'))

@push('css_or_js')
    <link href="{{ asset('assets/back-end/css/croppie.css') }}" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endpush

@section('content')
    <div class="content container-fluid">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ translate('Dashboard') }}</a></li>
                <li class="breadcrumb-item" aria-current="page">{{ translate('Banner') }} {{ translate('Update') }}</li>
            </ol>
        </nav>

        <!-- Content Row -->
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h1 class="h3 mb-0 text-black-50">{{ translate('Banner') }} {{ translate('Update') }}</h1>
                    </div>
                    <div class="card-body"
                        style="text-align: {{ Session::get('direction') === 'rtl' ? 'right' : 'left' }};">
                        <form action="{{ route('admin.service.banner_update', ['id' => $banner->id]) }}" method="post"
                            enctype="multipart/form-data">
                            @csrf

                            <div class="">
                                <div class="form-group">
                                    <label for="brand">{{ translate('title') }}</label>
                                    <div class="custom-file" style="text-align: left">
                                        <input type="text" name="title" class="form-control"
                                            value="{{ $banner->title }}">
                                    </div>
                                </div>
                            </div>

                            <div class="">
                                <div class="form-group">
                                    <label for="brand">{{ translate('short descriptions') }}</label>
                                    <div class="custom-file" style="text-align: left">
                                        <textarea name="description" class="form-control" cols="30" rows="5" required
                                            placeholder="write short description">{{ $banner->descriptions }}</textarea>
                                    </div>
                                </div>
                            </div>


                            <div class="">
                                <div class="form-group">
                                    <label class="input-label" for="logo">{{ translate('Image') }}</label>
                                </div>

                                <div class="row" id="logo_img">
                                    @if ($banner->banner_image)
                                        <div class="col-6">
                                            <div class="card">
                                                <div class="card-body">
                                                    <img style="width: 100%" height="auto"
                                                        onerror="this.src='{{ asset('assets/front-end/img/image-place-holder.png') }}'"
                                                        src="{{ asset('storage/' . $banner->banner_image) }}"
                                                        alt="Product image">
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>


                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary">{{ translate('update') }}</button>
                            </div>
                        </form>
                    </div>
                </div>
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
                fieldName: 'banner_image',
                maxCount: 1,
                rowHeight: 'auto',
                groupClassName: 'col-6',
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
@endpush
