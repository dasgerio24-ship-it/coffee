<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateDrinkRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('drink'));
    }

    public function rules(): array
    {
        return [
            'category_id' => ['required', 'integer', 'exists:categories,id'],
            'title' => [
                'required',
                'string',
                'min:2',
                'max:120',
                'regex:/^[\p{L}\p{N}\s\-\.,()]+$/u',
                Rule::unique('drinks', 'title')->ignore($this->route('drink')),
            ],
            'price' => ['required', 'numeric', 'min:0.01', 'max:99999.99'],
            'volume' => ['required', 'integer', 'between:50,2000'],
            'description' => ['nullable', 'string', 'max:1000'],
            'calories' => ['nullable', 'integer', 'between:0,2000'],
            'in_stock' => ['boolean'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'attachment' => ['nullable', 'mimes:pdf', 'max:5120'],
            'remove_image' => ['nullable', 'boolean'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'in_stock' => $this->boolean('in_stock'),
        ]);
    }

    public function messages(): array
    {
        return [
            'required' => 'Поле «:attribute» обязательно для заполнения.',
            'exists' => 'Выбранная категория не существует.',
            'unique' => 'Такое название уже используется.',
            'min' => 'Поле «:attribute» должно содержать не менее :min символов.',
            'max' => 'Поле «:attribute» не должно превышать :max символов.',
            'numeric' => 'Поле «:attribute» должно быть числом.',
            'integer' => 'Поле «:attribute» должно быть целым числом.',
            'regex' => 'Поле «:attribute» содержит недопустимые символы.',
            'between' => 'Поле «:attribute» должно быть от :min до :max.',
            'boolean' => 'Поле «:attribute» должно иметь значение да или нет.',
            'image' => 'Файл должен быть изображением.',
            'mimes' => 'Недопустимый формат файла.',
            'max' => 'Размер файла не должен превышать :max КБ.',
        ];
    }

    public function attributes(): array
    {
        return [
            'category_id' => 'Категория',
            'title' => 'Название',
            'price' => 'Цена',
            'volume' => 'Объём',
            'description' => 'Описание',
            'calories' => 'Калорийность',
            'in_stock' => 'В наличии',
            'image' => 'Изображение',
            'attachment' => 'Документ',
            'remove_image' => 'Удалить изображение',
        ];
    }
}
