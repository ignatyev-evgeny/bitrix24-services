<!DOCTYPE html>
<html lang="ru">
@include('services.bitrix.certification.chunk.head')
@include('services.bitrix.certification.chunk.script')
<body class="hold-transition sidebar-mini">
<div class="wrapper">
    <nav class="main-header navbar navbar-expand navbar-white navbar-light">
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
            </li>
            <li class="nav-item d-none d-sm-inline-block">
                <a href="{{ route('services.bitrix.certification.home', ['member_id' => Auth::user()->member_id]) }}" class="nav-link">{{ __('Главная') }}</a>
            </li>
        </ul>
    </nav>
    @include('services.bitrix.certification.chunk.sidebar')
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">{{ __('Сотрудники') }}</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('services.bitrix.certification.home', ['member_id' => Auth::user()->member_id]) }}">{{ __('Главная') }}</a></li>
                            <li class="breadcrumb-item active">{{ __('Сотрудники') }}</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <div class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-body">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th class="text-center">#</th>
                                            <th class="text-center">Bitrix ID</th>
                                            <th class="text-center">{{ __('Статус') }}</th>
                                            <th class="text-center">{{ __('Имя') }}</th>
                                            <th class="text-center">{{ __('Пол') }}</th>
                                            <th class="text-center">{{ __('День рождения') }}</th>
                                            <th class="text-center">{{ __('Email') }}</th>
                                            <th class="text-center">{{ __('Администратор') }}</th>
                                            <th class="text-center">{{ __('Руководитель') }}</th>
                                            <th class="text-center">{{ __('Подразделения') }}</th>
                                            <th class="text-center">{{ __('Дата авторизации') }}</th>
                                            <th class="text-center">{{ __('Дата регистрации') }}</th>
                                            <th class="text-center">{{ __('Язык') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @each('services.bitrix.certification.users.list-table-element', $users, 'user')
                                    </tbody>
                                </table>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <aside class="control-sidebar control-sidebar-dark">
    </aside>
    @include('services.bitrix.certification.chunk.footer')
</div>
</body>
</html>
