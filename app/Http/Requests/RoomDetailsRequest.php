<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class RoomDetailsRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::check() && Auth::user()->role === 'admin';
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'hotel_id' => ['required', 'exists:hotels,id'],
            'description' => ['string', 'min:5', 'max:1000'],
            'type' => ['required', 'string'],
            'category' => ['required', 'string'],
            // 'qty' => ['required', 'numeric', 'min:0'],
            'max_adults' => ['required', 'numeric', 'min:1'],
            'max_children' => ['required', 'numeric', 'min:0'],
            'price' => ['required', 'decimal:0,2', 'min:0'],

            // 'images' => ['nullable', 'array'],
            // 'images.*' => [
            //     'image',
            //     'mimetypes:image/*',
            //     'max:10240'
            // ],
        ];
    }
}
