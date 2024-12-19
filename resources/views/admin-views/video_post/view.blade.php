@extends('layouts.back-end.app')

@section('title', translate('Video post'))

@push('css_or_js')

@endpush

@section('content')
    <div class="content container-fluid">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a
                            href="{{route('admin.dashboard')}}">{{translate('Dashboard')}}</a>
                </li>
                <li class="breadcrumb-item" aria-current="page">{{translate('video_post')}}</li>
            </ol>
        </nav>

        <!-- Content Row -->

        <div class="row" style="margin-top: 20px" id="cate-table">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <div class="flex-between justify-content-between align-items-center flex-grow-1">
                            <div>
                                <h5>{{ translate('video_post')}} <span style="color: red;">({{ $posts->total() }})</span>
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
                                    <th style="width: 100px">{{ translate('sl#')}}</th>
                                    <th>{{ translate('video')}}</th>
                                    <th>{{ translate('title')}}</th>
                                    <th>{{ translate('user')}}</th>
                                    <th>{{ translate('status')}}</th>

                                    <th class="text-center"
                                        style="width:15%;">{{ translate('action')}}</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($posts as $key=>$post)
                                    <tr>
                                        <td class="text-center">{{$key +1}}</td>
                                        <td>
                                            <img src="{{asset('storage/post_video')}}/{{$post['video']}}"
                                                 alt="video" id="viewer" height="60"/>
                                        </td>
                                        <td>{{$post['title']}}</td>
                                        <td>{{$post->user->f_name}} {{$post->user->l_name}}</td>
                                        <td>
                                            <label class="switch switch-status">
                                                <input type="checkbox" class="status"
                                                       id="{{$post['id']}}" {{$post->is_approved  == 1?'checked':''}}>
                                                <span class="slider round"></span>
                                            </label>
                                        </td>





                                        <td>

                                            <a class="btn btn-danger btn-sm delete" style="cursor: pointer;"
                                               id="{{$post['id']}}">
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
                        {{$posts->links()}}
                    </div>
                    @if(count($posts)==0)
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
                        url: "{{route('admin.video_post.delete')}}",
                        method: 'POST',
                        data: {id: id},
                        success: function () {
                            toastr.success('{{translate('Video_Post_deleted_Successfully.')}}');
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
                url: "{{route('admin.video_post.status-update')}}",
                method: 'POST',
                data: {
                    id: id,
                    status: status
                },
                success: function (data) {
                    toastr.success('{{translate('Status updated successfully')}}');
                }
            });
        });
    </script>
@endpush
