<x-mail::message>
# Заказ принят

Здравствуйте, {{ $booking->user->name }}!

Ваш заказ на «{{ $booking->drink->title }}» принят.

Мы свяжемся с вами по номеру {{ $booking->phone }}.

<x-mail::button :url="route('profile')">
Мои заказы
</x-mail::button>

С уважением,<br>
{{ config('app.name') }}
</x-mail::message>
