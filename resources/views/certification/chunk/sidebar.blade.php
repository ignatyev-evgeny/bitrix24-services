<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <div class="sidebar">
        <div class="user-panel mt-3 pb-3 mb-3 d-flex">
            <div class="image">
                <img src="{!! $auth->photo !!}" class="img-circle elevation-2" alt="{!! $auth->name !!}">
            </div>
            <div class="info">
                <a href="#" class="d-block">{!! $auth->name !!}</a>
            </div>
        </div>
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                <li class="nav-item">
                    <a href="{{ route('home', ['member_id' => $auth->member_id]) }}" class="nav-link @if(Route::getCurrentRoute()->getName() == 'home') active @endif">
                        <i class="nav-icon fas fa-chart-pie"></i>
                        <p>{{ __('Главная') }}</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('get.departments', ['member_id' => $auth->member_id]) }}" class="nav-link @if(Route::getCurrentRoute()->getName() == 'get.departments') active @endif">
                        <i class="nav-icon fas fa-users-cog"></i>
                        <p>{{ __('Подразделения') }}</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('get.users', ['member_id' => $auth->member_id]) }}" class="nav-link @if(Route::getCurrentRoute()->getName() == 'get.users') active @endif">
                        <i class="nav-icon fas fa-users"></i>
                        <p>{{ __('Сотрудники') }}</p>
                    </a>
                </li>
{{--                <li class="nav-item">--}}
{{--                    <a href="{{ route('get.tests', ['member_id' => $auth->member_id]) }}" class="nav-link @if(Route::getCurrentRoute()->getName() == 'get.tests') active @endif">--}}
{{--                        <i class="nav-icon fas fa-edit"></i>--}}
{{--                        <p>{{ __('Тесты') }}</p>--}}
{{--                    </a>--}}
{{--                </li>--}}
{{--                <li class="nav-item">--}}
{{--                    <a href="{{ route('get.сertifications', ['member_id' => $auth->member_id]) }}" class="nav-link @if(Route::getCurrentRoute()->getName() == 'get.сertifications') active @endif">--}}
{{--                        <i class="nav-icon fas fa-stamp"></i>--}}
{{--                        <p>{{ __('Аттестации') }}</p>--}}
{{--                    </a>--}}
{{--                </li>--}}
                <li class="nav-item">
                    <a href="{{ route('questions.list', ['member_id' => $auth->member_id]) }}" class="nav-link @if(in_array(Route::getCurrentRoute()->getName(), ['questions.list', 'questions.create', 'questions.show'])) active @endif">
                        <i class="nav-icon fas fa-question"></i>
                        <p>{{ __('Вопросы') }}</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('tests.list', ['member_id' => $auth->member_id]) }}" class="nav-link @if(in_array(Route::getCurrentRoute()->getName(), ['tests.list', 'tests.create', 'tests.show'])) active @endif">
                        <i class="nav-icon fas fa-spell-check"></i>
                        <p>{{ __('Тесты') }}</p>
                    </a>
                </li>
            </ul>
        </nav>
    </div>
</aside>
