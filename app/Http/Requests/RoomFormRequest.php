<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class RoomFormRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::user()->role == 'admin';
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
            'room_detail_id' => ['required', 'exists:room_details,id'],
            'room_number' => ['required_if:room-add-option,single-room', 'nullable', 'string', 'max:10'],
            'room_number_prefix' => ['nullable', 'string', 'max:5'],
            'room_number_from' => ['required_if:room-add-option,multiple-rooms', 'nullable', 'numeric', 'min:0'],
            'room_number_to' => ['required_if:room-add-option,multiple-rooms', 'nullable', 'numeric', 'gt:room_number_from'],
            'status' => ['boolean'],
        ];
    }
}
