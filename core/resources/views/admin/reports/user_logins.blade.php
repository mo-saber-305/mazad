@extends('admin.layouts.app')

@section('panel')
    <div class="row">

        <div class="col-lg-12">
            <div class="card b-radius--10 ">
                <div class="card-body p-0">

                    <div class="table-responsive--sm table-responsive">
                        <table class="table table--light style--two">
                            <thead>
                            <tr>
                                <th>@lang('User')</th>
                                <th>@lang('Login at')</th>
                                <th>@lang('IP')</th>
                                <th>@lang('Location')</th>
                                <th>@lang('Browser | OS')</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($login_logs as $log)
                                <tr>
                                    <td data-label="@lang('User')">
                                        <span class="font-weight-bold">{{ @$log->user->fullname }}</span>
                                        <br>
                                        <span class="small"> <a
                                                    href="{{ route('admin.users.detail', $log->user_id) }}"><span>@</span>{{ @$log->user->username }}</a>
                                        </span>
                                    </td>
                                    <td data-label="@lang('Login at')">{{showDateTime($log->created_at) }} <br> {{diffForHumans($log->created_at) }}</td>
                                    <td data-label="@lang('IP')">
                                        <span class="font-weight-bold">
                                            <a href="{{route('admin.report.user.login.ipHistory',[$log->user_ip])}}">{{ $log->user_ip }}</a>
                                        </span>
                                    </td>
                                    <td data-label="@lang('Location')">{{ __($log->city) }} <br> {{ __($log->country) }}</td>
                                    <td data-label="@lang('Browser | OS')">{{ __($log->browser) }} <br> {{ __($log->os) }}</td>
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
                    {{ paginateLinks($login_logs) }}
                </div>
            </div><!-- card end -->
        </div>


    </div>
@endsection



@push('breadcrumb-plugins')
    <div class="d-flex flex-wrap justify-content-sm-end ">
        @if(request()->routeIs('admin.report.user.login.history'))
            <form action="{{ route('admin.report.user.login.history') }}" method="GET" class="form-inline float-sm-right bg--white">
                <div class="input-group has_append">
                    <input type="text" name="search" class="form-control" placeholder="@lang('Search Username')" value="{{ $search ?? '' }}">
                    <div class="input-group-append">
                        <button class="btn btn--primary" type="submit"><i class="fa fa-search"></i></button>
                    </div>
                </div>
            </form>
        @endif
        <div class="dropdown ml-3">
            <button class="btn btn--primary box--shadow1 btn-lg dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false">
                @lang('Export')
            </button>
            <div class="dropdown-menu text-center">
                <a class="dropdown-item"
                   href="{{ route('admin.export.user-logins-report', ['file_type' => 'excel']) }}">@lang('Excel')</a>
                <a class="dropdown-item"
                   href="{{ route('admin.export.user-logins-report', ['file_type' => 'csv']) }}">@lang('Csv')</a>
            </div>
        </div>
    </div>
@endpush
@if(request()->routeIs('admin.report.user.login.ipHistory'))
    @push('breadcrumb-plugins')
        <a href="https://www.ip2location.com/{{ $ip }}" target="_blank" class="btn btn--primary">@lang('Lookup IP') {{ $ip }}</a>
    @endpush
@endif