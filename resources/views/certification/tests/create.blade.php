<!DOCTYPE html>
<html lang="ru">
@include('certification.chunk.head')
@include('certification.chunk.script')
<body class="hold-transition sidebar-mini">

<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.js"></script>
<script src="{{ asset('/plugins/select2/js/select2.full.min.js') }}"></script>
<link rel="stylesheet" href="{{ asset('/plugins/select2/css/select2.min.css') }}">

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
                        <h1 class="m-0">{{ __('Новый тест') }}</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('home', ['member_id' => $auth->member_id]) }}">{{ __('Главная') }}</a></li>
                            <li class="breadcrumb-item active">{{ __('Новый тест') }}</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
        <div class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                        <form method="POST" id="newTestForm" multiple="">
                            <input type="hidden" name="portal" value="{{ $auth->portal }}">
                            <div class="card card-info">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-6">
                                            <div class="form-group">
                                                <label>{{ __('Наименование теста') }}</label>
                                                <input type="text" class="form-control" name="title">
                                            </div>
                                            <div class="row">
                                                <div class="col-6 text-center">
                                                    <div class="form-group">
                                                        <div class="custom-control custom-switch">
                                                            <input type="checkbox" class="custom-control-input skippingQuestion" name="skipping" id="skippingQuestion">
                                                            <label class="custom-control-label" for="skippingQuestion">{{ __('Пропуск вопросов') }}</label>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-6 text-center">
                                                    <div class="form-group">
                                                        <div class="custom-control custom-switch">
                                                            <input type="checkbox" class="custom-control-input rangingQuestion" name="ranging" id="rangingQuestion">
                                                            <label class="custom-control-label" for="rangingQuestion">{{ __('Ранжирование вопросов') }}</label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="form-group col-4">
                                                    <label>{{ __('Время теста (ММ:СС)') }}</label>
                                                    <div class="row">
                                                        <div class="col-5">
                                                            <input type="number" min="0" max="59" value="0" class="form-control" name="maximum_time_min">
                                                        </div>
                                                        <div class="col-1 text-center lh-38-px">:</div>
                                                        <div class="col-6">
                                                            <input type="number" min="0" max="59" value="0" class="form-control" name="maximum_time_sec">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="form-group col-4">
                                                    <label>{{ __('Максимальный балл') }}</label>
                                                    <input type="number" class="form-control" value="0" name="test_maximum_score" >
                                                </div>
                                                <div class="form-group col-4">
                                                    <label>{{ __('Проходной балл') }}</label>
                                                    <input type="number" class="form-control" value="0" name="test_passing_score" >
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label>{{ __('Описание теста') }}</label>
                                                <textarea id="testText" name="descriptions"></textarea>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="row mb-2">
                                                <div class="col-6">
                                                    <button type="button" data-question="0" class="btn btn-success addQuestion btn-block">{{ __('Добавить вопрос') }}</button>
                                                </div>
                                                <div class="col-6">
                                                    <button type="button" class="btn btn-danger deleteQuestion disabled btn-block">{{ __('Удалить последний вопрос') }}</button>
                                                </div>
                                            </div>
                                            <div class="questionsBlock mt-3">
                                                <div class="row">
                                                    <div class="form-group col-4">
                                                        <label>{{ __('Время вопроса (ММ:СС)') }}</label>
                                                        <div class="row">
                                                            <div class="col-5">
                                                                <input type="number" min="0" max="59" value="0" class="form-control question_time" name="question_maximum_time_min[0]">
                                                            </div>
                                                            <div class="col-1 text-center lh-38-px">:</div>
                                                            <div class="col-6">
                                                                <input type="number" min="0" max="59" value="0" class="form-control question_time" name="question_maximum_time_sec[0]">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="form-group col-2">
                                                        <label>{{ __('Оценка') }}</label>
                                                        <input type="number" name="question_score[0]" value="0" class="form-control">
                                                    </div>
                                                    <div class="form-group col-6">
                                                        <label>{{ __('Вопрос') }}</label>
                                                        <select class="form-control select2" name="question[0]" style="width: 100%;">
                                                            <option value="null" selected="selected">{{ __('Выберите вопрос') }}</option>
                                                            @foreach($questions as $question)
                                                                <option value="{{ $question['id'] }}">{{ $question['title'] }} {{ !empty($question['tags']) ? '#'.implode(' #', $question['tags']) : null }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-footer">

                                    <div class="alert alert-success alert-dismissible d-none alertSuccessBlock">
                                        <h5>{{ __('Успешно') }}</h5>
                                        {{ __('Тест был создан') }}
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
            $('#testText').summernote({
                height: 300,
            });

            $('.addQuestion').on('click', function () {
                var questionIndex = parseInt($(this).attr('data-question'));
                var currentQuestion = questionIndex + 1;
                var skippingQuestionCheckedStatus = $(".skippingQuestion").is(":checked");

                $(this).attr('data-question', currentQuestion);
                $('.questionsBlock').append('<div class="row question"><div class="form-group col-4"><div class="row"><div class="col-5"><input type="number" min="0" max="59" value="0" class="form-control question_time" name="question_maximum_time_min['+currentQuestion+']"></div><div class="col-1 text-center lh-38-px">:</div><div class="col-6"><input type="number" min="0" max="59" value="0" class="form-control question_time" name="question_maximum_time_sec['+currentQuestion+']"></div></div></div><div class="form-group col-2"><input type="number" value="0" name="question_score['+currentQuestion+']" class="form-control"></div><div class="form-group col-6"><select class="form-control select2" name="question['+currentQuestion+']" style="width: 100%;"><option value="null" selected="selected">{{ __('Выберите вопрос') }}</option>@foreach($questions as $question)<option value="{{ $question['id'] }}">{{ $question['title'] }} {{ !empty($question['tags']) ? '#'.implode(' #', $question['tags']) : null }}</option>@endforeach</select></div></div>');
                $('.deleteQuestion').removeClass('disabled');
                $('.select2').select2();
                if(skippingQuestionCheckedStatus === true) {
                    $('.question_time').val(0).attr('disabled', true);
                } else {
                    $('.question_time').val(0).attr('disabled', false);
                }
            })

            $('.deleteQuestion').on('click', function () {
                var questionIndex = parseInt($('.addQuestion').attr('data-question'));
                var currentQuestion = questionIndex - 1;
                $('.addQuestion').attr('data-question', currentQuestion);
                $(".question").last().remove();
                if($(".question").length === 0) {
                    $('.deleteQuestion').addClass('disabled');
                    $('.addQuestion').attr('data-question', 0);
                }
            })

            $('.btnSave').click(function() {

                $(this).prop('disabled', true);

                var request = $.ajax({
                    url: "{{ route('tests.store', ['member_id' => $auth->member_id]) }}",
                    type: 'POST',
                    dataType: 'JSON',
                    data: $('form#newTestForm').serialize(),
                })

                request.done(function( data ) {
                    $('.btnSave').addClass('d-none');
                    $('.alertSuccessBlock').removeClass('d-none');
                    console.log(data.responseJSON.message);
                });

                request.fail(function( data ) {
                    $('.alertSuccessBlock').addClass('d-none');
                    $('.btnSave').prop('disabled', false);
                    toastr.error(data.responseJSON.message);
                });

            });

            $('.skippingQuestion').on('change', function () {
                var skippingQuestionCheckedStatus = $(".skippingQuestion").is(":checked");
                if(skippingQuestionCheckedStatus === true) {
                    $('.question_time').val(0).attr('disabled', true);
                } else {
                    $('.question_time').val(0).attr('disabled', false);
                }
            })

            $('.select2').select2();



        });


    </script>
    @include('certification.chunk.footer')
</div>
</body>
</html>
