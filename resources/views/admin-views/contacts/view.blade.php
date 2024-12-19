@extends('layouts.back-end.app')
@section('title', translate('Contact View'))
@push('css_or_js')

    <link href="{{asset('assets/back-end/css/croppie.css')}}" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endpush

@section('content')
    <div class="content container-fluid">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a
                            href="{{route('admin.dashboard')}}"> {{translate('Dashboard')}}</a>
                </li>
                <li class="breadcrumb-item"
                    aria-current="page">{{translate('Message')}} {{translate('view')}}</li>
            </ol>
        </nav>
        <!-- Page Heading -->
        <div class="container">
            <div class="d-sm-flex align-items-center justify-content-between mb-2">
                <h1 class="h3 mb-0 text-black-50">{{translate('View_User_Message')}}</h1>
            </div>

            <!-- Content Row -->

            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body mt-3 ml-4">
                            <div class="row "
                                 style="text-align: {{Session::get('direction') === "rtl" ? 'right' : 'left'}};">
                                <div class="col-md-3 col-lg-3 hidden-xs hidden-sm">
                                    <img
                                            onerror="this.src='{{asset('assets/front-end/img/image-place-holder.png')}}'"
                                            style="height: 8rem; width: 9rem;" class="img-circle"
                                            src="{{asset('assets/front-end')}}/img/contacts/blank.jpg"
                                            alt="User Pic">

                                </div>

                                <div class=" col-md-9 col-lg-9 hidden-xs hidden-sm">
                                    <strong style="margin-right: 20px">{{$contact->subject}}</strong>
                                    @if($contact->seen==1)
                                        <label
                                                style="color: green; border: 1px solid;padding: 2px;border-radius: 10px">{{translate('Seen')}}</label>
                                    @else
                                        <label
                                                style="color: red; border: 1px solid;padding: 2px;border-radius: 10px">{{translate('Not_Seen_Yet')}}</label>
                                    @endif
                                    <br>
                                    <table class="table table-user-information">
                                        <tbody>
                                        <tr>
                                            <td>{{translate('User')}} {{translate('name')}}
                                                :
                                            </td>
                                            <td>{{$contact->name}}</td>
                                        </tr>
                                        <tr>
                                            <td>{{translate('mobile_no')}}:</td>
                                            <td>{{$contact->mobile_number}}</td>
                                        </tr>
                                        <tr>
                                            <td>{{translate('Email')}}:</td>
                                            <td>{{$contact->email}}</td>
                                        </tr>
                                        <tr>
                                            <td>{{translate('messages')}}</td>
                                            <td><p style="font-width:16px;"> {{$contact->message}}</p></td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>


                                <div class="col-12">
                                    <div class="card">
                                        <div class="card-body">
                                            <h3>
                                                <u>{{translate('Reply_from_admin')}}</u>
                                            </h3>
                                            @if($contact['reply']!=null)
                                                @php($data=json_decode($contact['reply'],true))
                                                <div class="flex-start">
                                                    <h6 class="{{Session::get('direction') === "rtl" ? 'ml-1' : 'mr-1'}}">{{translate('Subject')}}
                                                        : </h6>
                                                    <h6>{{$data['subject']}}</h6>
                                                </div>
                                                <div class="flex-start">
                                                    <h6 class="{{Session::get('direction') === "rtl" ? 'ml-1' : 'mr-1'}}">{{translate('Body')}}
                                                        : </h6>
                                                    <h6>{{$data['body']}}</h6>
                                                </div>
                                            @else
                                                <label class="badge badge-danger">{{translate('No_reply')}}
                                                    .</label>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <div class="col-12">
                                    <form action="{{route('admin.contact.update',$contact->id)}}" method="post">
                                        @csrf
                                        <div class="form-group" style="display: none">
                                            <div class="row">
                                                <div class="col-md-10">
                                                    <h4>{{translate('Feedback')}}</h4>
                                                    <textarea class="form-control " name="feedback" id="" rows="5"
                                                              placeholder="{{translate('Please_send_a_Feedback')}}">{{$contact->feedback}}</textarea>
                                                </div>
                                            </div>
                                        </div>


                                        <div class="card-footer pl-0">
                                            @if($contact->seen==0)
                                                <button type="submit" class="btn btn-primary ">
                                                    <i class="fa fa-check"></i> {{translate('Seen')}}
                                                </button>
                                            @endif
                                        </div>
                                    </form>
                                </div>

                                <div class="col-12 mt-4">
                                    <center>
                                        <h3>{{translate('Send_Mail')}}</h3>
                                        <label class="badge badge-soft-danger">{{translate('Configure_your_mail_setup_first')}}
                                            .</label>
                                    </center>


                                    <form action="{{route('admin.contact.send-mail',$contact->id)}}" method="post">
                                        @csrf
                                        <div class="form-group">
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <h6>{{translate('Subject')}}</h6>
                                                    <input class="form-control" name="subject">
                                                </div>
                                                <div class="col-md-12 mt-3">
                                                    <h6>{{translate('Mail_Body')}}</h6>
                                                    <textarea class="form-control " name="mail_body" id="" rows="5"
                                                              placeholder="{{translate('Please_send_a_Feedback')}}"></textarea>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="card-footer pl-0">
                                            <button type="submit" class="btn btn-primary ">
                                                <i class="fa fa-check"></i>{{translate('send')}}
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('script')

@endpush
