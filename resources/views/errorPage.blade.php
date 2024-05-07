<!DOCTYPE html>
<html lang="ru">
@include('certification.chunk.head')
@include('certification.chunk.script')
<body class="hold-transition sidebar-mini">
<div class="wrapper">
    @include('certification.chunk.sidebar')
    <div class="content-wrapper">
        <div class="content-header"></div>
        <section class="content mt-5">
            <div class="error-page">
                <h2 class="headline text-danger">{{ $status ?? 500 }}</h2>
                <div class="error-content">
                    <h3><i class="fas fa-exclamation-triangle text-danger mr-2"></i><b>{{ __('Произошла ошибка') }}</b></h3>
                    @if($message)
                        <p>{{ $message }}</p>
                    @else
                        <p>
                            {{ __('Мы уже работаем над решение возникшей проблемы.') }}
                            {!! __('Тем временем вы можете <a href="'.route('home', ['member_id' => $auth->member_id]).'">вернуться на главный экран</a>.') !!}
                        </p>
                    @endif
                </div>
            </div>
        </section>
    </div>
    <aside class="control-sidebar control-sidebar-dark"></aside>
    @include('certification.chunk.footer')
</div>
</body>
</html>
