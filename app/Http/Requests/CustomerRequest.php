<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CustomerRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:150',
            'cpfCnpj' => 'required|string|max:30',
            'email' => 'required|email|max:150',
            'phone' => 'required|string|max:20',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'O campo :attribute é obrigatório.',
            'name.string' => 'O campo :attribute deve ser uma string.',
            'name.max' => 'O campo :attribute deve ter no máximo :max caracteres.',

            'cpfCnpj.required' => 'O campo :attribute é obrigatório.',
            'cpfCnpj.string' => 'O campo :attribute deve ser uma string.',
            'cpfCnpj.max' => 'O campo :attribute deve ter no máximo :max caracteres.',

            'email.required' => 'O campo :attribute é obrigatório.',
            'email.email' => 'O campo :attribute deve ser um e-mail válido.',
            'email.max' => 'O campo :attribute deve ter no máximo :max caracteres.',

            'phone.required' => 'O campo :attribute é obrigatório.',
            'phone.string' => 'O campo :attribute deve ser uma string.',
            'phone.max' => 'O campo :attribute deve ter no máximo :max caracteres.',
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => 'Nome',
            'cpfCnpj' => 'CPF ou CNPJ',
            'email' => 'Email',
            'phone' => 'Celular',
        ];
    }
}
