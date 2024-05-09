<!DOCTYPE html>
<html lang="ru">
@include('certification.chunk.head')
<body class="hold-transition sidebar-mini">
    <div class="wrapper">
        <div class="content-header"></div>
        <section class="content mt-5">
            <div class="error-page">
                <h2 class="headline text-danger">403</h2>
                <div class="error-content">
                    <h3>{{ __('Хьюстон, у нас проблемы!') }}</h3>
                    <p>{!! __("Нет доступа <br> или пользователь не активирован") !!}</p>
                </div>
            </div>
        </section>
    </div>
</body>
</html>
