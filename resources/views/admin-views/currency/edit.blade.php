@extends('layouts.back-end.app')

@section('title', translate('Update Currency'))

@push('css_or_js')

@endpush

@section('content')
    @php($currency_model=\App\Services\AdditionalServices::get_business_settings('currency_model'))
    <div class="content container-fluid">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a
                            href="{{route('admin.dashboard')}}">{{translate('Dashboard')}}</a>
                </li>
                <li class="breadcrumb-item" aria-current="page">{{translate('Currency')}}</li>
            </ol>
        </nav>
        <!-- Page Heading -->

        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="text-center">
                            <i class="tio-money"></i>
                            {{translate('Update Currency')}}
                        </h5>
                    </div>
                    <div class="card-body">
                        <form action="{{route('admin.currency.update',[$data['id']])}}" method="post"
                              style="text-align: {{Session::get('direction') === "rtl" ? 'right' : 'left'}};">
                            @csrf
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-6">
                                        <label>{{translate('Currency Name')}} :</label>
                                        <input type="text" name="name"
                                               placeholder="{{translate('Currency Name')}}"
                                               class="form-control" id="name"
                                               value="{{$data->name}}">
                                    </div>
                                    <div class="col-md-6">
                                        <label>{{translate('Currency Symbol')}} :</label>
                                        <input type="text" name="symbol"
                                               placeholder="{{translate('Currency Symbol')}}"
                                               class="form-control" id="symbol"
                                               value="{{$data->symbol}}">
                                    </div>
                                </div>

                            </div>
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-6">
                                        <label>{{translate('Currency Code')}} :</label>
                                        <input type="text" name="code"
                                               placeholder="{{translate('Currency Code')}}"
                                               class="form-control" id="code"
                                               value="{{$data->code}}">
                                    </div>
                                    @if($currency_model=='multi_currency')
                                        <div class="col-md-6">
                                            <label>{{translate('Exchange Rate')}} :</label>
                                            <input type="number" min="0" max="1000000"
                                                   name="exchange_rate" step="0.00000001"
                                                   placeholder="{{translate('Exchange Rate')}}"
                                                   class="form-control" id="exchange_rate"
                                                   value="{{$data->exchange_rate}}">
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <div class="form-group text-center">
                                <button type="submit" id="add" class="btn btn-primary"
                                        style="color: white">{{translate('Update')}}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')

@endpush
