@extends('layouts.back-end.app')
@section('title', translate('Banner'))
@push('css_or_js')
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endpush

@section('content')
    <div class="content container-fluid">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a
                        href="{{route('admin.dashboard')}}">{{translate('Dashboard')}}</a>
                </li>
                <li class="breadcrumb-item" aria-current="page">{{translate('Banner')}}</li>
            </ol>
        </nav>
        <!-- Content Row -->
        <div class="row pt-4" style="text-align: {{Session::get('direction') === "rtl" ? 'right' : 'left'}};">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        {{ translate('banner_update_form')}}
                    </div>
                    <div class="card-body">
                        <form action="{{route('admin.banner.update',[$banner['id']])}}" method="post"
                              enctype="multipart/form-data"
                              class="banner_form">
                            @csrf
                            @method('put')
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <input type="hidden" id="id" name="id">
                                            <label for="name">{{ translate('banner_url')}}</label>
                                            <input type="text" name="url" class="form-control"
                                                   value="{{$banner['url']}}" required>
                                        </div>

                                        <div class="form-group">
                                            <label for="name">{{translate('banner_type')}}</label>
                                            <select style="width: 100%"
                                                    class="js-example-responsive form-control"
                                                    name="banner_type" required>
                                                <option
                                                    value="Main Banner" {{$banner['banner_type']=='Main Banner'?'selected':''}}>
                                                    Main Banner
                                                </option>
                                                <option
                                                    value="Footer Banner" {{$banner['banner_type']=='Footer Banner'?'selected':''}}>
                                                    Footer Banner
                                                </option>
                                                <option
                                                    value="Popup Banner" {{$banner['banner_type']=='Popup Banner'?'selected':''}}>
                                                    Popup Banner
                                                </option>
                                            </select>
                                        </div>

                                        <div class="form-group">
                                            <label
                                                for="resource_id">{{translate('resource_type')}}</label>
                                            <select style="width: 100%" onchange="display_data(this.value)"
                                                    class="js-example-responsive form-control"
                                                    name="resource_type">
                                                <option
                                                    value="product" {{$banner['resource_type']=='product'?'selected':''}}>
                                                    Product
                                                </option>
                                                <option
                                                    value="category" {{$banner['resource_type']=='category'?'selected':''}}>
                                                    Category
                                                </option>
                                                <option value="shop" {{$banner['resource_type']=='order'?'selected':''}}>
                                                    Shop
                                                </option>
                                                <option
                                                    value="brand" {{$banner['resource_type']=='brand'?'selected':''}}>
                                                    Brand
                                                </option>
                                            </select>
                                        </div>

                                        <div class="form-group" id="resource-product"
                                             style="display: {{$banner['resource_type']=='product'?'block':'none'}}">
                                            <label for="product_id">{{translate('product')}}</label>
                                            <select style="width: 100%"
                                                    class="js-example-responsive form-control"
                                                    name="product_id" required>
                                                @foreach(\App\Models\Product::active()->get() as $product)
                                                    <option
                                                        value="{{$product['id']}}" {{$banner['resource_id']==$product['id']?'selected':''}}>{{$product['name']}}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="form-group" id="resource-category"
                                             style="display: {{$banner['resource_type']=='category'?'block':'none'}}">
                                            <label for="name">{{translate('category')}}</label>
                                            <select style="width: 100%"
                                                    class="js-example-responsive form-control"
                                                    name="category_id" required>
                                                @foreach(\App\Services\CategoryManager::parents() as $category)
                                                    <option
                                                        value="{{$category['id']}}" {{$banner['resource_id']==$category['id']?'selected':''}}>{{$category['name']}}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="form-group" id="resource-brand"
                                             style="display: {{$banner['resource_type']=='brand'?'block':'none'}}">
                                            <label for="brand_id">{{translate('brand')}}</label>
                                            <select style="width: 100%"
                                                    class="js-example-responsive form-control"
                                                    name="brand_id" required>
                                                @foreach(\App\Models\Brand::all() as $brand)
                                                    <option
                                                        value="{{$brand['id']}}" {{$banner['resource_id']==$brand['id']?'selected':''}}>{{$brand['name']}}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <label for="name">{{ translate('Image')}}</label><span
                                            class="badge badge-soft-danger">( {{translate('ratio')}} 4:1 )</span>
                                        <br>
                                        <div class="custom-file" style="text-align: left">
                                            <input type="file" name="image" id="mbimageFileUploader"
                                                   class="custom-file-input"
                                                   accept=".jpg, .png, .jpeg, .gif, .bmp, .tif, .tiff|image/*">
                                            <label class="custom-file-label"
                                                   for="mbimageFileUploader">{{translate('choose')}} {{translate('file')}}</label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <center>
                                            <img
                                                style="width: auto;border: 1px solid; border-radius: 10px; max-width:400px;"
                                                id="mbImageviewer"
                                                src="{{asset('storage/banner')}}/{{$banner['photo']}}"
                                                alt="banner image"/>
                                        </center>
                                    </div>

                                    <div class="col-md-12 mt-3">
                                        <button type="submit"
                                                class="btn btn-primary">{{ translate('update')}}</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script>
        $(".js-example-theme-single").select2({
            theme: "classic"
        });

        $(".js-example-responsive").select2({
            // dir: "rtl",
            width: 'resolve'
        });

        function display_data(data) {

            $('#resource-product').hide()
            $('#resource-brand').hide()
            $('#resource-category').hide()
            $('#resource-order').hide()

            if (data === 'product') {
                $('#resource-product').show()
            } else if (data === 'brand') {
                $('#resource-brand').show()
            } else if (data === 'category') {
                $('#resource-category').show()
            } else if (data === 'order') {
                $('#resource-order').show()
            }
        }
    </script>

    <script>
        function mbimagereadURL(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();

                reader.onload = function (e) {
                    $('#mbImageviewer').attr('src', e.target.result);
                }

                reader.readAsDataURL(input.files[0]);
            }
        }

        $("#mbimageFileUploader").change(function () {
            mbimagereadURL(this);
        });
    </script>
@endpush