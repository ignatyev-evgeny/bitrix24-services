<!DOCTYPE html>
<html lang="ru">
@include('certification.chunk.head')
@include('certification.chunk.script')
<body class="hold-transition sidebar-mini">

<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.js"></script>

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
                        <h1 class="m-0">{{ __('Новый вопрос') }}</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('home', ['auth_id' => $auth->auth_id]) }}">{{ __('Главная') }}</a></li>
                            <li class="breadcrumb-item active">{{ __('Новый вопрос') }}</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
        <div class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                        <form method="POST" id="newQuestionForm" multiple="">
                            <input type="hidden" name="portal" value="{{ $auth->portal }}">
                            <div class="card card-info">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-6">
                                            <div class="row">
                                                <div class="col-8">
                                                    <div class="form-group">
                                                        <label>{{ __('Наименование вопроса') }}</label>
                                                        <input type="text" class="form-control" name="title">
                                                    </div>
                                                </div>
                                                <div class="col-4">
                                                    <div class="form-group">
                                                        <label>{{ __('Время на ответ') }}</label>
                                                        <div class="row">
                                                            <div class="col-5">
                                                                <input type="number" min="0" max="59" value="0" class="form-control" name="time_min">
                                                            </div>
                                                            <div class="col-1 text-center lh-38-px">:</div>
                                                            <div class="col-6">
                                                                <input type="number" min="0" max="59" value="0" class="form-control" name="time_sec">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-12">
                                                    <div class="form-group">
                                                        <label>{{ __('Используемые теги') }}</label>
                                                        <select class="form-control select2-tags" multiple name="tags[]" style="width: 100%;"></select>
                                                    </div>
                                                </div>
                                            </div>

                                        </div>
                                        <div class="col-6">
                                            <div class="form-group">
                                                <textarea id="questionText" name="text"></textarea>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mb-2">
                                        <div class="col-6">
                                            <button type="button" data-answer="0" class="btn btn-success addAnswer btn-block">{{ __('Добавить вариант ответа') }}</button>
                                        </div>
                                        <div class="col-6">
                                            <button type="button" class="btn btn-danger deleteAnswer disabled btn-block">{{ __('Удалить вариант ответа') }}</button>
                                        </div>
                                    </div>
                                    <div class="answersBlock">
                                        <div class="row">
                                            <div class="input-group col-12">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text"><input name="correctAnswer[0]" type="checkbox"></span>
                                                </div>
                                                <input type="text" name="answerText[0]" class="form-control">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-footer">

                                    <div class="alert alert-success alert-dismissible d-none alertSuccessBlock">
                                        <h5>{{ __('Успешно') }}</h5>
                                        {{ __('Вопрос был успешно создан') }}
                                    </div>

                                    <button type="button" class="btn btn-success w-100 btnSave">{{ __('Сохранить вопрос') }}</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <aside class="control-sidebar control-sidebar-dark"></aside>
    <script>
        $(document).ready(function() {

            $('#questionText').summernote({
                minHeight: 100,
            });

            $(".select2-tags").select2({
                tags: true,
                tokenSeparators: [',']
            })

            $('.addAnswer').on('click', function () {
                var answerCount = parseInt($(this).attr('data-answer'));
                var currentAnswer = answerCount + 1;
                $(this).attr('data-answer', currentAnswer);
                $('.answersBlock').append('<div class="row answer mt-2"><div class="input-group col-12"><div class="input-group-prepend"><span class="input-group-text"><input name="correctAnswer['+currentAnswer+']" type="checkbox"></span></div><input name="answerText['+currentAnswer+']" type="text" class="form-control"></div></div>');
                $('.deleteAnswer').removeClass('disabled');
            })

            $('.deleteAnswer').on('click', function () {
                var answerCount = parseInt($('.addAnswer').attr('data-answer'));
                var currentAnswer = answerCount - 1;
                $('.addAnswer').attr('data-answer', currentAnswer);
                $(".answer").last().remove();
                if($(".answer").length === 0) {
                    $('.deleteAnswer').addClass('disabled');
                    $('.addAnswer').attr('data-answer', 0);
                }
            })

            $('.btnSave').click(function() {

                $('.serializedTags').val($(".tagsinput").val());

                $(this).prop('disabled', true);

                var request = $.ajax({
                    url: "{{ route('questions.store', ['auth_id' => $auth->auth_id]) }}",
                    type: 'POST',
                    dataType: 'JSON',
                    data: $('form#newQuestionForm').serialize(),
                })

                request.done(function( data ) {
                    $('.btnSave').addClass('d-none');
                    $('.alertSuccessBlock').removeClass('d-none');
                    console.log(data.question.id);
                    $(location).prop('href', '/questions/show/'+data.question.id+'/{{ $auth->auth_id }}?redirect=true')
                });

                request.fail(function( data ) {
                    $('.alertSuccessBlock').addClass('d-none');
                    $('.btnSave').prop('disabled', false);
                    toastr.error(data.responseJSON.message);
                });

            });

        });
    </script>
    @include('certification.chunk.footer')
</div>
</body>
</html>
