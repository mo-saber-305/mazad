<!-- navbar-wrapper start -->
<nav class="navbar-wrapper">
    <form class="navbar-search" onsubmit="return false;">
        <button type="submit" class="navbar-search__btn">
            <i class="las la-search"></i>
        </button>
        <input type="search" name="navbar-search__field" id="navbar-search__field"
               placeholder="@lang('Search')">
        <button type="button" class="navbar-search__close"><i class="las la-times"></i></button>

        <div id="navbar_search_result_area">
            <ul class="navbar_search_result"></ul>
        </div>
    </form>

    <div class="navbar__left">
        <button class="res-sidebar-open-btn"><i class="las la-bars"></i></button>
        <button type="button" class="fullscreen-btn">
            <i class="fullscreen-open las la-compress" onclick="openFullscreen();"></i>
            <i class="fullscreen-close las la-compress-arrows-alt" onclick="closeFullscreen();"></i>
        </button>
        <ul class="navbar__action-list">
            <li>
                <a href="{{route('admin.setting.custom.css')}}" data-toggle="tooltip" data-placement="top" title="@lang('Custom CSS')">
                    <i class="lab la-css3-alt"></i>
                </a>
            </li>

            {{--            <li>--}}
            {{--                <a href="{{route('admin.frontend.templates')}}" data-toggle="tooltip" data-placement="top" title="@lang('Manage Templates')">--}}
            {{--                    <i class="lab la-html5"></i>--}}
            {{--                </a>--}}
            {{--            </li>--}}

            <li>
                <a href="{{route('admin.frontend.manage.pages')}}" data-toggle="tooltip" data-placement="top" title="@lang('Manage Pages')">
                    <i class="la la-list"></i>
                </a>
            </li>

            <li class="dropdown">
                <span data-toggle="tooltip" data-placement="right" title="@lang('Manage Section')">
                    <button type="button" class="" data-toggle="dropdown" data-display="static" aria-haspopup="true" aria-expanded="false">
                        <i class="lab la-html5"></i>
                    </button>
                    <div class="dropdown-menu dropdown-menu--sm manage-sections p-0 border-0 box--shadow1 dropdown-menu-right">


                        @php
                            $lastSegment =  collect(request()->segments())->last();
                        @endphp
                        @foreach(getPageSections(true) as $k => $secs)
                            @if($secs['builder'])
                                <a href="{{ route('admin.frontend.sections',$k) }}"
                                   class="dropdown-menu__item d-flex align-items-center px-3 py-2">
                                    <i class="dropdown-menu__icon las la-dot-circle"></i>
                                    <span class="dropdown-menu__caption">{{__($secs['name'])}}</span>
                                </a>
                            @endif
                        @endforeach
                    </div>
                </span>
            </li>

            <li class="dropdown">
                <span data-toggle="tooltip" data-placement="right" title="@lang('SMS Manager')">
                    <button type="button" class="" data-toggle="dropdown" data-display="static" aria-haspopup="true" aria-expanded="false">
                        <i class="las la-mobile"></i>
                    </button>
                    <div class="dropdown-menu dropdown-menu--sm p-0 border-0 box--shadow1 dropdown-menu-right">

                        <a href="{{route('admin.sms.template.global')}}"
                           class="dropdown-menu__item d-flex align-items-center px-3 py-2">
                            <i class="dropdown-menu__icon las la-dot-circle"></i>
                            <span class="dropdown-menu__caption">@lang('Global Setting')</span>
                        </a>

                        <a href="{{route('admin.sms.templates.setting')}}"
                           class="dropdown-menu__item d-flex align-items-center px-3 py-2">
                            <i class="dropdown-menu__icon las la-dot-circle"></i>
                            <span class="dropdown-menu__caption">@lang('SMS Gateways')</span>
                        </a>

                        <a href="{{ route('admin.sms.template.index') }}"
                           class="dropdown-menu__item d-flex align-items-center px-3 py-2">
                            <i class="dropdown-menu__icon las la-dot-circle"></i>
                            <span class="dropdown-menu__caption">@lang('SMS Templates')</span>
                        </a>
                    </div>
                </span>
            </li>

            <li class="dropdown">
                <span data-toggle="tooltip" data-placement="right" title="@lang('Email Manager')">
                    <button type="button" class="" data-toggle="dropdown" data-display="static" aria-haspopup="true" aria-expanded="false">
                        <i class="la la-envelope-o"></i>
                    </button>
                    <div class="dropdown-menu dropdown-menu--sm p-0 border-0 box--shadow1 dropdown-menu-right">

                        <a href="{{route('admin.email.template.global')}}"
                           class="dropdown-menu__item d-flex align-items-center px-3 py-2">
                            <i class="dropdown-menu__icon las la-dot-circle"></i>
                            <span class="dropdown-menu__caption">@lang('Global Template')</span>
                        </a>

                        <a href="{{route('admin.email.template.index')}}"
                           class="dropdown-menu__item d-flex align-items-center px-3 py-2">
                            <i class="dropdown-menu__icon las la-dot-circle"></i>
                            <span class="dropdown-menu__caption">@lang('Email Templates')</span>
                        </a>

                        <a href="{{ route('admin.email.template.setting') }}"
                           class="dropdown-menu__item d-flex align-items-center px-3 py-2">
                            <i class="dropdown-menu__icon las la-dot-circle"></i>
                            <span class="dropdown-menu__caption">@lang('Email Configure')</span>
                        </a>
                    </div>
                </span>
            </li>

            <li>
                <a href="{{route('admin.setting.cookie')}}" data-toggle="tooltip" data-placement="top" title="@lang('GDPR Cookie')">
                    <i class="las la-cookie-bite"></i>
                </a>
            </li>

            <li>
                <a href="{{route('admin.setting.optimize')}}" data-toggle="tooltip" data-placement="top" title="@lang('Clear Cache')">
                    <i class="las la-broom"></i>
                </a>
            </li>
        </ul>
    </div>

    <div class="navbar__right">
        <ul class="navbar__action-list">
            <li>
                <button type="button" class="navbar-search__btn-open">
                    <i class="las la-search"></i>
                </button>
            </li>

            <li class="dropdown">
                <button type="button" class="" data-toggle="dropdown" data-display="static" aria-haspopup="true" aria-expanded="false">
                    <i class="las la-cog"></i>
                </button>
                <div class="dropdown-menu dropdown-menu--sm p-0 border-0 box--shadow1 dropdown-menu-right">
                    <a href="{{route('admin.setting.index')}}"
                       class="dropdown-menu__item d-flex align-items-center px-3 py-2">
                        <i class="dropdown-menu__icon las la-life-ring"></i>
                        <span class="dropdown-menu__caption">@lang('General Setting')</span>
                    </a>

                    <a href="{{route('admin.setting.logo.icon')}}"
                       class="dropdown-menu__item d-flex align-items-center px-3 py-2">
                        <i class="dropdown-menu__icon las la-store-alt"></i>
                        <span class="dropdown-menu__caption">@lang('Logo & Favicon')</span>
                    </a>

                    <a href="{{route('admin.merchant.profile')}}"
                       class="dropdown-menu__item d-flex align-items-center px-3 py-2">
                        <i class="dropdown-menu__icon las la-sign-out-alt"></i>
                        <span class="dropdown-menu__caption">@lang('Merchant Profile')</span>
                    </a>

                    <a href="{{route('admin.extensions.index')}}"
                       class="dropdown-menu__item d-flex align-items-center px-3 py-2">
                        <i class="dropdown-menu__icon las la-cogs"></i>
                        <span class="dropdown-menu__caption">@lang('Extensions')</span>
                    </a>

                    <a href="{{route('admin.language.manage')}}"
                       class="dropdown-menu__item d-flex align-items-center px-3 py-2">
                        <i class="dropdown-menu__icon las la-language"></i>
                        <span class="dropdown-menu__caption">@lang('Language')</span>
                    </a>

                    <a href="{{route('admin.seo')}}"
                       class="dropdown-menu__item d-flex align-items-center px-3 py-2">
                        <i class="dropdown-menu__icon las la-globe"></i>
                        <span class="dropdown-menu__caption">@lang('SEO Manager')</span>
                    </a>
                </div>
            </li>

            <li class="dropdown">
                <button type="button" class="primary--layer" data-toggle="dropdown" data-display="static" aria-haspopup="true" aria-expanded="false">
                    <i class="las la-bell text--primary"></i>
                    @if($adminNotifications->count() > 0)
                        <span class="pulse--primary"></span>
                    @endif
                </button>
                <div class="dropdown-menu dropdown-menu--md p-0 border-0 box--shadow1 dropdown-menu-right">
                    <div class="dropdown-menu__header">
                        <span class="caption">@lang('Notification')</span>
                        @if($adminNotifications->count() > 0)
                            <p>@lang('You have') {{ $adminNotifications->count() }} @lang('unread notification')</p>
                        @else
                            <p>@lang('No unread notification found')</p>
                        @endif
                    </div>
                    <div class="dropdown-menu__body">
                        @foreach($adminNotifications as $notification)
                            <a href="{{ route('admin.notification.read',$notification->id) }}" class="dropdown-menu__item">
                                <div class="navbar-notifi">
                                    <div class="navbar-notifi__left bg--green b-radius--rounded"><img
                                                src="{{ getImage(imagePath()['profile']['user']['path'].'/'.@$notification->user->image, null, true)}}"
                                                alt="@lang('Profile Image')"></div>
                                    <div class="navbar-notifi__right">
                                        <h6 class="notifi__title">{{ __($notification->title) }}</h6>
                                        <span class="time"><i class="far fa-clock"></i> {{ $notification->created_at->diffForHumans() }}</span>
                                    </div>
                                </div><!-- navbar-notifi end -->
                            </a>
                        @endforeach
                    </div>
                    <div class="dropdown-menu__footer">
                        <a href="{{ route('admin.notifications') }}" class="view-all-message">@lang('View all notification')</a>
                    </div>
                </div>
            </li>

            <li class="dropdown">
                <button type="button" class="" data-toggle="dropdown" data-display="static" aria-haspopup="true" aria-expanded="false">
                    <span class="navbar-user">
                        <span class="navbar-user__thumb"><img src="{{ getImage('assets/admin/images/profile/'. auth()->guard('admin')->user()->image) }}"
                                                              alt="image"></span>
                        <span class="navbar-user__info">
                            <span class="navbar-user__name">{{auth()->guard('admin')->user()->username}}</span>
                        </span>
                        <span class="icon"><i class="las la-chevron-circle-down"></i></span>
                    </span>
                </button>
                <div class="dropdown-menu dropdown-menu--sm p-0 border-0 box--shadow1 dropdown-menu-right">
                    <a href="{{ route('admin.profile') }}"
                       class="dropdown-menu__item d-flex align-items-center px-3 py-2">
                        <i class="dropdown-menu__icon las la-user-circle"></i>
                        <span class="dropdown-menu__caption">@lang('Profile')</span>
                    </a>

                    <a href="{{route('admin.password')}}"
                       class="dropdown-menu__item d-flex align-items-center px-3 py-2">
                        <i class="dropdown-menu__icon las la-key"></i>
                        <span class="dropdown-menu__caption">@lang('Password')</span>
                    </a>

                    <a href="{{ route('admin.logout') }}"
                       class="dropdown-menu__item d-flex align-items-center px-3 py-2">
                        <i class="dropdown-menu__icon las la-sign-out-alt"></i>
                        <span class="dropdown-menu__caption">@lang('Logout')</span>
                    </a>
                </div>
            </li>
        </ul>
    </div>
</nav>
<!-- navbar-wrapper end -->
