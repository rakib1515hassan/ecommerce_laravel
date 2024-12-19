@extends('layouts.back-end.app')

@section('title', translate('Question and answering'))

@push('css_or_js')

@endpush

@section('content')
    <div class="content container-fluid">
        <!-- Page Header -->
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}">{{translate('Dashboard')}}</a></li>
                <li class="breadcrumb-item" aria-current="page">{{translate('question and answering')}}</li>
            </ol>
        </nav>
        <!-- End Page Header -->
        <div class="row gx-2 gx-lg-3">
            <div class="col-sm-12 col-lg-12 mb-3 mb-lg-2">
                <!-- Card -->
                <div class="card">
                    <!-- Header -->
                    <div class="card-header">
                        <div class="flex-between row justify-content-between align-items-center flex-grow-1 mx-1">
                            <div>
                                <div class="flex-start">
                                    <div>
                                        <h5>{{ translate('Question and Answering')}}</h5>
                                    </div>
                                    {{-- <div class="mx-1"><h5 style="color: rgb(252, 59, 10);">({{ $reviews->total() }})</h5></div> --}}
                                </div>
                            </div>
                            <div style="width: 40vw">
                                <!-- Search -->
                                <form>
                                    <div class="input-group input-group-merge input-group-flush">
                                        <div class="input-group-prepend">
                                            <div class="input-group-text">
                                                <i class="tio-search"></i>
                                            </div>
                                        </div>
                                        <input id="datatableSearch_" type="search" name="search" class="form-control"
                                               placeholder="{{translate('Search by Product or Question')}}"
                                               aria-label="Search orders"> {{-- value="{{ $search }}" --}}
                                        <button type="submit" class="btn btn-primary">{{translate('search')}}</button>
                                    </div>
                                </form>
                                <!-- End Search -->
                            </div>
                        </div>
                    </div>
                    <!-- End Header -->

                    <!-- Table -->
                    <div class="card-body" style="padding: 0">
                        <div class="table-responsive datatable-custom">
                            <table id="columnSearchDatatable"
                                   style="text-align: {{Session::get('direction') === "rtl" ? 'right' : 'left'}};"
                                   class="table table-borderless table-thead-bordered table-nowrap table-align-middle card-table"
                                   data-hs-datatables-options='{
                                    "order": [],
                                    "orderCellsTop": true
                                }'>
                                <thead class="thead-light">
                                <tr>
                                    <th>#{{ translate('sl')}}</th>
                                    <th style="width: 30%">{{ translate('Product')}}</th>
                                    <th style="width: 25%">{{ translate('Question')}}</th>
                                    <th>{{ translate('Status')}}</th>
                                    <th>{{ translate('Action')}}</th>
                                </tr>
                                </thead>

                                <tbody>
                                @foreach($qas as $qa)
                                        <tr>
                                            <td>{{$loop->index}}</td>
                                            <td>
                                            <span class="d-block font-size-sm text-body">
                                                @if($qa->product_id)
                                                <a href="{{route('admin.product.view',$qa->product_id)}}">
                                                    {{ $qa->name }}
                                                </a>
                                                @else
                                                {{ $qa->name }}
                                                @endif
                                            </span>
                                            </td>
                                            <td>{{ $qa->question}}</td>
                                            <td>
                                                <label class="uppercase badge @if($qa->status == 'read') badge-soft-info @else badge-soft-danger @endif">
                                                    {{$qa->status}}
                                                </label>
                                            </td>
                                            <td>
                                                <a href="{{ route('admin.qa.show',$qa->id)}}" class="btn btn-success btn-sm">view</a>
                                            </td>
                                        </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="card-footer">
                        {{$qas->links()}}
                    </div>
                    @if(count($qas)==0)
                        <div class="text-center p-4">
                            <img class="mb-3" src="{{asset('assets/back-end')}}/svg/illustrations/sorry.svg"
                                 alt="Image Description" style="width: 7rem;">
                            <p class="mb-0">{{ translate('No_data_to_show')}}</p>
                        </div>
                    @endif
                    <!-- End Table -->
                </div>
                <!-- End Card -->
            </div>
        </div>
    </div>
@endsection
