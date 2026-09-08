<?php

namespace App\Http\Controllers\Api\V2;

use App\Http\Resources\V2\SliderCollection;
use App\Models\Slider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SliderController extends Controller
{
    public function index()
    {
        return new SliderCollection(Slider::where('published', 1)->latest()->get());
    }

    public function all()
    {
        return new SliderCollection(Slider::latest()->get());
    }

    public function store(Request $request)
    {
        $request->validate([
            'title'       => 'nullable|string|max:255',
            'description' => 'nullable|string|max:1000',
            'link'        => 'nullable|string|max:500',
            'button_text' => 'nullable|string|max:100',
            'badge'       => 'nullable|string|max:50',
            'type'        => 'nullable|string|max:50',
            'photo'       => 'nullable|image|max:4096',
        ]);

        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('uploads/sliders', 'public');
        }

        $slider = Slider::create([
            'title'       => $request->input('title'),
            'description' => $request->input('description'),
            'link'        => $request->input('link', '/products-list'),
            'button_text' => $request->input('button_text'),
            'badge'       => $request->input('badge'),
            'type'        => $request->input('type', 'main'),
            'photo'       => $photoPath,
            'published'   => $request->input('published', 1),
        ]);

        return response()->json([
            'success' => true,
            'status'  => 200,
            'message' => 'Slider created successfully.',
            'data'    => $this->formatSlider($slider),
        ]);
    }

    public function update(Request $request, $id)
    {
        $slider = Slider::findOrFail($id);

        $request->validate([
            'title'       => 'nullable|string|max:255',
            'description' => 'nullable|string|max:1000',
            'link'        => 'nullable|string|max:500',
            'button_text' => 'nullable|string|max:100',
            'badge'       => 'nullable|string|max:50',
            'type'        => 'nullable|string|max:50',
            'photo'       => 'nullable|image|max:4096',
        ]);

        if ($request->hasFile('photo')) {
            if ($slider->photo && Storage::disk('public')->exists($slider->photo)) {
                Storage::disk('public')->delete($slider->photo);
            }
            $slider->photo = $request->file('photo')->store('uploads/sliders', 'public');
        }

        if ($request->has('title'))       $slider->title       = $request->input('title');
        if ($request->has('description')) $slider->description = $request->input('description');
        if ($request->has('link'))        $slider->link        = $request->input('link');
        if ($request->has('button_text')) $slider->button_text = $request->input('button_text');
        if ($request->has('badge'))       $slider->badge       = $request->input('badge');
        if ($request->has('type'))        $slider->type        = $request->input('type');
        if ($request->has('published'))   $slider->published   = (int) $request->input('published');

        $slider->save();

        return response()->json([
            'success' => true,
            'status'  => 200,
            'message' => 'Slider updated successfully.',
            'data'    => $this->formatSlider($slider),
        ]);
    }

    public function destroy($id)
    {
        $slider = Slider::findOrFail($id);

        if ($slider->photo && Storage::disk('public')->exists($slider->photo)) {
            Storage::disk('public')->delete($slider->photo);
        }

        $slider->delete();

        return response()->json([
            'success' => true,
            'status'  => 200,
            'message' => 'Slider deleted.',
        ]);
    }

    public function toggle($id)
    {
        $slider = Slider::findOrFail($id);
        $slider->published = $slider->published ? 0 : 1;
        $slider->save();

        return response()->json([
            'success'   => true,
            'status'    => 200,
            'published' => $slider->published,
            'message'   => $slider->published ? 'Slider published.' : 'Slider unpublished.',
        ]);
    }

    private function formatSlider(Slider $s): array
    {
        return [
            'id'          => $s->id,
            'title'       => $s->title,
            'description' => $s->description,
            'button_text' => $s->button_text,
            'badge'       => $s->badge,
            'type'        => $s->type ?? 'main',
            'photo'       => $s->photo,
            'link'        => $s->link,
            'published'   => (int) $s->published,
        ];
    }
}
