<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Slider;
use App\Models\Upload;

class SliderController extends Controller
{
    public function index()
    {
        $sliders = Slider::latest()->get();
        return view('sliders.index', compact('sliders'));
    }

    public function create()
    {
        return view('sliders.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'photos'      => 'required',
            'url'         => 'nullable|string|max:255',
            'title'       => 'nullable|string|max:255',
            'button_text' => 'nullable|string|max:100',
        ]);

        $ids = $request->input('photos');
        if (is_array($ids)) {
            $ids = $request->input('photos.0');
        }

        $ids = is_string($ids) ? $ids : '';
        $idList = collect(explode(',', $ids))
            ->map(fn($v) => (int) trim($v))
            ->filter(fn($v) => $v > 0)
            ->values();

        if ($idList->isEmpty()) {
            flash(translate('Please select at least one photo'))->error();
            return redirect()->route('sliders.admin.index');
        }

        $uploads = Upload::whereIn('id', $idList)->where('type', 'image')->get();

        if ($uploads->isEmpty()) {
            flash(translate('Please select at least one photo'))->error();
            return redirect()->route('sliders.admin.index');
        }

        $buttonText = $request->input('button_text');
        if (is_string($buttonText)) {
            $buttonText = trim($buttonText);
            if ($buttonText === '') {
                $buttonText = null;
            }
        }

        foreach ($uploads as $upload) {
            $slider = new Slider;
            $slider->title       = $request->input('title');
            $slider->link        = $request->input('url');
            $slider->button_text = $buttonText;
            $slider->photo       = $upload->file_name;
            $slider->published   = 1;
            $slider->save();
        }

        flash(translate('Slider has been inserted successfully'))->success();
        return redirect()->route('sliders.admin.index');
    }

    public function show($id)
    {
        //
    }

    public function edit($id)
    {
        $slider = Slider::findOrFail($id);
        $sliders = Slider::latest()->get();
        return view('sliders.index', compact('slider', 'sliders'));
    }

    /**
     * Update a slider.
     * Supports two request styles:
     *   - JSON status toggle (no fields other than _token + status) -> toggles published.
     *   - Full form update (title, link, button_text, published, optional photo) -> saves all.
     * The "photo" field can be either an uploaded file OR an aiz-uploader hidden id
     * (the form posts `photos[]` as comma-separated Upload ids, like in store()).
     */
    public function update(Request $request, $id)
    {
        $slider = Slider::findOrFail($id);

        // Detect "full edit" form vs. status-only toggle.
        $isFullUpdate = $request->hasFile('photo')
            || $request->has('photos')
            || $request->has('title')
            || $request->has('url')
            || $request->has('button_text');

        if (!$isFullUpdate) {
            // status-only toggle
            $slider->published = (int) $request->input('status', $slider->published);
            if ($slider->save()) {
                return '1';
            }
            return '0';
        }

        $request->validate([
            'photos'      => 'nullable',
            'url'         => 'nullable|string|max:255',
            'title'       => 'nullable|string|max:255',
            'button_text' => 'nullable|string|max:100',
            'photo'       => 'nullable|image|max:4096',
        ]);

        if ($request->filled('title'))   $slider->title   = $request->input('title');
        if ($request->has('url'))        $slider->link    = $request->input('url');
        if ($request->has('button_text')) {
            $btn = $request->input('button_text');
            $slider->button_text = is_string($btn) ? trim($btn) : $btn;
            if ($slider->button_text === '') {
                $slider->button_text = null;
            }
        }
        if ($request->has('published')) {
            $slider->published = (int) $request->input('published', 1);
        }

        // Photo replacement: file upload takes priority, otherwise aiz-uploader id.
        if ($request->hasFile('photo')) {
            if ($slider->photo && Storage::disk('public')->exists($slider->photo)) {
                Storage::disk('public')->delete($slider->photo);
            }
            $slider->photo = $request->file('photo')->store('uploads/sliders', 'public');
        } else {
            $ids = $request->input('photos');
            if (is_array($ids)) {
                $ids = $request->input('photos.0');
            }
            $ids = is_string($ids) ? trim($ids) : '';
            if ($ids !== '') {
                $idList = collect(explode(',', $ids))
                    ->map(fn($v) => (int) trim($v))
                    ->filter(fn($v) => $v > 0)
                    ->values();
                $upload = $idList->isEmpty()
                    ? null
                    : Upload::whereIn('id', $idList)->where('type', 'image')->first();
                if ($upload) {
                    if ($slider->photo && Storage::disk('public')->exists($slider->photo)) {
                        Storage::disk('public')->delete($slider->photo);
                    }
                    $slider->photo = $upload->file_name;
                }
            }
        }

        if ($slider->save()) {
            flash(translate('Slider has been updated successfully'))->success();
        } else {
            flash(translate('Something went wrong'))->error();
        }

        return redirect()->route('sliders.admin.index');
    }

    public function destroy($id)
    {
        $slider = Slider::findOrFail($id);
        if (Slider::destroy($id)) {
            flash(translate('Slider has been deleted successfully'))->success();
        } else {
            flash(translate('Something went wrong'))->error();
        }
        return redirect()->route('sliders.admin.index');
    }
}
