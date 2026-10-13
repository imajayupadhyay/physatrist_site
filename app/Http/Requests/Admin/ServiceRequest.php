<?php

namespace App\Http\Requests\Admin;

use App\Models\Service;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

/**
 * Create + update a service (all sections of the editor).
 */
class ServiceRequest extends FormRequest
{
    /**
     * Normalise input: slug format, booleans, and drop empty list rows.
     */
    protected function prepareForValidation(): void
    {
        $slug = Str::slug((string) ($this->input('slug') ?: $this->input('title')));

        $rows = fn (string $key, array $fields) => collect($this->input($key, []))
            ->map(fn ($row) => collect($fields)->mapWithKeys(fn ($field) => [$field => trim((string) ($row[$field] ?? ''))])->all())
            ->reject(fn ($row) => collect($row)->every(fn ($value) => $value === ''))
            ->values()
            ->all();

        $this->merge([
            'slug' => $slug,
            'issues' => collect($this->input('issues', []))->map(fn ($item) => trim((string) $item))->filter()->values()->all(),
            'approach' => $rows('approach', ['title', 'text']),
            'faqs' => $rows('faqs', ['question', 'answer']),
            ...collect(['is_published', 'show_on_home', 'is_featured', 'robots_index', 'robots_follow', 'remove_hero_image', 'remove_og_image'])
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
        $image = ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:3072'];

        return [
            // Basics
            'title' => ['required', 'string', 'max:120'],
            'slug' => [
                'required', 'string', 'max:100', 'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/',
                Rule::unique('services', 'slug')->ignore($this->route('service')),
            ],
            'concern_label' => ['nullable', 'string', 'max:100'],
            'is_published' => ['boolean'],

            // Homepage card
            'show_on_home' => ['boolean'],
            'is_featured' => ['boolean'],
            'icon' => ['required', Rule::in(Service::ICONS)],
            'audience' => ['nullable', 'string', 'max:80'],
            'summary' => ['required', 'string', 'max:300'],

            // Hero
            'hero_eyebrow' => ['nullable', 'string', 'max:80'],
            'hero_title' => ['required', 'string', 'max:150'],
            'intro' => ['required', 'string', 'max:800'],
            'hero_image_file' => $image,
            'remove_hero_image' => ['boolean'],

            // Overview + issues
            'overview_heading' => ['nullable', 'string', 'max:150'],
            'overview_body' => ['nullable', 'string', 'max:6000'],
            'issues_heading' => ['nullable', 'string', 'max:150'],
            'issues' => ['array', 'max:20'],
            'issues.*' => ['string', 'max:200'],

            // Approach
            'approach_heading' => ['nullable', 'string', 'max:150'],
            'approach' => ['array', 'max:8'],
            'approach.*.title' => ['required', 'string', 'max:100'],
            'approach.*.text' => ['required', 'string', 'max:400'],

            // CTA
            'cta_heading' => ['nullable', 'string', 'max:150'],
            'cta_text' => ['nullable', 'string', 'max:400'],

            // FAQs
            'faqs' => ['array', 'max:15'],
            'faqs.*.question' => ['required', 'string', 'max:200'],
            'faqs.*.answer' => ['required', 'string', 'max:2000'],

            // SEO
            'seo_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string', 'max:320'],
            'meta_keywords' => ['nullable', 'string', 'max:255'],
            'canonical_url' => ['nullable', 'url:http,https', 'max:255'],
            'og_title' => ['nullable', 'string', 'max:255'],
            'og_description' => ['nullable', 'string', 'max:320'],
            'og_image_file' => $image,
            'remove_og_image' => ['boolean'],
            'robots_index' => ['boolean'],
            'robots_follow' => ['boolean'],
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
            'slug.regex' => 'Use lowercase letters, numbers and single hyphens only (e.g. ocd-therapy).',
            'slug.unique' => 'Another service already uses this URL.',
            'approach.*.title.required' => 'Each step needs a title.',
            'approach.*.text.required' => 'Each step needs a description.',
            'faqs.*.question.required' => 'Each FAQ needs a question.',
            'faqs.*.answer.required' => 'Each FAQ needs an answer.',
            'canonical_url.url' => 'Enter a full URL starting with https://',
            '*.image' => 'Upload a JPG, PNG or WebP image.',
            '*.max' => 'This file is too large (max 3 MB).',
        ];
    }

    /**
     * Validated data ready to save (images handled by the controller).
     *
     * @return array<string, mixed>
     */
    public function serviceData(): array
    {
        return collect($this->validated())
            ->except(['hero_image_file', 'og_image_file', 'remove_hero_image', 'remove_og_image'])
            ->all();
    }
}
