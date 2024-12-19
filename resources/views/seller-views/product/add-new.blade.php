@extends('layouts.back-end.app-seller')

@push('css_or_js')
    <link href="{{ asset('assets/back-end/css/tags-input.min.css') }}" rel="stylesheet">

    <meta name="csrf-token" content="{{ csrf_token() }}">
@endpush

@section('content')
    <div class="content container-fluid">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a
                            href="{{ route('seller.dashboard.index') }}">{{ translate('Dashboard') }}</a>
                </li>
                <li class="breadcrumb-item" aria-current="page"><a
                            href="{{ route('seller.product.list') }}">{{ translate('Product') }}</a></li>
                <li class="breadcrumb-item">{{ translate('Add_new') }}</li>
            </ol>
        </nav>

        <!-- Content Row -->
        <div class="row">
            <div class="col-md-12">

                <form class="product-form" action="{{ route('seller.product.add-new') }}" method="post"
                      enctype="multipart/form-data"
                      style="text-align: {{ Session::get('direction') === 'rtl' ? 'right' : 'left' }};"
                      id="product_form">
                    @csrf
                    <div class="card">
                        <div class="card-header">
                            @php($language = \App\Models\BusinessSetting::where('type', 'pnc_language')->first())
                            @php($language = $language->value ?? null)
                            @php($default_lang = 'en')

                            @php($default_lang = json_decode($language)[0])
                            <ul class="nav nav-tabs mb-4">
                                @foreach (json_decode($language) as $lang)
                                    <li class="nav-item">
                                        <a class="nav-link lang_link {{ $lang == $default_lang ? 'active' : '' }}"
                                           href="#"
                                           id="{{ $lang }}-link">{{ \App\Services\AdditionalServices::get_language_name($lang) . '(' . strtoupper($lang) . ')' }}</a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>

                        <div class="card-body">
                            @foreach (json_decode($language) as $lang)
                                <div class="{{ $lang != $default_lang ? 'd-none' : '' }} lang_form"
                                     id="{{ $lang }}-form">
                                    <div class="form-group">
                                        <label class="input-label" for="{{ $lang }}_name">{{ translate('name') }}
                                            ({{ strtoupper($lang) }})
                                        </label>
                                        <input type="text" {{ $lang == $default_lang ? 'required' : '' }} name="name[]"
                                               id="{{ $lang }}_name" class="form-control" placeholder="New Product"
                                               required>
                                    </div>
                                    <input type="hidden" name="lang[]" value="{{ $lang }}">
                                    <div class="form-group pt-4">
                                        <label class="input-label"
                                               for="{{ $lang }}_description">{{ translate('description') }}
                                            ({{ strtoupper($lang) }})</label>
                                        <textarea name="description[]" class="editor textarea" cols="30" rows="10"
                                                  required>{{ old('details') }}</textarea>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="card mt-2 rest-part">
                        <div class="card-header">
                            <h4>{{ translate('General_info') }}</h4>
                        </div>
                        <div class="card-body">
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-4">
                                        <label for="name">{{ translate('Category') }}</label>
                                        <select class="js-example-basic-multiple form-control" name="category_id"
                                                onchange="getRequest('{{ url('/') }}/seller/product/get-categories?parent_id='+this.value,'sub-category-select','select')"
                                                required>
                                            <option value="{{ old('category_id') }}" selected disabled>
                                                ---{{ translate('Select') }}---
                                            </option>
                                            @foreach ($cat as $c)
                                                <option value="{{ $c['id'] }}"
                                                        {{ old('name') == $c['id'] ? 'selected' : '' }}>
                                                    {{ $c['name'] }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label for="name">{{ translate('Sub_category') }}</label>
                                        <select class="js-example-basic-multiple form-control" name="sub_category_id"
                                                id="sub-category-select"
                                                onchange="getRequest('{{ url('/') }}/seller/product/get-categories?parent_id='+this.value,'sub-sub-category-select','select')">
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label for="name">{{ translate('Sub_sub_category') }}</label>
                                        <select class="js-example-basic-multiple form-control"
                                                name="sub_sub_category_id"
                                                id="sub-sub-category-select">

                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-6">
                                        <label for="name">{{ translate('Brand') }}</label>
                                        <select
                                                class="js-example-basic-multiple js-states js-example-responsive form-control"
                                                name="brand_id" required>
                                            <option value="{{ null }}" selected disabled>
                                                ---{{ translate('Select') }}---
                                            </option>
                                            @foreach ($br as $b)
                                                <option value="{{ $b['id'] }}">{{ $b['name'] }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="col-md-6">
                                        <label for="name">{{ translate('Unit') }}</label>
                                        <select class="js-example-basic-multiple form-control" name="unit">
                                            @foreach (\App\Services\AdditionalServices::units() as $x)
                                                <option value="{{ $x }}"
                                                        {{ old('unit') == $x ? 'selected' : '' }}>
                                                    {{ $x }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-6">
                                        <label for="name">{{ translate('Weight') }}</label>
                                        <input type="number" name="weight" class="form-control"
                                               placeholder="Enter Weight (gm)"
                                               required>
                                    </div>

                                    <div class="col-md-6">
                                        <label for="name">{{ translate('Warranty and Policy') }}</label>
                                        <input type="text" name="policy" class="form-control"
                                               placeholder="Enter Policy"/>
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
                                                        value="{{$product_manager->id}}" {{old('product_manager_id')==$product_manager->id? 'selected':''}}>
                                                    {{$product_manager->f_name}} {{$product_manager->l_name}}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="col-md-6">
                                        <label for="product_manager_amount">{{ translate('Product Manager Amount') }}</label>
                                        <input type="number" name="product_manager_amount" class="form-control"
                                               placeholder="Enter Product Manager Amount"
                                               value="{{ old('product_manager_amount', 0) }}"
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
                                                       name="is_admin_manage" {{old('is_admin_manage',0)==1?'checked':''}}>
                                                <span class="slider round"></span>
                                            </label>
                                        </div>

                                        <div class="col-md-6">
                                            <label for="">{{ translate('Seller Amount') }}</label>
                                            <input type="number" name="seller_amount" class="form-control"
                                                   placeholder="Enter Seller Amount"
                                                   value="{{ old('seller_amount') }}"
                                                   required>
                                        </div>

                                        <script>
                                            var is_admin_manage = document.querySelector('input[name="is_admin_manage"]');
                                            var seller_amount = document.querySelector('input[name="seller_amount"]');

                                            is_admin_manage.addEventListener('change', function () {
                                                seller_amount.disabled = !is_admin_manage.checked;
                                                seller_amount.required = is_admin_manage.checked;
                                                seller_amount.value = is_admin_manage.checked ? '0' : '0';
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
                                               value="{{ old('reseller_amount',0) }}"
                                               required>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card mt-2 rest-part">
                        <div class="card-header">
                            <h4>{{ translate('Variations') }}</h4>
                        </div>
                        <div class="card-body">

                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-6">
                                        <label for="colors">
                                            {{ translate('Colors') }} :
                                        </label>
                                        <label class="switch">
                                            <input type="checkbox" class="status" name="colors_active"
                                                   value="{{ old('colors_active') }}">
                                            <span class="slider round"></span>
                                        </label>
                                        <select
                                                class="js-example-basic-multiple js-states js-example-responsive form-control color-var-select"
                                                name="colors[]" multiple="multiple" id="colors-selector" disabled>
                                            @foreach (\App\Models\Color::orderBy('name', 'asc')->get() as $key => $color)
                                                <option value="{{ $color->code }}">
                                                    {{ $color['name'] }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="col-md-6">
                                        <label for="attributes" style="padding-bottom: 3px">
                                            {{ translate('Attributes') }} :
                                        </label>
                                        <select
                                                class="js-example-basic-multiple js-states js-example-responsive form-control"
                                                name="choice_attributes[]" id="choice_attributes" multiple="multiple">
                                            @foreach (\App\Models\Attribute::orderBy('name', 'asc')->get() as $key => $a)
                                                <option value="{{ $a['id'] }}">
                                                    {{ $a['name'] }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="col-md-12 mt-2 mb-2">
                                        <div class="customer_choice_options" id="customer_choice_options">

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card mt-2 rest-part">
                        <div class="card-header">
                            <h4>{{ translate('Product_price_&_stock') }}</h4>
                        </div>
                        <div class="card-body">
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-6">
                                        <label class="control-label">{{ translate('Unit_price') }}</label>
                                        <input type="number" min="0" step="0.01"
                                               placeholder="{{ translate('Unit_price') }}" name="unit_price"
                                               value="{{ old('unit_price') }}" class="form-control" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="control-label">{{ translate('Purchase_price') }}</label>
                                        <input type="number" min="0" step="0.01"
                                               placeholder="{{ translate('Purchase_price') }}" name="purchase_price"
                                               value="{{ old('purchase_price') }}" class="form-control" required>
                                    </div>
                                </div>

                                <div class="row pt-4">
                                    <div class="col-md-4">
                                        <label class="control-label">{{ translate('Tax') }}</label>
                                        <label class="badge badge-info">{{ translate('Percent') }} ( %
                                            )</label>
                                        <input type="number" min="0" step="0.01"
                                               placeholder="{{ translate('Tax') }}" name="tax"
                                               value="{{ old('tax') }}" class="form-control">
                                        <input name="tax_type" value="percent" style="display: none">
                                    </div>

                                    <div class="col-md-4">
                                        <label class="control-label">{{ translate('Discount') }}</label>
                                        <input type="number" min="0" step="0.01"
                                               placeholder="{{ translate('Discount') }}" name="discount"
                                               value="{{ old('discount') }}" class="form-control">
                                    </div>
                                    <div class="col-md-4" style="padding-top: 30px;">
                                        <select class="form-control js-select2-custom" name="discount_type">
                                            <option value="flat">{{ translate('Flat') }}</option>
                                            <option value="percent">{{ translate('Percent') }}</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="sku_combination" id="sku_combination">

                                </div>
                                <div class="row pt-4">
                                    <div class="col-md-6" id="quantity">
                                        <label class="control-label">{{ translate('total') }}
                                            {{ translate('Quantity') }}</label>
                                        <input type="number" min="0" step="1"
                                               placeholder="{{ translate('Quantity') }}" name="current_stock"
                                               value="{{ old('current_stock') }}" class="form-control" required>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card mt-2 mb-2 rest-part">
                        <div class="card-header">
                            <h4>{{ translate('seo_section') }}</h4>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-12 mb-4">
                                    <label class="control-label">{{ translate('Meta_title') }}</label>
                                    <input type="text" name="meta_title" placeholder="" class="form-control">
                                </div>

                                <div class="col-md-8 mb-4">
                                    <label class="control-label">{{ translate('Meta_description') }}</label>
                                    <textarea rows="10" type="text" name="meta_description"
                                              class="form-control"></textarea>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group mb-0">
                                        <label>{{ translate('Meta_image') }}</label>
                                    </div>
                                    <div class="border border-dashed">
                                        <div class="row" id="meta_img"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card mt-2 rest-part">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-12 mb-4">
                                    <label class="control-label">{{ translate('Youtube video link') }}</label>
                                    <small class="badge badge-soft-danger">
                                        ( {{ translate('optional, please provide embed link not direct link') }}
                                        .
                                        )</small>
                                    <input type="text" name="video_link"
                                           placeholder="EX : https://www.youtube.com/embed/5R06LRdUCSE"
                                           class="form-control"
                                           required>
                                </div>

                                <div class="col-md-8">
                                    <div class="form-group">
                                        <label>{{ translate('Upload_product_images') }}</label><small
                                                style="color: red">*
                                            ( {{ translate('ratio 1:1') }}
                                            )</small>
                                    </div>
                                    <div class="p-2 border border-dashed" style="max-width:430px;">
                                        <div class="row" id="coba"></div>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="name">{{ translate('Upload_thumbnail') }}</label><small
                                                style="color: red">* ( {{ translate('ratio 1:1') }}
                                            )</small>
                                    </div>
                                    <div style="max-width:200px;">
                                        <div class="row" id="thumbnail"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card card-footer">
                        <div class="row">
                            <div class="col-md-12" style="padding-top: 20px">
                                <button type="button" onclick="check()"
                                        class="btn btn-primary">{{ translate('Submit') }}</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script src="{{ asset('assets/back-end') }}/js/tags-input.min.js"></script>

    <script src="{{ asset('assets/back-end/js/spartan-multi-image-picker.js') }}"></script>
    <script>
        $(function () {
            $("#coba").spartanMultiImagePicker({
                fieldName: 'images[]',
                maxCount: 4,
                rowHeight: 'auto',
                groupClassName: 'col-6',
                maxFileSize: '',
                placeholderImage: {
                    image: '{{ asset('assets/back-end/img/400x400/img2.jpg') }}',
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
                    toastr.error('{{ translate('Please only input png or jpg type file') }}', {
                        CloseButton: true,
                        ProgressBar: true
                    });
                },
                onSizeErr: function (index, file) {
                    toastr.error('{{ translate('File size too big') }}', {
                        CloseButton: true,
                        ProgressBar: true
                    });
                }
            });

            $("#thumbnail").spartanMultiImagePicker({
                fieldName: 'image',
                maxCount: 1,
                rowHeight: 'auto',
                groupClassName: 'col-12',
                maxFileSize: '',
                placeholderImage: {
                    image: '{{ asset('assets/back-end/img/400x400/img2.jpg') }}',
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
                    toastr.error('{{ translate('Please only input png or jpg type file') }}', {
                        CloseButton: true,
                        ProgressBar: true
                    });
                },
                onSizeErr: function (index, file) {
                    toastr.error('{{ translate('File size too big') }}', {
                        CloseButton: true,
                        ProgressBar: true
                    });
                }
            });

            $("#meta_img").spartanMultiImagePicker({
                fieldName: 'meta_image',
                maxCount: 1,
                rowHeight: '280px',
                groupClassName: 'col-12',
                maxFileSize: '',
                placeholderImage: {
                    image: '{{ asset('assets/back-end/img/400x400/img2.jpg') }}',
                    width: '90%',
                },
                dropFileLabel: "Drop Here",
                onAddRow: function (index, file) {

                },
                onRenderedPreview: function (index) {

                },
                onRemoveRow: function (index) {

                },
                onExtensionErr: function (index, file) {
                    toastr.error('{{ translate('Please only input png or jpg type file') }}', {
                        CloseButton: true,
                        ProgressBar: true
                    });
                },
                onSizeErr: function (index, file) {
                    toastr.error('{{ translate('File size too big') }}', {
                        CloseButton: true,
                        ProgressBar: true
                    });
                }
            });
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
            $('#customer_choice_options').append(
                '<div class="row"><div class="col-md-3"><input type="hidden" name="choice_no[]" value="' + i +
                '"><input type="text" class="form-control" name="choice[]" value="' + n +
                '" placeholder="{{ trans('Choice Title') }}" readonly></div><div class="col-lg-9"><input type="text" class="form-control" name="choice_options_' +
                i +
                '[]" placeholder="{{ trans('Enter choice values') }}" data-role="tagsinput" onchange="update_sku()"></div></div>'
            );

            $("input[data-role=tagsinput], select[multiple][data-role=tagsinput]").tagsinput();
        }

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
                url: '{{ route('seller.product.sku-combination') }}',
                data: $('#product_form').serialize(),
                success: function (data) {
                    $('#sku_combination').html(data.view);
                    $('#sku_combination').addClass('pt-4');
                    if (data.length > 1) {
                        $('#quantity').hide();
                    } else {
                        $('#quantity').show();
                    }
                }
            });
        };

        $(document).ready(function () {
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
                return "<span class='color-preview' style='background-color:" + colorCode + ";'></span>" + state
                    .text;
            }
        });
    </script>

    <script>
        function check() {
            Swal.fire({
                title: '{{ translate('Are you sure') }}?',
                text: '',
                type: 'warning',
                showCancelButton: true,
                cancelButtonColor: 'default',
                confirmButtonColor: '#377dff',
                cancelButtonText: 'No',
                confirmButtonText: 'Yes',
                reverseButtons: true
            }).then((result) => {
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
                    url: '{{ route('seller.product.add-new') }}',
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
                            toastr.success('{{ translate('product updated successfully!') }}', {
                                CloseButton: true,
                                ProgressBar: true
                            });
                            $('#product_form').submit();
                        }
                    }
                });
            })
        };
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
            if (lang == '{{ $default_lang }}') {
                $(".rest-part").removeClass('d-none');
            } else {
                $(".rest-part").addClass('d-none');
            }
        })
    </script>

    {{-- ck editor --}}
    <script src="{{ asset('/') }}vendor/ckeditor/ckeditor/ckeditor.js"></script>
    <script src="{{ asset('/') }}vendor/ckeditor/ckeditor/adapters/jquery.js"></script>
    <script>
        $('.textarea').ckeditor({
            contentsLangDirection: '{{ Session::get('direction') }}',
        });
    </script>
    {{-- ck editor --}}
@endpush
