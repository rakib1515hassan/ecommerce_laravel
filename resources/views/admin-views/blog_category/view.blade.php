@extends('layouts.back-end.app')

@section('title', translate('BlogCategory'))

@push('css_or_js')

@endpush

@section('content')
    <div class="content container-fluid">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a
                            href="{{route('admin.dashboard')}}">{{translate('Dashboard')}}</a>
                </li>
                <li class="breadcrumb-item" aria-current="page">{{translate('blog_category')}}</li>
            </ol>
        </nav>

        <!-- Content Row -->
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        {{ translate('blog_category_form')}}
                    </div>
                    <div class="card-body"
                         style="text-align: {{Session::get('direction') === "rtl" ? 'right' : 'left'}};">
                        <form action="{{route('admin.blog_category.store')}}" method="POST"
                              enctype="multipart/form-data">
                            @csrf

                            <div class="row">
                                <div class="col-6">
                                    <div class="form-group ">
                                        <label class="input-label" for="">{{translate('name')}}</label>
                                        <input type="text" name="name" class="form-control" placeholder="">
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="form-group ">
                                        <label class="input-label"
                                               for="">{{translate('is_filterable')}}</label>
                                        <input type="radio" name="is_filterable" class="form-control-" checked
                                               value="1"> Yes
                                        <input type="radio" name="is_filterable" class="form-control-" value="0"> No
                                    </div>
                                </div>
                            </div>
                            <hr>
                            <button type="submit" class="btn btn-primary">{{translate('submit')}}</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="row" style="margin-top: 20px" id="cate-table">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <div class="flex-between justify-content-between align-items-center flex-grow-1">
                            <div>
                                <h5>{{ translate('blog_category_table')}} <span style="color: red;">({{ $blog_categories->total() }})</span>
                                </h5>
                            </div>
                            <div style="width: 30vw">
                                <!-- Search -->
                                <form action="{{ url()->current() }}" method="GET">
                                    <div class="input-group input-group-merge input-group-flush">
                                        <div class="input-group-prepend">
                                            <div class="input-group-text">
                                                <i class="tio-search"></i>
                                            </div>
                                        </div>
                                        <input id="" type="search" name="search" class="form-control"
                                               placeholder="" value="{{ $search }}" required>
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
                                   class="table table-borderless table-thead-bordered table-nowrap table-align-middle card-table">
                                <thead class="thead-light">
                                <tr>
                                    <th style="width: 100px">{{ translate('blog_category')}} {{ translate('ID')}}</th>
                                    <th>{{ translate('name')}}</th>
                                    <th>{{ translate('slug')}}</th>
                                    <th>{{ translate('is_filterable')}}</th>
                                    <th>{{ translate('order')}}</th>
                                    <th class="text-center"
                                        style="width:15%;">{{ translate('action')}}</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($blog_categories as $key=>$blog_category)
                                    <tr>
                                        <td class="text-center">{{$blog_category['id']}}</td>
                                        <td>{{$blog_category['name']}}</td>
                                        <td>{{$blog_category['slug']}}</td>
                                        <td>{{$blog_category['is_filterable']?'Yes':'No'}}</td>
                                        <td>{{$blog_category['order']}}</td>

                                        <td>
                                            <a class="btn btn-primary btn-sm edit" style="cursor: pointer;"
                                               href="{{route('admin.blog_category.edit',[$blog_category['id']])}}">
                                                <i class="tio-edit"></i>{{ translate('Edit')}}
                                            </a>
                                            <a class="btn btn-danger btn-sm delete" style="cursor: pointer;"
                                               id="{{$blog_category['id']}}">
                                                <i class="tio-add-to-trash"></i>{{ translate('Delete')}}
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="card-footer">
                        {{$blog_categories->links()}}
                    </div>
                    @if(count($blog_categories)==0)
                        <div class="text-center p-4">
                            <img class="mb-3" src="{{asset('assets/back-end')}}/svg/illustrations/sorry.svg"
                                 alt="Image Description" style="width: 7rem;">
                            <p class="mb-0">{{translate('no_data_found')}}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')

    <script>


        $(document).ready(function () {
            $('#dataTable').DataTable();
        });
    </script>

    <script>
        $(document).on('click', '.delete', function () {
            var id = $(this).attr("id");
            Swal.fire({
                title: '{{translate('Are_you_sure')}}?',
                text: "{{translate('You_will_not_be_able_to_revert_this')}}!",
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: '{{translate('Yes')}}, {{translate('delete_it')}}!'
            }).then((result) => {
                if (result.value) {
                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                        }
                    });
                    $.ajax({
                        url: "{{route('admin.blog_category.delete')}}",
                        method: 'POST',
                        data: {id: id},
                        success: function () {
                            toastr.success('{{translate('BlogCategory_deleted_Successfully.')}}');
                            location.reload();
                        }
                    });
                }
            })
        });
    </script>

    <script>
        function readURL(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();

                reader.onload = function (e) {
                    $('#viewer').attr('src', e.target.result);
                }

                reader.readAsDataURL(input.files[0]);
            }
        }

        $("#customFileEg1").change(function () {
            readURL(this);
        });
    </script>
@endpush
