@extends('layouts.back-end.app-seller')

@section('title', translate('RedX Profile and information'))

@push('css_or_js')
<style>
  body {
    margin-top: 20px;
    color: #1a202c;
    text-align: left;
    background-color: #e2e8f0;
  }

  .main-body {
    padding: 15px;
  }

  .card {
    box-shadow: 0 1px 3px 0 rgba(0, 0, 0, .1), 0 1px 2px 0 rgba(0, 0, 0, .06);
  }

  .card {
    position: relative;
    display: flex;
    flex-direction: column;
    min-width: 0;
    word-wrap: break-word;
    background-color: #fff;
    background-clip: border-box;
    border: 0 solid rgba(0, 0, 0, .125);
    border-radius: .25rem;
  }

  .card-body {
    flex: 1 1 auto;
    min-height: 1px;
    padding: 1rem;
  }

  .gutters-sm {
    margin-right: -8px;
    margin-left: -8px;
  }

  .gutters-sm>.col,
  .gutters-sm>[class*=col-] {
    padding-right: 8px;
    padding-left: 8px;
  }

  .mb-3,
  .my-3 {
    margin-bottom: 1rem !important;
  }

  .bg-gray-300 {
    background-color: #e2e8f0;
  }

  .h-100 {
    height: 100% !important;
  }

  .shadow-none {
    box-shadow: none !important;
  }
</style>
@endpush

@section('content')
<div class="content container-fluid">
  <!-- Page Header -->
  <nav aria-label="breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}">{{translate('Dashboard')}}</a></li>
      <li class="breadcrumb-item" aria-current="page">{{translate('redx proifle')}}</li>
    </ol>
  </nav>

  <div class="row gutters-sm">
    <div class="col-md-4 mb-3">
      <div class="card">
        <div class="card-body">
          <div class="d-flex flex-column align-items-center text-center">
            <img src="https://redx.com.bd/images/new-redx-logo.svg" alt="Admin" width="150">
            <div class="mt-3">
              <h4>{{$redxProfile->store_name}}</h4>
              <p class="text-secondary mb-1"><strong>Area :</strong> {{$redxProfile->area->name}}</p>
              <p class="text-secondary mb-1"><strong>Phone :</strong> {{$redxProfile->phone}}</p>
              <p class="text-muted font-size-sm">{{$redxProfile->address}}</p>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-md-8">
      <div class="card mb-3">
        <div class="card-header">
          <h4>Shipped Parcels</h4>
        </div>
        <div class="card-body">
          <table class="table table-bordered">
            <thead>
              <th>RedX Percel ID</th>
              <th>Action</th>
            </thead>
            <tbody>
              @foreach ($redxParcels as $redxParcel)
              <tr>
                <td>{{$redxParcel->tracking_id}}</td>
                <td>
                  <a href="{{route('seller.orders.details',[$redxParcel->order_id , 'tracking_id' => $redxParcel->tracking_id])}}"
                    class="btn btn-sm btn-info">View</a>
                </td>
              </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

</div>
@endsection