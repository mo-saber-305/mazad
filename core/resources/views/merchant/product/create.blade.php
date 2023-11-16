@extends('merchant.layouts.app')
@section('panel')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <form action="{{ route('merchant.product.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="card-body">
                        <div class="payment-method-item">
                            <div class="payment-method-header">
                                <div class="form-group">
                                    <label class="w-100 font-weight-bold">@lang('File Type') <span class="text-danger">*</span></label>
                                    <select name="file_type" class="form-control mb-3" id="fileType" required>
                                        <option value="image">@lang('Image')</option>
                                        <option value="video">@lang('Video')</option>
                                    </select>

                                    <div class="image-sec">
                                        <label class="font-weight-bold">@lang('Image') <span class="text-danger">*</span></label>
                                        <div class="thumb">
                                            <div class="avatar-preview">
                                                <div class="profilePicPreview"
                                                     style="background-image: url('{{getImage(imagePath()['product']['path'],imagePath()['product']['size'])}}')"></div>
                                            </div>
                                            <div class="avatar-edit">
                                                <input type="file" name="image" class="profilePicUpload" id="image" accept=".png, .jpg, .jpeg"/>
                                                <label for="image" class="bg--primary"><i class="la la-pencil"></i></label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="video-sec d-none">
                                        <label class="font-weight-bold">@lang('Video') <span class="text-danger">*</span></label>
                                        <div class="thumb">
                                            <div class="avatar-preview">
                                                <div class="profilePicPreview" style="display: flex;align-items: center;height: 220px;overflow: hidden;">
                                                    <video controls style="display: block; width: 100%;max-height: 220px;">
                                                        <source src="{{ asset('assets/default_ads.mp4') }}" id="video_here">
                                                        @lang('Your browser does not support HTML5 video')
                                                    </video>
                                                </div>
                                            </div>
                                            <div class="avatar-edit">
                                                <input type="file" name="video" class="profilePicUpload" id="video"
                                                       accept=".mp4, .mov, .ogg, .qt, .flv, .3gp, .avi, .wmv"/>


                                                <label for="video" class="bg--primary"><i class="la la-pencil"></i></label>
                                            </div>
                                        </div>
                                    </div>

                                    {{--                                    <label class="font-weight-bold">@lang('Image') <span class="text-danger">*</span></label>--}}
                                    {{--                                    <div class="thumb">--}}
                                    {{--                                        <div class="avatar-preview">--}}
                                    {{--                                            <div class="profilePicPreview"--}}
                                    {{--                                                 style="background-image: url('{{getImage(imagePath()['product']['path'],imagePath()['product']['size'])}}')"></div>--}}
                                    {{--                                        </div>--}}
                                    {{--                                        <div class="avatar-edit">--}}
                                    {{--                                            <input type="file" name="image" class="profilePicUpload" id="image" accept=".png, .jpg, .jpeg"/>--}}
                                    {{--                                            <label for="image" class="bg--primary"><i class="la la-pencil"></i></label>--}}
                                    {{--                                        </div>--}}
                                    {{--                                    </div>--}}
                                </div>

                                <div class="content">
                                    <div class="row mb-none-15">
                                        <div class="col-sm-12 col-xl-3 col-lg-6 mb-15">
                                            <div class="form-group">
                                                <label class="w-100 font-weight-bold">@lang('Name') <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control " placeholder="@lang('Product Name')" name="name"
                                                       value="{{ old('name') }}" required/>
                                            </div>
                                        </div>
                                        <div class="col-sm-12 col-xl-3 col-lg-6 mb-15">
                                            <div class="form-group">
                                                <label class="w-100 font-weight-bold">@lang('Category') <span class="text-danger">*</span></label>
                                                <select name="category" class="form-control" required>
                                                    <option value="">@lang('Select One')</option>
                                                    @foreach ($categories as $category)
                                                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-sm-12 col-xl-3 col-lg-6 mb-15">
                                            <label class="w-100 font-weight-bold">@lang('Price') <span class="text-danger">*</span></label>
                                            <div class="input-group has_append">
                                                <input type="text" class="form-control" placeholder="0" name="price" value="{{ old('price') }}" required/>
                                                <div class="input-group-append">
                                                    <span class="input-group-text">{{ __($general->cur_text) }}</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-12 col-xl-3 col-lg-6 mb-15">
                                            <label class="w-100 font-weight-bold">@lang('Max Price') <span class="text-danger">*</span></label>
                                            <div class="input-group has_append">
                                                <input type="text" class="form-control" placeholder="0" name="max_price" value="{{ old('max_price') }}"
                                                       required/>
                                                <div class="input-group-append">
                                                    <span class="input-group-text">{{ __($general->cur_text) }}</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-12 col-xl-4 col-lg-6 mb-15">
                                            <div class="form-group">
                                                <label class="w-100 font-weight-bold">@lang('Schedule') <span class="text-danger">*</span></label>
                                                <select name="schedule" class="form-control" required>
                                                    <option value="1">@lang('Yes')</option>
                                                    <option value="0">@lang('No')</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-sm-12 col-xl-4 col-lg-6 mb-15 started_at">
                                            <div class="form-group">
                                                <label class="w-100 font-weight-bold">@lang('Started_at') <span class="text-danger">*</span></label>
                                                <input type="text" name="started_at" placeholder="@lang('Select Date & Time')" id="startDateTime"
                                                       data-position="bottom left" class="form-control border-radius-5" value="{{ old('date_time') }}"
                                                       autocomplete="off" required/>
                                            </div>
                                        </div>
                                        <div class="col-sm-12 col-xl-4 col-lg-6 mb-15">
                                            <div class="form-group">
                                                <label class="w-100 font-weight-bold">@lang('Expired_at') <span class="text-danger">*</span></label>
                                                <input type="text" name="expired_at" placeholder="@lang('Select Date & Time')" id="endDateTime"
                                                       data-position="bottom left" class="form-control border-radius-5" value="{{ old('date_time') }}"
                                                       autocomplete="off" required/>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="form-group">
                                                <label class="font-weight-bold">@lang('Sponsor')</label>
                                                <input type="text" class="form-control border-radius-5" placeholder="@lang('Sponsor')"
                                                       name="sponsor" value="{{ old('sponsor') }}">
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="form-group">
                                                <label class="font-weight-bold">@lang('Upload Report')</label>
                                                <input type="file" class="form-control border-radius-5"
                                                       name="upload_report" value="{{ old('upload_report') }}" accept=".pdf, .xls, .xlsx, .csv">
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <div class="form-group">
                                                <label class="font-weight-bold">@lang('Short Description') <span class="text-danger">*</span></label>
                                                <textarea rows="4" class="form-control border-radius-5"
                                                          name="short_description">{{ old('short_description') }}</textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group mt-3">
                                <label class="font-weight-bold">@lang('Long Description') <span class="text-danger">*</span></label>
                                <textarea rows="8" class="form-control border-radius-5 nicEdit" name="long_description">{{ old('long_description') }}</textarea>
                            </div>

                            <div class="payment-method-body">
                                <div class="row">
                                    <div class="col-lg-12">
                                        <div class="card border--primary mt-3">
                                            <h5 class="card-header bg--primary  text-white">@lang('Specification')
                                                <button type="button" class="btn btn-sm btn-outline-light float-right addUserData"><i
                                                            class="la la-fw la-plus"></i>@lang('Add New')
                                                </button>
                                            </h5>

                                            <div class="card-body">
                                                <div class="row addedField">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn--primary btn-block">@lang('Submit')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection


