<!DOCTYPE html>
<html lang="ru">
@include('certification.chunk.head')
@include('certification.chunk.script')
<body class="hold-transition sidebar-mini">

<script src="{{ asset('/plugins/select2/js/select2.full.min.js') }}"></script>
<link rel="stylesheet" href="{{ asset('/plugins/select2/css/select2.min.css') }}">

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
                        <h1 class="m-0">{{ $knowledge->title }}</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('home', ['auth_id' => $auth->auth_id]) }}">{{ __('Главная') }}</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('knowledge.list', ['auth_id' => $auth->auth_id]) }}">{{ __('База знаний') }}</a></li>
                            <li class="breadcrumb-item active">{{ $knowledge->title }}</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
        <section class="content">
            <div class="card">
                <div class="card-body">
                    {!! $knowledge->description !!}
                </div>
                <div class="card-footer">
                    @isset($knowledge->tags)
                    <p class="mb-2">
                        {{ __('Tags') }}:
                        @foreach($knowledge->tags as $tag)
                            <span class="badge badge-info">{{ $tag }}</span>
                        @endforeach
                    </p>
                    @endisset
                    @isset($knowledge->questions)
                    <p class="mb-2">
                        {{ __('Вопросы') }}:
                        @foreach($knowledge->questions as $question)
                            <span class="badge badge-info">{{ $question }}</span>
                        @endforeach
                    </p>
                    @endisset
                    @isset($knowledge->tests)
                    <p class="mb-0">
                        {{ __('Тесты') }}:
                        @foreach($knowledge->tests as $test)
                            <span class="badge badge-info">{{ $test }}</span>
                        @endforeach
                    </p>
                    @endisset
                </div>
            </div>
        </section>
    </div>
    <aside class="control-sidebar control-sidebar-dark"></aside>
    @include('certification.chunk.footer')
</div>
</body>
</html>
