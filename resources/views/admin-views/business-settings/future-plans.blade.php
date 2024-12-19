@extends('layouts.back-end.app')
@section('title', translate('Future Plans'))
@section('content')
    <div class="content container-fluid">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ translate('Dashboard') }}</a></li>
                <li class="breadcrumb-item" aria-current="page">{{ translate('Future Plans') }}</li>
            </ol>
        </nav>

        <div class="row" style="margin-top: 20px">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row justify-content-between pl-4 pr-4">
                            <div>
                                <h5><b>{{ translate('future_plans') }}</b></h5>
                            </div>
                        </div>
                    </div>

                    <form action="{{ route('admin.business-settings.future_plans_update') }}" method="post">
                        @csrf
                        <div class="card-body">
                            <div class="form-group row">
                                <div class="col-md-12">
                                    <textarea name="future_plans" id="editor" cols="30" rows="20" class="form-control">{{ $future_plans->value }}</textarea>
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-md-12">
                                    <input class="btn btn-primary btn-block" type="submit" name="btn" value="submit">
                                </div>
                            </div>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
    {{-- ck editor --}}
    <script src="{{ asset('/') }}vendor/ckeditor/ckeditor/ckeditor.js"></script>
    <script src="{{ asset('/') }}vendor/ckeditor/ckeditor/adapters/jquery.js"></script>
    <script>
        $('#editor').ckeditor({
            contentsLangDirection: '{{ Session::get('direction') }}',
        });
    </script>
    {{-- ck editor --}}
@endpush
