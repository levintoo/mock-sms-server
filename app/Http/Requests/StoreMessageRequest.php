<?php

namespace App\Http\Requests;

use App\Enums\MessageStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;

class StoreMessageRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'to' => [
                'required',
                Rule::when(
                    fn ($input) => is_array($input['to'] ?? null),
                    ['array', 'min:1'],
                    ['string', 'regex:/^\d{10,15}$/']
                ),
            ],
            'to.*' => [
                Rule::when(
                    fn ($input) => is_array($input['to'] ?? null),
                    ['string', 'regex:/^\d{10,15}$/']
                ),
            ],
            'message' => ['nullable'],
            'status' => ['nullable', new enum(MessageStatus::class)],
            'delivered_at' => ['nullable', 'date'],
            'failed_at' => ['nullable', 'date'],
        ];
    }
}
