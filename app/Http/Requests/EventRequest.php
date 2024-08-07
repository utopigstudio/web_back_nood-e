<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EventRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'title' => 'required|string|max:255',
            'date' => 'required|date|date_format:d/m/Y|after_or_equal:today',
            'start' => 'required|date|date_format:H:i:s A|after_or_equal:date',
            'end' => 'required|date|date_format:H:i:s A|after:start',
        ];
    }
}
