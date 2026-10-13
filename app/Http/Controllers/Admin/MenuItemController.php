<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\MenuItemRequest;
use App\Models\MenuItem;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

/**
 * Header menu items: create, edit, delete, reorder, show/hide, bulk-add service pages.
 */
class MenuItemController extends Controller
{
    public function store(MenuItemRequest $request): RedirectResponse
    {
        MenuItem::create($request->itemData());

        return back()->with('success', 'Menu item added.');
    }

    public function update(MenuItemRequest $request, MenuItem $menuItem): RedirectResponse
    {
        $data = $request->itemData();

        // Moving to another level → append at the end of that level.
        if ($data['parent_id'] != $menuItem->parent_id) {
            $data['sort_order'] = (int) MenuItem::where('parent_id', $data['parent_id'])->max('sort_order') + 1;
        }

        $menuItem->update($data);

        return back()->with('success', 'Menu item saved.');
    }

    public function destroy(MenuItem $menuItem): RedirectResponse
    {
        $menuItem->delete(); // sub-items are removed by the foreign key cascade

        return back()->with('success', 'Menu item deleted.');
    }

    /**
     * Move an item one place up or down among its siblings.
     */
    public function move(Request $request, MenuItem $menuItem): RedirectResponse
    {
        $request->validate(['direction' => ['required', Rule::in(['up', 'down'])]]);

        DB::transaction(function () use ($request, $menuItem) {
            $ids = MenuItem::where('parent_id', $menuItem->parent_id)->ordered()->pluck('id')->values();
            $index = $ids->search($menuItem->id);
            $target = $request->input('direction') === 'up' ? $index - 1 : $index + 1;

            if ($target < 0 || $target >= $ids->count()) {
                return;
            }

            [$ids[$index], $ids[$target]] = [$ids[$target], $ids[$index]];
            $ids->each(fn ($id, $position) => MenuItem::whereKey($id)->update(['sort_order' => $position + 1]));
        });

        return back();
    }

    public function toggle(MenuItem $menuItem): RedirectResponse
    {
        $menuItem->update(['is_visible' => ! $menuItem->is_visible]);

        return back()->with('success', $menuItem->is_visible ? 'Menu item shown.' : 'Menu item hidden.');
    }

    /**
     * Add several service pages as sub-items of a top-level item (skips ones already there).
     */
    public function addServices(Request $request, MenuItem $menuItem): RedirectResponse
    {
        abort_if($menuItem->parent_id !== null, 422, 'Service pages can only be added under a top-level item.');

        $data = $request->validate([
            'service_ids' => ['required', 'array', 'min:1'],
            'service_ids.*' => ['integer', 'exists:services,id'],
        ]);

        $existing = $menuItem->children()->where('type', MenuItem::TYPE_SERVICE)->pluck('service_id');
        $added = collect($data['service_ids'])->unique()->diff($existing);

        $added->each(fn ($serviceId) => MenuItem::create([
            'parent_id' => $menuItem->id,
            'type' => MenuItem::TYPE_SERVICE,
            'service_id' => $serviceId,
        ]));

        return back()->with('success', $added->count() === 1 ? '1 service page added.' : "{$added->count()} service pages added.");
    }
}
