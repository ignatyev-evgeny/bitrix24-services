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
                        <h1 class="m-0">{{ __('Подразделения') }}</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('home', ['auth_id' => $auth->auth_id]) }}">{{ __('Главная') }}</a></li>
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
                                <table id="myTable" class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th class="text-center">#</th>
                                            <th class="text-center">Bitrix ID</th>
                                            <th class="text-center">{{ __('Название') }}</th>
                                            <th class="text-center">{{ __('Руководитель') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($departments as $department)
                                            <tr>
                                                <td class="text-center align-middle">{{ $department->id }}</td>
                                                <td class="text-center align-middle">{{ $department->bitrix_id }}</td>
                                                <td class="align-middle">{{ $department->name }}</td>
                                                <td class="text-center align-middle">
                                                    <select class="form-control select2" id="select2-{{ $department->id }}" multiple name="tags[]" style="width: 100%;">
                                                        @foreach($managers as $manager)
                                                            <option @if($manager->checkManagerInDepartment($department->id, $manager->id)) selected @endif value="{{ $manager->id }}">{{ $manager->name }} | {{ $manager->email ?? 'NaN' }}</option>
                                                        @endforeach
                                                    </select>
                                                </td>
                                            </tr>

                                            <script>

                                                $('#select2-{{ $department->id }}').on('select2:select', function (event) {
                                                    var userID = event.params.data.id;
                                                    var departmentID = {{ $department->id }};
                                                    updateDepartmentManagers('select', userID, departmentID);
                                                });

                                                $('#select2-{{ $department->id }}').on('select2:unselect', function (event) {
                                                    var userID = event.params.data.id;
                                                    var departmentID = {{ $department->id }};
                                                    updateDepartmentManagers('unselect', userID, departmentID);
                                                });

                                                function updateDepartmentManagers(eventName, userID, departmentID) {

                                                    var request = $.ajax({
                                                        url: "{{ route('departments.setManagers', ['auth_id' => $auth->auth_id]) }}",
                                                        type: 'POST',
                                                        dataType: 'JSON',
                                                        data: {departmentId: departmentID, userId: userID, event: eventName},
                                                    })

                                                    request.done(function( data ) {
                                                        toastr.success(data.message);
                                                    });

                                                    request.fail(function( data ) {
                                                        toastr.error(data.responseJSON.message);
                                                    });
                                                }

                                            </script>

                                        @endforeach
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

    <script>
        $(document).ready(function() {
            $(".select2").select2()
        })
    </script>
    @include('certification.chunk.footer')
</div>
</body>
</html>
