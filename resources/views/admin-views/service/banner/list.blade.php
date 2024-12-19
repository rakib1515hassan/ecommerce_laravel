@extends('layouts.back-end.app')

@section('title', translate('Service Banner List'))

@push('css_or_js')

@endpush

@section('content')
    <div class="content container-fluid">

        <!-- Page Heading -->
        <div class="d-sm-flex align-items-center justify-content-between mb-2">
            <h1 class="h3 mb-0 text-black-50">{{translate('banner_list')}} <span
                        style="color: rgb(252, 59, 10);">({{ $banners->total() }})</span></h1>
        </div>

        <div class="row" style="margin-top: 20px">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row flex-between justify-content-between align-items-center flex-grow-1">
                            <div>
                                <h4 class="flex-between ml-4">
                                    <div>{{ translate('Banner List') }}</div>
                                    <div style="color: red; padding: 0 .4375rem;"></div>
                                </h4>
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
                            {{-- <div>
                                <a href="{{ route('admin.service.category_create_index') }}"
                                    class="btn btn-primary  float-right">
                                    <i class="tio-add-circle"></i>
                                    <span class="text">{{ translate('Add new Category') }}</span>
                                </a>
                            </div> --}}
                        </div>
                    </div>

                    <div class="card-body" style="padding: 0">
                        <div class="table-responsive">
                            <table style="text-align: {{Session::get('direction') === "rtl" ? 'right' : 'left'}};"
                                   class="table table-borderless table-thead-bordered table-nowrap table-align-middle card-table">
                                <thead class="thead-light">
                                <tr>
                                    <th scope="col" style="width: 100px">
                                        {{ translate('banner')}} {{ translate('ID')}}
                                    </th>
                                    <th scope="col">{{ translate('title')}}</th>
                                    <th scope="col">{{ translate('banner_type')}}</th>
                                    <th scope="col" style="width: 100px" class="text-center">
                                        {{ translate('action')}}
                                    </th>
                                </tr>
                                </thead>
                                <tbody>

                                @foreach($banners as $k=>$b)
                                    <tr>
                                        <td class="text-center">{{$banners->firstItem()+$k}}</td>
                                        <td>{{$b['title']}}</td>
                                        <td style="text-transform: capitalize;">{{$b['banner_type']}}</td>
                                        <td>
                                            <a class="btn btn-primary btn-sm"
                                               href="{{route('admin.service.banner_edit',[$b['id']])}}">
                                                <i class="tio-edit"></i> {{ translate('Edit')}}
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach

                                </tbody>
                            </table>

                        </div>
                    </div>
                    <div class="card-footer">
                        {{$banners->links()}}
                    </div>
                    @if(count($banners)==0)
                        <div class="text-center p-4">
                            <img class="mb-3" src="{{asset('assets/back-end')}}/svg/illustrations/sorry.svg"
                                 alt="Image Description" style="width: 7rem;">
                            <p class="mb-0">{{ translate('No_data_to_show')}}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script>
        $(document).on('click', '.delete', function () {
            var id = $(this).attr("id");
            Swal.fire({
                title: '{{ translate('Are_you_sure_delete_this_brand')}}?',
                text: "{{ translate('You_will_not_be_able_to_revert_this')}}!",
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: '{{ translate('Yes')}}, {{ translate('delete_it')}}!'
            }).then((result) => {
                if (result.value) {
                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                        }
                    });
                    $.ajax({
                        url: "{{route('admin.brand.delete')}}",
                        method: 'POST',
                        data: {id: id},
                        success: function () {
                            toastr.success('{{ translate('Brand_deleted_successfully')}}');
                            location.reload();
                        }
                    });
                }
            })
        });
    </script>
@endpush
