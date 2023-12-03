@extends('admin.layouts.app')
@section('panel')
    <div class="d-flex flex-wrap justify-content-center align-items-center my-4 products-btn-list">
        <a href="{{route('admin.users.active')}}" class="btn bg--gradi-1 border-0 mr-3 mb-3 py-2">
            @lang('Active Users')
        </a>
        <a href="{{route('admin.users.banned')}}" class="btn bg--gradi-6 border-0 mr-3 mb-3 py-2">
            @lang('Banned Users')
            @if($banned_users_count)
                <span class="menu-badge pill bg--black {{ app()->getLocale() == 'ar' ? 'mr-2' : 'ml-2' }} rounded px-2">{{$banned_users_count}}</span>
            @endif
        </a>
        <a href="{{route('admin.users.email.unverified')}}" class="btn bg--gradi-8 border-0 mr-3 mb-3 py-2">
            @lang('Email Unverified')
            @if($email_unverified_users_count)
                <span class="menu-badge pill bg--black {{ app()->getLocale() == 'ar' ? 'mr-2' : 'ml-2' }} rounded px-2">{{$email_unverified_users_count}}</span>
            @endif
        </a>
        <a href="{{route('admin.users.sms.unverified')}}" class="btn bg--gradi-11 border-0 mr-3 mb-3 py-2">
            @lang('SMS Unverified')
            @if($sms_unverified_users_count)
                <span class="menu-badge pill bg--black {{ app()->getLocale() == 'ar' ? 'mr-2' : 'ml-2' }} rounded px-2">{{$sms_unverified_users_count}}</span>
            @endif
        </a>
        <a href="{{route('admin.users.with.balance')}}" class="btn bg--gradi-9 border-0 mr-3 mb-3 py-2">
            @lang('With Balance')
        </a>
        <a href="{{route('admin.users.email.all')}}" class="btn bg--gradi-10 border-0 mr-3 mb-3 py-2">
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
                                <th scope="col">@lang('User')</th>
                                <th scope="col">@lang('Email-Phone')</th>
                                <th scope="col">@lang('Country')</th>
                                <th scope="col">@lang('Joined At')</th>
                                <th scope="col">@lang('Balance')</th>
                                <th scope="col">@lang('Action')</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($users as $user)
                                <tr>
                                    <td data-label="@lang('User')">
                                        <span class="font-weight-bold">{{$user->fullname}}</span>
                                        <br>
                                        <span class="small">
                                            <a href="{{ route('admin.users.detail', $user->id) }}"><span>@</span>{{ $user->username }}</a>
                                        </span>
                                    </td>


                                    <td data-label="@lang('Email-Phone')">
                                        {{ $user->email }}<br>{{ $user->mobile }}
                                    </td>
                                    <td data-label="@lang('Country')">
                                        <span class="font-weight-bold" data-toggle="tooltip"
                                              data-original-title="{{ @$user->address->country }}">{{ $user->country_code }}</span>
                                    </td>


                                    <td data-label="@lang('Joined At')">
                                        {{ showDateTime($user->created_at) }} <br> {{ diffForHumans($user->created_at) }}
                                    </td>


                                    <td data-label="@lang('Balance')">
                                        <span class="font-weight-bold">

                                            {{ $general->cur_sym }}{{ showAmount($user->balance) }}
                                        </span>
                                    </td>


                                    <td data-label="@lang('Action')">
                                        <a href="{{ route('admin.users.detail', $user->id) }}" class="icon-btn" data-toggle="tooltip" title=""
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
                <div class="card-footer py-4">
                    {{ paginateLinks($users) }}
                </div>
            </div>
        </div>


    </div>
@endsection

@push('breadcrumb-plugins')
    <div class="d-flex flex-wrap justify-content-sm-end header-search-wrapper">

        <form action="{{ route('admin.users.search', $scope ?? str_replace('admin.users.', '', request()->route()->getName())) }}" method="GET"
              class="form-inline float-sm-right bg--white">
            <div class="input-group has_append">
                <input type="text" name="search" class="form-control" placeholder="@lang('Username or email')" value="{{ $search ?? '' }}">
                <div class="input-group-append">
                    <button class="btn btn--primary" type="submit"><i class="fa fa-search"></i></button>
                </div>
            </div>
        </form>

        @php
            $segments = request()->segments();
            $model_type = end($segments);
            if (request()->has('interest') && request()->interest != null) {
                   $interest = request()->interest;
            } else {
              $interest = null;
            }
        @endphp
        <div class="dropdown ml-4">
            <button class="btn btn--primary box--shadow1 btn-lg dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false">
                @lang('Export')
            </button>
            <div class="dropdown-menu text-center">
                <a class="dropdown-item"
                   href="{{ route('admin.export.users', ['model_type' => $model_type, 'file_type' => 'excel', 'interest' => $interest]) }}">@lang('Excel')</a>
                <a class="dropdown-item"
                   href="{{ route('admin.export.users', ['model_type' => $model_type, 'file_type' => 'csv', 'interest' => $interest]) }}">@lang('Csv')</a>
            </div>
        </div>
    </div>
@endpush
