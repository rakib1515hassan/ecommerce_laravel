@extends('layouts.back-end.app')

@section('title', translate('Language'))

@push('css_or_js')

    <link href="{{ asset('assets/back-end/css/custom.css')}}" rel="stylesheet">
    <style>
        .image-preview {
            height: 17px !important;
            display: inline-block !important;
            margin-right: 5px !important;
            margin-left: 3px !important;
            margin-top: -5px !important;
        }
    </style>
@endpush

@section('content')
    <div class="content container-fluid">
        <!-- Page Heading -->
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a
                        href="{{route('admin.dashboard')}}">{{translate('Dashboard')}}</a>
                </li>
                <li class="breadcrumb-item"
                    aria-current="page">{{translate('language_setting')}} {{translate('for_app')}}</li>
            </ol>
        </nav>

        <div class="row" style="margin-top: 20px">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h4>{{translate('Select Country code')}}
                            {{translate('for')}} {{translate('product')}} {{translate('and')}} {{translate('category')}} {{translate('language')}}</h4>
                        <label class="badge badge-danger">* {{translate('For mobile app only')}}</label>
                    </div>
                    <div class="card-body">
                        <form action="{{route('admin.business-settings.web-config.update-language')}}" method="post">
                            @csrf
                            @php($language=\App\Models\BusinessSetting::where('type','pnc_language')->first())
                            @php($language = json_decode($language->value,true) ?? [])

                            <div class="form-group">
                                <select name="language[]" id="language" onchange="$('#alert_box').show();"
                                        data-maximum-selection-length="3"
                                        class="form-control js-select2-custom country-var-select"
                                        required multiple=true>
                                    @foreach(\Illuminate\Support\Facades\File::files(base_path('public/assets/front-end/img/flags')) as $path)
                                        <option value="{{ pathinfo($path)['filename'] }}"
                                                {{in_array(pathinfo($path)['filename'],$language)?'selected':''}}
                                                title="{{ asset('assets/front-end/img/flags/'.pathinfo($path)['filename'].'.png') }}">
                                            {{ strtoupper(pathinfo($path)['filename']) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <button type="submit"
                                    class="btn btn-primary float-right ml-3">{{translate('Save')}}</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script>
        $(document).ready(function () {
            // color select select2
            $('.country-var-select').select2({
                templateResult: codeSelect,
                templateSelection: codeSelect,
                escapeMarkup: function (m) {
                    return m;
                }
            });

            function codeSelect(state) {
                var code = state.title;
                if (!code) return state.text;
                return "<img class='image-preview' src='" + code + "'>" + state.text;
            }
        });
    </script>
@endpush
