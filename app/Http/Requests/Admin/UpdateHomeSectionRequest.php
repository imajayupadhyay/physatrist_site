<?php

namespace App\Http\Requests\Admin;

use App\Rules\SafeLink;
use App\Support\HomeContent;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

/**
 * One homepage section at a time (route parameter {section}).
 */
class UpdateHomeSectionRequest extends FormRequest
{
    private function section(): string
    {
        return (string) $this->route('section');
    }

    /**
     * Trim text, cast switches, drop empty list rows.
     */
    protected function prepareForValidation(): void
    {
        $data = $this->all();

        $clean = function ($value) use (&$clean) {
            if (is_string($value)) {
                return trim($value);
            }
            if (is_array($value)) {
                return array_map($clean, $value);
            }

            return $value;
        };
        $data = $clean($data);

        // Lists of plain strings: drop blanks.
        foreach (['roles', 'credentials', 'who_items'] as $key) {
            if (isset($data[$key]) && is_array($data[$key])) {
                $data[$key] = array_values(array_filter($data[$key], fn ($v) => $v !== '' && $v !== null));
            }
        }

        // Lists of records without images: drop rows where every field is empty.
        // (Image lists — slides, photos, reels — are not filtered, so row numbers keep matching their uploads.)
        foreach (['types', 'process', 'items'] as $key) {
            if (isset($data[$key]) && is_array($data[$key])) {
                $data[$key] = array_values(array_filter($data[$key], fn ($row) => is_array($row) && collect($row)
                    ->except(['rating'])
                    ->contains(fn ($v) => $v !== '' && $v !== null && $v !== false)));
            }
        }

        foreach (['visible', 'og_image_remove'] as $key) {
            if (array_key_exists($key, $data)) {
                $data[$key] = filter_var($data[$key], FILTER_VALIDATE_BOOLEAN);
            }
        }
        if (isset($data['card']) && is_array($data['card'])) {
            $data['card']['visible'] = filter_var($data['card']['visible'] ?? false, FILTER_VALIDATE_BOOLEAN);
        }

        $this->replace($data);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $text = fn (int $max, bool $required = false) => [$required ? 'required' : 'nullable', 'string', "max:{$max}"];
        $link = fn (bool $required = false) => [$required ? 'required' : 'nullable', 'string', 'max:255', new SafeLink];
        $image = ['required', 'string', 'max:255', 'regex:#^/(images|storage)/[A-Za-z0-9._/\-]+$#'];
        $upload = ['nullable', 'file', 'mimes:jpg,jpeg,png,webp', 'max:4096'];
        $visible = ['visible' => ['boolean']];

        return match ($this->section()) {
            'hero' => [
                ...$visible,
                'interval' => ['required', 'numeric', 'min:3', 'max:20'],
                'slides' => ['required', 'array', 'min:1', 'max:6'],
                'slides.*.image' => $image,
                'slides.*.image_file' => $upload,
                'slides.*.alt' => $text(150, true),
                'slides.*.eyebrow' => $text(80),
                'slides.*.title' => $text(80, true),
                'slides.*.title_em' => $text(80),
                'slides.*.text' => $text(300),
                'slides.*.primary_label' => $text(40),
                'slides.*.primary_url' => [...$link(), 'required_with:slides.*.primary_label'],
                'slides.*.secondary_label' => $text(40),
                'slides.*.secondary_url' => [...$link(), 'required_with:slides.*.secondary_label'],
                'card' => ['required', 'array'],
                'card.visible' => ['boolean'],
                'card.photo' => $image,
                'card.photo_file' => $upload,
                'card.label' => $text(40),
                'card.name' => $text(80, true),
                'card.details' => $text(200),
                'card.badge' => $text(60),
                'card.link_label' => $text(40),
                'card.link_url' => [...$link(), 'required_with:card.link_label'],
            ],
            'trust' => [
                ...$visible,
                'items' => ['required', 'array', 'min:1', 'max:6'],
                'items.*.value' => $text(20, true),
                'items.*.label' => $text(60, true),
            ],
            'about' => [
                ...$visible,
                'image' => $image,
                'image_file' => $upload,
                'image_alt' => $text(150, true),
                'badge_title' => $text(40),
                'badge_text' => $text(100),
                'eyebrow' => $text(80),
                'heading' => $text(100, true),
                'roles' => ['array', 'max:4'],
                'roles.*' => $text(80),
                'body' => $text(3000),
                'quote' => $text(300),
                'credentials' => ['array', 'max:10'],
                'credentials.*' => $text(100),
                'primary_label' => $text(40),
                'primary_url' => [...$link(), 'required_with:primary_label'],
                'secondary_label' => $text(40),
                'secondary_url' => $link(),
            ],
            'assessment' => [
                ...$visible,
                'eyebrow' => $text(80),
                'heading' => $text(120, true),
                'lead' => $text(600),
                'types' => ['array', 'max:12'],
                'types.*.title' => $text(80, true),
                'types.*.text' => $text(300, true),
                'types.*.for' => $text(80),
                'process_heading' => $text(120),
                'process' => ['array', 'max:8'],
                'process.*.title' => $text(80, true),
                'process.*.text' => $text(300, true),
                'who_heading' => $text(120),
                'who_items' => ['array', 'max:10'],
                'who_items.*' => $text(200),
                'note' => $text(300),
                'primary_label' => $text(40, true),
                'whatsapp_label' => $text(40),
                'whatsapp_message' => $text(300),
            ],
            'steps' => [
                ...$visible,
                'eyebrow' => $text(80),
                'heading' => $text(120, true),
                'lead' => $text(300),
                'items' => ['required', 'array', 'min:1', 'max:6'],
                'items.*.title' => $text(80, true),
                'items.*.text' => $text(300, true),
            ],
            'space' => [
                ...$visible,
                'eyebrow' => $text(80),
                'heading' => $text(120, true),
                'lead' => $text(300),
                'photos' => ['required', 'array', 'size:4'],
                'photos.*.image' => $image,
                'photos.*.image_file' => $upload,
                'photos.*.alt' => $text(150, true),
                'photos.*.caption' => $text(60),
            ],
            'videos' => [
                ...$visible,
                'eyebrow' => $text(80),
                'heading' => $text(120, true),
                'lead' => $text(300),
                'channel_name' => $text(60, true),
                'channel_subtitle' => $text(80),
                'channel_image' => $image,
                'channel_image_file' => $upload,
                'reels' => ['array', 'max:6'],
                'reels.*.platform' => ['required', Rule::in(['youtube', 'instagram'])],
                'reels.*.video' => $text(255, true),
                'reels.*.image' => $image,
                'reels.*.image_file' => $upload,
                'reels.*.cover' => $text(40, true),
                'reels.*.cover_em' => $text(40),
                'reels.*.topic' => $text(40),
                'reels.*.title' => $text(80, true),
            ],
            'testimonials' => [
                ...$visible,
                'eyebrow' => $text(80),
                'heading' => $text(120, true),
                'interval' => ['required', 'numeric', 'min:3', 'max:30'],
                'items' => ['required', 'array', 'min:1', 'max:20'],
                'items.*.quote' => $text(600, true),
                'items.*.name' => $text(60, true),
                'items.*.service' => $text(60),
                'items.*.rating' => ['required', 'integer', 'min:1', 'max:5'],
            ],
            'faq' => [
                ...$visible,
                'eyebrow' => $text(80),
                'heading' => $text(120, true),
                'lead' => $text(300),
                'items' => ['required', 'array', 'min:1', 'max:30'],
                'items.*.question' => $text(200, true),
                'items.*.answer' => $text(2000, true),
            ],
            'seo' => [
                'title' => $text(255, true),
                'description' => $text(320, true),
                'keywords' => $text(255),
                'og_title' => $text(255),
                'og_description' => $text(320),
                'og_image' => ['nullable', 'string', 'max:255', 'regex:#^/(images|storage)/[A-Za-z0-9._/\-]+$#'],
                'og_image_file' => $upload,
                'og_image_remove' => ['boolean'],
            ],
            default => abort(404),
        };
    }

