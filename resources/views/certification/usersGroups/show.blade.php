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
<script src="{{ asset('plugins/bootstrap4-duallistbox/jquery.bootstrap-duallistbox.min.js') }}"></script>
<link rel="stylesheet" type="text/css" href="{{ asset('plugins/bootstrap4-duallistbox/bootstrap-duallistbox.min.css') }}">

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
                        <h1 class="m-0">{{ __('Редактирование группы сотрудников') }}</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('home', ['auth_id' => $auth->auth_id]) }}">{{ __('Главная') }}</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('users.groups.list', ['auth_id' => $auth->auth_id]) }}">{{ __('Группы сотрудников') }}</a></li>
                            <li class="breadcrumb-item active">{{ __('Редактирование группы сотрудников') }}</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
        <div class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                        <form method="POST" id="updateUserGroupForm" multiple="">
                            <input type="hidden" name="portal" value="{{ $auth->portal }}">
                            <input type="hidden" id="description" name="description">
                            <input type="hidden" name="authId" value="{{ $auth->auth_id }}">
                            <div class="card card-info">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="form-group">
                                                <label>{{ __('Наименование группы') }}</label>
                                                <input type="text" class="form-control" name="title" value="{{ $group->title }}">
                                            </div>
                                            <div class="form-group">
                                                <label>{{ __('Используемые теги') }}</label>
                                                <select class="form-control select2-tags" multiple name="tags[]" style="width: 100%;">
                                                    @foreach($group->tags as $key => $tag)
                                                        <option selected value="{{ $key }}">{{ $tag }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="form-group">
                                                <label>{{ __('Описание группы') }}</label>
                                                <textarea id="groupDescription" class="w-100">{{ $group->description }}</textarea>
                                            </div>
                                            <div class="form-group">
                                                <label>{{ __('Состав группы') }}</label>
                                                <select class="duallistbox w-100" multiple="multiple" name="users[]">
                                                    @foreach($users as $user)
                                                        <option @if(in_array($user->id, $groupUsers)) selected @endif value="{{ $user->id }}">{{ $user->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-footer">
                                    <button type="button" class="btn btn-success w-100 btnSave">{{ __('Сохранить изменение группы сотрудников') }}</button>
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
            const editor = SUNEDITOR.create((document.getElementById('groupDescription') || 'groupDescription'), {
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
            $('.duallistbox').bootstrapDualListbox({
                nonSelectedListLabel: '{{ __('Не входит') }}',
                selectedListLabel: '{{ __('Входит') }}',
                filterPlaceHolder: '{{ __('Поиск') }}',
                moveAllLabel: '{{ __('Выбрать всех') }}',
                filterTextClear: '{{ __('Показать всех') }}',
                infoText: '{{ __('Всего') }}: {0}',
                infoTextEmpty: '{{ __('Не выбрано') }}',
                infoTextFiltered: '<span class="label label-warning">{{ __('Отфильтровано') }}</span> {0} из {1}',
                preserveSelectionOnMove: 'moved',
                selectorMinimalHeight: '300',
                moveOnSelect: true,
            });
            $(".select2-tags").select2({
                tags: true,
                tokenSeparators: [',']
            })
            $('.btnSave').click(function() {
                $(this).prop('disabled', true);
                const contents = editor.getContents();
                $('#description').val(contents);
                var request = $.ajax({
                    url: "{{ route('users.groups.update', ['auth_id' => $auth->auth_id, 'group' => $group->id]) }}",
                    type: 'POST',
                    dataType: 'JSON',
                    data: $('form#updateUserGroupForm').serialize(),
                })
                request.done(function( data ) {
                    $('.btnSave').prop('disabled', false);
                    toastr.success(data.message);
                });
                request.fail(function( data ) {
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
