<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateHomeServicesRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        $clean = fn (string $key) => collect($this->input($key, []))->map(fn ($item) => trim((string) $item))->filter()->values()->all();

        $this->merge([
            'also_items' => $clean('also_items'),
            'notes' => $clean('notes'),
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
            'eyebrow' => ['nullable', 'string', 'max:80'],
            'heading' => ['required', 'string', 'max:150'],
            'lead' => ['nullable', 'string', 'max:600'],
            'card_link_label' => ['required', 'string', 'max:40'],
            'also_label' => ['nullable', 'string', 'max:80'],
            'also_items' => ['array', 'max:20'],
            'also_items.*' => ['string', 'max:80'],
            'notes' => ['array', 'max:6'],
            'notes.*' => ['string', 'max:200'],
        ];
    }
}
