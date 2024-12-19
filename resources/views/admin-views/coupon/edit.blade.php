@extends('layouts.back-end.app')
@section('title', translate('Coupon Edit'))
@push('css_or_js')

@endpush

@section('content')
    <div class="content container-fluid">
        <div class="page-header">
            <div class="row align-items-center">
                <div class="col-sm mb-2 mb-sm-0">
                    <h1 class="page-header-title"><i
                                class="tio-edit"></i> {{translate('Coupon')}} {{translate('update')}}
                    </h1>
                </div>
            </div>
        </div>

        <!-- Content Row -->
        <div class="row">
            <div class="col-md-12">
                <div class="card">

                    <div class="card-body">
                        <form action="{{route('admin.coupon.update',[$c['id']])}}" method="post">
                            @csrf
                            <div class="row">
                                <div class="col-4">
                                    <div class="form-group">
                                        <label for="name">{{translate('Type')}}</label>
                                        <select class="form-control" name="coupon_type"
                                                style="width: 100%" required>
                                            {{--<option value="delivery_charge_free">Delivery Charge Free</option>--}}
                                            <option value="discount_on_purchase" {{$c['coupon_type']=='discount_on_purchase'?'selected':''}}>{{translate('Discount on Purchase')}}</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="form-group">
                                        <label for="name">{{translate('Title')}}</label>
                                        <input type="text" name="title" class="form-control" id="title"
                                               value="{{$c['title']}}"
                                               placeholder="{{translate('Title')}}" required>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="form-group">
                                        <label for="name">{{translate('Code')}}</label>
                                        <input type="text" name="code" value="{{$c['code']}}"
                                               class="form-control" id="code"
                                               placeholder="" required>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-3 col-6">
                                    <div class="form-group">
                                        <label for="name">{{translate('start_date')}}</label>
                                        <input type="date" name="start_date" class="form-control" id="start date"
                                               value="{{date('Y-m-d',strtotime($c['start_date']))}}"
                                               placeholder="{{translate('start date')}}" required>
                                    </div>
                                </div>
                                <div class="col-md-3 col-6">
                                    <div class="form-group">
                                        <label for="name">{{translate('expire_date')}}</label>
                                        <input type="date" name="expire_date" class="form-control" id="expire date"
                                               value="{{date('Y-m-d',strtotime($c['expire_date']))}}"
                                               placeholder="{{translate('expire date')}}" required>
                                    </div>
                                </div>
                                <div class="col-md-3 col-6">
                                    <div class="form-group">
                                        <label for="exampleFormControlInput1">{{translate('limit')}} {{translate('for')}} {{translate('same')}} {{translate('user')}}</label>
                                        <input type="number" name="limit" value="{{ $c['limit'] }}" id="coupon_limit"
                                               class="form-control"
                                               placeholder="{{translate('EX')}}: {{translate('10')}}">
                                    </div>
                                </div>
                                <div class="col-md-3 col-6">
                                    <div class="form-group">
                                        <label for="name">{{translate('discount_type')}}</label>
                                        <select class="form-control" name="discount_type"
                                                onchange="checkDiscountType(this.value)"
                                                style="width: 100%">
                                            <option value="amount" {{$c['discount_type']=='amount'?'selected':''}}>{{translate('Amount')}}</option>
                                            <option value="percentage" {{$c['discount_type']=='percentage'?'selected':''}}>{{translate('percentage')}}</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-3 col-6">
                                    <div class="form-group">
                                        <label for="name">{{translate('Discount')}}</label>
                                        <input type="number" min="1" max="1000000" name="discount" class="form-control"
                                               id="discount"
                                               value="{{$c['discount_type']=='amount'?\App\Services\Converter::default($c['discount']):$c['discount']}}"
                                               placeholder="{{translate('discount')}}" required>
                                    </div>
                                </div>
                                <div class="col-md-3 col-6">
                                    <label for="name">{{translate('minimum_purchase')}}</label>
                                    <input type="number" min="1" max="1000000" name="min_purchase" class="form-control"
                                           id="minimum purchase"
                                           value="{{\App\Services\Converter::default($c['min_purchase'])}}"
                                           placeholder="{{translate('minimum purchase')}}" required>
                                </div>
                                <div class="col-md-3 col-6">
                                    <div class="form-group">
                                        <label for="name">{{translate('maximum_discount')}}</label>
                                        <input type="number" min="1" max="1000000" name="max_discount"
                                               class="form-control" id="maximum discount"
                                               value="{{\App\Services\Converter::default($c['max_discount'])}}"
                                               placeholder="{{translate('maximum discount')}}" required>
                                    </div>
                                </div>
                            </div>

                            <div class="card-footer">
                                <button type="submit"
                                        class="btn btn-primary">{{translate('Submit')}}</button>
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
        function checkDiscountType(val) {
            if (val == 'amount') {
                $('#max-discount').hide()
            } else if (val == 'percentage') {
                $('#max-discount').show()
            }
        }
    </script>

    <script>
        $(".js-example-theme-single").select2({
            theme: "classic"
        });

        $(".js-example-responsive").select2({
            width: 'resolve'
        });
    </script>
@endpush
