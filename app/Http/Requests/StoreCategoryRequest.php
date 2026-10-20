<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:80', 'unique:categories,name'],
            'description' => ['nullable', 'string', 'max:500'],
        ];
    }

    public function messages(): array
    {
        return [
            'required' => 'Поле «:attribute» обязательно для заполнения.',
            'string' => 'Поле «:attribute» должно быть текстом.',
            'max' => 'Поле «:attribute» не должно превышать :max символов.',
            'unique' => 'Такая категория уже существует.',
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => 'Название',
            'description' => 'Описание',
        ];
    }
}
