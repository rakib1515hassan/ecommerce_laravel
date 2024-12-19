@extends('layouts.back-end.app')

@section('title', translate('BlogPost'))

@push('css_or_js')

@endpush

@section('content')
    <style>
        #blog_comment_reply_show p {
            padding-left: 90px;
        }
    </style>
    <div class="content container-fluid">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a
                            href="{{route('admin.dashboard')}}">{{translate('Dashboard')}}</a>
                </li>
                <li class="breadcrumb-item" aria-current="page">{{translate('blog_comment')}}</li>
            </ol>
        </nav>

        <!-- Content Row -->
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        {{ translate('blog_comment_form')}}
                    </div>
                    <div class="card-body"
                         style="text-align: {{Session::get('direction') === "rtl" ? 'right' : 'left'}};">

                        <h4>{{ translate('Blog_Post')}}: {{$blog_post->title}}</h4>
                        <span style="display: none;" id="blog_post_title">{{$blog_post->title}}</span>

                        <div class="table-responsive">
                            <table style="text-align: {{Session::get('direction') === "rtl" ? 'right' : 'left'}};"
                                   class="table table-borderless table-thead-bordered table-nowrap table-align-middle card-table">
                                <thead class="thead-light">
                                <tr>
                                    <th style="width: 100px">{{ translate('ID') }} </th>
                                    <th style="width: 100px">{{ translate('Comments') }} </th>
                                    <th style="width: 100px">{{ translate('Comment_Type') }} </th>
                                    <th style="width: 100px">{{ translate('Is Approved') }} </th>
                                    <th style="width: 100px">{{ translate('Created_By') }} </th>
                                    <th style="width: 100px">{{ translate('User Type') }} </th>
                                    <th style="width: 100px">{{ translate('action') }} </th>
                                    <th class="text-center"
                                        style="width:15%;">{{ translate('action')}}</th>
                                </tr>
                                </thead>
                                <tbody>
                                @if(isset($blog_post->blog_comments))
                                    @foreach($blog_post->blog_comments as $key=>$blog_comment)
                                        @if($blog_comment['is_reply'] == 1)

                                        @endif

                                        <tr>
                                            <td class="text-center">{{$blog_comment['id']}}</td>
                                            <td>{{$blog_comment['comment']}}</td>
                                            <td>{{$blog_comment['is_reply']==1?'Reply':'Comment'}}</td>

                                            <td>
                                                <input class="approve_row" data-comment_id="{{$blog_comment['id']}}"
                                                       name="is_approved" type="checkbox"
                                                       {{($blog_comment['is_approved']=='1')?'checked':''}} value="1"/>
                                            </td>

                                                <?php
                                                if ($blog_comment['is_created_admin']) {
                                                    $created_by = $blog_comment->created_by_admin;
                                                } else {
                                                    $created_by = $blog_comment->created_by_user;
                                                }
                                                ?>

                                            <td>{{isset($created_by->name)? $created_by->name:''}}</td>
                                            <td>{{$blog_comment['is_created_admin']==1?'Admin':'Customer'}}</td>

                                            <td>

                                                @if($blog_comment['is_reply'] != 1)

                                                    <a data-id="{{$blog_comment['id']}}"
                                                       data-comment="{{$blog_comment['comment']}}" type="button"
                                                       data-toggle="modal" data-target="#myModal"
                                                       class="comment-row btn btn-info btn-sm edit"
                                                       style="cursor: pointer;">
                                                        <i class="tio-edit"></i>{{ translate('Reply')}}
                                                    </a>
                                                @endif

                                            </td>
                                            <td>
                                                <a class="btn btn-danger btn-sm delete-btn" data-toggle="modal"
                                                   data-target="#myModal2" style="cursor: pointer;"
                                                   id="{{$blog_comment['id']}}">
                                                    <i class="tio-add-to-trash"></i>{{ translate('Delete')}}
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="myModal" role="dialog">
        <div class="modal-dialog">

            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">{{ translate('Blog_Post')}}: {{$blog_post->title}}</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <p>
                        {{$blog_post->content}}
                    </p>
                    <h4>Comment: <small id="blog_comment_show"></small></h4>
                    <h4>Replies: </h4>
                    <ol id="blog_comment_reply_show"></ol>

                    <form action="{{route('admin.blog_comments.reply.store')}}" method="POST"
                          enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" class="form-control" name="blog_id" value="{{ $blog_post->id }}">
                        <input type="hidden" class="form-control" name="blog_comment_id" value="">
                        <input type="hidden" class="form-control" name="is_reply" value="1">

                        <div class="row">
                            <div class="col-12">
                                <div class="form-group ">
                                    <label class="input-label" for="">{{translate('Reply')}}:</label>

                                    <textarea class="form-control" name="comment"></textarea>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group ">
                                    <input class="btn btn-success btn-" type="submit" class="form-control"
                                           value="     Submit     "/>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal ends -->

    <!-- Modal -->
    <div class="modal fade" id="myModal2" role="dialog">
        <div class="modal-dialog">

            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">{{ translate('Comment_will_be_deleted')}}!</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">

                    <h4>Are you sure? </h4>
                    <hr>

                    <input type="hidden" class="form-control" name="blog_post_comment_id" value="">

                    <div class="row">
                        <div class="col-3 ">
                            <button class="btn btn-danger" id="comment_delete">Delete</button>
                        </div>
                        <div class="col-6">
                            <button class="btn btn-success" id="close_confirm" data-dismiss="modal">Cancecl</button>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal ends -->


    <div style="display: none;" id="replies">{{json_encode($replies)}}</div>
@endsection

@push('script')
    <script>
        $(document).ready(function () {

            $(document).on('click', '.comment-row', function () {

                var comment_id = $(this).data('id');

                var replies = $('#replies').text();
                replies = $.parseJSON(replies);

                console.log(replies);
                var this_replies = [];
                $('#blog_comment_reply_show').html('');
                $.each(replies, function (index, val) {
                    if (val.parent_id == comment_id) {
                        // this_replies.push(val.comment);

                        $('#blog_comment_reply_show').append('<li>' + val.comment + '</li>');
                    }
                });

                var comment = $(this).data('comment');

                $('input[name="blog_comment_id"]').val(comment_id);
                $('#blog_comment_show').text(comment);
            });

            $(document).on('click', '.delete-btn', function () {
                var comment_id = $(this).attr('id');
                $('#comment_delete').attr('data-comment_id', comment_id);
            });

            $(document).on('click', '#comment_delete', function () {

                var comment_id = $(this).data('comment_id');

                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                    }
                });

                $.ajax({
                    url: "{{route('admin.blog_comments.delete')}}",
                    data: {comment_id: comment_id},
                    method: 'POST',
                    success: function (data) {
                        console.log(data.message);
                        toastr.success(data.message);
                        $('#close_confirm').click();
                        location.reload();
                    }
                });
            });

            $(document).on('click', '.approve_row', function () {

                var status = $(this).is(':checked');

                var comment_id = $(this).data('comment_id');

                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                    }
                });

                $.ajax({
                    url: "{{route('admin.blog_comments.update_approvement')}}",
                    data: {status: status, comment_id: comment_id},
                    method: 'POST',
                    success: function (data) {
                        console.log(data.message);
                        toastr.success(data.message);
                    }
                });

            });


            // $('#dataTable').DataTable();
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
