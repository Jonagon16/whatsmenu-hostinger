<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Menu;

class MenuController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $menus = auth()->user()->menus()->latest()->get();
        return view('menus.index', compact('menus'));
    }

    public function create()
    {
        return view('menus.form');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'tree' => 'required|array', // Validated as JSON array in frontend
            'is_active' => 'boolean',
        ]);

        $menu = auth()->user()->menus()->create($validated);

        if ($request->input('is_active')) {
            auth()->user()->menus()->where('id', '!=', $menu->id)->update(['is_active' => false]);
        }

        return redirect()->route('menus.index')->with('success', 'Menu created successfully.');
    }

    public function edit(Menu $menu)
    {
        // Ensure user owns the menu
        if ($menu->user_id !== auth()->id()) {
            abort(403);
        }
        return view('menus.form', compact('menu'));
    }

    public function update(Request $request, Menu $menu)
    {
        if ($menu->user_id !== auth()->id()) {
            abort(403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'tree' => 'required|array',
            'is_active' => 'boolean',
        ]);

        $menu->update($validated);

        if ($request->input('is_active')) {
            auth()->user()->menus()->where('id', '!=', $menu->id)->update(['is_active' => false]);
        }

        return redirect()->route('menus.index')->with('success', 'Menu updated successfully.');
    }

    public function destroy(Menu $menu)
    {
        if ($menu->user_id !== auth()->id()) {
            abort(403);
        }

        $menu->delete();
        return redirect()->route('menus.index')->with('success', 'Menu deleted successfully.');
    }
}
