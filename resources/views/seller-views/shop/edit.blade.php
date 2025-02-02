@extends('layouts.back-end.app-seller')
@section('title', translate('Shop Edit'))
@push('css_or_js')
    <!-- Custom styles for this page -->
    <link href="{{asset('assets/back-end')}}/vendor/datatables/dataTables.bootstrap4.min.css" rel="stylesheet">
    <!-- Custom styles for this page -->
    <link href="{{asset('assets/back-end/css/croppie.css')}}" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endpush
@section('content')
    <!-- Content Row -->
    <div class="content container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h1 class="h3 mb-0 ">{{translate('Edit Shop Info')}}</h1>
                    </div>
                    <div class="card-body">
                        <form action="{{route('seller.order.update',[$shop->id])}}" method="post"
                              style="text-align: {{Session::get('direction') === "rtl" ? 'right' : 'left'}};"
                              enctype="multipart/form-data">
                            @csrf
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="name">{{translate('Shop Name')}} <span
                                                class="text-danger">*</span></label>
                                        <input type="text" name="name" value="{{$shop->name}}" class="form-control"
                                               id="name"
                                               required>
                                    </div>
                                    <div class="form-group">
                                        <label for="name">{{translate('Contact')}}  </label>
                                        <input type="number" name="contact" value="{{$shop->contact}}"
                                               class="form-control" id="name"
                                               required>
                                    </div>
                                    <div class="form-group">
                                        <label for="address">{{translate('Address')}} <span
                                                class="text-danger">*</span></label>
                                        <textarea type="text" rows="4" name="address" class="form-control"
                                                  id="address"
                                                  required>{{$shop->address}}</textarea>
                                    </div>


                                    <div class="form-group ">
                                        <select name="district_id" class="form-control" id="district_id">
                                            <option value="">{{translate('Select District')}}</option>
                                            @foreach ($districts as $district)
                                                <option value="{{$district->id}}">{{$district->name}}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="form-group ">
                                        <select name="area_id" class="form-control" id="area_id">
                                            <option value="">{{translate('Select Area')}}</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="name">{{translate('Upload')}} {{translate('image')}}</label>
                                        <div class="custom-file text-left">
                                            <input type="file" name="image" id="customFileUpload"
                                                   class="custom-file-input"
                                                   accept=".jpg, .png, .jpeg, .gif, .bmp, .tif, .tiff|image/*">
                                            <label class="custom-file-label"
                                                   for="customFileUpload">{{translate('choose')}} {{translate('file')}}</label>
                                        </div>
                                    </div>
                                    <div class="text-center">
                                        <img
                                            style="width: auto;border: 1px solid; border-radius: 10px; max-height:200px;"
                                            id="viewer"
                                            onerror="this.src='{{asset('assets/front-end/img/image-place-holder.png')}}'"
                                            src="{{asset('storage/order/'.$shop->image)}}" alt="Product thumbnail"/>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-4 mt-2">
                                    <div class="form-group">
                                        <div class="flex-start">
                                            <div for="name">{{translate('Upload')}} {{translate('Banner')}} </div>
                                            <div class="mx-1" for="ratio"><small
                                                    style="color: red">{{translate('Ratio')}} : ( 6:1
                                                    )</small></div>
                                        </div>
                                        <div class="custom-file text-left">
                                            <input type="file" name="banner" id="BannerUpload" class="custom-file-input"
                                                   accept=".jpg, .png, .jpeg, .gif, .bmp, .tif, .tiff|image/*">
                                            <label class="custom-file-label"
                                                   for="BannerUpload">{{translate('choose')}} {{translate('file')}}</label>
                                        </div>
                                    </div>
                                    <div class="text-center">
                                        <img
                                            style="width: auto; height:auto; border: 1px solid; border-radius: 10px; max-height:200px"
                                            id="viewerBanner"
                                            onerror="this.src='{{asset('assets/front-end/img/image-place-holder.png')}}'"
                                            src="{{asset('storage/order/banner/'.$shop->banner)}}"
                                            alt="Product thumbnail"/>
                                    </div>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary"
                                    id="btn_update">{{translate('Update')}}</button>
                            <a class="btn btn-danger"
                               href="{{route('seller.order.view')}}">{{translate('Cancel')}}</a>
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

        function readBannerURL(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();

                reader.onload = function (e) {
                    $('#viewerBanner').attr('src', e.target.result);
                }

                reader.readAsDataURL(input.files[0]);
            }
        }

        $("#customFileUpload").change(function () {
            readURL(this);
        });

        $("#BannerUpload").change(function () {
            readBannerURL(this);
        });

        $('#district_id').on('change', function () {
            var district_id = $('#district_id').val();
            if (district_id) {
                $.ajax({
                    url: "{{  url('/api/v1/address?district_id=') }}" + district_id,
                    type: "GET",
                    dataType: "json",
                    success: function (data) {
                        $('#area_id').empty();

                        var options = '<option value="">{{translate('Select District')}}</option>';

                        $.each(data, function (key, value) {
                            options += '<option value="' + value.id + '">' + value.name + '</option>';
                        });

                        $('#area_id').html(options);
                    },
                });
            }
        });

        // make active the previous selected district and area

        window.addEventListener('load', function () {
            $.ajax({
                url: "{{  url('/api/v1/address?area_id=') }}" + {{$shop->area_id??1}},
                type: "GET",
                dataType: "json",
                success: function (data) {
                    $('#district_id').val(data.district.id);

                    $('#area_id').empty();

                    var options = `<option value="${data.id}">${data.name}</option>`;

                    $('#area_id').html(options);
                },
            });
        });
    </script>

@endpush