    /**
     * Video links must point at a real YouTube video / Instagram post.
     *
     * @return array<int, callable>
     */
    public function after(): array
    {
        return [
            function (Validator $validator) {
                if ($this->section() !== 'videos') {
                    return;
                }

                foreach ((array) $this->input('reels', []) as $i => $reel) {
                    $video = (string) ($reel['video'] ?? '');

                    if (($reel['platform'] ?? '') === 'youtube' && ! HomeContent::youtubeId($video)) {
                        $validator->errors()->add("reels.{$i}.video", 'Paste a YouTube video link (or its 11-character ID).');
                    }

                    if (($reel['platform'] ?? '') === 'instagram' && ! preg_match('~^https://(www\.)?instagram\.com/(reel|p|tv)/[A-Za-z0-9_-]+/?~', $video)) {
                        $validator->errors()->add("reels.{$i}.video", 'Paste the Instagram reel or post link, e.g. https://www.instagram.com/reel/…');
                    }
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
            '*.required' => 'This field is required.',
            '*.*.*.required' => 'This field is required.',
            '*.required_with' => 'Add a link for this button, or clear the button text.',
            '*.*.*.required_with' => 'Add a link for this button, or clear the button text.',
            'photos.size' => 'This section needs exactly 4 photos.',
            '*.mimes' => 'Upload a JPG, PNG or WebP image.',
            '*.*.*.mimes' => 'Upload a JPG, PNG or WebP image.',
            '*.max' => 'Too long (or the file is over 4 MB).',
        ];
    }
}
