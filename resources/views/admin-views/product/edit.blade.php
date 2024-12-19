@extends('layouts.back-end.app')

@section('title', translate('Product Edit'))

@push('css_or_js')
    <link href="{{asset('assets/back-end/css/tags-input.min.css')}}" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endpush

@section('content')
    <!-- Page Heading -->
    <div class="content container-fluid">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a
                        href="{{route('admin.dashboard')}}">{{translate('Dashboard')}}</a>
                </li>
                <li class="breadcrumb-item" aria-current="page">
                    <a href="{{route('admin.product.list', ['in_house', ''])}}">{{translate('Product')}}</a>
                </li>
                <li class="breadcrumb-item" aria-current="page">{{translate('Edit')}}</li>
            </ol>
        </nav>

        <!-- Content Row -->
        <div class="row">
            <div class="col-md-12">
                <form class="product-form" action="{{route('admin.product.update',$product->id)}}" method="post"
                      style="text-align: {{Session::get('direction') === "rtl" ? 'right' : 'left'}};"
                      enctype="multipart/form-data"
                      id="product_form">
                    @csrf

                    <div class="card">
                        <div class="card-header">
                            @php($language=\App\Models\BusinessSetting::where('type','pnc_language')->first())
                            @php($language = $language->value ?? null)
                            @php($default_lang = 'en')

                            @php($default_lang = json_decode($language)[0])
                            <ul class="nav nav-tabs mb-4">
                                @foreach(json_decode($language) as $lang)
                                    <li class="nav-item">
                                        <a class="nav-link lang_link {{$lang == $default_lang? 'active':''}}" href="#"
                                           id="{{$lang}}-link">{{\App\Services\AdditionalServices::get_language_name($lang).'('.strtoupper($lang).')'}}</a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>

                        <div class="card-body">
                            @foreach(json_decode($language) as $lang)
                                    <?php
                                    if (count($product['translations'])) {
                                        $translate = [];
                                        foreach ($product['translations'] as $t) {

                                            if ($t->locale == $lang && $t->key == "name") {
                                                $translate[$lang]['name'] = $t->value;
                                            }
                                            if ($t->locale == $lang && $t->key == "description") {
                                                $translate[$lang]['description'] = $t->value;
                                            }

                                        }
                                    }
                                    ?>
                                <div class="{{$lang != 'en'? 'd-none':''}} lang_form" id="{{$lang}}-form">
                                    <div class="form-group">
                                        <label class="input-label"
                                               for="{{$lang}}_name">{{translate('name')}}
                                            ({{strtoupper($lang)}})</label>
                                        <input type="text" {{$lang == 'en'? 'required':''}} name="name[]"
                                               id="{{$lang}}_name"
                                               value="{{$translate[$lang]['name']??$product['name']}}"
                                               class="form-control"
                                               placeholder="{{translate('New Product')}}"
                                               required>
                                    </div>
                                    <input type="hidden" name="lang[]" value="{{$lang}}">
                                    <div class="form-group pt-4">
                                        <label class="input-label">{{translate('description')}}
                                            ({{strtoupper($lang)}})</label>
                                        <textarea name="description[]" style="display:none" class="textarea"
                                                  required>{!! $translate[$lang]['description']??$product['details'] !!}</textarea>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="card mt-2 rest-part">
                        <div class="card-header">
                            <h4>{{translate('General Info')}}</h4>
                        </div>
                        <div class="card-body">
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-4">
                                        <label for="name">{{translate('Category')}}</label>
                                        <select
                                            class="js-example-basic-multiple js-states js-example-responsive form-control"
                                            name="category_id"
                                            id="category_id"
                                            onchange="getRequest('{{admin_url()}}/product/get-categories?parent_id='+this.value,'sub-category-select','select')">
                                            <option value="0" selected disabled>---{{translate('Select')}}
                                                ---
                                            </option>
                                            @foreach($categories as $category)
                                                <option
                                                    value="{{$category['id']}}" {{ $category->id==$product_category[0]->id ? 'selected' : ''}} >{{$category['name']}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label for="name">{{translate('Sub Category')}}</label>
                                        <select
                                            class="js-example-basic-multiple js-states js-example-responsive form-control"
                                            name="sub_category_id" id="sub-category-select"
                                            data-id="{{count($product_category)>=2?$product_category[1]->id:''}}"
                                            onchange="getRequest('{{admin_url()}}/product/get-categories?parent_id='+this.value,'sub-sub-category-select','select')">
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label for="name">{{translate('Sub Sub Category')}}</label>

                                        <select
                                            class="js-example-basic-multiple js-states js-example-responsive form-control"
                                            data-id="{{count($product_category)>=3?$product_category[2]->id:''}}"
                                            name="sub_sub_category_id" id="sub-sub-category-select">

                                        </select>
                                    </div>
                                </div>

                            </div>

                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-6">
                                        <label for="name">{{translate('Brand')}}</label>
                                        <select
                                            class="js-example-basic-multiple js-states js-example-responsive form-control"
                                            name="brand_id">
                                            <option value="{{null}}" selected disabled>
                                                ---{{translate('Select')}}---
                                            </option>
                                            @foreach($br as $b)
                                                <option
                                                    value="{{$b['id']}}" {{ $b->id==$product->brand_id ? 'selected' : ''}} >{{$b['name']}}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="col-md-6">
                                        <label for="name">{{translate('Unit')}}</label>
                                        <select
                                            class="js-example-basic-multiple js-states js-example-responsive form-control"
                                            name="unit">
                                            @foreach(\App\Services\AdditionalServices::units() as $x)
                                                <option
                                                    value={{$x}} {{ $product->unit==$x ? 'selected' : ''}}>{{$x}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-6">
                                        <label for="name">{{translate('Weight')}}</label>
                                        <input type="number" name="weight" class="form-control"
                                               placeholder="Enter Weight" value="{{$product['weight']}}" required>
                                    </div>

                                    <div class="col-md-6">
                                        <label for="name">{{translate('Warranty and Policy')}}</label>
                                        <input type="text" name="policy" class="form-control" placeholder="Enter Policy"
                                               value="{{$product['policy']}}"/>
                                    </div>
                                </div>
                            </div>

                            {{--   product manager info--}}
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-6">
                                        <label for="name">{{ translate('Product Manager Name') }}</label>

                                        <select
                                            class="js-example-basic-multiple js-states js-example-responsive form-control"
                                            name="product_manager_id"
                                            required>
                                            <option selected>
                                                ---{{translate('Select')}}---
                                            </option>
                                            @foreach($product_managers as $product_manager)
                                                <option
                                                    value="{{$product_manager->id}}" {{old('product_manager_id', $product['product_manager_id']) == $product_manager->id ? 'selected':''}}>
                                                    {{$product_manager->f_name}} {{$product_manager->l_name}}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="col-md-6">
                                        <label
                                            for="product_manager_amount">{{ translate('Product Manager Amount') }}</label>
                                        <input type="number" name="product_manager_amount" class="form-control"
                                               placeholder="Enter Product Manager Amount"
                                               value="{{ old('product_manager_amount', $product['product_manager_amount']) }}"
                                               required>
                                    </div>
                                </div>
                            </div>

                            {{--  admin and resaller info--}}
                            <div class="form-group">
                                <div class="row">
                                    @if($seller && $seller->admin_manage == 1)
                                        <div class="col-md-6">
                                            <label for="is_admin_manage" class="mt-4">
                                                Admin Manage :
                                            </label>
                                            <label class="switch">
                                                <input type="checkbox" class="status" value="1"
                                                       name="is_admin_manage" {{$product['is_admin_manage']==1?'checked':''}}>
                                                <span class="slider round"></span>
                                            </label>
                                        </div>

                                        <div class="col-md-6">
                                            <label for="">{{ translate('Seller Amount') }}</label>
                                            <input type="number" name="seller_amount" class="form-control"
                                                   placeholder="Enter Seller Amount"
                                                   value="{{ old('seller_amount', $product['seller_amount']) }}"
                                                   required>
                                        </div>

                                        <script>
                                            var is_admin_manage = document.querySelector('input[name="is_admin_manage"]');
                                            var seller_amount = document.querySelector('input[name="seller_amount"]');

                                            is_admin_manage.addEventListener('change', function () {
                                                seller_amount.disabled = !is_admin_manage.checked;
                                                seller_amount.required = is_admin_manage.checked;
                                                seller_amount.value = is_admin_manage.checked ? '{{$product['seller_amount']}}' : '0';
                                            });
                                        </script>
                                    @endif
                                </div>
                            </div>

                            {{--  resaller info--}}
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-6">
                                        <label for="">{{ translate('Reseller Amount') }}</label>
                                        <input type="number" name="reseller_amount" class="form-control"
                                               placeholder="Enter Reseller Amount"
                                               value="{{ old('reseller_amount', $product['reseller_amount']) }}" required>
                                    </div>

                                    <div class="col-md-6">
                                        <label for="">{{ translate('Product SKU') }}</label>
                                        <input type="text" name="sku" class="form-control"
                                               placeholder="Enter Product SKU"
                                               value="{{ old('sku', $product['sku']) }}">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card mt-2 rest-part">
                        <div class="card-header">
                            <h4>{{translate('Variation')}}</h4>
                        </div>
                        <div class="card-body">

                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-6">

                                        <label for="colors">
                                            {{translate('Colors')}} :
                                        </label>
                                        <label class="switch">
                                            <input type="checkbox" class="status"
                                                   name="colors_active" {{count($product['colors'])>0?'checked':''}}>
                                            <span class="slider round"></span>
                                        </label>

                                        <select
                                            class="js-example-basic-multiple js-states js-example-responsive form-control color-var-select"
                                            name="colors[]" multiple="multiple"
                                            id="colors-selector" {{count($product['colors'])>0?'':'disabled'}}>
                                            @foreach (\App\Models\Color::orderBy('name', 'asc')->get() as $key => $color)
                                                <option
                                                    value={{ $color->code }} {{in_array($color->code,$product['colors'])?'selected':''}}>
                                                    {{$color['name']}}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="col-md-6">
                                        <label for="attributes" style="padding-bottom: 3px">
                                            {{translate('Attributes')}} :
                                        </label>
                                        <select
                                            class="js-example-basic-multiple js-states js-example-responsive form-control"
                                            name="choice_attributes[]" id="choice_attributes" multiple="multiple">
                                            @foreach (\App\Models\Attribute::orderBy('name', 'asc')->get() as $key => $a)
                                                @if($product['attributes']!='null')
                                                    <option
                                                        value="{{ $a['id']}}" {{in_array($a->id,json_decode($product['attributes'],true))?'selected':''}}>
                                                        {{$a['name']}}
                                                    </option>
                                                @else
                                                    <option value="{{ $a['id']}}">{{$a['name']}}</option>
                                                @endif
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="col-md-12 mt-2 mb-2">
                                        <div class="customer_choice_options" id="customer_choice_options">
                                            @include('admin-views.product.partials._choices',['choice_no'=>json_decode($product['attributes']),'choice_options'=>json_decode($product['choice_options'],true)])
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card mt-2 rest-part">
                        <div class="card-header">
                            <h4>{{translate('Product price & stock')}}</h4>
                        </div>
                        <div class="card-body">
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-6">
                                        <label class="control-label">MRP</label>
                                        <input type="number" min="0" step="0.01"
                                               placeholder="{{translate('Unit price') }}"
                                               name="unit_price" class="form-control"
                                               value={{\App\Services\Converter::default($product->unit_price)}} required>
                                    </div>
                                    <div class="col-md-6">
                                        <label
                                            class="control-label">{{translate('Purchase price')}}</label>
                                        <input type="number" min="0" step="0.01"
                                               placeholder="{{translate('Purchase price') }}"
                                               name="purchase_price" class="form-control"
                                               value={{ \App\Services\Converter::default($product->purchase_price) }} required>
                                    </div>

                                    <div class="col-md-5">
                                        <label class="control-label">{{translate('Tax')}}</label>
                                        <label class="badge badge-info">{{translate('Percent')}} ( %
                                            )</label>
                                        <input type="number" min="0" value={{ $product->tax }} step="0.01"
                                               placeholder="{{translate('Tax') }}" name="tax"
                                               class="form-control" required>
                                        <input name="tax_type" value="percent" style="display: none">
                                    </div>

                                    <div class="col-md-5">
                                        <label class="control-label">{{translate('Discount')}}</label>
                                        <input type="number" min="0"
                                               value={{ $product->discount_type=='flat'?\App\Services\Converter::default($product->discount): $product->discount}} step="0.01"
                                               placeholder="{{translate('Discount') }}" name="discount"
                                               class="form-control" required>
                                    </div>
                                    <div class="col-md-2" style="padding-top: 30px;">
                                        <select
                                            style="width: 100%"
                                            class="js-example-basic-multiple js-states js-example-responsive demo-select2"
                                            name="discount_type">
                                            <option
                                                value="percent" {{$product['discount_type']=='percent'?'selected':''}}>{{translate('Percent')}}</option>
                                            <option
                                                value="flat" {{$product['discount_type']=='flat'?'selected':''}}>{{translate('Flat')}}</option>

                                        </select>
                                    </div>
                                    <div class="col-12 pt-4 sku_combination" id="sku_combination">
                                        @include('admin-views.product.partials._edit_sku_combinations',['combinations'=>json_decode($product['variation'],true)])
                                    </div>
                                    <div class="col-md-6" id="quantity">
                                        <label
                                            class="control-label">{{translate('total')}} {{translate('Quantity')}}</label>
                                        <input type="number" min="0" value={{ $product->current_stock }} step="1"
                                               placeholder="{{translate('Quantity') }}"
                                               name="current_stock" class="form-control" required>
                                    </div>
                                </div>
                            </div>
                            <br>
                        </div>
                    </div>

                    <div class="card mt-2 mb-2 rest-part">
                        <div class="card-header">
                            <h4>{{translate('seo_section')}}</h4>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-12 mb-4">
                                    <label class="control-label">{{translate('Meta Title')}}</label>
                                    <input type="text" name="meta_title" value="{{$product['meta_title']}}"
                                           placeholder="" class="form-control">
                                </div>

                                <div class="col-md-8 mb-4">
                                    <label class="control-label">{{translate('Meta Description')}}</label>
                                    <textarea rows="10" type="text" name="meta_description"
                                              class="form-control">{{$product['meta_description']}}</textarea>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group mb-0">
                                        <label>{{translate('Meta Image')}}</label>
                                    </div>
                                    <div class="border-dashed">
                                        <div class="row" id="meta_img">
                                            <div class="col-6">
                                                <div class="card">
                                                    <div class="card-body">
                                                        <img style="width: 100%" height="auto"
                                                             onerror="this.src='{{asset('assets/front-end/img/image-place-holder.png')}}'"
                                                             src="{{asset("storage/product/meta")}}/{{$product['meta_image']}}"
                                                             alt="Meta image">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card mt-2 rest-part">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-12 mb-4">
                                    <label
                                        class="control-label">{{translate('Youtube video link')}}</label>
                                    <small class="badge badge-soft-danger">
                                        ( {{translate('optional, please provide embed link not direct link')}}
                                        .
                                        )</small>
                                    <input type="text" value="{{$product['video_url']}}" name="video_link"
                                           placeholder="{{translate('EX')}} : https://www.youtube.com/embed/5R06LRdUCSE"
                                           class="form-control" required>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>{{translate('Upload product images')}}</label><small
                                            style="color: red">* ( {{translate('ratio')}} 1:1
                                            )</small>
                                    </div>
                                    <div class="p-2 border border-dashed" style="max-width:430px;">
                                        <div class="row" id="coba">
                                            @foreach (json_decode($product->images) as $key => $photo)
                                                <div class="col-6">
                                                    <div class="card">
                                                        <div class="card-body">
                                                            <img style="width: 100%" height="auto"
                                                                 onerror="this.src='{{asset('assets/front-end/img/image-place-holder.png')}}'"
                                                                 src="{{asset("storage/product/$photo")}}"
                                                                 alt="Product image">
                                                            <a href="{{route('admin.product.remove-image',['id'=>$product['id'],'name'=>$photo])}}"
                                                               class="btn btn-danger btn-block">{{translate('Remove')}}</a>

                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>

                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="name">{{translate('Upload thumbnail')}}</label><small
                                            style="color: red">* ( {{translate('ratio')}} 1:1
                                            )</small>
                                    </div>

                                    <div class="row" id="thumbnail">
                                        <div class="col-6">
                                            <div class="card">
                                                <div class="card-body">
                                                    <img style="width: 100%" height="auto"
                                                         onerror="this.src='{{asset('assets/front-end/img/image-place-holder.png')}}'"
                                                         src="{{asset("storage/product")}}/{{$product['thumbnail']}}"
                                                         alt="Product image">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card card-footer">
                        <div class="row">
                            <div class="col-md-12" style="padding-top: 20px">
                                @if($product->request_status == 2)
                                    <button type="button" onclick="check()"
                                            class="btn btn-primary">{{translate('Update & Publish')}}</button>
                                @else
                                    <button type="button" onclick="check()"
                                            class="btn btn-primary">{{translate('Update')}}</button>
                                @endif
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('script_2')
    <script src="{{asset('assets/back-end')}}/js/tags-input.min.js"></script>
    <script src="{{asset('assets/back-end/js/spartan-multi-image-picker.js')}}"></script>
    <script>
        var imageCount = {{4-count(json_decode($product->images))}};
        var thumbnail = '{{\App\Services\ProductManager::product_image_path('thumbnail').'/'.$product->thumbnail??asset('assets/back-end/img/400x400/img2.jpg')}}';
        $(function () {
            if (imageCount > 0) {
                $("#coba").spartanMultiImagePicker({
                    fieldName: 'images[]',
                    maxCount: imageCount,
                    rowHeight: 'auto',
                    groupClassName: 'col-6',
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
            }

            $("#thumbnail").spartanMultiImagePicker({
                fieldName: 'image',
                maxCount: 1,
                rowHeight: 'auto',
                groupClassName: 'col-6',
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

            $("#meta_img").spartanMultiImagePicker({
                fieldName: 'meta_image',
                maxCount: 1,
                rowHeight: 'auto',
                groupClassName: 'col-6',
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

    <script>
        function getRequest(route, id, type) {
            $.get({
                url: route,
                dataType: 'json',
                success: function (data) {
                    if (type == 'select') {
                        $('#' + id).empty().append(data.select_tag);
                    }
                },
            });
        }

        $('input[name="colors_active"]').on('change', function () {
            if (!$('input[name="colors_active"]').is(':checked')) {
                $('#colors-selector').prop('disabled', true);
            } else {
                $('#colors-selector').prop('disabled', false);
            }
        });

        $('#choice_attributes').on('change', function () {
            $('#customer_choice_options').html(null);
            $.each($("#choice_attributes option:selected"), function () {
                //console.log($(this).val());
                add_more_customer_choice_option($(this).val(), $(this).text());
            });
        });

        function add_more_customer_choice_option(i, name) {
            let n = name.split(' ').join('');
            $('#customer_choice_options').append('<div class="row"><div class="col-md-3"><input type="hidden" name="choice_no[]" value="' + i + '"><input type="text" class="form-control" name="choice[]" value="' + n + '" placeholder="{{translate('Choice Title') }}" readonly></div><div class="col-lg-9"><input type="text" class="form-control" name="choice_options_' + i + '[]" placeholder="{{translate('Enter choice values') }}" data-role="tagsinput" onchange="update_sku()"></div></div>');
            $("input[data-role=tagsinput], select[multiple][data-role=tagsinput]").tagsinput();
        }

        setTimeout(function () {
            $('.call-update-sku').on('change', function () {
                update_sku();
            });
        }, 2000)

        $('#colors-selector').on('change', function () {
            update_sku();
        });

        $('input[name="unit_price"]').on('keyup', function () {
            update_sku();
        });

        function update_sku() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $.ajax({
                type: "POST",
                url: '{{route('admin.product.sku-combination')}}',
                data: $('#product_form').serialize(),
                success: function (data) {
                    $('#sku_combination').html(data.view);
                    update_qty();
                    if (data.length > 1) {
                        $('#quantity').hide();
                    } else {
                        $('#quantity').show();
                    }
                }
            });
        }

        $(document).ready(function () {
            setTimeout(function () {
                let category = $("#category_id").val();
                let sub_category = $("#sub-category-select").attr("data-id");
                let sub_sub_category = $("#sub-sub-category-select").attr("data-id");
                getRequest('{{admin_url()}}/product/get-categories?parent_id=' + category + '&sub_category=' + sub_category, 'sub-category-select', 'select');
                getRequest('{{admin_url()}}/product/get-categories?parent_id=' + sub_category + '&sub_category=' + sub_sub_category, 'sub-sub-category-select', 'select');
            }, 100)
            // color select select2
            $('.color-var-select').select2({
                templateResult: colorCodeSelect,
                templateSelection: colorCodeSelect,
                escapeMarkup: function (m) {
                    return m;
                }
            });

            function colorCodeSelect(state) {
                var colorCode = $(state.element).val();
                if (!colorCode) return state.text;
                return "<span class='color-preview' style='background-color:" + colorCode + ";'></span>" + state.text;
            }
        });
    </script>

    <script>
        function check() {
            for (instance in CKEDITOR.instances) {
                CKEDITOR.instances[instance].updateElement();
            }
            var formData = new FormData(document.getElementById('product_form'));
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.post({
                url: '{{route('admin.product.update',$product->id)}}',
                data: formData,
                contentType: false,
                processData: false,
                success: function (data) {
                    if (data.errors) {
                        for (var i = 0; i < data.errors.length; i++) {
                            toastr.error(data.errors[i].message, {
                                CloseButton: true,
                                ProgressBar: true
                            });
                        }
                    } else {
                        toastr.success('product updated successfully!', {
                            CloseButton: true,
                            ProgressBar: true
                        });
                        $('#product_form').submit();
                    }
                }
            });
        };
    </script>

    <script>
        update_qty();

        function update_qty() {
            var total_qty = 0;
            var qty_elements = $('input[name^="qty_"]');
            for (var i = 0; i < qty_elements.length; i++) {
                total_qty += parseInt(qty_elements.eq(i).val());
            }
            if (qty_elements.length > 0) {

                $('input[name="current_stock"]').attr("readonly", true);
                $('input[name="current_stock"]').val(total_qty);
            } else {
                $('input[name="current_stock"]').attr("readonly", false);
            }
        }

        $('input[name^="qty_"]').on('keyup', function () {
            var total_qty = 0;
            var qty_elements = $('input[name^="qty_"]');
            for (var i = 0; i < qty_elements.length; i++) {
                total_qty += parseInt(qty_elements.eq(i).val());
            }
            $('input[name="current_stock"]').val(total_qty);
        });
    </script>

    <script>
        $(".lang_link").click(function (e) {
            e.preventDefault();
            $(".lang_link").removeClass('active');
            $(".lang_form").addClass('d-none');
            $(this).addClass('active');

            let form_id = this.id;
            let lang = form_id.split("-")[0];
            console.log(lang);
            $("#" + lang + "-form").removeClass('d-none');
            if (lang == '{{$default_lang}}') {
                $(".rest-part").removeClass('d-none');
            } else {
                $(".rest-part").addClass('d-none');
            }
        })
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