@extends('layouts.back-end.app')

@section('title', translate('Question and answering'))

@push('css_or_js')

@endpush

@section('content')
    <div class="content container-fluid">
        <!-- Page Header -->
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}">{{translate('Dashboard')}}</a></li>
                <li class="breadcrumb-item" aria-current="page"><a
                        href="{{route('admin.qa.index')}}">{{translate('question and answering')}}</a></li>
                <li class="breadcrumb-item" aria-current="page">{{translate('Reply')}}</li>
            </ol>
        </nav>
        <!-- End Page Header -->
        <div class="row gx-2 gx-lg-3">
            <div class="col-sm-12 col-lg-12 mb-3 mb-lg-2">
                <!-- Card -->
                <div class="card">
                    <section style="background-color: #eee;">
                        <div class="container my-5 py-5">
                            <div class="row d-flex justify-content-center">
                                <div class="col-md-12 col-lg-10 col-xl-8">
                                    @if (session('success'))
                                        <div class="alert alert-success">{{ session('success')}}</div>
                                    @endif
                                    <div class="card">
                                        <div class="card-body">
                                            <div class="d-flex flex-start align-items-center">
                                                @php
                                                    $avatar = false;
                                                    $default = "https://ui-avatars.com/api/?rounded=true&name=".optional($qa->customer)->f_name."+".optional($qa->customer)->l_name;
                                                @endphp
                                                <img class="rounded-circle shadow-1-strong me-3"
                                                     src="{{ $avatar ? $avatar : $default }}" alt="avatar" width="60"
                                                     height="60"/>
                                                <div class="ml-3">
                                                    <h6 class="fw-bold text-primary mb-1">{{ optional($qa->customer)->fullname }}</h6>
                                                    <p class="text-muted small mb-0">
                                                        {{$qa->created_at->diffforhumans()}}
                                                    </p>
                                                </div>
                                            </div>

                                            <p class="mt-3 mb-4 pb-2">
                                                {{$qa->question}}
                                            </p>

                                            {{-- <div class="small d-flex justify-content-start">
                                              <a href="#!" class="d-flex align-items-center me-3">
                                                <i class="far fa-thumbs-up me-2"></i>
                                                <p class="mb-0">Like</p>
                                              </a>
                                              <a href="#!" class="d-flex align-items-center me-3">
                                                <i class="far fa-comment-dots me-2"></i>
                                                <p class="mb-0">Comment</p>
                                              </a>
                                              <a href="#!" class="d-flex align-items-center me-3">
                                                <i class="fas fa-share me-2"></i>
                                                <p class="mb-0">Share</p>
                                              </a> --}}
                                        </div>
                                    </div>
                                    <form action="{{ route('admin.qa.reply', $qa->id ) }}"
                                          class="card-footer py-3 border-0" style="background-color: #f8f9fa;">
                                        <div class="d-flex flex-start w-100">
                                            @php
                                                $avatar = file_exists(asset("/storage/seller/".auth('admin','seller')->user()->image));
                                                $default = "https://ui-avatars.com/api/?rounded=true&name=".auth('admin','seller')->user()->f_name."+".auth('admin','seller')->user()->l_name;
                                            @endphp
                                            <img class="rounded-circle shadow-1-strong me-3"
                                                 src="{{ $avatar ? asset("/storage/seller/".auth('admin','seller')->user()->image) : $default }}"
                                                 alt="avatar" width="40"
                                                 height="40"/>
                                            <div class="form-outline w-100 ml-4">
                                                <h5>Reply</h5>
                                                <textarea class="form-control" id="textAreaExample" name="answer"
                                                          rows="4" style="background: #fff;">{{$qa->answer}}</textarea>
                              </div>
                            </div>
                            <div class="float-end mt-2 pt-1">
                              <button type="submit" class="btn btn-primary btn-sm">Reply</button>
                            </div>
                          </form>
                        </div>
                      </div>
                    </div>
                  </div>
                </section>
                    <!-- End Table -->
                </div>
                <!-- End Card -->
            </div>
        </div>
    </div>
@endsection
