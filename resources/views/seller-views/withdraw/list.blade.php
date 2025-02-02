@extends('layouts.back-end.app-seller')

@section('title', translate('Withdraw Request'))

@push('css_or_js')
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endpush

@section('content')
    <div class="content container-fluid">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a
                            href="{{route('seller.dashboard.index')}}">{{translate('Dashboard')}}</a>
                </li>
                <li class="breadcrumb-item" aria-current="page">{{translate('Withdraw')}}  </li>
            </ol>
        </nav>

        <div class="row" style="margin-top: 20px">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h5>{{ translate('Withdraw Request Table')}}</h5>
                        <select name="withdraw_status_filter" onchange="status_filter(this.value)"
                                class="custom-select float-right" style="width: 200px">
                            <option value="all" {{session()->has('withdraw_status_filter') && session('withdraw_status_filter') == 'all'?'selected':''}}>{{translate('All')}}</option>
                            <option value="approved" {{session()->has('withdraw_status_filter') && session('withdraw_status_filter') == 'approved'?'selected':''}}>{{translate('Approved')}}</option>
                            <option value="denied" {{session()->has('withdraw_status_filter') && session('withdraw_status_filter') == 'denied'?'selected':''}}>{{translate('Denied')}}</option>
                            <option value="pending" {{session()->has('withdraw_status_filter') && session('withdraw_status_filter') == 'pending'?'selected':''}}>{{translate('Pending')}}</option>

                        </select>
                    </div>
                    <div class="card-body" style="padding: 0">
                        <div class="table-responsive">
                            <table id="datatable"
                                   style="text-align: {{Session::get('direction') === "rtl" ? 'right' : 'left'}};"
                                   class="table table-hover table-borderless table-thead-bordered table-nowrap table-align-middle card-table"
                                   style="width: 100%">
                                <thead class="thead-light">
                                <tr>
                                    <th>{{translate('SL#')}}</th>
                                    <th>{{translate('amount')}}</th>
                                    <th>{{translate('request_time')}}</th>
                                    <th>{{translate('status')}}</th>
                                    <th style="width: 5px">{{translate('Action')}}</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($withdraw_requests as $key=>$withdraw_request)
                                    <tr>
                                        <td scope="row">{{$withdraw_requests->firstitem()+$key}}</td>
                                        <td>{{\App\Services\BackEndHelper::set_symbol(\App\Services\BackEndHelper::usd_to_currency($withdraw_request['amount']))}}</td>
                                        <td>{{date("F jS, Y", strtotime($withdraw_request->created_at))}}</td>
                                        <td>
                                            @if($withdraw_request->approved==0)
                                                <label class="badge badge-primary">{{translate('Pending')}}</label>
                                            @elseif($withdraw_request->approved==1)
                                                <label class="badge badge-success">{{translate('Approved')}}</label>
                                            @else
                                                <label class="badge badge-danger">{{translate('Denied')}}</label>
                                            @endif
                                        </td>
                                        <td>
                                            @if($withdraw_request->approved==0)
                                                <button id="{{route('seller.business-settings.withdraw.cancel', [$withdraw_request['id']])}}"
                                                        onclick="close_request('{{ route('seller.business-settings.withdraw.cancel', [$withdraw_request['id']]) }}')"
                                                        class="btn btn-primary btn-sm">
                                                    {{translate('close')}}
                                                </button>
                                            @else
                                                <span class="btn btn-primary btn-sm disabled">
                                                    {{translate('close')}}
                                                </span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="card-footer">
                        {{$withdraw_requests->links()}}
                    </div>
                </div>
            </div>

        </div>

    </div>
@endsection


@push('script_2')
    <script>
        function status_filter(type) {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.post({
                url: '{{route('seller.business-settings.withdraw.status-filter')}}',
                data: {
                    withdraw_status_filter: type
                },
                beforeSend: function () {
                    $('#loading').show()
                },
                success: function (data) {
                    location.reload();
                },
                complete: function () {
                    $('#loading').hide()
                }
            });
        }
    </script>

    <script>
        function close_request(route_name) {
            swal({
                title: "{{translate('Are you sure?')}}",
                text: "{{translate('Once deleted, you will not be able to recover this')}}",
                icon: "{{translate('warning')}}",
                buttons: true,
                dangerMode: true,
            })
                .then((willDelete) => {
                    if (willDelete) {
                        window.location.href = (route_name);
                    }
                });
        }
    </script>
@endpush
