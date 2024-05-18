<!DOCTYPE html>
<html lang="ru">
@include('certification.chunk.head')
@include('certification.chunk.script')
<body class="hold-transition sidebar-mini">
<div class="wrapper">
    <nav class="main-header navbar navbar-expand navbar-white navbar-light">
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
            </li>
            <li class="nav-item d-none d-sm-inline-block">
                <a href="{{ route('home', ['auth_id' => $auth->auth_id]) }}" class="nav-link">{{ __('Главная') }}</a>
            </li>
        </ul>
        <ul class="navbar-nav ml-auto">
            <li class="nav-item">
                <a class="nav-link" target="_blank" href="{{ config('app.url')."/home/".$auth->auth_id }}">
                    <i class="fas fa-expand-arrows-alt"></i>
                </a>
            </li>
        </ul>
    </nav>
    @include('certification.chunk.sidebar')
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">{{ __('Группы пользователей') }}</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('home', ['auth_id' => $auth->auth_id]) }}">{{ __('Главная') }}</a></li>
                            <li class="breadcrumb-item active">{{ __('Группы пользователей') }}</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <div class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <a href="{{ route('users.groups.create', ['auth_id' => $auth->auth_id]) }}" class="btn btn-app ml-0 mb-0">
                                    <i class="fas fa-plus"></i> {{ __('Создать') }}
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-body">
                                <table @if(count($usersGroups) != 0) id="myTable" @endif class="table table-bordered">
                                    <thead>
                                    <tr>
                                        <th class="text-center">ID</th>
                                        <th class="text-center">{{ __('Название') }}</th>
                                        <th class="text-center">{{ __('Сотрудники') }}</th>
                                        <th class="text-center">{{ __('Действия') }}</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @if(count($usersGroups) != 0)
                                        @foreach($usersGroups as $userGroup)
                                            <tr class="tr-{{ $userGroup->id }}">
                                                <td class="text-center align-middle">{{ $userGroup->id }}</td>
                                                <td class="align-middle">{{ $userGroup->title }}</td>
                                                <td class="align-middle">{!! $userGroup->managers_format !!}</td>
                                                <td class="text-center align-middle">
                                                    <div class="btn-group">
                                                        <a href="{{ route('users.groups.show', ['auth_id' => $auth->auth_id, 'group' => $userGroup->id]) }}">
                                                            <button type="button" class="btn btn-warning">
                                                                <i class="fas fa-edit"></i>
                                                            </button>
                                                        </a>
                                                        <button type="button" data-knowledge-id="{{ $userGroup->id }}" data-knowledge-title="{{ $userGroup->title }}" data-toggle="modal" data-target="#modalDeleteUsersGroup" class="btn openModalDeleteUsersGroup btn-danger ml-2">
                                                            <i class="fas fa-trash-alt"></i>
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td class="text-center align-middle" colspan="20">
                                                <b>{{ __('Нет данных для отображения') }}</b>
                                            </td>
                                        </tr>
                                    @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <aside class="control-sidebar control-sidebar-dark"></aside>
    @include('certification.chunk.footer')
</div>
</body>
</html>
