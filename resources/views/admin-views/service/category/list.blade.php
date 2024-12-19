@extends('layouts.back-end.app')

@section('title', translate('Service Category List'))

@push('css_or_js')
@endpush

@section('content')
    <div class="content container-fluid"> <!-- Page Heading -->
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ translate('Dashboard') }}</a></li>
                <li class="breadcrumb-item" aria-current="page">{{ translate('Service Category') }}</li>
            </ol>
        </nav>

        <div class="row" style="margin-top: 20px">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row flex-between justify-content-between align-items-center flex-grow-1">
                            <div>
                                <h5 class="flex-between">
                                    <div>{{ translate('Service Category') }}</div>
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
                                            placeholder="{{ translate('Search Service Name') }}" aria-label="Search orders"
                                            value="" required>
                                        <input type="hidden" value="" name="status">
                                        <button type="submit" class="btn btn-primary">{{ translate('search') }}</button>
                                    </div>
                                </form>
                                <!-- End Search -->
                            </div>
                            <div>
                                <a href="{{ route('admin.service.category_create_index') }}"
                                    class="btn btn-primary  float-right">
                                    <i class="tio-add-circle"></i>
                                    <span class="text">{{ translate('Add new Category') }}</span>
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
                                        <th>{{ translate('Name') }}</th>
                                        <th>{{ translate('Type') }}</th>
                                        <th>{{ translate('Logo') }}</th>
                                        <th>{{ translate('Created') }}</th>
                                        <th>{{ translate('Updated') }}</th>
                                        <th style="width: 5px" class="text-center">{{ translate('Action') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($categories as $k => $categorie)
                                        <tr>
                                            <th scope="row">{{ $k + 1 }}</th> {{-- Loop count starts from 0, so add 1 --}}

                                            <td>
                                                <span style="text-transform: capitalize;">
                                                    {{ $categorie->name }}
                                                </span>
                                            </td>

                                            <td>
                                                <span style="text-transform: capitalize;">
                                                    {{ $categorie->category_type }}
                                                </span>
                                            </td>

                                            <td>
                                                @if ($categorie->logo)
                                                    <img src="{{ asset('storage/' . $categorie->logo) }}"
                                                        class="img-fluid flex-grow-1" alt="Logo" width="100px">
                                                @else
                                                    <span>Not Uploded!</span>
                                                @endif
                                            </td>

                                            <td>
                                                <span>{{ $categorie->created_at->format('d/m/Y') }}</span><br>
                                                <span>{{ $categorie->created_at->format('h:i A') }}</span>
                                            </td>
                                            <td>
                                                <span>{{ $categorie->updated_at->format('d/m/Y') }}</span><br>
                                                <span>{{ $categorie->updated_at->format('h:i A') }}</span>
                                            </td>

                                            <td>
                                                <a class="btn btn-primary btn-sm"
                                                    href="{{ route('admin.service.category_update_index', ['id' => $categorie->id]) }}">
                                                    <i class="tio-edit"></i>{{ translate('Edit') }}
                                                </a>

                                                <a class="btn btn-danger btn-sm" href="javascript:"
                                                    onclick="form_alert('service-{{ $categorie['id'] }}','Want to delete this item ?')">
                                                    <i class="tio-add-to-trash"></i> {{ translate('Delete') }}
                                                </a>

                                                <form
                                                    action="{{ route('admin.service.category_delete', [$categorie['id']]) }}"
                                                    method="post" id="service-{{ $categorie['id'] }}">
                                                    @csrf @method('delete')
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    @if (count($categories) == 0)
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
@endpush
