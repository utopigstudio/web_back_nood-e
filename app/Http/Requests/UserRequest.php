<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string',
            'surname' => 'nullable|string',
            'email' => 'required|email|max:255|unique:users',
            'password' => 'nullable|string|min:8',
            'role_id' => 'nullable|integer|exists:roles,id',
            'organization_id' => 'nullable|integer|exists:organizations,id',
            'description' => 'nullable|string',
        ];
    }
}
