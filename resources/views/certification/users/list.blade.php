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
                        <a href="{{ route('home', ['member_id' => $auth->member_id]) }}" class="nav-link">{{ __('Главная') }}</a>
                    </li>
                </ul>
            </nav>
            @include('certification.chunk.sidebar')
            <div class="content-wrapper">
                <div class="content-header">
                    <div class="container-fluid">
                        <div class="row mb-2">
                            <div class="col-sm-6">
                                <h1 class="m-0">{{ __('Сотрудники') }}</h1>
                            </div>
                            <div class="col-sm-6">
                                <ol class="breadcrumb float-sm-right">
                                    <li class="breadcrumb-item"><a href="{{ route('home', ['member_id' => $auth->member_id]) }}">{{ __('Главная') }}</a></li>
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
                                                    <th class="text-center">{{ __('Email') }}</th>
                                                    <th class="text-center">{{ __('Поддержка') }}</th>
                                                    <th class="text-center">{{ __('Подразделения') }}</th>
                                                    <th class="text-center">{{ __('Язык') }}</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @each('certification.users.list-table-element', $users, 'user')
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="card-footer">
                                        {{ $users->links('pagination::bootstrap-4') }}
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
        <script>
            $("input[data-switch]").each(function(){
                $(this).bootstrapSwitch('state');
            })
            function changeActiveStatus(userID, currentStatus) {
                $.post("{{ route('user.updateActive', ['member_id' => $auth->member_id]) }}", {
                    userID: userID,
                    currentStatus: currentStatus
                })
                .done(function(data) {
                    console.log(data);
                })
                .fail(function(data) {
                    console.log(data);
                })
            }
            function changeIsSupport(userID, isSupportStatus) {
                $.post("{{ route('user.updateIsSupport', ['member_id' => $auth->member_id]) }}", {
                    userID: userID,
                    isSupportStatus: isSupportStatus
                })
                    .done(function(data) {
                        console.log(data);
                    })
                    .fail(function(data) {
                        console.log(data);
                    })
            }
        </script>
    </body>
</html>
