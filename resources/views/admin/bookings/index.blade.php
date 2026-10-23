@extends('layouts.admin')

@section('title', 'Заказы')

@section('content')
    <h1>Заказы</h1>
    <div class="table-scroll">
        <table>
            <thead>
                <tr>
                    <th>Клиент</th>
                    <th>Напиток</th>
                    <th>Телефон</th>
                    <th>Дата</th>
                    <th>Статус</th>
                    <th>Действия</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($bookings as $booking)
                    <tr>
                        <td>{{ $booking->user->name }}</td>
                        <td>{{ $booking->drink->title }}</td>
                        <td>{{ $booking->phone }}</td>
                        <td>{{ $booking->created_at->format('d.m.Y H:i') }}</td>
                        <td>{{ $booking->status }}</td>
                        <td class="actions">
                            @if ($booking->status === 'new')
                                <form method="POST" action="{{ route('admin.bookings.update', $booking) }}">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="status" value="processed">
                                    <button type="submit">Обработан</button>
                                </form>
                                <form method="POST" action="{{ route('admin.bookings.update', $booking) }}">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="status" value="rejected">
                                    <button class="danger" type="submit">Отклонить</button>
                                </form>
                            @else
                                —
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6">Заказов пока нет.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    {{ $bookings->links() }}
@endsection
