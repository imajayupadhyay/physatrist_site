<?php

namespace App\Http\Requests\Admin;

use App\Models\MenuItem;
use App\Rules\SafeLink;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class MenuItemRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        $this->merge([
            'label' => trim((string) $this->input('label')) ?: null,
            'url' => trim((string) $this->input('url')) ?: null,
            'description' => trim((string) $this->input('description')) ?: null,
            'parent_id' => $this->input('parent_id') ?: null,
            'open_in_new_tab' => $this->boolean('open_in_new_tab'),
            'is_visible' => $this->boolean('is_visible', true),
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
            'type' => ['required', Rule::in([MenuItem::TYPE_LINK, MenuItem::TYPE_SERVICE])],
            'label' => ['nullable', 'required_if:type,link', 'string', 'max:60'],
            'url' => ['nullable', 'required_if:type,link', 'string', 'max:255', new SafeLink],
            'service_id' => ['nullable', 'required_if:type,service', 'exists:services,id'],
            'parent_id' => ['nullable', 'integer', Rule::exists('menu_items', 'id')->whereNull('parent_id')],
            'description' => ['nullable', 'string', 'max:160'],
            'dropdown_style' => ['required', Rule::in(MenuItem::STYLES)],
            'open_in_new_tab' => ['boolean'],
            'is_visible' => ['boolean'],
        ];
    }

    /**
     * Menus are two levels deep: an item with sub-items can't itself become a sub-item.
     *
     * @return array<int, callable>
     */
    public function after(): array
    {
        return [
            function (Validator $validator) {
                $item = $this->route('menuItem');
                $parentId = $this->input('parent_id');

                if ($item && $parentId && ((int) $parentId === $item->id || $item->children()->exists())) {
                    $validator->errors()->add('parent_id', 'An item with sub-items can only be a top-level item.');
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
            'label.required_if' => 'Enter the text shown in the menu.',
            'url.required_if' => 'Enter where this link goes.',
            'service_id.required_if' => 'Choose a service page.',
            'parent_id.exists' => 'Choose a top-level menu item.',
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function itemData(): array
    {
        $data = $this->validated();

        // Keep only the fields that apply to the chosen type.
        if ($data['type'] === MenuItem::TYPE_LINK) {
            $data['service_id'] = null;
        } else {
            $data['url'] = null;
        }

        return $data;
    }
}
