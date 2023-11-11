<!-- navbar-wrapper start -->
<nav class="navbar-wrapper">
    <form class="navbar-search" onsubmit="return false;">
        <button type="submit" class="navbar-search__btn">
            <i class="las la-search"></i>
        </button>
        <input type="search" name="navbar-search__field" id="navbar-search__field" placeholder="@lang('Search')">
        <button type="button" class="navbar-search__close"><i class="las la-times"></i></button>

        <div id="navbar_search_result_area">
            <ul class="navbar_search_result"></ul>
        </div>
    </form>

    <div class="navbar__right">
        <ul class="navbar__action-list">
            <li class="dropdown">
                <button type="button" class="" data-toggle="dropdown" data-display="static" aria-haspopup="true" aria-expanded="false">
                    <span class="navbar-user">

                        <span class="navbar-user__info">
                            @php
                                $lang = \App\Models\Language::where('code', session('lang'))->first();
                                if ($lang) {
                                            $langName = __($lang->name);
                                } else {
                                   $langName = __('Languages');
                                }
                            @endphp
                            <span class="navbar-user__name">{{$langName}}</span>
                        </span>
                        <span class="icon"><i class="las la-chevron-circle-down"></i></span>
                    </span>
                </button>
                <div class="dropdown-menu dropdown-menu--sm p-0 border-0 box--shadow1 dropdown-menu-right">
                    @foreach(\App\Models\Language::all() as $item)
                        <a href="{{ route('home') . '/change/' . $item->code  }}"
                           class="dropdown-menu__item d-flex justify-content-center px-3 py-2">
                            <span class="dropdown-menu__caption">{{ __($item->name) }}</span>
                        </a>
                    @endforeach
                </div>
            </li>
            <li>
                <button class="res-sidebar-open-btn"><i class="las la-bars"></i></button>
                <button type="button" class="fullscreen-btn">
                    <i class="fullscreen-open las la-compress" onclick="openFullscreen();"></i>
                    <i class="fullscreen-close las la-compress-arrows-alt" onclick="closeFullscreen();"></i>
                </button>
            </li>
        </ul>

    </div>
</nav>
<!-- navbar-wrapper end -->
