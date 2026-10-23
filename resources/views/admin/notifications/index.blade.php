@extends('layouts.admin')

@section('title', 'Уведомления')

@section('content')
    <h1>Уведомления</h1>
    <p>Непрочитано: {{ auth()->user()->unreadNotifications()->count() }}</p>
    <form method="POST" action="{{ route('admin.notifications.read') }}">
        @csrf
        <button type="submit">Отметить всё прочитанным</button>
    </form>

    @forelse ($notifications as $notification)
        <article @class(['notification', 'unread' => $notification->unread()])>
            <p>{{ $notification->data['text'] ?? 'У вас новое уведомление.' }}</p>
            <time datetime="{{ $notification->created_at->toIso8601String() }}">
                {{ $notification->created_at->diffForHumans() }}
            </time>
        </article>
    @empty
        <p>Уведомлений пока нет.</p>
    @endforelse
@endsection
