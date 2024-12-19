@extends('layouts.back-end.app')

@section('content')
    <div class="content container-fluid">
        <div class="card">
            <div class="card-header">
                <h5 class="text-center"><i class="tio-settings-outlined"></i>
                    {{translate('Shipping_Method_Select')}}
                </h5>

            </div>
            <div class="card-body">
                @php($shippingMethod=\App\Services\AdditionalServices::get_business_settings('shipping_method'))
                <form action="{{route('admin.business-settings.shipping-method.shipping-store')}}" method="post">
                    @csrf
                    <div class="row">
                        <div class="col-md-8">

                            <select class="form-control" name="shippingMethod"
                                    style="width: 100%">
                                <option value="0" selected disabled>---{{translate('select')}}---</option>
                                <option class="text-capitalize"
                                        value="inhouse_shipping" {{ $shippingMethod=='inhouse_shipping'?'selected':'' }} >{{translate('inhouse_shipping_method')}} </option>
                                <option class="text-capitalize"
                                        value="sellerwise_shipping" {{ $shippingMethod=='sellerwise_shipping'?'selected':'' }}>{{translate('seller_wise_shipping_method')}}</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <button type="submit" class="btn btn-primary">{{translate('submit')}}</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection
