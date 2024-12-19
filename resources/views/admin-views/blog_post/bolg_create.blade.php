@extends('layouts.back-end.app')

@section('title', translate('Blog Post Create'))

@push('css_or_js')
@endpush

@section('content')
    <div class="content container-fluid">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="{{ route('admin.dashboard') }}">{{ translate('Dashboard') }}</a>
                </li>
                <li class="breadcrumb-item" aria-current="page">{{ translate('blog_post_create') }}</li>
            </ol>
        </nav>

        <!-- Content Row -->
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        {{ translate('blog_post_form') }}
                    </div>
                    <div class="card-body"
                        style="text-align: {{ Session::get('direction') === 'rtl' ? 'right' : 'left' }};">
                        <form action="{{ route('admin.blog_post.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf

                            <div class="row">
                                <div class="col-6">
                                    <div class="form-group ">
                                        <label class="input-label" for="">{{ translate('blog_category') }}</label>
                                        <select name="blog_category_id" id="" class="form-control" required>
                                            <option value="">Select</option>
                                            @foreach ($blog_categories as $key => $value)
                                                <option value="{{ $value->id }}">{{ $value->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="form-group ">
                                        <label class="input-label" for="">{{ translate('title') }}</label>
                                        <input type="text" name="title" class="form-control" placeholder="">
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-group ">
                                        <label class="input-label" for="">{{ translate('content') }}</label>
                                        <textarea name="content" class="form-control"></textarea>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <label for="name">{{ translate('Thumbnail') }}</label><span
                                        class="badge badge-soft-danger">( {{ translate('ratio') }} 1:1 )</span>
                                    <div class="custom-file" style="text-align: left" required>
                                        <input type="file" name="thumbnail" id="customFileEg1" class="custom-file-input"
                                            accept=".jpg, .png, .jpeg, .gif, .bmp, .tif, .tiff|image/*">
                                        <label class="custom-file-label" for="customFileEg1">{{ translate('choose') }}
                                            {{ translate('file') }}</label>
                                    </div>
                                    <img src="" alt="" id="viewer" height="70" />
                                </div>
                                <div class="col-6">
                                    <div class="form-group ">
                                        <label class="input-label" for="">{{ translate('is_published') }}</label>
                                        <input type="radio" name="is_published" class="form-control-" checked
                                            value="1">
                                        Yes
                                        <input type="radio" name="is_published" class="form-control-" value="0"> No
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="form-group ">
                                        <label class="input-label" for="">{{ translate('is_approved') }}</label>
                                        <input type="radio" name="is_approved" class="form-control-" checked
                                            value="1">
                                        Yes
                                        <input type="radio" name="is_approved" class="form-control-" value="0"> No
                                    </div>
                                </div>

                                <div class="col-6">
                                    <div class="form-group ">
                                        <label class="input-label" for="">{{ translate('is_featured') }}</label>
                                        <input type="radio" name="is_featured" class="form-control-" checked
                                            value="1">
                                        Yes
                                        <input type="radio" name="is_featured" class="form-control-" value="0"> No
                                    </div>
                                </div>

                                <div class="col-6">
                                    <div class="form-group ">
                                        <label class="input-label" for="">{{ translate('is_commentable') }}</label>
                                        <input type="radio" name="is_commentable" class="form-control-" checked
                                            value="1"> Yes
                                        <input type="radio" name="is_commentable" class="form-control-"
                                            value="0"> No
                                    </div>
                                </div>

                            </div>
                            <hr>
                            <button type="submit" class="btn btn-primary">{{ translate('submit') }}</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection

@push('script')
    
@endpush
