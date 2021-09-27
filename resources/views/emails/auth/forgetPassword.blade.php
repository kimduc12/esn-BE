@component('mail::message')
<p>Xin chào bạn,</p>

<p>Chúng tôi nhận được yêu cầu thiết lập lại mật khẩu cho tài khoản của bạn<br/>

Nhấn <a href="{{ $url }}">tại đây</a> để thiết lập lại mật khẩu cho tài khoản của bạn
</p>
Trân trọng,<br>
{{ config('app.name') }}
@endcomponent
