@extends('layouts.back-end.app')

@section('title', translate('Point History List'))

@push('css_or_js')
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endpush

@section('content')
    <div class="content container-fluid">
        <!-- Page Header -->
        <div class="page-header">
            <div class="row align-items-center mb-3">
                <div class="col-sm">
                    <h1 class="page-header-title">{{ translate('Point History') }}
                        <span class="badge badge-soft-dark ml-2"></span>
                    </h1>
                </div>
            </div>
            <!-- End Row -->

            <!-- Nav Scroller -->
            <div class="js-nav-scroller hs-nav-scroller-horizontal">
                <span class="hs-nav-scroller-arrow-prev" style="display: none;">
                    <a class="hs-nav-scroller-arrow-link" href="javascript:;">
                        <i class="tio-chevron-left"></i>
                    </a>
                </span>

                <span class="hs-nav-scroller-arrow-next" style="display: none;">
                    <a class="hs-nav-scroller-arrow-link" href="javascript:;">
                        <i class="tio-chevron-right"></i>
                    </a>
                </span>

                <!-- Nav -->
                <ul class="nav nav-tabs page-header-tabs">
                    <li class="nav-item">
                        <a class="nav-link active" href="#">{{ translate('Point History') }} {{ translate('List') }}
                        </a>
                    </li>
                </ul>
                <!-- End Nav -->
            </div>
            <!-- End Nav Scroller -->
        </div>
        <!-- End Page Header -->

        <!-- Card -->
        <div class="card">
            <!-- Header -->
            <div class="card-header">

                <div class="flex-between row justify-content-between align-items-center flex-grow-1 mx-1">
                    <div>
                        <div class="flex-start">
                            <div>
                                <h5>{{ translate('Point History') }} {{ translate('Table') }}</h5>
                            </div>
                            <div class="mx-1">
                                <h5 style="color: red;">({{ $pointhistories->total() }})</h5>
                            </div>
                        </div>
                    </div>
                    <div style="width: 40vw">
                        <!-- Search -->
                        <form action="{{ url()->current() }}" method="GET">
                            <div class="input-group input-group-merge input-group-flush">
                                <div class="input-group-prepend">
                                    <div class="input-group-text">
                                        <i class="tio-search"></i>
                                    </div>
                                </div>
                                <input id="datatableSearch_" type="search" name="search" class="form-control"
                                    placeholder="{{ translate('Search by Name or Email or Phone') }}"
                                    aria-label="Search orders" value="{{ $search }}" required>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fa fa-search" aria-hidden="true"></i>
                                    {{ translate('search') }}
                                </button>
                            </div>
                        </form>
                        <!-- End Search -->
                    </div>
                </div>
                <!-- End Row -->
            </div>
            <!-- End Header -->

            <div>
                <form action="" method="GET" id="filter_form">
                    {{-- <h4 class="text-center mt-2">Filter</h4> --}}
                    <div class="row d-flex justify-content-center mt-4 mb-4">
                        <input type="date" class="form-control col-2 mr-3" name="from_date"
                            value="{{ request('from_date') }}" id="from_date">

                        <input type="date" class="form-control col-2 mr-3" name="to_date"
                            value="{{ request('to_date') }}" id="to_date">

                        <select class="form-control col-2 mr-3" name="status">
                            <option value="" {{ request('status') === '' ? 'selected' : '' }}>Select</option>
                            <option value="self" {{ request('status') === 'self' ? 'selected' : '' }}>Self</option>
                            <option value="referred" {{ request('status') === 'referred' ? 'selected' : '' }}>All Referred
                            </option>
                            @foreach ($referredUsers as $refe)
                                <option value="{{ $refe->user->id }}"
                                    {{ request('status') === (string) $refe->user->id ? 'selected' : '' }}>
                                    {{ $refe->user->f_name }} {{ $refe->user->l_name }}
                                </option>
                            @endforeach
                        </select>

                        <button class="btn btn-primary btn-sm" type="submit">
                            <i class="fa fa-filter" aria-hidden="true"></i>
                            Filter
                        </button>

                        <button class="btn btn-info btn-sm ml-2" type="submit" name="download_csv">
                            <i class="fa fa-cloud-download" aria-hidden="true"></i>
                            Download
                        </button>

                        <button class="btn btn-danger btn-sm ml-2" id="filter_clear" type="button">
                            <i class="fa fa-times" aria-hidden="true"></i>
                            Clear
                        </button>
                    </div>
                </form>
            </div>


            <!-- Table -->
            <div class="table-responsive datatable-custom">
                <table style="text-align: {{ Session::get('direction') === 'rtl' ? 'right' : 'left' }};"
                    class="table table-hover table-borderless table-thead-bordered table-nowrap table-align-middle card-table"
                    style="width: 100%">
                    <thead class="thead-light">
                        <tr>
                            <th class="">
                                #
                            </th>
                            {{-- <th class="table-column-pl-0">
                                Customer Info
                            </th> --}}
                            <th>Referred Customer Info</th>
                            {{-- <th>status</th> --}}
                            <th>Order Amount</th>
                            <th>Points</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($pointhistories as $key => $pointhistorie)
                            <tr class="">
                                <td class="">{{ $pointhistories->firstItem() + $key }}</td>
                                {{-- 
                                <td class="table-column-pl-0">
                                    <span>{{ $pointhistorie->user->f_name }}</span>
                                    <span>{{ $pointhistorie->user->l_name }}</span><br>
                                    <span>{{ $pointhistorie->user->email }}</span><br>
                                    <span>{{ $pointhistorie->user->phone }}</span><br>
                                </td> --}}

                                <td>
                                    @if ($pointhistorie->referredUser)
                                        <span>{{ $pointhistorie->referredUser->f_name }}</span>
                                        <span>{{ $pointhistorie->referredUser->l_name }}</span><br>
                                        <span>{{ $pointhistorie->referredUser->email }}</span><br>
                                        <span>{{ $pointhistorie->referredUser->phone }}</span>
                                    @else
                                        Self
                                    @endif
                                </td>

                                {{-- <td>{{ ucfirst($pointhistorie->status) }}</td> --}}

                                <td>{{ $pointhistorie->order_amount }}</td>

                                <td>{{ $pointhistorie->points }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="thead-light">
                        <tr>
                            <td></td>
                            <td></td>
                            <td>
                                Total={{ $totalAmount }}
                            </td>
                            <td>
                                Total={{ $totalPoints }}
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            <!-- End Table -->

            <!-- Footer -->
            <div class="card-footer">
                {!! $pointhistories->links() !!}
            </div>
            @if (count($pointhistories) == 0)
                <div class="text-center p-4">
                    <img class="mb-3" src="{{ asset('assets/back-end') }}/svg/illustrations/sorry.svg"
                        alt="Image Description" style="width: 7rem;">
                    <p class="mb-0">{{ translate('No data to show') }}</p>
                </div>
            @endif
            <!-- End Footer -->
        </div>
        <!-- End Card -->
    </div>
@endsection

@push('script_2')
    <script>
        const userId = {{ $userId }}; // Retrieve the userId from the PHP variable

        document.getElementById('filter_clear').addEventListener('click', function() {
            document.getElementById('from_date').value = '';
            document.getElementById('to_date').value = '';

            window.location.href = '{{ route('admin.membership.point_history', ['id' => $userId]) }}';
        });
    </script>
@endpush
