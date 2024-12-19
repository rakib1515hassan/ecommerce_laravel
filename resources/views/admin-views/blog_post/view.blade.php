@extends('layouts.back-end.app')

@section('title', translate('BlogPost'))

@push('css_or_js')
@endpush

@section('content')
    <div class="content container-fluid">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ translate('Dashboard') }}</a>
                </li>
                <li class="breadcrumb-item" aria-current="page">{{ translate('blog_post') }}</li>
            </ol>
        </nav>

        <!-- Content Row -->
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        {{ translate('blog_post_form') }}
                    </div>
                    <div class="card-body"
                        style="text-align: {{ Session::get('direction') === 'rtl' ? 'right' : 'left' }};">
                        <form action="{{ route('admin.blog_post.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf

                            <div class="row">
                                <div class="col-6">
                                    <div class="form-group ">
                                        <label class="input-label" for="">{{ translate('blog_category') }}</label>
                                        <select name="blog_category_id" id="" class="form-control" required>
                                            <option value="">Select</option>
                                            @foreach ($blog_categories as $key => $value)
                                                <option value="{{ $value->id }}">{{ $value->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="form-group ">
                                        <label class="input-label" for="">{{ translate('title') }}</label>
                                        <input type="text" name="title" class="form-control" placeholder="">
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-group ">
                                        <label class="input-label" for="">{{ translate('content') }}</label>
                                        <textarea name="content" class="form-control"></textarea>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <label for="name">{{ translate('Thumbnail') }}</label><span
                                        class="badge badge-soft-danger">( {{ translate('ratio') }} 1:1 )</span>
                                    <div class="custom-file" style="text-align: left" required>
                                        <input type="file" name="thumbnail" id="customFileEg1" class="custom-file-input"
                                            accept=".jpg, .png, .jpeg, .gif, .bmp, .tif, .tiff|image/*">
                                        <label class="custom-file-label" for="customFileEg1">{{ translate('choose') }}
                                            {{ translate('file') }}</label>
                                    </div>
                                    <img src="" alt="" id="viewer" height="70" />
                                </div>
                                <div class="col-6">
                                    <div class="form-group ">
                                        <label class="input-label" for="">{{ translate('is_published') }}</label>
                                        <input type="radio" name="is_published" class="form-control-" checked
                                            value="1">
                                        Yes
                                        <input type="radio" name="is_published" class="form-control-" value="0"> No
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="form-group ">
                                        <label class="input-label" for="">{{ translate('is_approved') }}</label>
                                        <input type="radio" name="is_approved" class="form-control-" checked
                                            value="1">
                                        Yes
                                        <input type="radio" name="is_approved" class="form-control-" value="0"> No
                                    </div>
                                </div>

                                <div class="col-6">
                                    <div class="form-group ">
                                        <label class="input-label" for="">{{ translate('is_featured') }}</label>
                                        <input type="radio" name="is_featured" class="form-control-" checked
                                            value="1">
                                        Yes
                                        <input type="radio" name="is_featured" class="form-control-" value="0"> No
                                    </div>
                                </div>

                                <div class="col-6">
                                    <div class="form-group ">
                                        <label class="input-label" for="">{{ translate('is_commentable') }}</label>
                                        <input type="radio" name="is_commentable" class="form-control-" checked
                                            value="1"> Yes
                                        <input type="radio" name="is_commentable" class="form-control-"
                                            value="0"> No
                                    </div>
                                </div>

                            </div>
                            <hr>
                            <button type="submit" class="btn btn-primary">{{ translate('submit') }}</button>
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
                                <h5>{{ translate('blog_post_table') }} <span
                                        style="color: red;">({{ $blog_posts->total() }})</span>
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

                                        <button type="submit" class="btn btn-primary">
                                            {{ translate('search') }}
                                        </button>

                                        {{-- <a class="btn btn-info ml-2" 
                                            href="{{ route('admin.blog_post.bloge_create') }}">
                                            {{ translate('Create') }}
                                        </a> --}}
                                    </div>
                                </form>
                                <!-- End Search -->
                            </div>
                        </div>
                    </div>
                    <div class="card-body" style="padding: 0">
                        <div class="table-responsive">
                            <table style="text-align: {{ Session::get('direction') === 'rtl' ? 'right' : 'left' }};"
                                class="table table-borderless table-thead-bordered table-nowrap table-align-middle card-table">
                                <thead class="thead-light">
                                    <tr>
                                        <th style="width: 100px">{{ translate('blog_post') }} {{ translate('ID') }}</th>
                                        <th>{{ translate('title') }}</th>
                                        {{-- <th>{{ translate('slug')}}</th> --}}
                                        <th>{{ translate('blog_category') }}</th>
                                        {{-- <th>{{ translate('content')}}</th> --}}
                                        {{-- <th>{{ translate('seo_title')}}</th> --}}
                                        {{-- <th>{{ translate('seo_description')}}</th> --}}
                                        {{-- <th>{{ translate('seo_keywords') }}</th> --}}
                                        <th>{{ translate('seo_image') }}</th>
                                        <th>{{ translate('thumbnail') }}</th>
                                        <th>{{ translate('created_by') }}</th>
                                        <th>{{ translate('is_created_admin') }}</th>
                                        <th>{{ translate('is_published') }}</th>
                                        <th>{{ translate('is_approved') }}</th>
                                        <th>{{ translate('is_featured') }}</th>
                                        <th>{{ translate('is_commentable') }}</th>
                                        <th class="text-center" style="width:15%;">{{ translate('action') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($blog_posts as $key => $blog_post)
                                        <tr>
                                            <td class="text-center">{{ $blog_post['id'] }}</td>

                                            <td>
                                                {{-- {{$blog_post['title']}} --}}
                                                @php
                                                    $titlel_ = $blog_post->title;
                                                    $trimmedDescription =
                                                        strlen($titlel_) > 100
                                                            ? substr($titlel_, 0, 100) . '...'
                                                            : $titlel_;
                                                @endphp
                                                {!! $trimmedDescription !!}
                                            </td>

                                            {{-- <td>{{$blog_post['slug']}}</td> --}}

                                            <td>
                                                {{ isset($blog_post['blog_category']->name) ? $blog_post['blog_category']->name : '' }}
                                            </td>

                                            {{-- <td>{{substr($blog_post['content'], 0, 10)}}..</td> --}}

                                            {{-- <td>{{$blog_post['seo_title']}}</td> --}}

                                            {{-- <td>{{$blog_post['seo_description']}}</td> --}}

                                            {{-- <td>{{ $blog_post['seo_keywords'] }}</td> --}}

                                            <td>{{ $blog_post['seo_image'] }}</td>

                                            <td>
                                                <img src="{{ asset('storage/blog_post') }}/{{ $blog_post['thumbnail'] }}"
                                                    alt="image" id="viewer" height="60" />
                                            </td>

                                            <?php
                                            if ($blog_post['is_created_admin']) {
                                                $created_by = $blog_post['created_by_admin'];
                                            } else {
                                                $created_by = $blog_post['created_by_user'];
                                            }
                                            ?>

                                            <td>{{ isset($created_by->name) ? $created_by->name : '' }}</td>
                                            <td>{{ $blog_post['is_created_admin'] == 1 ? 'Yes' : 'No' }}</td>
                                            <td>{{ $blog_post['is_published'] == 1 ? 'Yes' : 'No' }}</td>
                                            <td>{{ $blog_post['is_approved'] == 1 ? 'Yes' : 'No' }}</td>
                                            <td>{{ $blog_post['is_featured'] == 1 ? 'Yes' : 'No' }}</td>
                                            <td>{{ $blog_post['is_commentable'] == 1 ? 'Yes' : 'No' }}</td>

                                            <td>
                                                <a class="btn btn-primary btn-sm edit" style="cursor: pointer;"
                                                    href="{{ route('admin.blog_post.edit', [$blog_post['id']]) }}">
                                                    <i class="tio-edit"></i>{{ translate('Edit') }}
                                                </a>
                                                <a class="btn btn-primary btn-sm edit" style="cursor: pointer;"
                                                    href="{{ route('admin.blog_comments.view', [$blog_post['id']]) }}">
                                                    <i class="tio-edit"></i>{{ translate('Comments') }}
                                                </a>
                                                <a class="btn btn-danger btn-sm delete" style="cursor: pointer;"
                                                    id="{{ $blog_post['id'] }}">
                                                    <i class="tio-add-to-trash"></i>{{ translate('Delete') }}
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="card-footer">
                        {{ $blog_posts->links() }}
                    </div>
                    @if (count($blog_posts) == 0)
                        <div class="text-center p-4">
                            <img class="mb-3" src="{{ asset('assets/back-end') }}/svg/illustrations/sorry.svg"
                                alt="Image Description" style="width: 7rem;">
                            <p class="mb-0">{{ translate('no_data_found') }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script>
        $(document).ready(function() {
            $('#dataTable').DataTable();
        });
    </script>

    <script>
        $(document).on('click', '.delete', function() {
            var id = $(this).attr("id");
            Swal.fire({
                title: '{{ translate('Are_you_sure') }}?',
                text: "{{ translate('You_will_not_be_able_to_revert_this') }}!",
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: '{{ translate('Yes') }}, {{ translate('delete_it') }}!'
            }).then((result) => {
                if (result.value) {
                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                        }
                    });
                    $.ajax({
                        url: "{{ route('admin.blog_post.delete') }}",
                        method: 'POST',
                        data: {
                            id: id
                        },
                        success: function() {
                            toastr.success(
                                '{{ translate('Blog_Post_deleted_Successfully.') }}');
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

                reader.onload = function(e) {
                    $('#viewer').attr('src', e.target.result);
                }

                reader.readAsDataURL(input.files[0]);
            }
        }

        $("#customFileEg1").change(function() {
            readURL(this);
        });
    </script>
@endpush
