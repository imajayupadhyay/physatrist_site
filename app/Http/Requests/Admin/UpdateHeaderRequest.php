<?php

namespace App\Http\Requests\Admin;

use App\Rules\SafeLink;
use Illuminate\Foundation\Http\FormRequest;

class UpdateHeaderRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        $this->merge(collect(['topbar_visible', 'topbar_show_hours', 'topbar_show_phone', 'topbar_show_email', 'cta_visible', 'cta_new_tab', 'mega_promo_visible', 'remove_logo'])
            ->mapWithKeys(fn ($key) => [$key => $this->boolean($key)])
            ->all());

        if (is_string($this->input('topbar_text'))) {
            $this->merge(['topbar_text' => trim($this->input('topbar_text'))]);
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            // Top bar
            'topbar_visible' => ['boolean'],
            'topbar_show_hours' => ['boolean'],
            'topbar_text' => ['nullable', 'string', 'max:80'],
            'topbar_show_phone' => ['boolean'],
            'topbar_show_email' => ['boolean'],

            // Logo
            'logo_file' => ['nullable', 'file', 'mimes:png,jpg,jpeg,webp', 'max:2048'],
            'remove_logo' => ['boolean'],
            'logo_alt' => ['required', 'string', 'max:120'],
            'logo_height' => ['required', 'integer', 'min:24', 'max:80'],
            'brand_name' => ['required', 'string', 'max:40'],
            'brand_tagline' => ['nullable', 'string', 'max:40'],

            // CTA button
            'cta_visible' => ['boolean'],
            'cta_label' => ['required_if:cta_visible,true', 'nullable', 'string', 'max:30'],
            'cta_url' => ['required_if:cta_visible,true', 'nullable', 'string', 'max:255', new SafeLink],
            'cta_new_tab' => ['boolean'],

            // Mega menu promo panel
            'mega_promo_visible' => ['boolean'],
            'mega_promo_title' => ['required_if:mega_promo_visible,true', 'nullable', 'string', 'max:60'],
            'mega_promo_text' => ['nullable', 'string', 'max:200'],
            'mega_promo_cta_label' => ['required_if:mega_promo_visible,true', 'nullable', 'string', 'max:30'],
            'mega_promo_cta_url' => ['required_if:mega_promo_visible,true', 'nullable', 'string', 'max:255', new SafeLink],
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
            'logo_file.mimes' => 'Upload a PNG, JPG or WebP file.',
            'logo_file.max' => 'The logo must be under 2 MB.',
            '*.required_if' => 'This field is required while this part is switched on.',
        ];
    }
}
