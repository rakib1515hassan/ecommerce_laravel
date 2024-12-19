@extends('layouts.back-end.app-reseller')

@section('title', translate('Dashboard'))

@push('css_or_js')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        .grid-card {
            border: 2px solid #00000012;
            border-radius: 10px;
            padding: 10px;
        }

        .label_1 {
            /*position: absolute;*/
            font-size: 10px;
            background: #FF4C29;
            color: #ffffff;
            width: 80px;
            padding: 2px;
            font-weight: bold;
            border-radius: 6px;
            text-align: center;
        }

        .center-div {
            text-align: center;
            border-radius: 6px;
            padding: 6px;
            border: 2px solid #8080805e;
        }
    </style>
@endpush

@section('content')

    <div class="content container-fluid">
        <div class="page-header pb-0" style="border-bottom: 0!important">
            <div class="flex-between row align-items-center mx-1">
                <h1 class="page-header-title">Dashboard</h1>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-body">
                <div class="flex-between gx-2 gx-lg-3 mb-2">
                    <div>
                        <h4><i style="font-size: 30px" class="tio-wallet"></i>Reseller Wallet</h4>
                    </div>
                </div>

                <div class="row gx-2 gx-lg-3" id="order_stats">
                    <div class="flex-between" style="width: 100%">
                        <div class="mb-3 mb-lg-0" style="width: 50%">
                            <div class="card card-body card-hover-shadow h-100 text-white text-center"
                                 style="background-color: #22577A;">
                                <h1 class="p-2 text-white">{{\App\Models\Order::where('reseller_id', auth('reseller')->user()->id)->count()}}</h1>
                                <div class="text-uppercase">Total Order</div>
                            </div>
                        </div>

                        <div class="mb-3 mb-lg-0" style="width: 50%">
                            <div class="card card-body card-hover-shadow h-100 text-white text-center"
                                 style="background-color: #595260;">
                                <h1 class="p-2 text-white">৳{{auth('reseller')->user()->balance}}</h1>
                                <div class="text-uppercase">Total Balance</div>
                            </div>
                        </div>


                    </div>
                </div>

                <div class="row">
                    <!-- Earnings (Monthly) Card Example -->
                    <div class="col-xl-6 for-card col-md-6 mt-4">
                        <div class="card for-card-body-2 shadow h-100  badge-primary"
                             style="background: #362222!important;">
                            <div class="card-body text-light">
                                <div class="flex-between row no-gutters align-items-center">
                                    <div>
                                        <div class="font-weight-bold text-uppercase for-card-text mb-1">
                                            Withdrawable balance
                                        </div>
                                        <div class="for-card-count">৳0.00</div>
                                    </div>
                                    <div>
                                        <a href="javascript:" style="background: #3A6351!important;"
                                           class="btn btn-primary" data-toggle="modal" data-target="#balance-modal">
                                            <i class="tio-wallet-outlined"></i> Withdraw
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Earnings (Monthly) Card Example -->
                    <div class="col-xl-6 for-card col-md-6 mt-4" style="cursor: pointer">
                        <div class="card  shadow h-100 for-card-body-3 badge-info"
                             style="background: #171010!important;">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class=" font-weight-bold for-card-text text-uppercase mb-1">Withdrawn</div>
                                        <div class="for-card-count">৳0.00</div>
                                    </div>
                                    <div class="col-auto for-margin">
                                        <i class="tio-money-vs"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div class="modal fade" id="balance-modal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content"
                 style="text-align: {{Session::get('direction') === "rtl" ? 'right' : 'left'}};">
                <div class="modal-header">
                    <h5 class="modal-title"
                        id="exampleModalLabel">{{translate('Withdraw Request')}}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{route('reseller.withdraw.request')}}" method="post">
                    <div class="modal-body">
                        @csrf
                        <div class="form-group">
                            <label for="recipient-name" class="col-form-label">{{translate('Amount')}}
                                :</label>
                            <input type="number" name="amount" step=".01"
                                   value="{{auth('reseller')->user()->balance}}" class="form-control" id="">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary"
                                data-dismiss="modal">{{translate('Close')}}</button>
                        @if(auth('reseller')->user()->account_no==null || auth('reseller')->user()->bank_name==null)
                            <button type="button" class="btn btn-primary" onclick="call_duty()">
                                {{translate('Incomplete bank info')}}
                            </button>
                        @else
                            <button type="submit"
                                    class="btn btn-primary">{{translate('Request')}}</button>
                        @endif
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection


@push('script')
    <script>
        function call_duty() {
            toastr.warning('{{translate('Update your bank info first!')}}', '{{translate('Warning')}}!', {
                CloseButton: true,
                ProgressBar: true
            });
        }
    </script>
@endpush
