@extends('layouts.back-end.app')

@section('title', translate('Property List'))

@push('css_or_js')
@endpush

@section('content')
    <div class="content container-fluid"> <!-- Page Heading -->
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ translate('Dashboard') }}</a></li>
                <li class="breadcrumb-item" aria-current="page">{{ translate('Property') }}</li>
            </ol>
        </nav>

        <div class="row" style="margin-top: 20px">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row flex-between justify-content-between align-items-center flex-grow-1">
                            <div>
                                <h5 class="flex-between">
                                    <div>{{ translate('Property Table') }}</div>
                                    <div style="color: red; padding: 0 .4375rem;"></div>
                                </h5>
                            </div>
                            <div style="width: 40vw">
                                <!-- Search -->
                                <form action="{{ url()->current() }}" method="GET" id="filter_form">
                                    <div class="input-group input-group-merge input-group-flush">
                                        <div class="input-group-prepend">
                                            <div class="input-group-text">
                                                <i class="tio-search"></i>
                                            </div>
                                        </div>
                                        <input id="datatableSearch_" type="search" name="search" class="form-control"
                                            placeholder="{{ translate('Search Property Title') }}" aria-label="Search orders"
                                            value="" required>
                                        <input type="hidden" value="" name="status">

                                        <button type="submit" class="btn btn-primary">
                                            <i class="fa fa-search" aria-hidden="true"></i>
                                            {{ translate('search') }}
                                        </button>

                                        <button class="btn btn-info btn-sm ml-2" id="download_csv" type="button">
                                            <i class="fa fa-cloud-download" aria-hidden="true"></i>
                                            Download
                                        </button>
                                    </div>
                                </form>
                                <!-- End Search -->
                            </div>
                            <div>
                                <a href="{{ route('admin.property.add-new-property') }}" class="btn btn-primary  float-right">
                                    <i class="tio-add-circle"></i>
                                    <span class="text">{{ translate('Add new property') }}</span>
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body" style="padding: 0">
                        <div class="table-responsive">
                            <table id="datatable"
                                style="text-align: {{ Session::get('direction') === 'rtl' ? 'right' : 'left' }};"
                                class="table table-hover table-borderless table-thead-bordered table-nowrap table-align-middle card-table"
                                style="width: 100%">
                                <thead class="thead-light">
                                    <tr>
                                        <th>{{ translate('SL#') }}</th>
                                        <th>{{ translate('Property Title') }}</th>
                                        <th>{{ translate('Category') }}</th>
                                        {{-- <th>{{ translate('Service Description') }}</th> --}}
                                        <th>{{ translate('Price') }}</th>
                                        <th>{{ translate('Created') }}</th>
                                        <th>{{ translate('Updated') }}</th>
                                        <th style="width: 5px" class="text-center">{{ translate('Action') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($services as $k => $service)
                                        <tr>
                                            <th scope="row">{{ $k + 1 }}</th> {{-- Loop count starts from 0, so add 1 --}}

                                            <td>
                                                {{-- <a href="">
                                                    {{ $service->name }}
                                                </a> --}}

                                                <span>
                                                    @php
                                                        $name = $service->name;
                                                        $trimmedname =
                                                            strlen($name) > 40 ? substr($name, 0, 40) . '...' : $name;
                                                    @endphp
                                                    {!! $trimmedname !!}
                                                </span>
                                            </td>

                                            <td>
                                                <span>
                                                    @if ($service->category)
                                                        {{ $service->category->name }}
                                                    @else
                                                        No Category
                                                    @endif
                                                </span>
                                            </td>


                                            {{-- <td>
                                                @php
                                                    $description = $service->description;
                                                    $trimmedDescription = strlen($description) > 100 ? substr($description, 0, 100) . '...' : $description;
                                                @endphp
                                                {!! $trimmedDescription !!}
                                            </td> --}}

                                            <td>
                                                {{ $service->price }}
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
                                                <a class="btn btn-primary btn-sm"
                                                    href="{{ route('admin.property.show_update_page', ['id' => $service->id]) }}">
                                                    <i class="tio-edit"></i>{{ translate('Edit') }}
                                                </a>

                                                <a class="btn btn-danger btn-sm" href="javascript:"
                                                    onclick="form_alert('service-{{ $service['id'] }}','Want to delete this item ?')">
                                                    <i class="tio-add-to-trash"></i> {{ translate('Delete') }}
                                                </a>

                                                <form action="{{ route('admin.property.delete', [$service['id']]) }}"
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

                    <div class="card-footer">
                        {{ $services->links() }}
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
        // Add hidden input to trigger CSV download
        document.getElementById('download_csv').addEventListener('click', function() {

            console.log("CSV download");

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
