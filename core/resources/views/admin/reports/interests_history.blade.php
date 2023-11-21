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
                                <th>@lang('Name')</th>
                                <th>@lang('Status')</th>
                                <th>@lang('Users')</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($logs as $log)
                                <tr>
                                    <td data-label="@lang('Name')">
                                        <span class="font-weight-bold">{{ @$log->name }}</span>
                                    </td>
                                    <td data-label="@lang('Status')">
                                        @if($log->status == 1)
                                            <span class="text--small badge font-weight-normal badge--success">@lang('Active')</span>
                                        @else
                                            <span class="text--small badge font-weight-normal badge--warning">@lang('Inactive')</span>
                                        @endif
                                    </td>
                                    <td data-label="@lang('Users')">
                                        <a href="{{ route('admin.users.all',['interest' =>  $log->id]) }}" class="btn btn--primary">{{ $log->users_count }}</a>
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
                    {{ paginateLinks($logs) }}
                </div>
            </div><!-- card end -->
        </div>


    </div>
@endsection



@push('breadcrumb-plugins')
    <div class="d-flex flex-wrap justify-content-sm-end ">
        <div class="dropdown ml-3">
            <button class="btn btn--primary box--shadow1 btn-lg dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false">
                @lang('Export')
            </button>
            <div class="dropdown-menu text-center">
                <a class="dropdown-item"
                   href="{{ route('admin.export.interests', ['file_type' => 'excel']) }}">@lang('Excel')</a>
                <a class="dropdown-item"
                   href="{{ route('admin.export.interests', ['file_type' => 'csv']) }}">@lang('Csv')</a>
            </div>
        </div>
    </div>
@endpush
@if(request()->routeIs('admin.report.user.login.ipHistory'))
    @push('breadcrumb-plugins')
        <a href="https://www.ip2location.com/{{ $ip }}" target="_blank" class="btn btn--primary">@lang('Lookup IP') {{ $ip }}</a>
    @endpush
@endif