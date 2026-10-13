<?php

namespace App\Http\Requests;

use App\Models\Lead;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class StoreLeadRequest extends FormRequest
{
    /**
     * Trim text inputs and drop empty optional values before validating.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'name' => trim((string) $this->input('name')),
            'phone' => trim((string) $this->input('phone')),
            'email' => trim((string) $this->input('email')) ?: null,
            'message' => trim((string) $this->input('message')) ?: null,
            'preferred_date' => $this->input('preferred_date') ?: null,
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'min:2', 'max:120'],
            'phone' => ['required', 'string', 'max:30', 'regex:/^[0-9+\-\s().]+$/'],
            'email' => ['nullable', 'email', 'max:255'],
            'concern' => ['required', Rule::in(Lead::concernOptions())],
            'preferred_date' => ['nullable', 'date', 'after_or_equal:today', 'before_or_equal:+1 year'],
            'mode' => ['required', Rule::in(config('leads.modes'))],
            'message' => ['nullable', 'string', 'max:2000'],
        ];
    }

    /**
     * Require 10–15 digits in the phone number, whatever the formatting.
     *
     * @return array<int, callable>
     */
    public function after(): array
    {
        return [
            function (Validator $validator) {
                $digits = strlen(preg_replace('/\D/', '', (string) $this->input('phone')));

                if (! $validator->errors()->has('phone') && ($digits < 10 || $digits > 15)) {
                    $validator->errors()->add('phone', 'Please enter a valid phone number.');
                }
            },
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Please enter your name.',
            'phone.required' => 'Please enter your phone number.',
            'phone.regex' => 'Please enter a valid phone number.',
            'email.email' => 'Please enter a valid email address.',
            'concern.in' => 'Please choose an area of concern.',
            'preferred_date.after_or_equal' => 'Please choose today or a future date.',
            'preferred_date.before_or_equal' => 'Please choose a date within the next year.',
            'mode.in' => 'Please choose in-clinic or online.',
            'message.max' => 'Please keep your note under 2,000 characters.',
        ];
    }
}
