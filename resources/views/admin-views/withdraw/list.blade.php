@php use App\Models\Seller; @endphp
@extends('layouts.back-end.app')

@section('title', translate('Withdraw List'))

@push('css_or_js')
    <meta name="csrf-token" content="{{ csrf_token() }}">
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

        input:checked + .slider {
            background-color: #377dff;
        }

        input:focus + .slider {
            box-shadow: 0 0 1px #377dff;
        }

        input:checked + .slider:before {
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
                    <h1 class="page-header-title">{{ translate('Withdraw') }} {{ translate('List') }}
                        <span class="badge badge-soft-dark ml-2">{{ \App\Models\WithdrawRequest::count() }}</span>
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
                        <a class="nav-link active" href="#">{{ translate('Withdraw') }} {{ translate('List') }} </a>
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
                                <h5>{{ translate('withdraw') }} {{ translate('Table') }}</h5>
                            </div>
                            <div class="mx-1">
                                <h5 style="color: red;">({{ $withdraws->count() }})</h5>
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
                                <button type="submit" class="btn btn-primary">{{ translate('search') }}</button>
                            </div>
                        </form>
                        <!-- End Search -->
                    </div>
                </div>
                <!-- End Row -->
            </div>
            <!-- End Header -->

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
                        <th class="table-column-pl-0">
                            {{ translate('Person Type') }}
                        </th>

                        <th class="table-column-pl-0">
                            {{ translate('Person Information') }}
                        </th>
                        <th>{{ translate('Amount') }}</th>
                        <th>{{ translate('Approved') }}</th>
                        <th>{{ translate('Action') }}</th>
                    </tr>
                    </thead>

                    <tbody>
                    @foreach ($withdraws as $key => $withdraw)
                        <tr class="">
                            <td class="">
                                {{ $key + 1 }}
                            </td>

                            <td class="table-column-pl-0">
                                <span style="text-transform: capitalize">{{ $withdraw->person }}</span>
                            </td>

                            <td>
                                    <span>
                                        <i class="tio-user" aria-hidden="true"></i>
                                        #{{ $withdraw->person_id }} {{ $withdraw->user?->f_name }}
                                    </span><br>

                                <span>
                                        <i class="tio-online {{ Session::get('direction') === 'rtl' ? 'ml-2' : 'mr-2' }}"></i>
                                        {{ $withdraw->user?->email }}
                                    </span><br>

                                <span>
                                        <i class="tio-android-phone-vs {{ Session::get('direction') === 'rtl' ? 'ml-2' : 'mr-2' }}"></i>
                                        {{ $withdraw->user?->phone }}
                                    </span><br>
                            </td>

                            <td>
                                @if ($withdraw->person === 'seller')
                                    <span>
                                        <?php

                                            $user = Seller::where('id', $withdraw->person_id)->first();
                                            ?>
                                        {{ $user?->f_name }}
                                        {{ $user?->l_name }}
                                        </span>
                                    <br>
                                    <span>{{  $user?->email }}</span><br>
                                    <span>{{  $user?->phone }}</span><br>
                                @elseif($withdraw->person === 'reseller')
                                    <span>
                                        <?php
                                            $user = \App\Models\Reseller::where('id', $withdraw->person_id)->first();
                                            ?>
                                        {{ $user?->f_name }}
                                        {{ $user?->l_name }}
                                        </span>
                                    <br>
                                    <span>{{  $user?->email }}</span><br>
                                    <span>{{  $user?->phone }}</span><br>

                                @elseif($withdraw->person === 'product_manager')
                                    <span>
                                        <?php
                                            $user = \App\Models\ProductManager::where('id', $withdraw->person_id)->first();
                                            ?>
                                        {{ $user?->f_name }}
                                        {{ $user?->l_name }}
                                        </span>
                                    <br>
                                    <span>{{  $user?->email }}</span><br>
                                    <span>{{  $user?->phone }}</span><br>
                                @endif
                            </td>

                            <td>
                                {{ $withdraw->amount }}
                            </td>

                            <td>
                                <label class="switch">
                                    <input type="checkbox" class="status" id="{{ $withdraw->id }}"
                                        {{ $withdraw->approved == 1 ? 'checked' : '' }}>
                                    <span class="slider round"></span>
                                </label>
                            </td>

                            <td>
                                <div class="dropdown">
                                    <button class="btn btn-outline-secondary dropdown-toggle" type="button"
                                            id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true"
                                            aria-expanded="false">
                                        <i class="tio-settings"></i>
                                    </button>
                                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                        <a class="dropdown-item" href="">
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
            @if (count($withdraws) == 0)
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
        $(document).on('ready', function () {
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

            $('#datatableSearch').on('mouseup', function (e) {
                var $input = $(this),
                    oldValue = $input.val();

                if (oldValue == "") return;

                setTimeout(function () {
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
        $(document).on('change', '.status', function () {
            var id = $(this).attr("id");

            // console.log("id =", id);

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
                url: "{{ route('admin.withdraw.status_update') }}",
                method: 'POST',
                data: {
                    id: id,
                    status: status
                },
                success: function (response) {
                    // toastr.success('{{ translate('Status updated successfully') }}');
                    toastr.success(response.message);
                },
                error: function (xhr, textStatus, errorThrown) {
                    toastr.error("Failed to update status: " + errorThrown);
                }
            });
        });
    </script>
@endpush
