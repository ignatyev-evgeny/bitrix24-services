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
                        <h1 class="m-0">{{ __('Вопросы') }}</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('home', ['member_id' => $auth->member_id]) }}">{{ __('Главная') }}</a></li>
                            <li class="breadcrumb-item active">{{ __('Вопросы') }}</li>
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
                                <a href="{{ route('questions.create', ['member_id' => $auth->member_id]) }}" class="btn btn-app ml-0 mb-0">
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
                                            <th class="text-center">{{ __('Время на ответ') }}</th>
                                            <th class="text-center">{{ __('Теги') }}</th>
                                            <th class="text-center">{{ __('Управление') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if(!empty($questions))
                                            @foreach($questions as $question)
                                                <tr class="tr-{{ $question->id }}">
                                                    <td class="text-center align-middle">{{ $question->id }}</td>
                                                    <td class="text-center align-middle">{{ $question->title }}</td>
                                                    <td class="text-center align-middle">{{ $question->format_time }}</td>
                                                    <td class="text-center align-middle">
                                                        @isset($question->tags)
                                                            @foreach($question->tags as $tag)
                                                                <small class="badge badge-success">{{ $tag }}</small>
                                                            @endforeach
                                                        @else
                                                            {{ __('Не указаны') }}
                                                        @endisset
                                                    </td>
                                                    <td class="text-center align-middle">
                                                        <div class="btn-group">
                                                            <a href="{{ route('questions.show', ['member_id' => $auth->member_id, 'question' => $question->id]) }}">
                                                                <button type="button" class="btn btn-warning">
                                                                    <i class="fas fa-edit"></i>
                                                                </button>
                                                            </a>
                                                            <button type="button" data-question-id="{{ $question->id }}" data-question-title="{{ $question->title }}" data-toggle="modal" data-target="#modalDeleteQuestion" class="btn openModalDeleteQuestion btn-danger">
                                                                <i class="fas fa-trash-alt"></i>
                                                            </button>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @else
                                            <tr>
                                                <td class="text-center align-middle" colspan="4">
                                                    <b>{{ __('Нет данных для отображения') }}</b>
                                                </td>
                                            </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                            <div class="card-footer">
                                {{ $questions->links('pagination::bootstrap-4') }}
                            </div>
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
                    <h5 class="modal-title">{{ __('Вы уверены что хотите удалить вопрос?')  }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>{{ __('Данным действием вы удалите вопрос') }} - <b id="questionTitle"></b></p>
                    <p>{{ __('Удаление вопросы будет разрешено только в том случае, если выбранный вопрос не используется ни в одном из тестов. Это условие будет проверено на следующем этапе удаления вопроса.') }}</p>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-outline-light closeDeleteModal" data-dismiss="modal">{{ __('Закрыть') }}</button>
                    <button type="button" data-question-id="" class="btn btn-outline-light confirmDeleteQuestion">{{ __('Да, удалить!') }}</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        $('.openModalDeleteQuestion').on('click', function () {
            var questionTitle = $(this).attr('data-question-title');
            var questionID = $(this).attr('data-question-id');
            $('#questionTitle').html(questionTitle);
            $('.confirmDeleteQuestion').attr('data-question-id', questionID);
        })

        $('.confirmDeleteQuestion').on('click', function () {
            var questionID = $(this).attr('data-question-id');
            $(this).prop('disabled', true);

            var request = $.ajax({
                url: "/questions/destroy/"+questionID+"/{{ $auth->member_id }}",
                type: 'POST',
                dataType: 'JSON',
            })

            request.done(function( data ) {
                $('.confirmDeleteQuestion').prop('disabled', false);
                $('.closeDeleteModal').click();
                $('.tr-'+questionID).remove();
                toastr.success(data.message);
            });

            request.fail(function( data ) {
                $('.confirmDeleteQuestion').prop('disabled', false);
                $('.closeDeleteModal').click();
                toastr.error(data.responseJSON.message);
            });

        })

    </script>

    @include('certification.chunk.footer')
</div>
</body>
</html>
