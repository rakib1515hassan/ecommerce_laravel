@extends('layouts.back-end.app-reseller')


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
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3> Order For Your Customer </h3>
                    </div>

                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="d-flex">
                                    <div class="form-group">
                                        <label for="customer_id">{{ translate('Select Customer')}}</label>
                                        <select class="js-example-basic-single js-states form-control"
                                                data-placeholder="{{ translate('Choose Customer') }}"
                                                name="customer_id" id="customer_id" required>
                                            <option value="" selected disabled>Select Customer</option>
                                            @foreach ($customers as $customer)
                                                <option
                                                    value="{{ $customer['id'] }}">{{ $customer['f_name'] }} {{ $customer['l_name'] }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="d-flex align-items-center mt-2">
                                        <button class="btn btn-primary btn-circle" data-toggle="modal"
                                                data-target="#createCustomer">
                                            <i class="tio-add-circle"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="d-flex">
                                    <div class="form-group">
                                        <label
                                            for="shipping_address_id">{{ translate('Select Shipping Address')}}</label>
                                        <select class="js-example-basic-single js-states form-control"
                                                data-placeholder="{{ translate('Choose Shipping Address') }}"
                                                name="shipping_address_id" id="shipping_address_id" required>
                                            <option value="" selected disabled>Select Shipping Address</option>
                                        </select>
                                    </div>

                                    <div class="d-flex align-items-center mt-2">
                                        <button class="btn btn-primary btn-circle" data-toggle="modal"
                                                data-target="#createShippingAddress">
                                            <i class="tio-add-circle"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <table class="table table-bordered">
                                <thead>
                                <tr>
                                    <th>{{ translate('Product')}}</th>
                                    <th>{{ translate('Variation')}}</th>
                                    <th>{{ translate('Price')}}</th>
                                    <th>{{ translate('Quantity')}}</th>
                                    <th>{{ translate('Action')}}</th>
                                </tr>
                                </thead>
                                <tbody id="cart">
                                </tbody>

                                <tfoot>
                                <tr>
                                    <td colspan="2" class="text-right">{{ translate('Sub Total')}}</td>
                                    <td id="total">0</td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td colspan="2" class="text-right">{{ translate('Shipping Cost')}}</td>
                                    <td id="shipping_cost_total">0</td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td colspan="2" class="text-right">{{ translate('Total')}}</td>
                                    <td id="grand_total">0</td>
                                    <td></td>
                                </tr>
                                </tfoot>
                            </table>

                        </div>

                        <div id="shipping_cost"></div>
                    </div>

                    <div class="card-footer">
                        <button type="button" id="place_order" class="btn btn-primary">Order for Customer</button>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3> Select Product </h3>
                    </div>

                    <div class="card-body">
                        <div class="form-group">
                            <label for="product_id">{{ translate('Select Product')}}</label>
                            <select class="js-example basic-single js-states form-control"
                                    data-placeholder="{{ translate('Choose Product') }}"
                                    name="product_id" id="product_id" required>
                                <option value="" selected disabled>Select Product</option>
                            </select>
                        </div>


                        <div class="form-group">
                            <label for="price">{{ translate('Price')}}</label>
                            <input type="number" name="price" class="form-control" id="price" required>
                            <small class="text-muted"
                                   id="reseller_price">{{ translate('Reseller Amount Show Here')}}</small>
                        </div>

                        <div class="form-group">
                            <label for="quantity">{{ translate('Quantity')}}</label>
                            <input type="number" name="quantity" step="1" class="form-control" id="quantity" value="1"
                                   min="1"
                                   required>
                        </div>

                        <div class="form-group">
                            <label for="choice_options">{{ translate('Variation')}}</label>
                            <select name="choice_options" id="choice_options" class="form-control" disabled>
                                <option value="">{{ translate('Select Variation')}}</option>
                            </select>
                        </div>
                    </div>

                    <div class="card-footer">
                        <button type="button" class="btn btn-primary" id="addProduct">Add Product</button>
                    </div>
                </div>
            </div>
        </div>
    </div>



    <div class="modal fade" id="createCustomer" tabindex="-1" aria-labelledby="createCustomerLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="createCustomerLabel">Create New Customer</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="createCustomerForm">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="f_name" class="col-form-label">First Name:</label>
                                    <input type="text" name="f_name" class="form-control" id="f_name" required>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="l_name" class="col-form-label">Last Name:</label>
                                    <input type="text" name="l_name" class="form-control" id="l_name" required>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="email" class="col-form-label">Email:</label>
                            <input type="email" name="email" class="form-control" id="email" required>
                        </div>

                        <div class="form-group">
                            <label for="phone" class="col-form-label">Phone:</label>
                            <input type="number" name="phone" class="form-control" id="phone" required>
                        </div>
                    </form>

                    <div class="alert alert-danger" id="customer-error" style="display: none"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="createCustomerBtn">Create</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="createShippingAddress" tabindex="-1" aria-labelledby="createShippingAddressLabel"
         aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="createShippingAddressLabel">Create New Shipping Address</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    {{--                    'contact_person_name' => 'required',--}}
                    {{--                    'address' => 'required',--}}
                    {{--                    'city' => 'required',--}}
                    {{--                    'zip' => 'required',--}}
                    {{--                    'phone' => 'required',--}}
                    {{--                    'state' => 'required',--}}
                    {{--                    'area_id' => 'required',--}}
                    <form id="createShippingAddressForm">
                        <div class="form-group m-0">
                            <label for="contact_person_name" class="col-form-label">Contact Person Name:</label>
                            <input type="text" name="contact_person_name" class="form-control" id="contact_person_name"
                                   required>
                        </div>

                        <div class="form-group m-0">
                            <label for="address_phone" class="col-form-label">Phone:</label>
                            <input type="number" name="address_phone" class="form-control" id="address_phone" required>
                        </div>

                        <div class="form-group m-0">
                            <label for="address" class="col-form-label">Address:</label>
                            <textarea name="address" class="form-control" id="address" required></textarea>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group m-0">
                                    <label for="district_id" class="col-form-label">District:</label>
                                    <select class="js-example-basic-single js-states form-control"
                                            data-placeholder="{{ translate('Choose District') }}"
                                            name="district_id" id="district_id" required>
                                        <option value="" selected disabled>Select District</option>
                                        @foreach ($districts as $district)
                                            <option value="{{ $district['id'] }}">{{ $district['name'] }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group m-0">
                                    <label for="area_id" class="col-form-label">City:</label>
                                    <select class="js-example-basic-single js-states form-control"
                                            data-placeholder="{{ translate('Choose City') }}"
                                            name="area_id" id="area_id" required>
                                        <option value="" selected disabled>Select City</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </form>

                    <div class="alert alert-danger" id="shipping-address-error" style="display: none"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="createShippingAddressBtn">Create</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script>
        $(document).ready(function () {
            $(".js-example-basic-single").select2({
                width: '100%'
            });
            $(".js-example-responsive").select2({
                width: 'resolve'
            });

            $('#createCustomerBtn').on('click', function () {
                var form = $('#createCustomerForm');

                // validate form
                if (!form[0].checkValidity()) {
                    form[0].reportValidity();
                    return;
                }

                var f_name = $('#f_name').val();
                var l_name = $('#l_name').val();
                var email = $('#email').val();
                var phone = $('#phone').val();


                $.post({
                    url: '{{ route('reseller.sale-product.create-customer') }}',
                    data: {
                        '_token': '{{ csrf_token() }}',
                        'f_name': f_name,
                        'l_name': l_name,
                        'email': email,
                        'phone': phone
                    },
                    success: function (data) {
                        console.log(data);

                        var customer = data;
                        var html = '<option value="' + customer.id + '">' + customer.f_name + ' ' + customer.l_name + '</option>';
                        $('#customer_id').append(html);
                        $('#customer_id').val(customer.id).trigger('change');
                        $('#customer-error').hide();
                        $('#createCustomer').modal('toggle');
                    },

                    error: function (data) {
                        var errors = data.responseJSON.errors;
                        var errorHtml = '';
                        for (var key in errors) {
                            errorHtml += errors[key][0] + '<br>';
                        }
                        $('#customer-error').html(errorHtml);
                        $('#customer-error').show();
                    }
                });
            });


            $('#customer_id').on('select2:select', function (e) {
                var customer_id = e.params.data['id']

                $.ajax({
                    url: '{{ route('reseller.sale-product.get-shipping-address') }}',
                    type: 'GET',
                    data: {
                        'customer_id': customer_id
                    },
                    success: function (data) {
                        data.forEach(function (address) {
                            var html = '<option value="' + address.id + '">' + address.address + ' address.phone' + +'</option>';
                            $('#shipping_address_id').append(html);
                        });
                    }
                });
            });


            $('#district_id').on('change', function () {
                var district_id = $(this).val();
                $.ajax({
                    url: '{{ route('reseller.sale-product.get-area') }}',
                    type: 'GET',
                    data: {
                        'district_id': district_id
                    },
                    success: function (data) {
                        console.log(data)

                        var html = '';
                        data.forEach(function (area) {
                            html += '<option value="' + area.id + '">' + area.name + '</option>';
                        });
                        $('#area_id').html(html);
                    }
                });
            });


            $('#createShippingAddressBtn').on('click', function () {
                var form = $('#createShippingAddressForm');

                // validate form
                if (!form[0].checkValidity()) {
                    form[0].reportValidity();
                    return;
                }

                var contact_person_name = $('#contact_person_name').val();
                var phone = $('#address_phone').val();
                var address = $('#address').val();
                var district_id = $('#district_id').val();
                var area_id = $('#area_id').val();

                $.post({
                    url: '{{ route('reseller.sale-product.create-shipping-address') }}',
                    data: {
                        '_token': '{{ csrf_token() }}',
                        'contact_person_name': contact_person_name,
                        'phone': phone,
                        'address': address,
                        'district_id': district_id,
                        'area_id': area_id,
                        'customer_id': $('#customer_id').val(),
                    },
                    success: function (data) {
                        var address = data;
                        var html = '<option value="' + address.id + '">' + address.address + '</option>';
                        $('#shipping_address_id').append(html);
                        $('#shipping_address_id').val(address.id).trigger('change');
                        $('#shipping-address-error').hide();
                        $('#createShippingAddress').modal('toggle');
                    },

                    error: function (data) {
                        var errors = data.responseJSON.errors;
                        var errorHtml = '';
                        for (var key in errors) {
                            errorHtml += errors[key][0] + '<br>';
                        }
                        $('#shipping-address-error').html(errorHtml);
                        $('#shipping-address-error').show();
                    }
                });
            });


            $('#product_id').select2({
                ajax: {
                    url: '{{ route('reseller.sale-product.search-product') }}',
                    dataType: 'json',
                    delay: 250,
                    data: function (params) {
                        return {
                            q: params.term, // search term
                            page: params.page
                        };
                    },
                    processResults: function (data, params) {
                        const discounted_price = (discount_type, discount_value, price) => {
                            if (discount_type == 'percent') {
                                return (price - (price * discount_value / 100)).toFixed(0);
                            } else {
                                return price - discount_value;
                            }
                        }

                        return {


                            results: data.products.map(function (item) {
                                return {
                                    id: item.id,
                                    text: item.name,
                                    unit_price: discounted_price(item.discount_type, item.discount, item.unit_price),
                                    reseller_amount: item.reseller_amount,
                                    colors: item.colors,
                                    choice_options: item.choice_options,
                                    variation: item.variation,
                                    max_price: item.unit_price,
                                };
                            }),
                        };
                    },
                    cache: true
                },
                placeholder: 'Search for a product',
                minimumInputLength: 1,
            });

            $('#product_id').on('select2:select', function (e) {
                var data = e.params.data;
                $('#price').val(data.unit_price);
                $('#quantity').val(1);
                $('#reseller_price').html('Reseller Price: ' + data.reseller_amount);

                if ((data.variation || {}).length > 0) {
                    var variation = data.variation;
                    var str = '';
                    for (var i = 0; i < variation.length; i++) {
                        str += '<option value="' + variation[i].type + '">' + variation[i].type + '</option>';
                    }
                    $('#choice_options').html(str);
                    $('#choice_options').val('');
                    $('#choice_options').prop('disabled', false);
                } else {
                    $('#choice_options').html('');
                    $('#choice_options').prop('disabled', true);
                }
            });

            var cart = {};

            function renderCart() {
                var html = '';
                var total = 0;


                Object.values(cart).forEach(function (product, index) {
                    var subTotal = product.price * product.quantity;
                    total += subTotal;
                    html += '<tr>';
                    html += '<td>' + product.name + '</td>';
                    html += '<td>' + product.choice_options + '</td>';
                    html += '<td>' + product.price + '</td>';
                    html += '<td>' + product.quantity + '</td>';
                    html += '<td><button class="btn btn-danger remove-product" data-index="' + product.id + '">Remove</button></td>';
                    html += '</tr>';
                });

                $('#cart').html(html);
                $('#total').html(total);
            }


            function get_shipping_cost() {
                var shipping_address_id = $('#shipping_address_id').val();
                var products = Object.values(cart);


                if (shipping_address_id == null) {
                    return;
                }

                if (products.length === 0) {
                    return;
                }

                $.post({
                    url: '{{ route('reseller.sale-product.shipping-cost') }}',
                    data: {
                        '_token': '{{ csrf_token() }}',
                        'shipping_address_id': shipping_address_id,
                        'products': products
                    },
                    success: function (data) {
                        var co = `
                            <div class="alert alert-info">
                                <p><b>Shipping Cost: </b> ${data.shipping_cost} | Weight: ${data.kg} KG | Total Vendor : ${data.total_seller}</p>
                            </div>
                        `;
                        $('#shipping_cost').html(co);

                        $('#shipping_cost_total').html(data.shipping_cost);
                        $('#grand_total').html(parseInt($('#total').html()) + parseInt(data.shipping_cost));
                    },
                    error: function (data) {
                        console.log(data);
                    }
                });
            }


            $('#addProduct').on('click', function () {
                var product_data = $('#product_id').select2('data')[0];

                var price = $('#price').val();
                var quantity = $('#quantity').val();
                var choice_options = $('#choice_options').val();


                if (product_data == null) {
                    alert('Please select a product');
                    return;
                }

                if (parseInt(quantity) < 1) {
                    alert('Quantity can not be less than 1');
                    return;
                }

                if (parseInt(price) < parseInt(product_data.unit_price)) {
                    alert('Price can not be less than discounted price');
                    return;
                }

                if (parseInt(price) > parseInt(product_data.max_price)) {
                    alert('Price can not be greater than original price');
                    return;
                }

                if ((product_data.variation || []).length > 0 && (choice_options == '' || choice_options == null)) {
                    alert('Please select variation');
                    return;
                }

                var product = {
                    id: product_data.id,
                    name: product_data.text,
                    price,
                    quantity,
                    choice_options: choice_options ? choice_options : ''
                };

                cart[product_data.id] = product;
                renderCart();
                get_shipping_cost();

                // clear form
                $('#product_id').val(null).trigger('change');
                $('#price').val(null);
                $('#reseller_price').html('Reseller Amount Show Here');
                $('#quantity').val(1);
                $('#choice_options').html('');

            });


            // watch cart on by clicking on add product
            $('#cart').on('click', '.remove-product', function () {
                var index = $(this).data('index');
                delete cart[index];
                renderCart();
            });

            $('#place_order').on('click', function () {
                var customer_id = $('#customer_id').val();
                var shipping_address_id = $('#shipping_address_id').val();
                var products = Object.values(cart);

                if (customer_id == null) {
                    alert('Please select a customer');
                    return;
                }

                if (shipping_address_id == null) {
                    alert('Please select a shipping address');
                    return;
                }

                if (products.length == 0) {
                    alert('Please add product to cart');
                    return;
                }

                $.post({
                    url: '{{ route('reseller.sale-product.create-order') }}',
                    data: {
                        '_token': '{{ csrf_token() }}',
                        'customer_id': customer_id,
                        'shipping_address_id': shipping_address_id,
                        'products': products
                    },
                    success: function (data) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Order Placed Successfully',
                            text: 'Order has been placed successfully for customer',
                            showConfirmButton: false,
                            timer: 1500,
                        }).then(function () {
                            window.location.href = '{{ route('reseller.orders.list','all') }}';
                        });
                    },
                    error: function (data) {
                        console.log(data);
                    }
                });
            });

            // on address call shipping cost
            $('#shipping_address_id').on('change', function () {
                get_shipping_cost();
            });
        });
    </script>
@endpush
