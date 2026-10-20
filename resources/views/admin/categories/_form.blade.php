@if ($errors->any())
    <x-alert type="error">Исправьте ошибки в форме ({{ $errors->count() }})</x-alert>
@endif

<form method="POST" action="{{ $action }}" class="admin-form">
    @csrf
    @isset($category)
        @method('PUT')
    @endisset

    <label for="name">Название</label>
    <input id="name" name="name" type="text" value="{{ old('name', $category->name ?? '') }}" @class(['invalid' => $errors->has('name')])>
    @error('name') <div class="error">{{ $message }}</div> @enderror

    <label for="description">Описание</label>
    <textarea id="description" name="description" @class(['invalid' => $errors->has('description')])>{{ old('description', $category->description ?? '') }}</textarea>
    @error('description') <div class="error">{{ $message }}</div> @enderror

    <div class="form-actions">
        <button type="submit">Сохранить</button>
        <a href="{{ route('admin.categories.index') }}">Назад к категориям</a>
    </div>
</form>
