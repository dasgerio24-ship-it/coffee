@extends('layouts.app')

@section('title', 'Мои заказы')

@section('content')
    <h1>Мои заказы</h1>
    @if ($bookings->isEmpty())
        <p>У вас пока нет заказов.</p>
    @else
        <div class="table-scroll">
            <table>
                <thead>
                    <tr>
                        <th>Напиток</th>
                        <th>Дата</th>
                        <th>Статус</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($bookings as $booking)
                        <tr>
                            <td>{{ $booking->drink->title }}</td>
                            <td>{{ $booking->created_at->format('d.m.Y H:i') }}</td>
                            <td>{{ $booking->status }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

    <h2>Непрочитанные уведомления</h2>
    <p>Непрочитано: {{ auth()->user()->unreadNotifications()->count() }}</p>
    @if ($notifications->isNotEmpty())
        <form method="POST" action="{{ route('profile.notifications.read') }}">
            @csrf
            <button type="submit">Отметить прочитанными</button>
        </form>
    @endif
    @forelse ($notifications as $notification)
        <article class="notification unread">
            <p>{{ $notification->data['text'] ?? 'У вас новое уведомление.' }}</p>
            <time datetime="{{ $notification->created_at->toIso8601String() }}">
                {{ $notification->created_at->diffForHumans() }}
            </time>
        </article>
    @empty
        <p>Новых уведомлений нет.</p>
    @endforelse
@endsection
