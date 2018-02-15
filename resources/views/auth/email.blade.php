<h1>{{ $data['name'] }}</h1>
<div>Для завершения регистрации перейдите по ссылке <a href="{{$data['url']}}/user/activation/?email={{ $data['email'] }}&code={{ $data['activation_code'] }}">подтвердить email</a></div>
