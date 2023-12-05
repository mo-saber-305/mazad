@extends('merchant.layouts.app')
@section('panel')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <form action="{{ route('merchant.product.update', $product->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="card-body">
                        <div class="payment-method-item">
                            <div class="payment-method-header">
{{--                                <div class="form-group">--}}
{{--                                    <label class="font-weight-bold">@lang('Image') <span class="text-danger">*</span></label>--}}
{{--                                    <div class="thumb">--}}
{{--                                        <div class="avatar-preview">--}}
{{--                                            <div class="profilePicPreview" style="background-image: url('{{getImage(imagePath()['product']['path'].'/'.$product->image,imagePath()['product']['size'])}}')"></div>--}}
{{--                                        </div>--}}
{{--                                        <div class="avatar-edit">--}}
{{--                                            <input type="file" name="image" class="profilePicUpload" id="image" accept=".png, .jpg, .jpeg"/>--}}
{{--                                            <label for="image" class="bg--primary"><i class="la la-pencil"></i></label>--}}
{{--                                        </div>--}}
{{--                                    </div>--}}
{{--                                </div>--}}

                                <div class="form-group">
                                    <label class="w-100 font-weight-bold">@lang('File Type') <span class="text-danger">*</span></label>
                                    <select name="file_type" class="form-control mb-3" id="fileType" required>
                                        <option value="image" {{ $product->file_type == 'image' ? 'selected' : '' }}>@lang('Image')</option>
                                        <option value="video" {{ $product->file_type == 'video' ? 'selected' : '' }}>@lang('Video')</option>
                                    </select>

                                    <div class="image-sec {{ $product->file_type == 'video' ? 'd-none' : ''  }}">
                                        <label class="font-weight-bold">@lang('Image') <span class="text-danger">*</span></label>
                                        <div class="thumb">
                                            <div class="avatar-preview">
                                                <div class="profilePicPreview"
                                                     style="background-image: url('{{$product->file_type == 'image' ? getImage(imagePath()['product']['path'].'/'.$product->image,imagePath()['product']['size'] ) : getImage(imagePath()['product']['path'],imagePath()['product']['size'])}}')"></div>
                                            </div>
                                            <div class="avatar-edit">
                                                <input type="file" name="image" class="profilePicUpload" id="image" accept=".png, .jpg, .jpeg"/>
                                                <label for="image" class="bg--primary"><i class="la la-pencil"></i></label>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="video-sec {{ $product->file_type == 'image' ? 'd-none' : ''  }}">
                                        <label class="font-weight-bold">@lang('Video') <span class="text-danger">*</span></label>
                                        <div class="thumb">
                                            <div class="avatar-preview">
                                                <div class="profilePicPreview" style="display: flex;align-items: center;height: 220px;overflow: hidden;">
                                                    <video controls style="display: block; width: 100%;max-height: 220px;">
                                                        <source src="{{ asset($product->file_type == 'video' ? getImage(imagePath()['product']['path'].'/'.$product->image) : 'assets/default_ads.mp4') }}"
                                                                id="video_here">
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
                                </div>

                                <div class="content">
                                    <div class="row mb-none-15">
                                        <div class="col-sm-12 col-xl-3 col-lg-6 mb-15">
                                            <div class="form-group">
                                                <label class="w-100 font-weight-bold">@lang('Name') <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control " placeholder="@lang('Product Name')" name="name" value="{{ $product->name }}" required/>
                                            </div>
                                        </div>
                                        <div class="col-sm-12 col-xl-3 col-lg-6 mb-15">
                                            <div class="form-group">
                                                <label class="w-100 font-weight-bold">@lang('Category') <span class="text-danger">*</span></label>
                                                <select name="category" class="form-control" required>
                                                    <option value="">@lang('Select One')</option>
                                                    @foreach ($categories as $category)
                                                        <option value="{{ $category->id }}" {{ $product->category_id == $category->id ? 'Selected':'' }}>{{ $category->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-sm-12 col-xl-3 col-lg-6 mb-15">
                                            <label class="w-100 font-weight-bold">@lang('Price') <span class="text-danger">*</span></label>
                                            <div class="input-group has_append">
                                                <input type="text" class="form-control" placeholder="0" name="price" value="{{ getAmount($product->price) }}" required/>
                                                <div class="input-group-append">
                                                    <span class="input-group-text">{{ __($general->cur_text) }}</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-12 col-xl-3 col-lg-6 mb-15">
                                            <label class="w-100 font-weight-bold">@lang('Max Price') <span class="text-danger">*</span></label>
                                            <div class="input-group has_append">
                                                <input type="text" class="form-control" placeholder="0" name="max_price" value="{{ old('max_price', getAmount($product->max_price)) }}" required/>
                                                <div class="input-group-append">
                                                    <span class="input-group-text">{{ __($general->cur_text) }}</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-12 col-xl-4 col-lg-6 mb-15">
                                            <div class="form-group">
                                                <label class="w-100 font-weight-bold">@lang('Schedule') <span class="text-danger">*</span></label>
                                                <select name="schedule" class="form-control" required>
                                                    <option value="1" {{ $product->started_at > now() ? 'Selected' : '' }}>@lang('Yes')</option>
                                                    <option value="0">@lang('No')</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-sm-12 col-xl-4 col-lg-6 mb-15 started_at">
                                            <div class="form-group">
                                                <label class="w-100 font-weight-bold">@lang('Started_at') <span class="text-danger">*</span></label>
                                                <input type="text" name="started_at" placeholder="@lang('Select Date & Time')" id="startDateTime" data-position="bottom left" class="form-control border-radius-5" value="{{ $product->started_at }}" autocomplete="off" required/>
                                            </div>
                                        </div>
                                        <div class="col-sm-12 col-xl-4 col-lg-6 mb-15">
                                            <div class="form-group">
                                                <label class="w-100 font-weight-bold">@lang('Expired_at') <span class="text-danger">*</span></label>
                                                <input type="text" name="expired_at" placeholder="@lang('Select Date & Time')" id="endDateTime" data-position="bottom left" class="form-control border-radius-5" value="{{ $product->expired_at }}" autocomplete="off" required/>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="form-group">
                                                <label class="font-weight-bold">@lang('Sponsor')</label>
                                                <input type="text" class="form-control border-radius-5" placeholder="@lang('Sponsor')"
                                                       name="sponsor" value="{{ old('sponsor', $product->sponsor) }}">
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="form-group">
                                                <label class="font-weight-bold">@lang('Upload Report')</label>
                                                <input type="file" class="form-control border-radius-5"
                                                       name="upload_report" value="{{ old('upload_report') }}" accept=".pdf, .xls, .xlsx, .csv">
                                                @if($product->report_file)
                                                    <a href="{{ asset(imagePath()['reports']['path'] . '/' . $product->report_file) }}" class="d-block mt-1"
                                                       style="font-size: 16px">
                                                        <i class="fas fa-download"></i> @lang('Report File')
                                                    </a>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="col-12">
                                            <div class="form-group">
                                                <label class="font-weight-bold">@lang('Upload Images')</label>
                                                <input type="file" id="imagesInput" class="form-control border-radius-5" name="images[]" multiple>
                                                <div id="imagePreviewContainer" class="row mt-4">
                                                    @foreach($product->images as $image)
                                                        <div class="image-preview col-lg-4 col-sm-6 position-relative mb-3">
                                                            <img src="{{ getImage(imagePath()['product']['path'] . '/' . $image->image, imagePath()['product']['size']) }}" class="preview-image">
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-12">
                                            <div class="form-group">
                                                <label class="font-weight-bold">@lang('Short Description') <span class="text-danger">*</span></label>
                                                <textarea rows="4" class="form-control border-radius-5" name="short_description">{{ $product->short_description }}</textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group mt-3">
                                <label class="font-weight-bold">@lang('Long Description') <span class="text-danger">*</span></label>
                                <textarea rows="8" class="form-control border-radius-5 nicEdit" name="long_description">{{ $product->long_description }}</textarea>
                            </div>

                            <div class="payment-method-body">
                                <div class="row">
                                    <div class="col-lg-12">
                                        <div class="card border--primary mt-3">
                                            <h5 class="card-header bg--primary  text-white">@lang('Specification')
                                                <button type="button" class="btn btn-sm btn-outline-light float-right addUserData"><i class="la la-fw la-plus"></i>@lang('Add New')
                                                </button>
                                            </h5>

                                            <div class="card-body">
                                                <div class="row addedField">
                                                    @if ($product->specification)
                                                        @foreach ($product->specification as $spec)
                                                            <div class="col-md-12 user-data">
                                                                <div class="form-group">
                                                                    <div class="input-group mb-md-0 mb-4">
                                                                        <div class="col-md-4">
                                                                            <input name="specification[{{ $loop->iteration }}][name]" class="form-control" type="text" value="{{ $spec['name'] }}" required placeholder="@lang('Field Name')">
                                                                        </div>
                                                                        <div class="col-md-6">
                                                                            <input name="specification[{{ $loop->iteration }}][value]" class="form-control" type="text" value="{{ $spec['value'] }}" required placeholder="@lang('Field Value')">
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
                                                            </div>
                                                        @endforeach
                                                    @endif
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
    <a href="{{ route('merchant.product.index') }}" class="btn btn-sm btn--primary box--shadow1 text--small"><i class="la la-fw la-backward"></i> @lang('Go Back') </a>
@endpush

@push('style')
    <style>
        .payment-method-item .payment-method-header .thumb .avatar-edit{
            bottom: auto;
            top: 175px;
        }

        .image-preview .delete-image {
            position: absolute;
            right: 20px;
            top: 5px;
        }

        .image-preview .delete-image i {
            margin-right: 0;
        }
    </style>
@endpush

@push('script-lib')
  <script src="{{ asset('assets/admin/js/vendor/datepicker.min.js') }}"></script>
  <script src="{{ asset('assets/admin/js/vendor/datepicker.en.js') }}"></script>
@endpush

@push('script')
    <script>

        (function ($) {
            "use strict";

            var specCount = `{{ $product->specification ? count($product->specification) : 0 }}`;
            specCount = parseInt(specCount);
            specCount = specCount ? specCount + 1 : 1;

            // Create start date
            var start = new Date(),
                    prevDay,
                    startHours = 0;

                // 09:00 AM
                start.setHours(0);
                start.setMinutes(0);

                // If today is Saturday or Sunday set 10:00 AM
                if ([6, 0].indexOf(start.getDay()) != -1) {
                    start.setHours(10);
                    startHours = 10
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

                    // If chosen day is Saturday or Sunday when set
                    // hour value for weekends, else restore defaults
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

                    // If chosen day is Saturday or Sunday when set
                    // hour value for weekends, else restore defaults
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

            $("[name=schedule]").on('change', function(e){
                var schedule = e.target.value;
                if(schedule != 1){
                    $("[name=started_at]").attr('disabled', true);
                    $('.started_at').css('display', 'none');
                }else{
                    $("[name=started_at]").attr('disabled', false);
                    $('.started_at').css('display', 'block');
                }
            }).change();

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


            $('#imagesInput').on('change', function (e) {
                $('#imagePreviewContainer').html(''); // Clear previous previews
                var files = e.target.files;

                for (var i = 0; i < files.length; i++) {
                    var reader = new FileReader();

                    reader.onload = function (e) {
                        $('#imagePreviewContainer').append('<div class="image-preview col-lg-4 col-sm-6 position-relative mb-3">' +
                            '<img src="' + e.target.result + '" class="preview-image">' +
                            '<button type="button" class="delete-image btn btn-danger"><i class="fas fa-trash-alt"></i></button>' +
                            '</div>');
                    };

                    reader.readAsDataURL(files[i]);
                }
            });

            // Image deletion
            $('#imagePreviewContainer').on('click', '.delete-image', function () {
                $(this).parent().remove();
            });

        })(jQuery);
    </script>
@endpush
