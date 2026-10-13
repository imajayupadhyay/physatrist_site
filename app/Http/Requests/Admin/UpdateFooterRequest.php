<?php

namespace App\Http\Requests\Admin;

use App\Rules\SafeLink;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Footer settings + site-wide contact details (one form in Admin → Footer).
 */
class UpdateFooterRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        $trim = fn ($value) => is_string($value) ? trim($value) : $value;

        // Drop empty rows; services columns don't keep links.
        $columns = collect($this->input('columns', []))->map(function ($column) use ($trim) {
            $type = $column['type'] ?? 'links';

            return [
                'heading' => $trim($column['heading'] ?? ''),
                'type' => $type,
                'limit' => $type === 'services' ? (int) ($column['limit'] ?? 6) : null,
                'links' => $type === 'services' ? [] : collect($column['links'] ?? [])
                    ->map(fn ($link) => [
                        'label' => $trim($link['label'] ?? ''),
                        'url' => $trim($link['url'] ?? ''),
                        'new_tab' => filter_var($link['new_tab'] ?? false, FILTER_VALIDATE_BOOLEAN),
                    ])
                    ->reject(fn ($link) => $link['label'] === '' && $link['url'] === '')
                    ->values()->all(),
            ];
        })->values()->all();

        $contact = (array) $this->input('contact', []);
        $contact['phones'] = collect($contact['phones'] ?? [])->map($trim)->filter()->values()->all();
        $contact['whatsapp_number'] = preg_replace('/\D/', '', (string) ($contact['whatsapp_number'] ?? ''));
        foreach (['instagram', 'youtube', 'facebook', 'linkedin', 'maps_url', 'website_url', 'hours_short'] as $key) {
            $contact[$key] = $trim($contact[$key] ?? null) ?: null;
        }

        $this->merge([
            'columns' => $columns,
            'contact' => $contact,
            ...collect(['show_socials', 'show_website', 'show_hours', 'helpline_visible', 'remove_logo'])
                ->mapWithKeys(fn ($key) => [$key => $this->boolean($key)])
                ->all(),
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $url = ['nullable', 'url:http,https', 'max:255'];

        return [
            // Brand column
            'logo_file' => ['nullable', 'file', 'mimes:png,jpg,jpeg,webp', 'max:2048'],
            'remove_logo' => ['boolean'],
            'logo_alt' => ['required', 'string', 'max:120'],
            'logo_height' => ['required', 'integer', 'min:24', 'max:80'],
            'brand_name' => ['required', 'string', 'max:40'],
            'brand_tagline' => ['nullable', 'string', 'max:40'],
            'about_text' => ['nullable', 'string', 'max:300'],
            'credentials_name' => ['nullable', 'string', 'max:80'],
            'credentials_text' => ['nullable', 'string', 'max:300'],
            'show_socials' => ['boolean'],

            // Link columns
            'columns' => ['array', 'max:3'],
            'columns.*.heading' => ['required', 'string', 'max:40'],
            'columns.*.type' => ['required', Rule::in(['links', 'services'])],
            'columns.*.limit' => ['nullable', 'integer', 'min:1', 'max:12'],
            'columns.*.links' => ['array', 'max:12'],
            'columns.*.links.*.label' => ['required', 'string', 'max:60'],
            'columns.*.links.*.url' => ['required', 'string', 'max:255', new SafeLink],
            'columns.*.links.*.new_tab' => ['boolean'],

            // Contact column + bottom bar
            'contact_heading' => ['required', 'string', 'max:40'],
            'show_website' => ['boolean'],
            'show_hours' => ['boolean'],
            'helpline_visible' => ['boolean'],
            'helpline_text' => ['required_if:helpline_visible,true', 'nullable', 'string', 'max:300'],
            'copyright_text' => ['nullable', 'string', 'max:200'],
            'bottom_text' => ['nullable', 'string', 'max:200'],

            // Site-wide contact details
            'contact.email' => ['required', 'email', 'max:255'],
            'contact.phones' => ['required', 'array', 'min:1', 'max:4'],
            'contact.phones.*' => ['string', 'max:30', 'regex:/^\+?[0-9\s\-()]{8,}$/'],
            'contact.whatsapp_number' => ['required', 'digits_between:10,15'],
            'contact.address' => ['required', 'string', 'max:300'],
            'contact.maps_url' => $url,
            'contact.website_url' => $url,
            'contact.hours' => ['required', 'string', 'max:80'],
            'contact.hours_short' => ['nullable', 'string', 'max:60'],
            'contact.instagram' => $url,
            'contact.youtube' => $url,
            'contact.facebook' => $url,
            'contact.linkedin' => $url,
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
            'columns.*.heading.required' => 'Each column needs a heading.',
            'columns.*.links.*.label.required' => 'Enter the link text.',
            'columns.*.links.*.url.required' => 'Enter where this link goes.',
            'contact.phones.required' => 'Add at least one phone number.',
            'contact.phones.*.regex' => 'Enter a valid phone number, e.g. +91 82853 54608.',
            'contact.whatsapp_number.digits_between' => 'Enter the country code and number, digits only, e.g. 918285354608.',
            '*.url' => 'Enter a full URL starting with https://',
            'contact.*.url' => 'Enter a full URL starting with https://',
            'helpline_text.required_if' => 'Enter the helpline text, or switch the box off.',
            'logo_file.mimes' => 'Upload a PNG, JPG or WebP file.',
            'logo_file.max' => 'The logo must be under 2 MB.',
        ];
    }
}
