<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class DiscountFormRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::user()->role === 'admin';
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $discountId = $this->route('discount');
        return [
            'coupen_code' => [
                'required',
                'string',
                'max:50',
                \Illuminate\Validation\Rule::unique('discounts', 'coupen_code')->ignore($discountId),
            ],
            'country_id' => 'nullable|exists:countries,id',
            'type' => 'required|in:percentage,fixed',
            'value' => 'required|numeric|min:0',
            'max_discount' => 'nullable|numeric|min:0',
            'min_amount' => 'nullable|numeric|min:0',
            'min_nights' => 'nullable|integer|min:1',
            'usage_limit' => 'nullable|integer|min:1',
            'user_limit' => 'nullable|integer|min:1',
            'active_status' => 'required|boolean',
            'starts_from' => 'required|date',
            'ends_at' => 'nullable|date|after_or_equal:starts_from',
            'required_code' => 'required|boolean',
            'message' => 'nullable|string',

            // "starts_from": "2026-04-02",
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            if (empty($this->country_id) && $this->type === 'fixed') {
                $validator->errors()->add(
                    'type',
                    'Global coupons must be of "Percentage" type to avoid currency conflicts.'
                );
            }
        });
    }
}
