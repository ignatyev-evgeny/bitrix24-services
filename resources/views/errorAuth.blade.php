<!DOCTYPE html>
<html lang="ru">
@include('certification.chunk.head')
<body class="hold-transition sidebar-mini">
    <div class="wrapper">
        <div class="content-header"></div>
        <section class="content mt-5">
            <div class="error-page">
                <h2 class="headline text-danger">401</h2>
                <div class="error-content">
                    <h3><b>{{ __('Ошибка авторизации') }}</b></h3>
                    <p>{{ __('Ваша сессия завершена, требуется повторная авторизация') }}</p>
                </div>
            </div>
        </section>
    </div>
</body>
</html>
