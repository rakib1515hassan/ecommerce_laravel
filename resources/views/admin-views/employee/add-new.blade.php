@extends('layouts.back-end.app')
@section('title', translate('Employee Add'))
@push('css_or_js')

    <meta name="csrf-token" content="{{ csrf_token() }}">
@endpush

@section('content')
    <div class="content container-fluid">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a
                            href="{{route('admin.dashboard')}}">{{translate('Dashboard')}}</a></li>
                <li class="breadcrumb-item" aria-current="page">{{translate('employee_add')}}</li>
            </ol>
        </nav>

        <!-- Content Row -->
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        {{translate('employee_form')}}
                    </div>
                    <div class="card-body">
                        <form action="{{route('admin.employee.add-new')}}" method="post" enctype="multipart/form-data"
                              style="text-align: {{Session::get('direction') === "rtl" ? 'right' : 'left'}};">
                            @csrf
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-6">
                                        <label for="name">{{translate('Name')}}</label>
                                        <input type="text" name="name" class="form-control" id="name"
                                               placeholder="{{translate('Ex')}} : {{translate('Md. Al Imrun')}}"
                                               value="{{old('name')}}" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="name">{{translate('Phone')}}</label>
                                        <input type="text" name="phone" value="{{old('phone')}}" class="form-control"
                                               id="phone"
                                               placeholder="{{translate('Ex')}} : +88017********">
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-6">
                                        <label for="name">{{translate('Email')}}</label>
                                        <input type="email" name="email" value="{{old('email')}}" class="form-control"
                                               id="email"
                                               placeholder="{{translate('Ex')}} : ex@gmail.com" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="name">{{translate('Role')}}</label>
                                        <select class="form-control" name="role_id"
                                                style="width: 100%">
                                            <option value="0" selected disabled>---{{translate('select')}}
                                                ---
                                            </option>
                                            @foreach($rls as $r)
                                                <option value="{{$r->id}}">{{$r->name}}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                </div>
                            </div>

                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-6">
                                        <label for="name">{{translate('password')}}</label>
                                        <input type="password" name="password" class="form-control" id="password"
                                               placeholder="{{translate('Password')}}">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="name">{{translate('employee_image')}}</label><span
                                                class="badge badge-soft-danger">( {{translate('ratio')}} 1:1 )</span>
                                        <br>
                                        <div class="form-group">
                                            <div class="custom-file text-left">
                                                <input type="file" name="image" id="customFileUpload"
                                                       class="custom-file-input"
                                                       accept=".jpg, .png, .jpeg, .gif, .bmp, .tif, .tiff|image/*">
                                                <label class="custom-file-label"
                                                       for="customFileUpload">{{translate('choose')}} {{translate('file')}}</label>
                                            </div>
                                        </div>
                                        <div class="text-center">
                                            <img style="width: auto;border: 1px solid; border-radius: 10px; max-height:200px;"
                                                 id="viewer"
                                                 src="{{asset('assets\back-end\img\400x400\img2.jpg')}}"
                                                 alt="Product thumbnail"/>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary">{{translate('submit')}}</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')

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

        $(".js-example-theme-single").select2({
            theme: "classic"
        });

        $(".js-example-responsive").select2({
            width: 'resolve'
        });
    </script>
@endpush
