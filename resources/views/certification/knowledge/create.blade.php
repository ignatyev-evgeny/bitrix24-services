<!DOCTYPE html>
<html lang="ru">
@include('certification.chunk.head')
@include('certification.chunk.script')
<body class="hold-transition sidebar-mini">

<link href="https://cdn.jsdelivr.net/npm/suneditor@latest/dist/css/suneditor.min.css" rel="stylesheet">
<!-- <link href="https://cdn.jsdelivr.net/npm/suneditor@latest/assets/css/suneditor.css" rel="stylesheet"> -->
<!-- <link href="https://cdn.jsdelivr.net/npm/suneditor@latest/assets/css/suneditor-contents.css" rel="stylesheet"> -->
<script src="https://cdn.jsdelivr.net/npm/suneditor@latest/dist/suneditor.min.js"></script>
<!-- languages (Basic Language: English/en) -->
<script src="https://cdn.jsdelivr.net/npm/suneditor@latest/src/lang/ru.js"></script>

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
                        <h1 class="m-0">{{ __('Новая статья') }}</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('home', ['auth_id' => $auth->auth_id]) }}">{{ __('Главная') }}</a></li>
                            <li class="breadcrumb-item active">{{ __('Новая статья') }}</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
        <div class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                        <form method="POST" id="newArticleForm" multiple="">
                            <input type="hidden" name="portal" value="{{ $auth->portal }}">
                            <input type="hidden" id="description" name="description">
                            <div class="card card-info">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="form-group">
                                                <label>{{ __('Наименование статьи') }}</label>
                                                <input type="text" class="form-control" name="title">
                                            </div>
                                            <div class="form-group">
                                                <label>{{ __('Используемые теги') }}</label>
                                                <select class="form-control select2-tags" multiple name="tags[]" style="width: 100%;"></select>
                                            </div>
                                            <div class="form-group">
                                                <label>{{ __('Связанные вопросы') }}</label>
                                                <select class="form-control select2" multiple name="questions[]" style="width: 100%;">
                                                    @foreach($questions as $question)
                                                        <option value="{{ $question['id'] }}">{{ $question['title'] }} {{ !empty($question['tags']) ? '#'.implode(' #', $question['tags']) : null }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="form-group">
                                                <label>{{ __('Связанные тесты') }}</label>
                                                <select class="form-control select2" multiple name="tests[]" style="width: 100%;">
                                                    @foreach($tests as $test)
                                                        <option value="{{ $test['id'] }}">{{ $test['title'] }} {{ !empty($test['tags']) ? '#'.implode(' #', $test['tags']) : null }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="form-group">
                                                <textarea id="articleText" class="w-100"></textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-footer">

                                    <div class="alert alert-success alert-dismissible d-none alertSuccessBlock">
                                        <h5>{{ __('Успешно') }}</h5>
                                        {{ __('Статья была успешно создана') }}
                                    </div>

                                    <button type="button" class="btn btn-success w-100 btnSave">{{ __('Создать статью') }}</button>
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

            $('.select2').select2();
            $(".select2-tags").select2({
                tags: true,
                tokenSeparators: [',']
            })

            const editor = SUNEDITOR.create((document.getElementById('articleText') || 'articleText'), {
                lang: SUNEDITOR_LANG['ru'],
                height : 'auto',
                minHeight : '300px',
                buttonList: [
                    ['undo', 'redo'],
                    ['paragraphStyle', 'blockquote'],
                    ['fontColor', 'hiliteColor', 'textStyle'],
                    ['removeFormat'],
                    ['outdent', 'indent'],
                    ['align', 'horizontalRule', 'list', 'lineHeight'],
                    /** ['imageGallery'] */ // You must add the "imageGalleryUrl".
                    ['fullScreen', 'showBlocks', 'codeView'],
                    ['preview', 'print'],
                    '/',
                    ['font', 'fontSize', 'formatBlock'],
                    ['bold', 'underline', 'italic', 'strike', 'subscript', 'superscript'],
                    ['table', 'link', 'image', 'video', 'audio' /** ,'math' */], // You must add the 'katex' library at options to use the 'math' plugin.
                    // ['save', 'template'],
                    // ['dir', 'dir_ltr', 'dir_rtl']
                ]
            });

            $('.btnSave').click(function() {

                $(this).prop('disabled', true);

                const contents = editor.getContents();
                $('#description').val(contents);

                var request = $.ajax({
                    url: "{{ route('knowledge.store', ['auth_id' => $auth->auth_id]) }}",
                    type: 'POST',
                    dataType: 'JSON',
                    data: $('form#newArticleForm').serialize(),
                })

                request.done(function( data ) {
                    $('.btnSave').addClass('d-none');
                    $('.alertSuccessBlock').removeClass('d-none');
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
