@extends('layouts.back-end.app')

@section('title', translate('BlogCategory'))

@push('css_or_js')

@endpush

@section('content')
    <div class="content container-fluid">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a
                            href="{{route('admin.dashboard')}}">{{translate('Dashboard')}}</a>
                </li>
                <li class="breadcrumb-item" aria-current="page">{{translate('blog_category')}}</li>
            </ol>
        </nav>

        <!-- Content Row -->
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        {{ translate('blog_category_form')}}
                    </div>
                    <div class="card-body"
                         style="text-align: {{Session::get('direction') === "rtl" ? 'right' : 'left'}};">
                        <form action="{{route('admin.blog_category.update',[$blog_category['id']])}}" method="POST"
                              enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="id" value="{{ $blog_category->id }}">
                            <div class="row">
                                <div class="col-6">
                                    <div class="form-group ">
                                        <label class="input-label" for="">{{translate('name')}}</label>
                                        <input type="text" name="name" class="form-control"
                                               value="{{$blog_category->name}}">
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="form-group ">
                                        <label class="input-label"
                                               for="">{{translate('is_filterable')}}</label>
                                        <input type="radio" name="is_filterable"
                                               {{ $blog_category->is_filterable?'checked':''}} class="form-control-"
                                               value="1"> Yes
                                        <input type="radio" name="is_filterable"
                                               {{ !$blog_category->is_filterable?'checked':''}} class="form-control-"
                                               value="0"> No
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
