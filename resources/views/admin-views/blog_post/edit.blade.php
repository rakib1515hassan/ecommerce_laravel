@extends('layouts.back-end.app')

@section('title', translate('BlogPost'))

@push('css_or_js')
<style>
    .ck-editor__editable_inline {
        min-height: 200px; 
    }
</style>
@endpush

@section('content')
    <div class="content container-fluid">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a
                            href="{{route('admin.dashboard')}}">{{translate('Dashboard')}}</a>
                </li>
                <li class="breadcrumb-item" aria-current="page">{{translate('blog_post')}}</li>
            </ol>
        </nav>

        <!-- Content Row -->
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        {{ translate('blog_post_form')}}
                    </div>
                    <div class="card-body"
                         style="text-align: {{Session::get('direction') === "rtl" ? 'right' : 'left'}};">
                        <form action="{{route('admin.blog_post.update',[$blog_post['id']])}}" method="POST"
                              enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="id" value="{{ $blog_post->id }}">
                            <div class="row">
                                <div class="col-6">
                                    <div class="form-group ">
                                        <label class="input-label"
                                               for="">{{translate('blog_category')}}</label>
                                        <select name="blog_category_id" id="" class="form-control" required>
                                            <option value="">Select</option>
                                            @foreach($blog_categories as $key => $value)

                                                <option {{ ($value->id==$blog_post->blog_category_id) ? 'selected':'' }} value="{{$value->id}}">{{$value->name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="form-group ">
                                        <label class="input-label" for="">{{translate('title')}}</label>
                                        <input type="text" name="title" class="form-control" placeholder=""
                                               value="{{$blog_post->title}}">
                                    </div>
                                </div>

                                <div class="col-12">
                                    <div class="form-group">
                                        <label class="input-label" for="description">{{translate('content')}}</label>
                                        <textarea name="content" class="form-control" id="description">{{$blog_post->content}}</textarea>
                                    </div>
                                </div>

                                <div class="col-6">
                                    <label for="name">{{ translate('Thumbnail')}}</label><span
                                            class="badge badge-soft-danger">( {{translate('ratio')}} 1:1 )</span>
                                    <div class="custom-file" style="text-align: left" required>
                                        <input type="file" name="thumbnail" id="customFileEg1" class="custom-file-input"
                                               accept=".jpg, .png, .jpeg, .gif, .bmp, .tif, .tiff|image/*">
                                        <label class="custom-file-label"
                                               for="customFileEg1">{{translate('choose')}} {{translate('file')}}</label>
                                    </div>
                                    <img src="{{asset('storage/blog_post')}}/{{$blog_post['thumbnail']}}" alt="image"
                                         id="viewer" height="70"/>
                                </div>
                                <div class="col-6">
                                    <div class="form-group ">
                                        <label class="input-label"
                                               for="">{{translate('is_published')}}</label>
                                        <input type="radio" name="is_published" class="form-control-"
                                               {{ $blog_post['is_published'] ? 'checked':'' }} value="1"> Yes
                                        <input type="radio" name="is_published" class="form-control-"
                                               {{ ! $blog_post['is_published'] ? 'checked':'' }} value="0"> No
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="form-group ">
                                        <label class="input-label"
                                               for="">{{translate('is_approved')}}</label>
                                        <input type="radio" name="is_approved" class="form-control-"
                                               {{ $blog_post['is_approved'] ? 'checked':'' }} value="1"> Yes
                                        <input type="radio" name="is_approved" class="form-control-"
                                               {{ ! $blog_post['is_approved'] ? 'checked':'' }} value="0"> No
                                    </div>
                                </div>

                                <div class="col-6">
                                    <div class="form-group ">
                                        <label class="input-label"
                                               for="">{{translate('is_featured')}}</label>
                                        <input type="radio" name="is_featured" class="form-control-"
                                               {{ $blog_post['is_featured'] ? 'checked':'' }} value="1"> Yes
                                        <input type="radio" name="is_featured" class="form-control-"
                                               {{ ! $blog_post['is_featured'] ? 'checked':'' }} value="0"> No
                                    </div>
                                </div>

                                <div class="col-6">
                                    <div class="form-group ">
                                        <label class="input-label"
                                               for="">{{translate('is_commentable')}}</label>
                                        <input type="radio" name="is_commentable" class="form-control-"
                                               {{ $blog_post['is_commentable'] ? 'checked':'' }} value="1"> Yes
                                        <input type="radio" name="is_commentable" class="form-control-"
                                               {{ ! $blog_post['is_commentable'] ? 'checked':'' }} value="0"> No
                                    </div>
                                </div>

                            </div>

                            <hr>
                            <button type="submit" class="btn btn-primary">{{translate('update')}}</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script src="{{asset('assets/back-end')}}/js/tags-input.min.js"></script>
    <script src="{{asset('assets/back-end/js/spartan-multi-image-picker.js')}}"></script>


    <!-- Include CKEditor from CDN -->
<script src="https://cdn.ckeditor.com/ckeditor5/34.0.0/classic/ckeditor.js"></script>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Initialize CKEditor
        ClassicEditor
            .create(document.querySelector('#description'))
            .catch(error => {
                console.error(error);
            });
    });
</script>

    <script>

        $(document).ready(function () {
            $('#dataTable').DataTable();
        });
    </script>

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

@endpush
