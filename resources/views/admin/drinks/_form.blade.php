@if ($errors->any())
    <x-alert type="error">Исправьте ошибки в форме ({{ $errors->count() }})</x-alert>
@endif

<form method="POST" action="{{ $action }}" class="admin-form">
    @csrf
    @isset($drink)
        @method('PUT')
    @endisset

    <label for="category_id">Категория</label>
    <select id="category_id" name="category_id" @class(['invalid' => $errors->has('category_id')])>
        <option value="">Выберите категорию</option>
        @foreach ($categories as $category)
            <option value="{{ $category->id }}" @selected(old('category_id', $drink->category_id ?? '') == $category->id)>
                {{ $category->name }}
            </option>
        @endforeach
    </select>
    @error('category_id') <div class="error">{{ $message }}</div> @enderror

    <label for="title">Название</label>
    <input id="title" name="title" type="text" value="{{ old('title', $drink->title ?? '') }}" @class(['invalid' => $errors->has('title')])>
    @error('title') <div class="error">{{ $message }}</div> @enderror

    <label for="price">Цена</label>
    <input id="price" name="price" type="number" step="0.01" value="{{ old('price', $drink->price ?? '') }}" @class(['invalid' => $errors->has('price')])>
    @error('price') <div class="error">{{ $message }}</div> @enderror

    <label for="volume">Объём (мл)</label>
    <input id="volume" name="volume" type="number" value="{{ old('volume', $drink->volume ?? '') }}" @class(['invalid' => $errors->has('volume')])>
    @error('volume') <div class="error">{{ $message }}</div> @enderror

    <label for="description">Описание</label>
    <textarea id="description" name="description" @class(['invalid' => $errors->has('description')])>{{ old('description', $drink->description ?? '') }}</textarea>
    @error('description') <div class="error">{{ $message }}</div> @enderror

    <label for="calories">Калорийность</label>
    <input id="calories" name="calories" type="number" value="{{ old('calories', $drink->calories ?? '') }}" @class(['invalid' => $errors->has('calories')])>
    @error('calories') <div class="error">{{ $message }}</div> @enderror

    <label class="checkbox-label" for="in_stock">
        <input type="hidden" name="in_stock" value="0">
        <input id="in_stock" name="in_stock" type="checkbox" value="1" @checked((string) old('in_stock', $drink->in_stock ?? true) === '1')>
        В наличии
    </label>
    @error('in_stock') <div class="error">{{ $message }}</div> @enderror

    <div class="form-actions">
        <button type="submit">Сохранить</button>
        <a href="{{ route('admin.drinks.index') }}">Назад к напиткам</a>
    </div>
</form>
