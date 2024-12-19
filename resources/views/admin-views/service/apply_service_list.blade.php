@extends('layouts.back-end.app')

@section('title', translate('Apply Service List'))

@push('css_or_js')
@endpush

@section('content')
    <div class="content container-fluid"> <!-- Page Heading -->
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ translate('Dashboard') }}</a></li>
                <li class="breadcrumb-item" aria-current="page">{{ translate('Service') }}</li>
            </ol>
        </nav>

        <div class="row" style="margin-top: 20px">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row flex-between justify-content-between align-items-center flex-grow-1">
                            <div>
                                <h5 class="flex-between ml-3">
                                    <div>{{ translate('Apply Service Table') }}</div>
                                    <div style="color: red; padding: 0 .4375rem;"></div>
                                </h5>
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
                                            placeholder="{{ translate('Search') }}" aria-label="Search orders"
                                            value="" required>
                                        <input type="hidden" value="" name="status">

                                        <button type="submit" class="btn btn-primary">
                                            <i class="fa fa-search" aria-hidden="true"></i>
                                            {{ translate('search') }}
                                        </button>
                                    </div>
                                </form>
                                <!-- End Search -->
                            </div>

                        </div>
                    </div>

                    <div class="card-body" style="padding: 0">

                        {{-- Filter --}}
                        <div>
                            <form action="{{ route('admin.service.apply_service_list') }}" method="GET" id="filter_form">
                                <h4 class="text-center mt-2">Filter By Apply Date</h4>
                                <div class="row d-flex justify-content-center mt-4 mb-4">
                                    <input type="date" class="form-control col-2 mr-2" name="from_date" required value="{{ request('from_date') }}" id="from_date">

                                    <input type="date" class="form-control col-2 mr-2" name="to_date" required
                                        value="{{ request('to_date') }}" id="to_date">

                                    <select class="form-control col-2 mr-2" name="category_type" id="category_type">

                                        <option value="all" selected>All Services</option>

                                        <option value="service"
                                            {{ request('category_type') == 'service' ? 'selected' : '' }}>
                                            Service
                                        </option>

                                        <option value="car" {{ request('category_type') == 'car' ? 'selected' : '' }}>
                                            Car
                                        </option>

                                        <option value="property"
                                            {{ request('category_type') == 'property' ? 'selected' : '' }}>
                                            Property
                                        </option>
                                    </select>

                                    <button class="btn btn-primary btn-sm" type="submit">
                                        <i class="fa fa-filter" aria-hidden="true"></i>
                                        Filter
                                    </button>

                                    <button class="btn btn-info btn-sm ml-2" id="download_csv" type="button">
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

                        {{-- Table --}}
                        <div class="table-responsive">
                            <table id="datatable"
                                style="text-align: {{ Session::get('direction') === 'rtl' ? 'right' : 'left' }};"
                                class="table table-hover table-borderless table-thead-bordered table-nowrap table-align-middle card-table"
                                style="width: 100%">
                                <thead class="thead-light">
                                    <tr>
                                        <th style="width: 5%;">{{ translate('SL#') }}</th>
                                        <th style="width: 15%;">{{ translate('Customer Info') }}</th>
                                        <th style="width: 20%;">{{ translate('Contact') }}</th>
                                        <th style="width: 15%;">{{ translate('Service Name') }}</th>
                                        <th style="width: 15%;">{{ translate('Service Category') }}</th>
                                        <th style="width: 10%;">{{ translate('Price') }}</th>
                                        <th style="width: 10%;">{{ translate('Created') }}</th>
                                        <th style="width: 10%;">{{ translate('Updated') }}</th>
                                        <th style="width: 5%;" class="text-center">{{ translate('Action') }}</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    @foreach ($services as $k => $service)
                                        <tr>
                                            <th scope="row">{{ $k + 1 }}</th> {{-- Loop count starts from 0, so add 1 --}}

                                            <td>
                                                <a href="{{ route('admin.customer.view', [$service->user->id]) }}">
                                                    <span>{{ $service->user->fullname }}</span>
                                                </a><br>
                                                <span>{{ $service->user->phone }}</span><br>
                                                <span>{{ $service->user->email }}</span>
                                            </td>

                                            <td>
                                                <span>{{ $service->phone }}</span><br>

                                                {{-- Description --}}
                                                {{-- <span>{{ $service->description }}</span> --}}
                                            </td>

                                            <td>
                                                <a
                                                    href="{{ route('admin.service.show_update_page', [$service->service->id]) }}">
                                                    {{-- {{ $service->service->name }} --}}
                                                    <span>
                                                        @php
                                                            $name = $service->service->name;
                                                            $trimmedname =
                                                                strlen($name) > 40
                                                                    ? substr($name, 0, 40) . '...'
                                                                    : $name;
                                                        @endphp
                                                        {!! $trimmedname !!}
                                                    </span>
                                                </a>
                                            </td>

                                            <td>
                                                <span>
                                                    {{ $service->service->category->name }}
                                                </span>
                                            </td>

                                            {{-- <td>
                                                @php
                                                    $description = $service->service->description;
                                                    $trimmedDescription = strlen($description) > 100 ? substr($description, 0, 100) . '...' : $description;
                                                @endphp
                                                {!! $trimmedDescription !!}
                                            </td> --}}

                                            <td>
                                                {{ $service->service->price }}
                                            </td>

                                            <td>
                                                <span>{{ $service->created_at->format('d/m/Y') }}</span><br>
                                                <span>{{ $service->created_at->format('h:i A') }}</span>
                                            </td>

                                            <td>
                                                <span>{{ $service->updated_at->format('d/m/Y') }}</span><br>
                                                <span>{{ $service->updated_at->format('h:i A') }}</span>
                                            </td>

                                            <td>
                                                <a class="btn btn-success btn-sm mb-2" style="width: 50px;"
                                                    href="{{ route('admin.service.apply_service_details', [$service['id']]) }}">
                                                    <i class="fa fa-eye" aria-hidden="true"></i>
                                                    {{-- {{ translate('View') }} --}}
                                                </a><br>

                                                <a class="btn btn-danger btn-sm" style="width: 50px;" href="javascript:"
                                                    onclick="form_alert('service-{{ $service['id'] }}','Want to delete this item ?')">
                                                    <i class="fa fa-trash-o" aria-hidden="true"></i>
                                                    {{-- {{ translate('Delete') }} --}}
                                                </a>

                                                <form
                                                    action="{{ route('admin.service.apply_service_delete', [$service['id']]) }}"
                                                    method="post" id="service-{{ $service['id'] }}">
                                                    @csrf @method('delete')
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Footer -->
                    <div class="card-footer">
                        {!! $services->links() !!}
                    </div>

                    @if (count($services) == 0)
                        <div class="text-center p-4">
                            <img class="mb-3" src="{{ asset('assets/back-end') }}/svg/illustrations/sorry.svg"
                                alt="Image Description" style="width: 7rem;">
                            <p class="mb-0">{{ translate('No data to show') }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <!-- Page level plugins -->
    <script src="{{ asset('assets/back-end') }}/vendor/datatables/jquery.dataTables.min.js"></script>
    <script src="{{ asset('assets/back-end') }}/vendor/datatables/dataTables.bootstrap4.min.js"></script>
    <!-- Page level custom scripts -->
    <script>
        // Call the dataTables jQuery plugin
        $(document).ready(function() {
            $('#dataTable').DataTable();
        });
    </script>


    <script>
        document.getElementById('filter_clear').addEventListener('click', function() {
            document.getElementById('from_date').value = '';
            document.getElementById('to_date').value = '';

            window.location.href = '{{ route('admin.service.apply_service_list') }}';
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
