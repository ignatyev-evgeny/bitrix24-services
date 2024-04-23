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
                        <h1 class="m-0">{{ __('Подразделения') }}</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('home', ['member_id' => $auth->member_id]) }}">{{ __('Главная') }}</a></li>
                            <li class="breadcrumb-item active">{{ __('Подразделения') }}</li>
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
                                            <th class="text-center">{{ __('Название') }}</th>
                                            <th class="text-center">{{ __('Руководитель') }}</th>
                                            <th class="text-center">{{ __('Управление') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @each('certification.departments.list-table-element', $departments, 'department')
                                    </tbody>
                                </table>
                            </div>
                            <div class="card-footer">
                                {{ $departments->links('pagination::bootstrap-4') }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <aside class="control-sidebar control-sidebar-dark">
    </aside>

    @foreach($departments as $department)
        <div class="modal fade" id="modal-managers-department-{{ $department->id }}">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">{{ $department->name }}</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-12">
                                <form id="setManagers-{{ $department->id }}">
                                    <div class="form-group">
                                        <input type="hidden" name="id" value="{{ $department->id }}">
                                        <input type="hidden" name="bitrix_id" value="{{ $department->bitrix_id }}">
                                        <input type="hidden" name="portal_id" value="{{ $auth->portal }}">
                                        <select multiple="" name="managers[]" class="form-control" style="height: 300px;">
                                            @foreach($managers as $manager)
                                                <option @if($manager->checkManagerInDepartment($department->id, $manager->id)) selected @endif value="{{ $manager->id }}">{{ $manager->id }} | {{ $manager->bitrix_id }} | {{ $manager->name }} | {{ $manager->email ?? 'NaN' }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer justify-content-between">
                        <button type="button" class="btn btn-default" data-dismiss="modal">{{ __('Закрыть') }}</button>
                        <button type="button" class="btn btn-primary setManagers-{{ $department->id }}">{{ __('Сохранить') }}</button>
                    </div>
                </div>
            </div>
        </div>

        <script>
            $('.setManagers-{{ $department->id }}').on('click', function () {
                $.post("{{ route('departments.setManagers', ['member_id' => $auth->member_id]) }}", $('#setManagers-{{ $department->id }}').serialize())
                    .done(function(data) {
                        location.reload();
                    })
                    .fail(function(data) {
                        console.log(data);
                    })
            });
        </script>
    @endforeach

    @include('certification.chunk.footer')
</div>
</body>
</html>
