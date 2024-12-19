@extends('layouts.back-end.app-seller')

@section('title',translate('Update Product Manager'))

@push('css_or_js')

@endpush

@section('content')
    <div class="content container-fluid">
        <!-- Page Header -->
        <div class="page-header">
            <div class="row align-items-center">
                <div class="col-sm mb-2 mb-sm-0">
                    <h1 class="page-header-title"><i
                                class="tio-edit"></i> {{translate('update')}} {{translate('Product Manager')}}
                    </h1>
                </div>
            </div>
        </div>
        <!-- End Page Header -->
        <div class="row gx-2 gx-lg-3">
            <div class="col-sm-12 col-lg-12 mb-3 mb-lg-2">
                <div class="card">
                    <div class="card-body">
                        <form action="{{route('seller.product_manager.update',[$product_manager['id']])}}" method="post"
                              enctype="multipart/form-data">
                            @csrf
                            <div class="row">
                                <div class="col-md-6 col-12">
                                    <div class="form-group">
                                        <label class="input-label"
                                               for="exampleFormControlInput1">{{translate('first')}} {{translate('name')}}</label>
                                        <input type="text" value="{{$product_manager['f_name']}}" name="f_name"
                                               class="form-control" placeholder="New Product-manager"
                                               required>
                                    </div>
                                </div>
                                <div class="col-md-6 col-12">
                                    <div class="form-group">
                                        <label class="input-label"
                                               for="exampleFormControlInput1">{{translate('last')}} {{translate('name')}}</label>
                                        <input type="text" value="{{$product_manager['l_name']}}" name="l_name"
                                               class="form-control" placeholder="Last Name"
                                               required>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 col-12">
                                    <div class="form-group">
                                        <label class="input-label"
                                               for="exampleFormControlInput1">{{translate('email')}}</label>
                                        <input type="email" value="{{$product_manager['email']}}" name="email"
                                               class="form-control"
                                               placeholder="Ex : ex@example.com"
                                               required>
                                    </div>
                                </div>
                                <div class="col-md-6 col-12">
                                    <div class="form-group">
                                        <label class="input-label"
                                               for="exampleFormControlInput1">{{translate('phone')}}</label>
                                        <input type="text" name="phone" value="{{$product_manager['phone']}}"
                                               class="form-control"
                                               placeholder="Ex : 017********"
                                               required>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 col-12">
                                    <div class="form-group">
                                        <label class="input-label"
                                               for="exampleFormControlInput1">{{translate('identity')}} {{translate('type')}}</label>
                                        <select name="identity_type" class="form-control">
                                            <option
                                                    value="passport" {{$product_manager['identity_type']=='passport'?'selected':''}}>
                                                {{translate('passport')}}
                                            </option>
                                            <option
                                                    value="driving_license" {{$product_manager['identity_type']=='driving_license'?'selected':''}}>
                                                {{translate('driving')}} {{translate('license')}}
                                            </option>
                                            <option value="nid" {{$product_manager['identity_type']=='nid'?'selected':''}}>{{translate('nid')}}
                                            </option>
                                            <option
                                                    value="company_id" {{$product_manager['identity_type']=='company_id'?'selected':''}}>
                                                {{translate('company')}} {{translate('id')}}
                                            </option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6 col-12">
                                    <div class="form-group">
                                        <label class="input-label"
                                               for="exampleFormControlInput1">{{translate('identity')}} {{translate('number')}}</label>
                                        <input type="text" name="identity_number"
                                               value="{{$product_manager['identity_number']}}"
                                               class="form-control"
                                               placeholder="Ex : DH-23434-LS"
                                               required>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="input-label"
                                       for="exampleFormControlInput1">{{translate('password')}}</label>
                                <input type="text" name="password" class="form-control" placeholder="Ex : password">
                            </div>

                            <div class="form-group">
                                <label>{{translate('product_manager')}} {{translate('image')}}</label><small
                                        style="color: red">* ( {{translate('ratio')}} 1:1 )</small>
                                <div class="custom-file">
                                    <input type="file" name="image" id="customFileEg1" class="custom-file-input"
                                           accept=".jpg, .png, .jpeg, .gif, .bmp, .tif, .tiff|image/*">
                                    <label class="custom-file-label"
                                           for="customFileEg1">{{translate('choose')}} {{translate('file')}}</label>
                                </div>
                                <hr>
                                <center>
                                    <img style="height: 200px;border: 1px solid; border-radius: 10px;" id="viewer"
                                         src="{{asset('storage/product_manager').'/'.$product_manager['image']}}"
                                         alt="product_manager image"/>
                                </center>
                            </div>
                            <hr>
                            <button type="submit" class="btn btn-primary">{{translate('submit')}}</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

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

        $("#customFileEg1").change(function () {
            readURL(this);
        });
    </script>

@endpush
