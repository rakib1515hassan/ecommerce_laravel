@extends('layouts.back-end.app')

@section('title', translate('Deal Page'))

@push('css_or_js')

    <meta name="csrf-token" content="{{ csrf_token() }}">
@endpush

@section('content')
    <div class="content container-fluid">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a
                            href="{{route('admin.dashboard')}}">{{translate('Dashboard')}}</a></li>
                <li class="breadcrumb-item" aria-current="page">{{translate('deal_of_the_day')}}</li>
                <li class="breadcrumb-item">{{translate('add_new')}}</li>
            </ol>
        </nav>

        <!-- Content Row -->
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        {{translate('Deal of the day form')}}
                    </div>
                    <div class="card-body">
                        <form action="{{route('admin.deal.day')}}"
                              style="text-align: {{Session::get('direction') === "rtl" ? 'right' : 'left'}};"
                              method="post">
                            @csrf
                            @php($language=\App\Models\BusinessSetting::where('type','pnc_language')->first())
                            @php($language = $language->value ?? null)
                            @php($default_lang = 'en')

                            @php($default_lang = json_decode($language)[0])
                            <ul class="nav nav-tabs mb-4">
                                @foreach(json_decode($language) as $lang)
                                    <li class="nav-item">
                                        <a class="nav-link lang_link {{$lang == $default_lang? 'active':''}}"
                                           href="#"
                                           id="{{$lang}}-link">{{\App\Services\AdditionalServices::get_language_name($lang).'('.strtoupper($lang).')'}}</a>
                                    </li>
                                @endforeach
                            </ul>

                            <div class="form-group">
                                @foreach(json_decode($language) as $lang)
                                    <div class="row {{$lang != $default_lang ? 'd-none':''}} lang_form"
                                         id="{{$lang}}-form">
                                        <div class="col-md-12">
                                            <label for="name">{{ translate('Title')}}
                                                ({{strtoupper($lang)}}
                                                )</label>
                                            <input type="text" name="title[]" class="form-control" id="title"
                                                   placeholder="{{translate('Ex')}} : {{translate('LUX')}}"
                                                    {{$lang == $default_lang? 'required':''}}>
                                        </div>
                                    </div>
                                    <input type="hidden" name="lang[]" value="{{$lang}}" id="lang">
                                @endforeach
                                <div class="row">
                                    <div class="col-md-12">
                                        <label for="name">{{ translate('Products')}}</label>
                                        <select
                                                class="js-example-basic-multiple js-states js-example-responsive form-control"
                                                name="product_id">
                                            @foreach (\App\Models\Product::orderBy('name', 'asc')->get() as $key => $product)
                                                <option value="{{ $product->id }}">
                                                    {{$product['name']}}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="card-footer pl-0">
                                <button type="submit"
                                        class="btn btn-primary ">{{ translate('save')}}</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="row" style="margin-top: 20px">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">

                        <div class="flex-between row justify-content-between align-items-center flex-grow-1 mx-1">
                            <div class="flex-between">
                                <div><h5>{{ translate('deal_of_the_day')}}</h5></div>
                                <div class="mx-1"><h5 style="color: red;">({{ $deals->total() }})</h5></div>
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
                                               placeholder="{{translate('Search by Title')}}"
                                               aria-label="Search orders" value="{{ $search }}" required>
                                        <button type="submit"
                                                class="btn btn-primary">{{translate('search')}}</button>
                                    </div>
                                </form>
                                <!-- End Search -->
                            </div>
                        </div>
                    </div>
                    <div class="card-body" style="padding: 0">
                        <div class="table-responsive">
                            <table style="text-align: {{Session::get('direction') === "rtl" ? 'right' : 'left'}};"
                                   class="table table-hover table-borderless table-thead-bordered table-nowrap table-align-middle card-table">
                                <thead class="thead-light">
                                <tr>
                                    <th scope="col">{{ translate('sl')}}</th>
                                    <th scope="col">{{ translate('title')}}</th>
                                    <th scope="col">{{ translate('status')}}</th>
                                    <th scope="col" style="width: 100px">{{ translate('action')}}</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($deals as $k=>$deal)
                                    <tr>
                                        <th scope="row">{{$deals->firstItem()+ $k}}</th>
                                        <td>{{$deal['title']}}</td>
                                        <td>
                                            <label class="switch">
                                                <input type="checkbox" class="status"
                                                       id="{{$deal['id']}}" {{$deal->status == 1?'checked':''}}>
                                                <span class="slider round"></span>
                                            </label>
                                        </td>
                                        <td>
                                            <a href="{{route('admin.deal.day-update',[$deal['id']])}}"
                                               class="btn btn-primary btn-sm">
                                                {{ trans ('Edit')}}
                                            </a>
                                            <a href="{{route('admin.deal.day-delete',[$deal['id']])}}"
                                               class="btn btn-danger btn-sm">
                                                {{ trans ('Delete')}}
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="card-footer">
                        {{$deals->links()}}
                    </div>
                    @if(count($deals)==0)
                        <div class="text-center p-4">
                            <img class="mb-3" src="{{asset('assets/back-end')}}/svg/illustrations/sorry.svg"
                                 alt="Image Description" style="width: 7rem;">
                            <p class="mb-0">{{translate('No data to show')}}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script>
        $(".lang_link").click(function (e) {
            e.preventDefault();
            $(".lang_link").removeClass('active');
            $(".lang_form").addClass('d-none');
            $(this).addClass('active');

            let form_id = this.id;
            let lang = form_id.split("-")[0];
            console.log(lang);
            $("#" + lang + "-form").removeClass('d-none');
            if (lang == '{{$default_lang}}') {
                $(".from_part_2").removeClass('d-none');
            } else {
                $(".from_part_2").addClass('d-none');
            }
        });
    </script>


    <script>
        $(".js-example-theme-single").select2({
            theme: "classic"
        });

        $(".js-example-responsive").select2({
            width: 'resolve'
        });

        // Call the dataTables jQuery plugin
        $(document).ready(function () {
            $('#dataTable').DataTable();
        });

        $(document).on('change', '.status', function () {
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
                url: "{{route('admin.deal.status-update')}}",
                method: 'POST',
                data: {
                    id: id,
                    status: status
                },
                success: function () {
                    toastr.success('{{translate('Status updated successfully')}}');
                }
            });
        });
    </script>

    <script>
        $(document).on('change', '.status', function () {
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
                url: "{{route('admin.deal.day-status-update')}}",
                method: 'POST',
                data: {
                    id: id,
                    status: status
                },
                success: function () {
                    toastr.success('{{translate('Status updated successfully')}}');
                    location.reload();
                }
            });
        });
    </script>
@endpush
