@extends('backend.layouts.app')

@section('content')

<div class="aiz-titlebar text-left mt-2 mb-3">
    <div class="row align-items-center">
        <div class="col-md-6">
            <h1 class="h3">{{ translate('Homepage Sections') }}</h1>
        </div>
    </div>
</div>

<div class="row">
    <!-- Left Column: Add / Edit Form -->
    <div class="col-lg-4">
        @if(isset($section))
        <!-- Edit Form -->
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0 h6">{{ translate('Edit Homepage Section') }} #{{ $section->id }}</h5>
                <a href="{{ route('home-sections.index') }}" class="btn btn-sm btn-soft-secondary">{{ translate('Cancel') }}</a>
            </div>
            <div class="card-body">
                <form action="{{ route('home-sections.update', $section->id) }}" method="POST">
                    @csrf
                    <div class="form-group">
                        <label class="font-weight-bold">{{ translate('Section Title') }} <span class="text-danger">*</span></label>
                        <input type="text" name="title" class="form-control" value="{{ old('title', $section->title) }}" placeholder="e.g. আপনার বাজারের জন্য বাছাই" required>
                    </div>
                    <div class="form-group">
                        <label>{{ translate('Subtitle / Description') }}</label>
                        <input type="text" name="subtitle" class="form-control" value="{{ old('subtitle', $section->subtitle) }}" placeholder="e.g. পছন্দের পণ্য দিয়ে পূর্ণ হোক বাজারের ঝুড়ি">
                    </div>
                    <div class="form-group">
                        <label class="font-weight-bold">{{ translate('Section Default Source') }}</label>
                        <select name="type" class="form-control aiz-selectpicker">
                            <option value="featured" {{ old('type', $section->type) == 'featured' ? 'selected' : '' }}>{{ translate('Featured Products (বাছাইকৃত পণ্য)') }}</option>
                            <option value="new_arrivals" {{ old('type', $section->type) == 'new_arrivals' ? 'selected' : '' }}>{{ translate('New Arrivals / Latest Products (নতুন পণ্য)') }}</option>
                            <option value="category" {{ old('type', $section->type) == 'category' ? 'selected' : '' }}>{{ translate('Specific Category Products (নির্দিষ্ট ক্যাটাগরি)') }}</option>
                            <option value="custom" {{ old('type', $section->type) == 'custom' ? 'selected' : '' }}>{{ translate('Custom Handpicked Products (পছন্দের কিছু প্রোডাক্ট)') }}</option>
                        </select>
                    </div>

                    <!-- Category Selector (Optional) -->
                    <div class="form-group">
                        <label>{{ translate('Select Specific Category (Optional)') }}</label>
                        <select name="category_id" class="form-control aiz-selectpicker" data-live-search="true">
                            <option value="">{{ translate('All Categories / None') }}</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" {{ old('category_id', $section->category_id) == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Custom Products Multiselect (Optional) -->
                    <div class="form-group">
                        <label class="font-weight-bold text-primary">{{ translate('Select Specific Products (পণ্য ম্যানুয়ালি নির্বাচন করুন)') }}</label>
                        <small class="form-text text-muted mb-2">{{ translate('এখানে একাধিক পণ্য সিলেক্ট করতে পারেন। এগুলোই স্লাইডারে দেখানো হবে।') }}</small>
                        @php
                            $selectedIds = array_filter(array_map('intval', explode(',', $section->product_ids ?? '')));
                        @endphp
                        <select name="product_ids[]" id="product_ids_select" class="form-control aiz-selectpicker" data-live-search="true" multiple data-actions-box="true" data-selected-text-format="count > 1" title="{{ translate('Select Products...') }}">
                            @foreach($products as $prod)
                                <option value="{{ $prod->id }}" {{ in_array($prod->id, $selectedIds) ? 'selected' : '' }}>{{ $prod->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label>{{ translate('Max Products Limit') }}</label>
                        <input type="number" name="limit" class="form-control" value="{{ old('limit', $section->limit ?? 12) }}" min="1" max="50">
                    </div>
                    <div class="form-group">
                        <label>{{ translate('Sort Order') }}</label>
                        <input type="number" name="sort_order" class="form-control" value="{{ old('sort_order', $section->sort_order ?? 0) }}">
                    </div>
                    <div class="form-group">
                        <label>{{ translate('Status') }}</label>
                        <select name="status" class="form-control aiz-selectpicker">
                            <option value="1" {{ old('status', $section->status) == 1 ? 'selected' : '' }}>{{ translate('Active') }}</option>
                            <option value="0" {{ old('status', $section->status) == 0 ? 'selected' : '' }}>{{ translate('Inactive') }}</option>
                        </select>
                    </div>
                    <div class="d-flex justify-content-end">
                        <a href="{{ route('home-sections.index') }}" class="btn btn-light mr-2">{{ translate('Cancel') }}</a>
                        <button type="submit" class="btn btn-primary">{{ translate('Update Section') }}</button>
                    </div>
                </form>
            </div>
        </div>

        @else

        <!-- Add Form -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0 h6">{{ translate('Add New Homepage Section') }}</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('home-sections.store') }}" method="POST">
                    @csrf
                    <div class="form-group">
                        <label class="font-weight-bold">{{ translate('Section Title') }} <span class="text-danger">*</span></label>
                        <input type="text" name="title" class="form-control" placeholder="e.g. আপনার বাজারের জন্য বাছাই" required>
                    </div>
                    <div class="form-group">
                        <label>{{ translate('Subtitle / Description') }}</label>
                        <input type="text" name="subtitle" class="form-control" placeholder="e.g. পছন্দের পণ্য দিয়ে পূর্ণ হোক বাজারের ঝুড়ি">
                    </div>
                    <div class="form-group">
                        <label class="font-weight-bold">{{ translate('Section Default Source') }}</label>
                        <select name="type" class="form-control aiz-selectpicker">
                            <option value="featured">{{ translate('Featured Products (বাছাইকৃত পণ্য)') }}</option>
                            <option value="new_arrivals">{{ translate('New Arrivals / Latest Products (নতুন পণ্য)') }}</option>
                            <option value="category">{{ translate('Specific Category Products (নির্দিষ্ট ক্যাটাগরি)') }}</option>
                            <option value="custom">{{ translate('Custom Handpicked Products (পছন্দের কিছু প্রোডাক্ট)') }}</option>
                        </select>
                    </div>

                    <!-- Category Selector (Optional) -->
                    <div class="form-group">
                        <label>{{ translate('Select Specific Category (Optional)') }}</label>
                        <select name="category_id" class="form-control aiz-selectpicker" data-live-search="true">
                            <option value="">{{ translate('All Categories / None') }}</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Custom Products Multiselect (Optional) -->
                    <div class="form-group">
                        <label class="font-weight-bold text-primary">{{ translate('Select Specific Products (পণ্য ম্যানুয়ালি নির্বাচন করুন)') }}</label>
                        <small class="form-text text-muted mb-2">{{ translate('এখানে একাধিক পণ্য সিলেক্ট করতে পারেন। এগুলোই স্লাইডারে দেখানো হবে।') }}</small>
                        <select name="product_ids[]" id="product_ids_select_add" class="form-control aiz-selectpicker" data-live-search="true" multiple data-actions-box="true" data-selected-text-format="count > 1" title="{{ translate('Select Products...') }}">
                            @foreach($products as $prod)
                                <option value="{{ $prod->id }}">{{ $prod->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label>{{ translate('Max Products Limit') }}</label>
                        <input type="number" name="limit" class="form-control" value="12" min="1" max="50">
                    </div>
                    <div class="form-group">
                        <label>{{ translate('Sort Order') }}</label>
                        <input type="number" name="sort_order" class="form-control" value="0">
                    </div>
                    <div class="text-right">
                        <button type="submit" class="btn btn-primary">
                            {{ translate('Save Section') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
        @endif
    </div>

    <!-- Right Column: Sections List -->
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0 h6">{{ translate('All Homepage Sections') }}</h5>
            </div>
            <div class="card-body">
                @if($homeSections->count() > 0)
                <div class="table-responsive">
                    <table class="table aiz-table mb-0">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>{{ translate('Sort') }}</th>
                                <th>{{ translate('Title & Subtitle') }}</th>
                                <th>{{ translate('Type / Source') }}</th>
                                <th>{{ translate('Limit') }}</th>
                                <th>{{ translate('Status') }}</th>
                                <th class="text-right">{{ translate('Options') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($homeSections as $key => $sec)
                            <tr>
                                <td>{{ $key + 1 }}</td>
                                <td><span class="badge badge-inline badge-soft-secondary font-weight-bold">{{ $sec->sort_order }}</span></td>
                                <td>
                                    <div class="font-weight-bold text-dark">{{ $sec->title }}</div>
                                    @if($sec->subtitle)
                                    <small class="text-muted">{{ $sec->subtitle }}</small>
                                    @endif
                                </td>
                                <td>
                                    @if(!empty($sec->product_ids))
                                        <span class="badge badge-inline badge-warning">{{ translate('Custom Products') }} ({{ count(array_filter(explode(',', $sec->product_ids))) }})</span>
                                    @elseif($sec->type == 'featured')
                                        <span class="badge badge-inline badge-success">{{ translate('Featured Products') }}</span>
                                    @elseif($sec->type == 'new_arrivals')
                                        <span class="badge badge-inline badge-primary">{{ translate('New Arrivals') }}</span>
                                    @elseif($sec->type == 'category')
                                        <span class="badge badge-inline badge-info">{{ translate('Category') }}: {{ $sec->category->name ?? 'N/A' }}</span>
                                    @else
                                        <span class="badge badge-inline badge-secondary">{{ translate('Default') }}</span>
                                    @endif
                                </td>
                                <td>{{ $sec->limit }}</td>
                                <td>
                                    <label class="aiz-switch aiz-switch-success mb-0">
                                        <input
                                            type="checkbox"
                                            onchange="update_section_status(this)"
                                            value="{{ $sec->id }}"
                                            {{ $sec->status ? 'checked' : '' }}
                                        >
                                        <span></span>
                                    </label>
                                </td>
                                <td class="text-right">
                                    <a href="{{ route('home-sections.edit', $sec->id) }}" class="btn btn-soft-primary btn-icon btn-circle btn-sm" title="{{ translate('Edit') }}">
                                        <i class="las la-edit"></i>
                                    </a>
                                    <a href="{{ route('home-sections.destroy', $sec->id) }}" class="btn btn-soft-danger btn-icon btn-circle btn-sm ml-1" onclick="return confirm('{{ translate('Are you sure you want to delete this section?') }}')" title="{{ translate('Delete') }}">
                                        <i class="las la-trash"></i>
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="text-center py-5 text-muted">
                    <i class="las la-layer-group" style="font-size:3rem;"></i>
                    <p class="mt-2">{{ translate('No homepage sections found. Add your first section!') }}</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

@endsection

@section('script')
<script type="text/javascript">
    $(document).ready(function() {
        if (typeof AIZ !== 'undefined' && AIZ.plugins && typeof AIZ.plugins.bootstrapSelect === 'function') {
            AIZ.plugins.bootstrapSelect('refresh');
        }
    });

    function update_section_status(el) {
        $.post('{{ route('home-sections.update', ':id') }}'.replace(':id', el.value), {
            _token: '{{ csrf_token() }}',
            status: el.checked ? 1 : 0
        }).done(function(res) {
            if (res == 1) {
                AIZ.plugins.notify('success', '{{ translate('Section status updated') }}');
            } else {
                AIZ.plugins.notify('danger', '{{ translate('Something went wrong') }}');
            }
        });
    }
</script>
@endsection
