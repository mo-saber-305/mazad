@extends('admin.layouts.app')
@section('panel')
    <div class="d-flex justify-content-center align-items-center my-4 products-btn-list">
        <a href="{{route('admin.merchants.active')}}" class="btn bg--gradi-1 border-0 mr-3 py-2">
            @lang('Active Users')
        </a>
        <a href="{{route('admin.merchants.banned')}}" class="btn bg--gradi-6 border-0 mr-3 py-2">
            @lang('Banned Users')
            @if($banned_merchants_count)
                <span class="menu-badge pill bg--black {{ app()->getLocale() == 'ar' ? 'mr-2' : 'ml-2' }} rounded px-2">{{$banned_merchants_count}}</span>
            @endif
        </a>
        <a href="{{route('admin.merchants.email.unverified')}}" class="btn bg--gradi-8 border-0 mr-3 py-2">
            @lang('Email Unverified')
            @if($email_unverified_merchants_count)
                <span class="menu-badge pill bg--black {{ app()->getLocale() == 'ar' ? 'mr-2' : 'ml-2' }} rounded px-2">{{$email_unverified_merchants_count}}</span>
            @endif
        </a>
        <a href="{{route('admin.merchants.sms.unverified')}}" class="btn bg--gradi-11 border-0 mr-3 py-2">
            @lang('SMS Unverified')
            @if($sms_unverified_merchants_count)
                <span class="menu-badge pill bg--black {{ app()->getLocale() == 'ar' ? 'mr-2' : 'ml-2' }} rounded px-2">{{$sms_unverified_merchants_count}}</span>
            @endif
        </a>
        <a href="{{route('admin.merchants.with.balance')}}" class="btn bg--gradi-9 border-0 mr-3 py-2">
            @lang('With Balance')
        </a>
        <a href="{{route('admin.merchants.email.all')}}" class="btn bg--gradi-10 border-0 mr-3 py-2">
            @lang('Email to All')
        </a>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="card b-radius--10 ">
                <div class="card-body p-0">
                    <div class="table-responsive--md  table-responsive">
                        <table class="table table--light style--two">
                            <thead>
                            <tr>
                                <th>@lang('Merchant')</th>
                                <th>@lang('Email-Phone')</th>
                                <th>@lang('Country')</th>
                                <th>@lang('Joined At')</th>
                                <th>@lang('Balance')</th>
                                <th>@lang('Action')</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($merchants as $merchant)
                                <tr>
                                    <td data-label="@lang('Merchant')">
                                        <span class="font-weight-bold">{{$merchant->fullname}}</span>
                                        <br>
                                        <span class="small">
                                            <a href="{{ route('admin.merchants.detail', $merchant->id) }}"><span>@</span>{{ $merchant->username }}</a>
                                        </span>
                                    </td>


                                    <td data-label="@lang('Email-Phone')">
                                        {{ $merchant->email }}<br>{{ $merchant->mobile }}
                                    </td>
                                    <td data-label="@lang('Country')">
                                        <span class="font-weight-bold" data-toggle="tooltip"
                                              data-original-title="{{ @$merchant->address->country }}">{{ $merchant->country_code }}</span>
                                    </td>


                                    <td data-label="@lang('Joined At')">
                                        {{ showDateTime($merchant->created_at) }} <br> {{ diffForHumans($merchant->created_at) }}
                                    </td>

                                    <td data-label="@lang('Balance')">
                                        <span class="font-weight-bold">

                                            {{ $general->cur_sym }}{{ showAmount($merchant->balance) }}
                                        </span>
                                    </td>

                                    <td data-label="@lang('Action')">
                                        <a href="{{ route('admin.merchants.detail', $merchant->id) }}" class="icon-btn" data-toggle="tooltip" title=""
                                           data-original-title="@lang('Details')">
                                            <i class="las la-desktop text--shadow"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td class="text-muted text-center" colspan="100%">{{ __($emptyMessage) }}</td>
                                </tr>
                            @endforelse

                            </tbody>
                        </table><!-- table end -->
                    </div>
                </div>
                @if ($merchants->hasPages())
                    <div class="card-footer py-4">
                        {{ paginateLinks($merchants) }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@push('breadcrumb-plugins')
    <div class="d-flex flex-wrap justify-content-sm-end header-search-wrapper">
        <form action="{{ route('admin.merchants.search', $scope ?? str_replace('admin.merchants.', '', request()->route()->getName())) }}" method="GET"
              class="form-inline float-sm-right bg--white">
            <div class="input-group has_append">
                <input type="text" name="search" class="form-control" placeholder="@lang('Merchantname or email')" value="{{ $search ?? '' }}">
                <div class="input-group-append">
                    <button class="btn btn--primary" type="submit"><i class="fa fa-search"></i></button>
                </div>
            </div>
        </form>

        @php
            $segments = request()->segments();
            $model_type = end($segments);
        @endphp
        <div class="dropdown ml-4">
            <button class="btn btn--primary box--shadow1 btn-lg dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false">
                @lang('Export')
            </button>
            <div class="dropdown-menu text-center">
                <a class="dropdown-item"
                   href="{{ route('admin.export.merchants', ['model_type' => $model_type, 'file_type' => 'excel']) }}">@lang('Excel')</a>
                <a class="dropdown-item"
                   href="{{ route('admin.export.merchants', ['model_type' => $model_type, 'file_type' => 'csv']) }}">@lang('Csv')</a>
            </div>
        </div>
    </div>
@endpush
