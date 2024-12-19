@extends('layouts.back-end.app')

@section('title', translate('Customer List'))

@push('css_or_js')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.css" />
    <style>
        .switch {
            position: relative;
            display: inline-block;
            width: 48px;
            height: 23px;
        }

        .switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }

        .slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #ccc;
            -webkit-transition: .4s;
            transition: .4s;
        }

        .slider:before {
            position: absolute;
            content: "";
            height: 15px;
            width: 15px;
            left: 4px;
            bottom: 4px;
            background-color: white;
            -webkit-transition: .4s;
            transition: .4s;
        }

        input:checked+.slider {
            background-color: #377dff;
        }

        input:focus+.slider {
            box-shadow: 0 0 1px #377dff;
        }

        input:checked+.slider:before {
            -webkit-transform: translateX(26px);
            -ms-transform: translateX(26px);
            transform: translateX(26px);
        }

        /* Rounded sliders */
        .slider.round {
            border-radius: 34px;
        }

        .slider.round:before {
            border-radius: 50%;
        }

        #banner-image-modal .modal-content {
            width: 1116px !important;
            margin-left: -264px !important;
        }

        @media (max-width: 768px) {
            #banner-image-modal .modal-content {
                width: 698px !important;
                margin-left: -75px !important;
            }


        }

        @media (max-width: 375px) {
            #banner-image-modal .modal-content {
                width: 367px !important;
                margin-left: 0 !important;
            }

        }

        @media (max-width: 500px) {
            #banner-image-modal .modal-content {
                width: 400px !important;
                margin-left: 0 !important;
            }


        }
    </style>
@endpush