@push('breadcrumb-plugins')
    <a href="{{ route('merchant.product.index') }}" class="btn btn-sm btn--primary box--shadow1 text--small"><i
                class="la la-fw la-backward"></i> @lang('Go Back') </a>
@endpush

@push('script-lib')
    <script src="{{ asset('assets/admin/js/vendor/datepicker.min.js') }}"></script>
    <script src="{{ asset('assets/admin/js/vendor/datepicker.en.js') }}"></script>
@endpush


@push('style')
    <style>
        .payment-method-item .payment-method-header .thumb .avatar-edit {
            bottom: auto;
            top: 175px;
        }
    </style>
@endpush

@push('script')
    <script>

        (function ($) {
            "use strict";

            var specCount = 1;

            // Create start date
            var start = new Date(),
                prevDay,
                startHours = 0;

            start.setHours(0);
            start.setMinutes(0);

            if ([6, 0].indexOf(start.getDay()) != -1) {
                start.setHours(0);
                startHours = 0
            }
            // date and time picker 
            $('#startDateTime').datepicker({
                timepicker: true,
                language: 'en',
                dateFormat: 'dd-mm-yyyy',
                startDate: start,
                minHours: startHours,
                maxHours: 23,
                onSelect: function (fd, d, picker) {
                    // Do nothing if selection was cleared
                    if (!d) return;

                    var day = d.getDay();

                    // Trigger only if date is changed
                    if (prevDay != undefined && prevDay == day) return;
                    prevDay = day;


                    if (day == 6 || day == 0) {
                        picker.update({
                            minHours: 0,
                            maxHours: 23
                        })
                    } else {
                        picker.update({
                            minHours: 0,
                            maxHours: 23
                        })
                    }
                }
            });

            // date and time picker 
            $('#endDateTime').datepicker({
                timepicker: true,
                language: 'en',
                dateFormat: 'dd-mm-yyyy',
                startDate: start,
                minHours: startHours,
                maxHours: 23,
                onSelect: function (fd, d, picker) {
                    // Do nothing if selection was cleared
                    if (!d) return;

                    var day = d.getDay();

                    // Trigger only if date is changed
                    if (prevDay != undefined && prevDay == day) return;
                    prevDay = day;

                    if (day == 6 || day == 0) {
                        picker.update({
                            minHours: 0,
                            maxHours: 23
                        })
                    } else {
                        picker.update({
                            minHours: 0,
                            maxHours: 23
                        })
                    }
                }
            });


            $('input[name=currency]').on('input', function () {
                $('.currency_symbol').text($(this).val());
            });
            $('.addUserData').on('click', function () {
                var html = `
                    <div class="col-md-12 user-data">
                        <div class="form-group">
                            <div class="input-group mb-md-0 mb-4">
                                <div class="col-md-4">
                                    <input name="specification[${specCount}][name]" class="form-control" type="text" required placeholder="@lang('Field Name')">
                                </div>
                                <div class="col-md-6">
                                    <input name="specification[${specCount}][value]" class="form-control" type="text" required placeholder="@lang('Field Value')">
                                </div>
                                <div class="col-md-2 mt-md-0 mt-2 text-right">
                                    <span class="input-group-btn">
                                        <button class="btn btn--danger btn-lg removeBtn w-100" type="button">
                                            <i class="fa fa-times"></i>
                                        </button>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>`;
                $('.addedField').append(html);
                specCount += 1;
            });

            $(document).on('click', '.removeBtn', function () {
                $(this).closest('.user-data').remove();
            });

            @if(old('currency'))
            $('input[name=currency]').trigger('input');
            @endif

            $("[name=schedule]").on('change', function (e) {
                var schedule = e.target.value;

                if (schedule != 1) {
                    $("[name=started_at]").attr('disabled', true);
                    $('.started_at').css('display', 'none');
                } else {
                    $("[name=started_at]").attr('disabled', false);
                    $('.started_at').css('display', 'block');
                }
            }).change()

            $("select#fileType").on('change', function (e) {
                var file_type = $(this).val();

                if (file_type == 'video') {
                    $(".image-sec").addClass('d-none');
                    $(".video-sec").removeClass('d-none');
                } else {
                    $(".image-sec").removeClass('d-none');
                    $(".video-sec").addClass('d-none');
                }
            }).change();

            $(document).on("change", ".video-sec #video", function (evt) {
                var $source = $('#video_here');
                $source[0].src = URL.createObjectURL(this.files[0]);
                $source.parent()[0].load();

            });

        })(jQuery);
    </script>
@endpush
