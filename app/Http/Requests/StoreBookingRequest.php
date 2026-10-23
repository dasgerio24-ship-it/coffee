<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreBookingRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'phone' => ['required', 'regex:/^\+7\d{10}$/'],
            'comment' => ['nullable', 'string', 'max:300'],
        ];
    }

    public function messages(): array
    {
        return [
            'phone.required' => 'Укажите телефон.',
            'phone.regex' => 'Телефон в формате +7XXXXXXXXXX',
            'comment.max' => 'Комментарий не должен превышать 300 символов.',
        ];
    }

    public function attributes(): array
    {
        return [
            'phone' => 'Телефон',
            'comment' => 'Комментарий',
        ];
    }
}
