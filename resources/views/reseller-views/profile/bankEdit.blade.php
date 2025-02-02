@extends('layouts.back-end.app-reseller')

@section('title', translate('Bank Info'))

@push('css_or_js')
    <!-- Custom styles for this page -->
    <link href="{{ asset('assets/back-end') }}/vendor/datatables/dataTables.bootstrap4.min.css" rel="stylesheet">
    <!-- Custom styles for this page -->
    <link href="{{ asset('assets/back-end/css/croppie.css') }}" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endpush

@section('content')
    <div class="content container-fluid" style="text-align: {{ Session::get('direction') === 'rtl' ? 'right' : 'left' }};">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a
                        href="{{ route('reseller.dashboard.index') }}">{{ translate('Dashboard') }}</a></li>
                <li class="breadcrumb-item" aria-current="page">{{ translate('reseller') }}</li>
                <li class="breadcrumb-item">{{ translate('Bank info') }}</li>
            </ol>
        </nav>

        <!-- Content Row -->
        <div class="row mt-4">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h1 class="h3 mb-0 ">{{ translate('Edit Bank Info') }}</h1>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('reseller.profile.bank_update', [$data->id]) }}" method="post"
                            enctype="multipart/form-data">
                            @csrf
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-6" <label for="name">{{ translate('Bank Name') }} <span
                                            class="text-danger">*</span></label>
                                        <input type="text" name="bank_name" value="{{ $data->bank_name }}"
                                            class="form-control" id="name" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="name">{{ translate('Branch Name') }} <span
                                                class="text-danger">*</span></label>
                                        <input type="text" name="branch" value="{{ $data->branch }}"
                                            class="form-control" id="name" required>
                                    </div>
                                </div>

                            </div>
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-6">
                                        <label for="account_no">{{ translate('Holder Name') }} <span
                                                class="text-danger">*</span></label>
                                        <input type="text" name="holder_name" value="{{ $data->holder_name }}"
                                            class="form-control" id="account_no" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="account_no">{{ translate('Account No') }} <span
                                                class="text-danger">*</span></label>
                                        <input type="number" name="account_no" value="{{ $data->account_no }}"
                                            class="form-control" id="account_no" required>
                                    </div>

                                </div>

                            </div>

                            <button type="submit" class="btn btn-primary"
                                id="btn_update">{{ translate('Update') }}</button>
                            <a class="btn btn-danger"
                                href="{{ route('reseller.profile.view') }}">{{ translate('Cancel') }}</a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
@endpush