@section('content')
    <div class="content container-fluid">
        <!-- Page Header -->
        <div class="page-header">
            <div class="row align-items-center mb-3">
                <div class="col-sm">
                    <h1 class="page-header-title">{{ translate('membership') }}
                        {{-- <span class="badge badge-soft-dark ml-2">{{\App\User::count()}}</span> --}}
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
                        <a class="nav-link active" href="#">{{ translate('membership') }} {{ translate('List') }} </a>
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
                                <h5>{{ translate('Membership') }} {{ translate('Table') }}</h5>
                            </div>
                            <div class="mx-1">
                                <h5 style="color: red;">({{ $customers->total() }})</h5>
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
                <form action="{{ route('admin.membership.list') }}" method="GET" id="filter_form">
                    <h4 class="text-center mt-2">Filter By Apply Date</h4>
                    <div class="row d-flex justify-content-center mt-4 mb-4">
                        <input type="date" class="form-control col-3 mr-3" 
                            name="from_date" required
                            value="{{ request('from_date') }}" id="from_date">

                        <input type="date" class="form-control col-3 mr-3" 
                            name="to_date" required
                            value="{{ request('to_date') }}" id="to_date">

                        <button class="btn btn-primary btn-sm" type="submit">
                            <i class="fa fa-filter" aria-hidden="true"></i>
                            Filter
                        </button>

                        <button class="btn btn-info btn-sm ml-2" id="download_csv" 
                            type="button">
                            <i class="fa fa-cloud-download" aria-hidden="true"></i>
                            Download
                        </button>

                        <button class="btn btn-danger btn-sm ml-2" id="filter_clear" 
                            type="button">
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
                    style="width: 100%"
                    data-hs-datatables-options='{
                     "columnDefs": [{
                        "targets": [0],
                        "orderable": false
                      }],
                     "order": [],
                     "info": {
                       "totalQty": "#datatableWithPaginationInfoTotalQty"
                     },
                     "search": "#datatableSearch",
                     "entries": "#datatableEntries",
                     "pageLength": 25,
                     "isResponsive": false,
                     "isShowPaging": false,
                     "pagination": "datatablePagination"
                   }'>
                    <thead class="thead-light">
                        <tr>
                            <th class="">
                                #
                            </th>
                            <th class="table-column-pl-0">{{ translate('Name') }}</th>
                            <th>{{ translate('Email') }}/{{ translate('Phone') }}</th>
                            <th>NID/Passport</th>
                            <th>Referral ID</th>
                            <th>Points</th>
                            <th>{{ translate('Total') }} Refferd </th>
                            <th>Status</th>
                            <th>{{ translate('Action') }}</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($customers as $key => $customer)
                            <tr class="">
                                <td class="">
                                    {{ $customers->firstItem() + $key }}
                                </td>
                                <td class="table-column-pl-0">
                                    <a href="{{ route('admin.customer.view', [$customer['id']]) }}">
                                        {{ $customer['f_name'] . ' ' . $customer['l_name'] }}
                                    </a>
                                </td>

                                <td>
                                    <span>{{ $customer['email'] }}</span><br>
                                    <span>{{ $customer['phone'] }}</span>
                                </td>

                                <td>
                                    <span class="text-uppercase">
                                        {{ $customer->membership->verification_type ?? '' }}
                                    </span><br>
                                    <span>
                                        {{ $customer->membership->verification_id ?? '' }}
                                    </span>
                                </td>

                                <td>
                                    {{ $customer->membership->referral_id ?? '-' }}
                                </td>

                                <td>
                                    {{ $customer->membership->points }}
                                </td>

                                <td>
                                    <label class="badge badge-info p-2">
                                        {{ $customer->referral_count }}
                                    </label>
                                </td>

                                <td>
                                    @if ($customer->membership->status == 'approved')
                                        <span class="badge bg-success text-light p-2" style="width: 100px;">
                                            {{ $customer->membership && $customer->membership->status == 'approved' ? 'Approved' : '-' }}
                                        </span>
                                    @elseif ($customer->membership->status == 'pending')
                                        <span class="badge bg-warning text-dark p-2" style="width: 100px;">
                                            {{ $customer->membership && $customer->membership->status == 'pending' ? 'Pending' : '-' }}
                                        </span>
                                    @elseif ($customer->membership->status == 'rejected')
                                        <span class="badge bg-danger text-light p-2" style="width: 100px;">
                                            {{ $customer->membership && $customer->membership->status == 'rejected' ? 'Rejected' : '-' }}
                                        </span>
                                    @endif
                                </td>

                                <td>
                                    <div class="dropdown">
                                        <button class="btn btn-outline-secondary dropdown-toggle" type="button"
                                            id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true"
                                            aria-expanded="false">
                                            <i class="tio-settings"></i>
                                        </button>
                                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                            <a class="dropdown-item"
                                                href="{{ route('admin.membership.show', [$customer['id']]) }}">
                                                <i class="tio-visible"></i> {{ translate('View') }}
                                            </a>
                                            {{-- <a class="dropdown-item" target="" href="">
                                            <i class="tio-download"></i> Suspend
                                        </a> --}}
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <!-- End Table -->

            <!-- Footer -->
            <div class="card-footer">
                {!! $customers->links() !!}
            </div>
            @if (count($customers) == 0)
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
        $(document).on('ready', function() {
            // INITIALIZATION OF DATATABLES
            // =======================================================
            var datatable = $.HSCore.components.HSDatatables.init($('#datatable'), {
                dom: 'Bfrtip',
                buttons: [{
                        extend: 'copy',
                        className: 'd-none'
                    },
                    {
                        extend: 'excel',
                        className: 'd-none'
                    },
                    {
                        extend: 'csv',
                        className: 'd-none'
                    },
                    {
                        extend: 'pdf',
                        className: 'd-none'
                    },
                    {
                        extend: 'print',
                        className: 'd-none'
                    },
                ],
                select: {
                    style: 'multi',
                    selector: 'td:first-child input[type="checkbox"]',
                    classMap: {
                        checkAll: '#datatableCheckAll',
                        counter: '#datatableCounter',
                        counterInfo: '#datatableCounterInfo'
                    }
                },
                // language: {
                //     zeroRecords: '<div class="text-center p-4">' +
                //         '<img class="mb-3" src="{{ asset('assets/back-end') }}/svg/illustrations/sorry.svg" alt="Image Description" style="width: 7rem;">' +
                //         '<p class="mb-0">No data to show</p>' +
                //         '</div>'
                // }
            });

            $('#datatableSearch').on('mouseup', function(e) {
                var $input = $(this),
                    oldValue = $input.val();

                if (oldValue == "") return;

                setTimeout(function() {
                    var newValue = $input.val();

                    if (newValue == "") {
                        // Gotcha
                        datatable.search('').draw();
                    }
                }, 1);
            });
        });
    </script>

    <script>
        $(document).on('change', '.status', function() {
            var id = $(this).attr("id");
            if ($(this).prop("checked") == true) {
                var status = 1;
            } else if ($(this).prop("checked") == false) {
                var status = 0;
            }

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                }
            });
            $.ajax({
                url: "{{ route('admin.customer.status-update') }}",
                method: 'POST',
                data: {
                    id: id,
                    status: status
                },
                success: function() {
                    toastr.success('{{ translate('Status updated successfully') }}');
                }
            });
        });
    </script>


    <script>
        document.getElementById('filter_clear').addEventListener('click', function() {
            document.getElementById('from_date').value = '';
            document.getElementById('to_date').value = '';

            window.location.href = '{{ route('admin.membership.list') }}';
        });

        document.getElementById('download_csv').addEventListener('click', function() {
            // Add hidden input to trigger CSV download
            var form = document.getElementById('filter_form');
            var input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'download';
            input.value = 'csv';
            form.appendChild(input);

            // Submit the form
            form.submit();

            form.removeChild(input);
        });
    </script>
@endpush
