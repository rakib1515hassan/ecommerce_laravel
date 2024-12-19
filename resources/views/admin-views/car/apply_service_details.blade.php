@extends('layouts.back-end.app')
{{-- @section('title', 'Customer') --}}
@section('title', translate('Apply Service Details'))

@push('css_or_js')
@endpush

@section('content')
    <div class="content container-fluid">
        <!-- Page Header -->
        <div class="d-print-none pb-2">
            <div class="row align-items-center">
                <div class="col-sm mb-2 mb-sm-0">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb breadcrumb-no-gutter">
                            <li class="breadcrumb-item">
                                <a class="breadcrumb-link" href="{{ route('admin.service.apply_service_list') }}">
                                    {{ translate('Apply Service List') }}
                                </a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">{{ translate('Apply Service Details') }}
                            </li>
                        </ol>
                    </nav>

                    <div class="d-sm-flex align-items-sm-center">
                        <h1 class="page-header-title">{{ translate('Apply Service ID') }}
                            #{{ $apply_service->id ?? '' }}</h1>
                        <span class="{{ Session::get('direction') === 'rtl' ? 'mr-2 mr-sm-3' : 'ml-2 ml-sm-3' }}">
                            <i class="tio-date-range">
                            </i> {{ translate('Apply At') }} :
                            {{ date('d M Y H:i:s', strtotime($apply_service->created_at)) }}
                        </span>
                    </div>


                    <div class="card-body bg-light border-top mb-3">
                        <div class="row">
                            <div class="col-lg col-xxl-10">
                                <table class="table table-bordered table-striped">
                                    <h4 class="fw-semi-bold ls mb-3 text-uppercase">
                                        Customer Profile 
                                        <a class="btn btn-primary btn-sm" 
                                            href="{{ route('admin.customer.view', [$apply_service->user->id]) }}">
                                            Details
                                        </a>
                                        {{-- #{{ $customer['id'] }} --}}
                                    </h4>

                                    <thead style="background: #0062CC; color: white;">
                                        <tr>
                                            <th class="col-4">Field</th>
                                            <th class="col-8">Value</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td class="fw-semi-bold">Name</td>
                                            <td>{{ $apply_service->user->fullname }}</td>
                                        </tr>

                                        <tr>
                                            <td class="fw-semi-bold">Phone Number</td>
                                            <td>{{ $apply_service->user->phone }}</td>
                                        </tr>

                                        <tr>
                                            <td class="fw-semi-bold">Email</td>
                                            <td>{{ $apply_service->user->email }}</td>
                                        </tr>

                                        <tr>
                                            <td class="fw-semi-bold">Service Contact</td>
                                            <td>{{ $apply_service->phone }}</td>
                                        </tr>

                                        <tr>
                                            <td class="fw-semi-bold">Service Description</td>
                                            <td>{{ $apply_service->description }}</td>
                                        </tr>

                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>



                    <div class="card-body bg-light border-top mb-3">
                        <div class="row">
                            <div class="col-lg col-xxl-10">
                                <table class="table table-bordered table-striped">
                                    <h4 class="fw-semi-bold ls mb-3 text-uppercase">
                                        Service Details
                                        <a class="btn btn-primary btn-sm" 
                                            href="{{ route('admin.service.show_update_page', [$apply_service->service->id]) }}">
                                            Details
                                        </a>
                                        {{-- #{{ $customer['id'] }} --}}
                                    </h4>

                                    <thead style="background: #0062CC; color: white;">
                                        <tr>
                                            <th class="col-4">Field</th>
                                            <th class="col-8">Value</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td class="fw-semi-bold">Service Name</td>
                                            <td>{{ $apply_service->service->name }}</td>
                                        </tr>

                                        <tr>
                                            <td class="fw-semi-bold">Category Name</td>
                                            <td>{{ $apply_service->service->category->name }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                </div>


            </div>
        </div>
        <!-- End Page Header -->


        <!-- End Row -->
    </div>

@endsection

@push('script_2')
@endpush
