@extends('backend.layouts.app')

@section('content')

<div class="aiz-titlebar text-left mt-2 mb-3">
    <div class="row align-items-center">
        <div class="col-md-6">
            <h1 class="h3">{{ translate('Sliders & Banners') }}</h1>
        </div>
    </div>
</div>

<div class="row">
    <!-- Left Column: Edit Form or Add Form -->
    <div class="col-lg-4">
        @if(isset($slider))
        <!-- Edit Form -->
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0 h6">{{ translate('Edit Banner') }} #{{ $slider->id }}</h5>
                <a href="{{ route('sliders.admin.index') }}" class="btn btn-sm btn-soft-secondary">{{ translate('Cancel') }}</a>
            </div>
            <div class="card-body">
                <form action="{{ route('sliders.admin.update', $slider->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="form-group">
                        <label class="font-weight-bold">{{ translate('Banner Position / Type') }}</label>
                        <select name="type" class="form-control aiz-selectpicker">
                            <option value="main" {{ old('type', $slider->type ?? 'main') == 'main' ? 'selected' : '' }}>{{ translate('Main Hero Slider (Left / Carousel)') }}</option>
                            <option value="side" {{ old('type', $slider->type ?? 'main') == 'side' ? 'selected' : '' }}>{{ translate('Right Mini Banner (Side Banner)') }}</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>{{ translate('Title (optional)') }}</label>
                        <input type="text" name="title" class="form-control" value="{{ old('title', $slider->title) }}" placeholder="{{ translate('Leave empty if title is inside image') }}">
                    </div>
                    <div class="form-group">
                        <label>{{ translate('Description / Subtitle (optional)') }}</label>
                        <textarea name="description" class="form-control" rows="2" placeholder="{{ translate('Leave empty if text is inside image') }}">{{ old('description', $slider->description) }}</textarea>
                    </div>
                    <div class="form-group">
                        <label>{{ translate('Badge Tag (optional)') }}</label>
                        <input type="text" name="badge" class="form-control" value="{{ old('badge', $slider->badge) }}" placeholder="e.g. FRESH, 20% OFF, SPECIAL">
                    </div>
                    <div class="form-group">
                        <label>{{ translate('Link URL') }}</label>
                        <input type="text" name="url" class="form-control" value="{{ old('url', $slider->link) }}" placeholder="e.g. /products-list">
                    </div>
                    <div class="form-group">
                        <label>{{ translate('Button Text (optional)') }}</label>
                        <input type="text" name="button_text" class="form-control" value="{{ old('button_text', $slider->button_text) }}" placeholder="e.g. Order Now, Shop Now">
                    </div>
                    <div class="form-group">
                        <label>{{ translate('Published Status') }}</label>
                        <select name="published" class="form-control aiz-selectpicker">
                            <option value="1" {{ old('published', $slider->published) == 1 ? 'selected' : '' }}>{{ translate('Published') }}</option>
                            <option value="0" {{ old('published', $slider->published) == 0 ? 'selected' : '' }}>{{ translate('Unpublished') }}</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>{{ translate('Current Photo') }}</label>
                        <div class="p-2 border rounded text-center bg-light">
                            @if($slider->photo)
                                <img src="{{ my_asset($slider->photo) }}" alt="slider" class="img-fluid rounded" style="max-height:120px; object-fit:contain;">
                            @else
                                <span class="text-muted">{{ translate('No photo uploaded') }}</span>
                            @endif
                        </div>
                    </div>
                    <div class="form-group">
                        <label>{{ translate('Replace Photo (optional)') }} <small class="text-muted">({{ translate('Recommended: 1200x400px for main, 600x300px for side') }})</small></label>
                        <div class="input-group" data-toggle="aizuploader" data-type="image">
                            <div class="input-group-prepend">
                                <div class="input-group-text bg-soft-secondary font-weight-medium">
                                    {{ translate('Browse') }}
                                </div>
                            </div>
                            <div class="form-control file-amount">{{ translate('Choose File') }}</div>
                            <input type="hidden" name="photos[]" class="selected-files">
                        </div>
                        <div class="file-preview box sm"></div>
                    </div>
                    <div class="d-flex justify-content-end">
                        <a href="{{ route('sliders.admin.index') }}" class="btn btn-light mr-2">{{ translate('Cancel') }}</a>
                        <button type="submit" class="btn btn-primary">{{ translate('Update Banner') }}</button>
                    </div>
                </form>
            </div>
        </div>

        @else

        <!-- Add New Form -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0 h6">{{ translate('Add New Banner / Slider') }}</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('sliders.admin.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="form-group">
                        <label class="font-weight-bold">{{ translate('Banner Position / Type') }}</label>
                        <select name="type" class="form-control aiz-selectpicker">
                            <option value="main">{{ translate('Main Hero Slider (Left / Carousel)') }}</option>
                            <option value="side">{{ translate('Right Mini Banner (Side Banner)') }}</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>{{ translate('Title (optional)') }}</label>
                        <input type="text" name="title" class="form-control" placeholder="{{ translate('Leave empty if title is inside image') }}">
                    </div>
                    <div class="form-group">
                        <label>{{ translate('Description / Subtitle (optional)') }}</label>
                        <textarea name="description" class="form-control" rows="2" placeholder="{{ translate('Leave empty if text is inside image') }}"></textarea>
                    </div>
                    <div class="form-group">
                        <label>{{ translate('Badge Tag (optional)') }}</label>
                        <input type="text" name="badge" class="form-control" placeholder="e.g. FRESH, 20% OFF, SPECIAL">
                    </div>
                    <div class="form-group">
                        <label>{{ translate('Link URL (optional)') }}</label>
                        <input type="text" name="url" class="form-control" value="" placeholder="e.g. /products-list">
                    </div>
                    <div class="form-group">
                        <label>{{ translate('Button Text (optional)') }}</label>
                        <input type="text" name="button_text" class="form-control" value="" placeholder="e.g. Order Now, Shop Now">
                    </div>
                    <div class="form-group">
                        <label>{{ translate('Photo') }} <small class="text-muted">({{ translate('Recommended: 1200x400px for main, 600x300px for side') }})</small></label>
                        <div class="input-group" data-toggle="aizuploader" data-type="image" data-multiple="true">
                            <div class="input-group-prepend">
                                <div class="input-group-text bg-soft-secondary font-weight-medium">
                                    {{ translate('Browse') }}
                                </div>
                            </div>
                            <div class="form-control file-amount">{{ translate('Choose File') }}</div>
                            <input type="hidden" name="photos[]" class="selected-files">
                        </div>
                        <div class="file-preview box sm"></div>
                    </div>
                    <div class="text-right">
                        <button type="submit" class="btn btn-primary">
                            {{ translate('Save') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
        @endif
    </div>

    <!-- Right Column: Sliders List -->
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0 h6">{{ translate('All Sliders & Banners') }}</h5>
            </div>
            <div class="card-body">
                @if($sliders->count() > 0)
                <div class="row">
                    @foreach($sliders as $sliderItem)
                    <div class="col-md-6 mb-4">
                        <div class="card border shadow-none rounded overflow-hidden">
                            <!-- Slider Image + Badges -->
                            <div style="height:150px; overflow:hidden; background:#f8f9fa;" class="position-relative d-flex align-items-center justify-content-center p-1">
                                @if($sliderItem->photo)
                                    <img src="{{ my_asset($sliderItem->photo) }}"
                                         alt="{{ $sliderItem->title }}"
                                         class="w-100 h-100"
                                         style="object-fit:cover;">
                                @else
                                    <div class="text-muted">
                                        <i class="las la-image" style="font-size:2rem;"></i>
                                    </div>
                                @endif

                                <!-- Position Badge -->
                                <span class="badge {{ ($sliderItem->type ?? 'main') == 'side' ? 'badge-info' : 'badge-primary' }} position-absolute" style="top:8px; left:8px; z-index:2;">
                                    {{ ($sliderItem->type ?? 'main') == 'side' ? translate('Right Mini Banner') : translate('Main Slider') }}
                                </span>

                                @if(!empty($sliderItem->badge))
                                <span class="badge badge-success position-absolute" style="top:8px; right:8px; z-index:2;">
                                    {{ $sliderItem->badge }}
                                </span>
                                @endif
                            </div>

                            <div class="card-body p-3">
                                @if($sliderItem->title)
                                <p class="mb-1 font-weight-bold small text-truncate">{{ $sliderItem->title }}</p>
                                @else
                                <p class="mb-1 text-muted small italic">({{ translate('No Title - Clean Banner') }})</p>
                                @endif

                                @if($sliderItem->description)
                                <p class="mb-1 text-muted text-truncate" style="font-size: 11px;">{{ $sliderItem->description }}</p>
                                @endif

                                <p class="mb-3 text-muted small text-truncate">
                                    <i class="las la-link"></i> {{ $sliderItem->link ?? '/' }}
                                </p>

                                <!-- Publish Toggle & Actions -->
                                <div class="d-flex align-items-center justify-content-between pt-2 border-top">
                                    <label class="aiz-switch aiz-switch-success mb-0" title="{{ translate('Toggle Published Status') }}">
                                        <input
                                            type="checkbox"
                                            onchange="update_slider_status(this)"
                                            value="{{ $sliderItem->id }}"
                                            {{ $sliderItem->published ? 'checked' : '' }}
                                        >
                                        <span></span>
                                    </label>
                                    <div>
                                        <a href="{{ route('sliders.admin.edit', $sliderItem->id) }}"
                                           class="btn btn-icon btn-circle btn-sm btn-soft-primary"
                                           title="{{ translate('Edit banner') }}">
                                            <i class="las la-edit"></i>
                                        </a>
                                        <a href="{{ route('sliders.admin.destroy', $sliderItem->id) }}"
                                           class="btn btn-icon btn-circle btn-sm btn-soft-danger ml-1"
                                           onclick="return confirm('{{ translate('Are you sure you want to delete this banner?') }}')"
                                           title="{{ translate('Delete banner') }}">
                                            <i class="las la-trash"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <div class="text-center py-5 text-muted">
                    <i class="las la-image" style="font-size:3rem;"></i>
                    <p class="mt-2">{{ translate('No sliders or banners found. Add your first banner!') }}</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

@endsection

@section('script')
<script type="text/javascript">
    function update_slider_status(el) {
        $.post('{{ route('sliders.admin.update', ':id') }}'.replace(':id', el.value), {
            _token: '{{ csrf_token() }}',
            status: el.checked ? 1 : 0
        }).done(function(res) {
            if (res == 1) {
                AIZ.plugins.notify('success', '{{ translate('Slider status updated') }}');
            } else {
                AIZ.plugins.notify('danger', '{{ translate('Something went wrong') }}');
            }
        });
    }
</script>
@endsection
