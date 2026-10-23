<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateBookingStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'status' => ['required', Rule::in(['processed', 'rejected'])],
        ];
    }

    public function messages(): array
    {
        return [
            'status.required' => 'Выберите статус заказа.',
            'status.in' => 'Выбран недопустимый статус заказа.',
        ];
    }

    public function attributes(): array
    {
        return ['status' => 'Статус'];
    }
}
