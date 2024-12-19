@extends('layouts.back-end.app-seller')

@section('title', translate('RedX Profile and information'))

@push('css_or_js')
@endpush

@section('content')
    <div class="content container-fluid">
        <!-- Page Header -->
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{route('seller.dashboard.index')}}">
                        {{translate('Dashboard')}}</a>
                </li>
                <li class="breadcrumb-item" aria-current="page">{{translate('redx proifle')}}</li>
            </ol>
        </nav>


        <!-- End Page Header -->
        <div class="row gx-2 gx-lg-3">
            <div class="col-sm-12 col-lg-12 mb-3 mb-lg-2">
                <div class="card">
                    <div class="card-body">
                        <div class="card-header">
                            <h4>Create RedX Profile</h4>
                        </div>

                        @if ($errors->any())
                            @foreach ($errors->all() as $error)
                                <div class="alert alert-warning">{{$error}}</div>
                            @endforeach
                        @endif


                        <form class="content" action="{{route('seller.business-settings.redx.profile.save')}}"
                              method="POST">
                            @csrf
                            <div class="row">
                                <div class="col-8 offset-4">
                                    <div class="my-2">
                                        <label for="name">Store Name</label>
                                        <input type="text" required name="store_name" class="form-control" id="name">
                                    </div>
                                </div>


                                <div class="col-8 offset-4">
                                    <div class="my-2">
                                        <label for="phone">Phone</label>
                                        <input type="text" required name="phone" class="form-control" id="phone">
                                    </div>
                                </div>

                                <div class="col-8 offset-4">
                                    <div class="my-2">
                                        <label for="division_id">Divisions</label>
                                        <select name="division_id" required class="form-control" id="division_id"
                                                required>
                                            <option value="">Select Division</option>
                                            @foreach ($divisions as $division)
                                                <option value="{{$division->id}}">{{ $division->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="col-8 offset-4">
                                    <div class="my-2">
                                        <label for="district_id">Districts</label>
                                        <select name="district_id" required class="form-control" id="district_id">
                                            <option value="">Select District</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-8 offset-4">
                                    <div class="my-2">
                                        <label for="area_id">Area</label>
                                        <select name="area_id" required class="form-control" id="area_id">
                                            <option value="">Select Area</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-8 offset-4">
                                    <div class="my-2">
                                        <label for="address">Short Address</label>
                                        <input type="text" name="address" required class="form-control" id="address">
                                    </div>
                                </div>
                            </div>

                            <div>
                                <button type="submit" class="btn btn-success">Save</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script>
        $(document).ready(function () {
            $(document).on('change', "select[name='division_id']", function () {
                let divisionId = $(this).val();
                if (divisionId) {
                    $.ajax({
                        url: "{{  url('/api/v1/address?division_id=') }}" + divisionId,
                        type: "GET",
                        dataType: "json",
                        success: function (data) {
                            $("select[name='district_id']").html(data.map(item => `<option value="${item.id}">${item.name}</option>`))
                        },
                    });
                }
            })

            $(document).on('change', "select[name='district_id']", function () {
                let districtId = $(this).val();
                if (districtId) {
                    $.ajax({
                        url: "{{  url('/api/v1/address?district_id=') }}" + districtId,
                        type: "GET",
                        dataType: "json",
                        success: function (data) {
                            $("select[name='area_id']").html(data.map(item => `<option value="${item.id}">${item.name}</option>`))
                        },
                    });
                }
            })
        })
    </script>
@endpush
