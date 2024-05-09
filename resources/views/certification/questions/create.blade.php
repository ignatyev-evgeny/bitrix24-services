<!DOCTYPE html>
<html lang="ru">
@include('certification.chunk.head')
@include('certification.chunk.script')
<body class="hold-transition sidebar-mini">

<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.js"></script>

<link href="{{ asset('/plugins/bootstrap-tagsinput/bootstrap-tagsinput.css') }}" rel="stylesheet">
<script src="{{ asset('/plugins/bootstrap-tagsinput/bootstrap-tagsinput.min.js') }}"></script>


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
                        <h1 class="m-0">{{ __('Новый вопрос') }}</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('home', ['member_id' => $auth->member_id]) }}">{{ __('Главная') }}</a></li>
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
                        <form method="POST" id="newQuestionForm" multiple="" action="{{ route('questions.store', ['member_id' => $auth->member_id]) }}">
                            <input type="hidden" name="portal" value="{{ $auth->portal }}">
                            <input type="hidden" class="serializedTags" name="serializedTags" value="">
                            <div class="card card-info">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-6">
                                            <div class="input-group mb-3">
                                                <input type="text" class="form-control bigInput" name="title" placeholder="{{ __('Наименование вопроса') }}">
                                            </div>
                                            <div class="form-group">
                                                <textarea id="questionText" name="text"></textarea>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="row">
                                                <div class="col-12">
                                                    <div class="input-group">
                                                        <input type="text" class="form-control tagsinput" data-role="tagsinput">
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
                                                    <div class="col-12">
                                                        <div class="input-group">
                                                            <div class="input-group-prepend">
                                                                <span class="input-group-text"><input name="correctAnswer[0]" type="checkbox"></span>
                                                            </div>
                                                            <input type="text" name="answerText[0]" class="form-control">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-footer">
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

            $('.bootstrap-tagsinput')
                .addClass('mb-3')
                .addClass('w-100')
                .css('min-height', '38px')
                .find('input')
                    .css('height', '30px');

            $('#questionText').summernote({
                height: 300,
            });

            $('.addAnswer').on('click', function () {
                var answerCount = parseInt($(this).attr('data-answer'));
                var currentAnswer = answerCount + 1;
                $(this).attr('data-answer', currentAnswer);
                $('.answersBlock').append('<div class="row answer col-12 mt-2"><div class="input-group"><div class="input-group-prepend"><span class="input-group-text"><input name="correctAnswer['+currentAnswer+']" type="checkbox"></span></div><input name="answerText['+currentAnswer+']" type="text" class="form-control"></div></div>');
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

            $('.btnSave').on('click', function () {
                $('.serializedTags').val($(".tagsinput").val());
                $('#newQuestionForm').submit();
            })

        });
    </script>
    @include('certification.chunk.footer')
</div>
</body>
</html>
