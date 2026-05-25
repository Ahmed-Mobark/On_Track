<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HeroBanner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BannerController extends Controller
{
    public function index()
    {
        $banners = HeroBanner::orderBy('sort_order')->get();
        return view('admin.banners.index', compact('banners'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'nullable|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'link' => 'nullable|string|max:500',
            'button_text' => 'nullable|string|max:100',
            'image' => 'required|image|mimes:jpeg,png,webp|max:5120',
        ]);

        $path = $request->file('image')->store('banners', 'public');

        HeroBanner::create([
            'title' => $request->title,
            'subtitle' => $request->subtitle,
            'image' => $path,
            'link' => $request->link,
            'button_text' => $request->button_text,
            'is_active' => true,
            'sort_order' => HeroBanner::max('sort_order') + 1,
        ]);

        return back()->with('success', 'تم إضافة البانر');
    }

    public function update(Request $request, HeroBanner $banner)
    {
        $request->validate([
            'title' => 'nullable|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'link' => 'nullable|string|max:500',
            'button_text' => 'nullable|string|max:100',
            'image' => 'nullable|image|mimes:jpeg,png,webp|max:5120',
        ]);

        $data = $request->only(['title', 'subtitle', 'link', 'button_text']);
        $data['is_active'] = $request->boolean('is_active', true);

        if ($request->hasFile('image')) {
            Storage::disk('public')->delete($banner->image);
            $data['image'] = $request->file('image')->store('banners', 'public');
        }

        $banner->update($data);
        return back()->with('success', 'تم تحديث البانر');
    }

    public function destroy(HeroBanner $banner)
    {
        Storage::disk('public')->delete($banner->image);
        $banner->delete();
        return back()->with('success', 'تم حذف البانر');
    }
}
