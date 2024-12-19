@extends('layouts.back-end.app')
{{-- @section('title', 'Customer') --}}
@section('title', translate('Membership Details'))

@push('css_or_js')
    <style>
        .custom-select option {
            line-height: 18px !important;
        }
    </style>
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
                                <a class="breadcrumb-link" href="{{ route('admin.membership.list') }}">
                                    {{ translate('Membership List') }}
                                </a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">{{ translate('Membership Details') }}
                            </li>
                        </ol>
                    </nav>

                    <div class="d-sm-flex align-items-sm-center">
                        <div style="display: flex; justify-content: space-between;">
                            <div class="d-sm-flex align-items-sm-center">
                                <h1 class="page-header-title">{{ translate('Membership ID') }}
                                    #{{ $customer->membership->id ?? '' }}</h1>
                                <span class="{{ Session::get('direction') === 'rtl' ? 'mr-2 mr-sm-3' : 'ml-2 ml-sm-3' }}">
                                    <i class="tio-date-range">
                                    </i> {{ translate('Joined At') }} :
                                    {{ date('d M Y H:i:s', strtotime($customer->membership->created_at)) }}
                                </span>
                            </div>

                            <div class="ml-2">
                                <a class="btn btn-danger btn-sm" href="javascript:"
                                    onclick="form_alert('customer-{{ $customer->membership->id }}','Want to delete this item ?')">
                                    <i class="tio-add-to-trash"></i> {{ translate('Delete') }}
                                </a>

                                <form action="{{ route('admin.membership.delete', [$customer->membership->id]) }}"
                                    method="post" id="customer-{{ $customer->membership->id }}">
                                    @csrf @method('delete')
                                </form>
                            </div>
                        </div>
                    </div>


                    <div class="card-body bg-light border-top mb-3">
                        <div class="row">
                            <div class="col-lg col-xxl-10">
                                <table class="table table-bordered table-striped">
                                    <h4 class="fw-semi-bold ls mb-3 text-uppercase">
                                        Customer Profile
                                        #{{ $customer['id'] }}
                                    </h4>

                                    <thead style="background: #0062CC; color: white;">
                                        <tr>
                                            <th class="col-4">Field</th>
                                            <th class="col-8">Value</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td class="fw-semi-bold">First Name</td>
                                            <td>{{ $customer['f_name'] }}</td>
                                        </tr>


                                        <tr>
                                            <td class="fw-semi-bold">Last Name</td>
                                            <td>{{ $customer['l_name'] }}</td>
                                        </tr>

                                        <tr>
                                            <td class="fw-semi-bold">Phone Number</td>
                                            <td>{{ $customer['phone'] }}</td>
                                        </tr>

                                        <tr>
                                            <td class="fw-semi-bold">Email</td>
                                            <td>{{ $customer['email'] }}</td>
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
                                        Membership Information
                                        #{{ $customer->membership->id }}
                                    </h4>

                                    <thead style="background: #0062CC; color: white;">
                                        <tr>
                                            <th class="col-4">Field</th>
                                            <th class="col-8">Value</th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                        <tr>
                                            <td class="fw-semi-bold">Referral ID</td>
                                            <td>{{ $customer->membership->referral_id }}</td>
                                        </tr>

                                        <tr>
                                            <td class="fw-semi-bold">Points</td>
                                            <td>
                                                {{ $customer->membership->points }}

                                                <a href="{{ route('admin.membership.point_history', [$customer['id']]) }}"
                                                    class="btn btn-primary btn-sm ml-2">
                                                    Show Point History
                                                </a>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td class="fw-semi-bold">NID/Passport</td>
                                            <td>
                                                <span class="text-uppercase">
                                                    {{ $customer->membership->verification_type ?? '' }}
                                                </span>
                                                <strong>:</strong>
                                                <span>
                                                    {{ $customer->membership->verification_id ?? '' }}
                                                </span><br>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td class="fw-semi-bold">Referred Customer</td>
                                            <td>
                                                @if ($customer->membership->referredUser)
                                                    <span>
                                                        <i class="tio-user" aria-hidden="true"></i>
                                                        {{ $customer->membership->referredUser->customerfullname() }}
                                                    </span>

                                                    <span>#
                                                        {{ $customer->membership->referredUser->id }}
                                                    </span><br>

                                                    <span><i
                                                            class="tio-online {{ Session::get('direction') === 'rtl' ? 'ml-2' : 'mr-2' }}"></i>
                                                        {{ $customer->membership->referredUser->email }}
                                                    </span><br>

                                                    <span><i
                                                            class="tio-android-phone-vs {{ Session::get('direction') === 'rtl' ? 'ml-2' : 'mr-2' }}"></i>
                                                        {{ $customer->membership->referredUser->phone }}
                                                    </span><br>
                                                @else
                                                    No Referral
                                                @endif

                                            </td>
                                        </tr>

                                        <tr>
                                            <td class="fw-semi-bold">Status</td>
                                            <td class="text-capitalize">
                                                {{-- @if ($customer->membership->status == 'approved')
                                                    <span class="badge badge-success">
                                                        {{ $customer->membership->status }}
                                                    </span>
                                                @elseif($customer->membership->status == 'rejected')
                                                    <span class="badge badge-danger">
                                                        {{ $customer->membership->status }}
                                                    </span>
                                                @elseif($customer->membership->status == 'pending')
                                                    <span class="badge badge-warning p-2" style="font-size: 18px;">
                                                        {{ $customer->membership->status }}
                                                    </span>
                                                @endif --}}

                                                <div class="mt-2">
                                                    <form
                                                        action="{{ route('admin.membership.update', ['id' => $customer->membership->id]) }}"
                                                        method="POST">
                                                        @csrf
                                                        @method('PUT')
                                                        <select class="custom-select" name="membership_status"
                                                            id="membership_status_select" style="width:200px;">
                                                            <option value="approved"
                                                                @if ($customer->membership->status == 'approved') selected @endif
                                                                style="background-color: #28a745; color: white;">
                                                                Approved
                                                            </option>
                                                            <option value="pending"
                                                                @if ($customer->membership->status == 'pending') selected @endif
                                                                style="background-color: #ffc107; color: black;">
                                                                Pending
                                                            </option>
                                                            <option value="rejected"
                                                                @if ($customer->membership->status == 'rejected') selected @endif
                                                                style="background-color: #dc3545; color: white;">
                                                                Rejected
                                                            </option>
                                                        </select>
                                                        <button class="btn btn-primary" type="submit">Change</button>
                                                    </form>
                                                </div>



                                            </td>
                                        </tr>

                                        <tr>
                                            <td class="fw-semi-bold">Apply Date</td>
                                            <td>
                                                {{-- {{ date('d M Y H:i:s', strtotime($customer->membership->created_at)) }} --}}
                                                {{ $customer->membership->created_at->format('d M Y H:i:s') }}
                                            </td>
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



    {{-- Model For Edit --}}
    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Modal title</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    ...
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary">Save changes</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script_2')
    <script>
        $(document).on('ready', function() {
            // INITIALIZATION OF DATATABLES
            // =======================================================
            var datatable = $.HSCore.components.HSDatatables.init($('#columnSearchDatatable'));

            $('#column1_search').on('keyup', function() {
                datatable
                    .columns(1)
                    .search(this.value)
                    .draw();
            });


            $('#column3_search').on('change', function() {
                datatable
                    .columns(2)
                    .search(this.value)
                    .draw();
            });


            // INITIALIZATION OF SELECT2
            // =======================================================
            $('.js-select2-custom').each(function() {
                var select2 = $.HSCore.components.HSSelect2.init($(this));
            });
        });
    </script>

    <script>
        // Function to update select styles based on selected option
        function updateSelectStyles() {
            var selectElement = document.getElementById('membership_status_select');
            var selectedOption = selectElement.options[selectElement.selectedIndex];
            selectElement.style.backgroundColor = selectedOption.style.backgroundColor;
            selectElement.style.color = selectedOption.style.color;
        }

        // Add event listener to detect changes in select input
        document.getElementById('membership_status_select').addEventListener('change', updateSelectStyles);

        // Call the function initially to set initial styles
        updateSelectStyles();
    </script>
@endpush
