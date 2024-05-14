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
                        <h1 class="m-0">{{ __('Тесты') }}</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('home', ['member_id' => $auth->member_id]) }}">{{ __('Главная') }}</a></li>
                            <li class="breadcrumb-item active">{{ __('Тесты') }}</li>
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
                                <a href="{{ route('tests.create', ['member_id' => $auth->member_id]) }}" class="btn btn-app ml-0 mb-0">
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
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th class="text-center">#</th>
                                            <th class="text-center">{{ __('Название') }}</th>
                                            <th class="text-center">{{ __('Время выполнения') }}</th>
                                            <th class="text-center">{{ __('Максимальный бал') }}</th>
                                            <th class="text-center">{{ __('Проходной бал') }}</th>
                                            <th class="text-center">{{ __('Пропуск вопроса') }}</th>
                                            <th class="text-center">{{ __('Ранжирование') }}</th>
                                            <th class="text-center">{{ __('Кол-во вопросов') }}</th>
                                            <th class="text-center">{{ __('Действия') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    @if(!empty($tests))
                                        @foreach($tests as $test)
                                            <tr>
                                                <td class="text-center align-middle">{{ $test->id }}</td>
                                                <td class="text-center align-middle">{{ $test->title }}</td>
                                                <td class="text-center align-middle">{{ floor($test->maximum_time / 60) }}:{{ $test->maximum_time % 60 }}</td>
                                                <td class="text-center align-middle">{{ $test->maximum_score }}</td>
                                                <td class="text-center align-middle">{{ $test->passing_score }}</td>
                                                <td class="text-center align-middle">{{ $test->skipping ? __('Да') : __('Нет') }}</td>
                                                <td class="text-center align-middle">{{ $test->ranging ? __('Да') : __('Нет') }}</td>
                                                <td class="text-center align-middle">{{ $test->questions_count }}</td>
                                                <td class="text-center align-middle">
                                                    <div class="btn-group">
                                                        <a href="{{ route('tests.show', ['member_id' => $auth->member_id, 'test' => $test->id]) }}">
                                                            <button type="button" class="btn btn-warning">
                                                                <i class="fas fa-edit"></i>
                                                            </button>
                                                        </a>
                                                        <a href="{{ route('tests.destroy', ['member_id' => $auth->member_id, 'test' => $test->id]) }}">
                                                            <button type="button" class="btn btn-danger ml-2">
                                                                <i class="fas fa-trash-alt"></i>
                                                            </button>
                                                        </a>
{{--                                                        <button type="button" data-test-id="{{ $test->id }}" data-test-title="{{ $test->title }}" data-toggle="modal" data-target="#modalDeleteTest" class="btn openModalDeleteTest btn-danger">--}}
{{--                                                            <i class="fas fa-trash-alt"></i>--}}
{{--                                                        </button>--}}
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
                            @isset($tests)
                                <div class="card-footer">
                                    {{ $tests->links('pagination::bootstrap-4') }}
                                </div>
                            @endisset
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <aside class="control-sidebar control-sidebar-dark"></aside>

    <div class="modal fade" id="modalDeleteQuestion">
        <div class="modal-dialog">
            <div class="modal-content bg-danger">
                <div class="modal-header">
                    <h4 class="modal-title">{{ __('Вы уверены что хотите удалить вопрос?')  }}</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>{{ __('Данным действием вы удалите тест') }} - <b id="questionTitle"></b></p>
                    <p>{{ __('Удаление теста будет разрешено только в том случае, если выбранный тест не используется ни в одной из аттестаций. Это условие будет проверено на следующем этапе удаления вопроса.') }}</p>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-outline-light" data-dismiss="modal">{{ __('Закрыть') }}</button>
                    <button type="button" class="btn btn-outline-light">{{ __('Да, удалить!') }}</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        $('.openModalDeleteQuestion').on('click', function () {
            var questionTitle = $(this).attr('data-question-title');
            $('#questionTitle').html(questionTitle);
        })
    </script>

    @include('certification.chunk.footer')
</div>
</body>
</html>
