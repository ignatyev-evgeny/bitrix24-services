
<!DOCTYPE html>
<html lang="ru">
@include('certification.chunk.head')
@include('certification.chunk.script')
<body class="hold-transition login-page">
<div class="login-box">
    <div class="card card-outline card-primary">
        <div class="card-header text-center">
            <a href="/" class="h2"><b>{{ __('Аттестация') }}</b>24</a>
        </div>
        <div class="card-body">
            <form action="{{ route('oAuth.bitrix.login') }}" method="POST">
                <div class="row">
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary btn-block">{{ __('Войти через Bitrix24') }}</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
</body>
</html>