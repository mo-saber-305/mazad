@extends('admin.layouts.app')

@section('panel')

    <div class="row mb-none-30">
        <div class="col-xl-12">
            <div class="card">
                <form action="{{ route('admin.users.email.all') }}" method="POST">
                    @csrf
                    <div class="card-body">
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label class="font-weight-bold">@lang('Interests')</label>
                                <select name="interests[]" class="form-control" multiple="multiple">
                                    <option value="" disabled>@lang('Select Interests')</option>
                                    @foreach(\App\Models\Interest::where('status', 1)->get() as $interest)
                                        <option value="{{ $interest->id }}">@lang($interest->name)</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group col-md-6">
                                <label class="font-weight-bold">@lang('Subject') <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" placeholder="@lang('Email subject')" name="subject" required/>
                            </div>

                            <div class="form-group col-md-12">
                                <label class="font-weight-bold">@lang('Message') <span class="text-danger">*</span></label>
                                <textarea name="message" rows="10" class="form-control nicEdit"></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="card-footer">
                        <div class="form-row">
                            <div class="form-group col-md-12 text-center">
                                <button type="submit" class="btn btn-block btn--primary mr-2">@lang('Send Email')</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection

@push('style')
        <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet"/>
        <style>
            .select2-selection__rendered {
                line-height: 24px !important;
            }

            .select2-container .select2-search--inline .select2-search__field {
                height: 27px;
            }
        </style>
@endpush

@push('script')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function () {
            $('select').select2({
                placeholder: "@lang('Select Interests')",
                allowClear: true
            });
        });
    </script>
@endpush