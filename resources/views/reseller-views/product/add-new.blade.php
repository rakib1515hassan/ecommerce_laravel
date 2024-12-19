@extends('layouts.back-end.app-reseller')

@push('css_or_js')
    <link href="{{ asset('assets/back-end/css/tags-input.min.css') }}" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endpush

@section('content')
    <div class="content container-fluid">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a
                        href="{{ route('reseller.dashboard.index') }}">{{ translate('Dashboard') }}</a></li>
                <li class="breadcrumb-item" aria-current="page"><a
                        href="{{ route('reseller.product.list') }}">{{ translate('Product') }}</a></li>
                <li class="breadcrumb-item">{{ translate('Add_new') }}</li>
            </ol>
        </nav>

        <!-- Content Row -->
        <div class="row">
            <div class="col-md-12">

                <form class="product-form" action="{{ route('reseller.product.add-new') }}" method="post"
                    enctype="multipart/form-data"
                    style="text-align: {{ Session::get('direction') === 'rtl' ? 'right' : 'left' }};" id="product_form">
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
                            <div class="row form-group">
                                <div class="col-sm-12 mb-3 mb-sm-0">
                                    <label for="name">{{ translate('Product') }}</label>
                                    <select class="js-example-basic-multiple js-states js-example-responsive form-control"
                                        name="product_id" id="product_id" required>
                                        <option value="{{ null }}" selected disabled>
                                            ---{{ translate('Select') }}---
                                        </option>
                                        {{-- @foreach ($products as $product) --}}
                                        @foreach (\App\Models\Product::where(['added_by' => 'seller', 'status' => '1', 'user_id' => auth('seller')->id()])->get() as $key => $p)
                                            <option value="{{ $p['id'] }}">{{ $p['name'] }}
                                            </option>
                                        @endforeach
                                    </select>
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
                                @if (count($products) > 0)
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr class="btn-secondary">
                                                <td class="text-center">
                                                    <label for=""
                                                        class="control-label">{{ translate('Variant') }}</label>
                                                </td>
                                                <td class="text-center">
                                                    <label for=""
                                                        class="control-label">{{ translate('Variant Price') }}</label>
                                                </td>
                                                <td class="text-center">
                                                    <label for=""
                                                        class="control-label">{{ translate('SKU') }}</label>
                                                </td>
                                                <td class="text-center">
                                                    <label for=""
                                                        class="control-label">{{ translate('Quantity') }}</label>
                                                </td>
                                            </tr>
                                        </thead>
                                        <tbody>
                                @endif
                                @foreach ($products as $key => $product)
                                    <tr>
                                        <td>
                                            <label for="" class="control-label">{{ $product['type'] }}</label>
                                            <input value="{{ $product['type'] }}" name="type[]" style="display: none">
                                        </td>
                                        <td>
                                            <input type="number" name="price_{{ $product['type'] }}"
                                                value="{{ \App\Services\Converter::default($product['price']) }}"
                                                min="0" step="0.01" class="form-control" required>
                                        </td>
                                        <td>
                                            <label for="" class="control-label">{{ $product['sku'] }}</label>
                                            <input value="{{ $product['sku'] }}" name="type[]" style="display: none">

                                            {{-- <input type="text" name="sku_{{ $product['type'] }}" value="{{ $product['sku'] }}"
                           class="form-control" required> --}}
                                        </td>
                                        <td>
                                            <label for="" class="control-label">{{ $product['qty'] }}</label>
                                            <input value="{{ $product['qty'] }}" name="type[]" style="display: none">

                                            {{-- <input type="number" onkeyup="update_qty()" name="qty_{{ $product['type'] }}"
                           value="{{ $product['qty'] }}" min="1" max="100000" step="1"
                           class="form-control" style="display: none"
                           required> --}}
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                                </table>

                            </div>
                        </div>
                    </div>

                    <div class="card card-footer">
                        <div class="row">
                            <div class="col-md-12" style="padding-top: 20px">
                                <button type="button" onclick="check()"
                                    class="btn btn-primary">{{ translate('Submit') }}</button>
                            </div>
                            <!-- Display the link -->
                            <div class="col-md-6" id="linkContainer" style="display: none;">
                                <p>Generated Link: <span id="generatedLink"></span></p>
                                <button id="copyButton">Copy Link</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('script')
<script src="//code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <script src="{{ asset('assets/back-end') }}/js/tags-input.min.js"></script>
    <script src="{{ asset('assets/back-end/js/spartan-multi-image-picker.js') }}"></script>
    <script>
        $(".js-example-theme-single").select2({
            theme: "classic"
        });

        $(".js-example-responsive").select2({
            width: 'resolve'
        });


        $(document).ready(function() {
            $("#product_id").on('select2:select', function (e) {
                console.log(e);
                var product = e.params.data;

                $.post({
                    url: '{{ route('reseller.product.add-new') }}',
                    type: "GET",
                    data: {
                        'product': product
                    },
                    success: function(data) {
                        var products = data.products;
                        var html = '';
                        // if (let i = 0; i < products.length; i++) {
                        //     html += '<tr>\
                        //             <td></td>\
                        //             </tr>';
                        // }
                    }
                });
            })
        })
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
                    url: '{{ route('reseller.product.add-new') }}',
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function(data) {

                        // Update UI with generated link
                        $('#generatedLink').text(data.link);
                        $('#linkContainer').show();

                        // if (data.errors) {
                        //     for (var i = 0; i < data.errors.length; i++) {
                        //         toastr.error(data.errors[i].message, {
                        //             CloseButton: true,
                        //             ProgressBar: true
                        //         });
                        //     }
                        // } else {
                        //     toastr.success('{{ translate('product updated successfully!') }}', {
                        //         CloseButton: true,
                        //         ProgressBar: true
                        //     });
                        //     $('#product_form').submit();
                        // }
                    }
                });
                // Copy link to clipboard
                $('#copyButton').click(function() {
                    // Logic to copy the link to the clipboard
                    var linkText = $('#generatedLink').text();
                    var tempInput = $('<input>');
                    $('body').append(tempInput);
                    tempInput.val(linkText).select();
                    document.execCommand('copy');
                    tempInput.remove();

                    alert('Link copied to clipboard!');
                });
            })
        };
    </script>
@endpush
