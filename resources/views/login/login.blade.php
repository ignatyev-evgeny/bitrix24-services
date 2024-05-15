
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
                <div class="input-group mb-3">
                    <input type="url" name="portal" required class="form-control text-center" placeholder="{{ __('Ссылка на портал Bitrix24') }}">
                </div>
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