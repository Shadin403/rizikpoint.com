<?php $__env->startSection('content'); ?>

<div class="aiz-titlebar text-left mt-2 mb-3">
    <div class="row align-items-center">
        <div class="col-md-6">
            <h1 class="h3"><?php echo e(translate('Homepage Sections')); ?></h1>
        </div>
    </div>
</div>

<div class="row">
    <!-- Left Column: Add / Edit Form -->
    <div class="col-lg-4">
        <?php if(isset($section)): ?>
        <!-- Edit Form -->
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0 h6"><?php echo e(translate('Edit Homepage Section')); ?> #<?php echo e($section->id); ?></h5>
                <a href="<?php echo e(route('home-sections.index')); ?>" class="btn btn-sm btn-soft-secondary"><?php echo e(translate('Cancel')); ?></a>
            </div>
            <div class="card-body">
                <form action="<?php echo e(route('home-sections.update', $section->id)); ?>" method="POST">
                    <?php echo csrf_field(); ?>
                    <div class="form-group">
                        <label class="font-weight-bold"><?php echo e(translate('Section Title')); ?> <span class="text-danger">*</span></label>
                        <input type="text" name="title" class="form-control" value="<?php echo e(old('title', $section->title)); ?>" placeholder="e.g. আপনার বাজারের জন্য বাছাই" required>
                    </div>
                    <div class="form-group">
                        <label><?php echo e(translate('Subtitle / Description')); ?></label>
                        <input type="text" name="subtitle" class="form-control" value="<?php echo e(old('subtitle', $section->subtitle)); ?>" placeholder="e.g. পছন্দের পণ্য দিয়ে পূর্ণ হোক বাজারের ঝুড়ি">
                    </div>
                    <div class="form-group">
                        <label class="font-weight-bold"><?php echo e(translate('Section Default Source')); ?></label>
                        <select name="type" class="form-control aiz-selectpicker">
                            <option value="featured" <?php echo e(old('type', $section->type) == 'featured' ? 'selected' : ''); ?>><?php echo e(translate('Featured Products (বাছাইকৃত পণ্য)')); ?></option>
                            <option value="new_arrivals" <?php echo e(old('type', $section->type) == 'new_arrivals' ? 'selected' : ''); ?>><?php echo e(translate('New Arrivals / Latest Products (নতুন পণ্য)')); ?></option>
                            <option value="category" <?php echo e(old('type', $section->type) == 'category' ? 'selected' : ''); ?>><?php echo e(translate('Specific Category Products (নির্দিষ্ট ক্যাটাগরি)')); ?></option>
                            <option value="custom" <?php echo e(old('type', $section->type) == 'custom' ? 'selected' : ''); ?>><?php echo e(translate('Custom Handpicked Products (পছন্দের কিছু প্রোডাক্ট)')); ?></option>
                        </select>
                    </div>

                    <!-- Category Selector (Optional) -->
                    <div class="form-group">
                        <label><?php echo e(translate('Select Specific Category (Optional)')); ?></label>
                        <select name="category_id" class="form-control aiz-selectpicker" data-live-search="true">
                            <option value=""><?php echo e(translate('All Categories / None')); ?></option>
                            <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($cat->id); ?>" <?php echo e(old('category_id', $section->category_id) == $cat->id ? 'selected' : ''); ?>><?php echo e($cat->name); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>

                    <!-- Custom Products Multiselect (Optional) -->
                    <div class="form-group">
                        <label class="font-weight-bold text-primary"><?php echo e(translate('Select Specific Products (পণ্য ম্যানুয়ালি নির্বাচন করুন)')); ?></label>
                        <small class="form-text text-muted mb-2"><?php echo e(translate('এখানে একাধিক পণ্য সিলেক্ট করতে পারেন। এগুলোই স্লাইডারে দেখানো হবে।')); ?></small>
                        <?php
                            $selectedIds = array_filter(array_map('intval', explode(',', $section->product_ids ?? '')));
                        ?>
                        <select name="product_ids[]" id="product_ids_select" class="form-control aiz-selectpicker" data-live-search="true" multiple data-actions-box="true" data-selected-text-format="count > 1" title="<?php echo e(translate('Select Products...')); ?>">
                            <?php $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $prod): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($prod->id); ?>" <?php echo e(in_array($prod->id, $selectedIds) ? 'selected' : ''); ?>><?php echo e($prod->name); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label><?php echo e(translate('Max Products Limit')); ?></label>
                        <input type="number" name="limit" class="form-control" value="<?php echo e(old('limit', $section->limit ?? 12)); ?>" min="1" max="50">
                    </div>
                    <div class="form-group">
                        <label><?php echo e(translate('Sort Order')); ?></label>
                        <input type="number" name="sort_order" class="form-control" value="<?php echo e(old('sort_order', $section->sort_order ?? 0)); ?>">
                    </div>
                    <div class="form-group">
                        <label><?php echo e(translate('Status')); ?></label>
                        <select name="status" class="form-control aiz-selectpicker">
                            <option value="1" <?php echo e(old('status', $section->status) == 1 ? 'selected' : ''); ?>><?php echo e(translate('Active')); ?></option>
                            <option value="0" <?php echo e(old('status', $section->status) == 0 ? 'selected' : ''); ?>><?php echo e(translate('Inactive')); ?></option>
                        </select>
                    </div>
                    <div class="d-flex justify-content-end">
                        <a href="<?php echo e(route('home-sections.index')); ?>" class="btn btn-light mr-2"><?php echo e(translate('Cancel')); ?></a>
                        <button type="submit" class="btn btn-primary"><?php echo e(translate('Update Section')); ?></button>
                    </div>
                </form>
            </div>
        </div>

        <?php else: ?>

        <!-- Add Form -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0 h6"><?php echo e(translate('Add New Homepage Section')); ?></h5>
            </div>
            <div class="card-body">
                <form action="<?php echo e(route('home-sections.store')); ?>" method="POST">
                    <?php echo csrf_field(); ?>
                    <div class="form-group">
                        <label class="font-weight-bold"><?php echo e(translate('Section Title')); ?> <span class="text-danger">*</span></label>
                        <input type="text" name="title" class="form-control" placeholder="e.g. আপনার বাজারের জন্য বাছাই" required>
                    </div>
                    <div class="form-group">
                        <label><?php echo e(translate('Subtitle / Description')); ?></label>
                        <input type="text" name="subtitle" class="form-control" placeholder="e.g. পছন্দের পণ্য দিয়ে পূর্ণ হোক বাজারের ঝুড়ি">
                    </div>
                    <div class="form-group">
                        <label class="font-weight-bold"><?php echo e(translate('Section Default Source')); ?></label>
                        <select name="type" class="form-control aiz-selectpicker">
                            <option value="featured"><?php echo e(translate('Featured Products (বাছাইকৃত পণ্য)')); ?></option>
                            <option value="new_arrivals"><?php echo e(translate('New Arrivals / Latest Products (নতুন পণ্য)')); ?></option>
                            <option value="category"><?php echo e(translate('Specific Category Products (নির্দিষ্ট ক্যাটাগরি)')); ?></option>
                            <option value="custom"><?php echo e(translate('Custom Handpicked Products (পছন্দের কিছু প্রোডাক্ট)')); ?></option>
                        </select>
                    </div>

                    <!-- Category Selector (Optional) -->
                    <div class="form-group">
                        <label><?php echo e(translate('Select Specific Category (Optional)')); ?></label>
                        <select name="category_id" class="form-control aiz-selectpicker" data-live-search="true">
                            <option value=""><?php echo e(translate('All Categories / None')); ?></option>
                            <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($cat->id); ?>"><?php echo e($cat->name); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>

                    <!-- Custom Products Multiselect (Optional) -->
                    <div class="form-group">
                        <label class="font-weight-bold text-primary"><?php echo e(translate('Select Specific Products (পণ্য ম্যানুয়ালি নির্বাচন করুন)')); ?></label>
                        <small class="form-text text-muted mb-2"><?php echo e(translate('এখানে একাধিক পণ্য সিলেক্ট করতে পারেন। এগুলোই স্লাইডারে দেখানো হবে।')); ?></small>
                        <select name="product_ids[]" id="product_ids_select_add" class="form-control aiz-selectpicker" data-live-search="true" multiple data-actions-box="true" data-selected-text-format="count > 1" title="<?php echo e(translate('Select Products...')); ?>">
                            <?php $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $prod): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($prod->id); ?>"><?php echo e($prod->name); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label><?php echo e(translate('Max Products Limit')); ?></label>
                        <input type="number" name="limit" class="form-control" value="12" min="1" max="50">
                    </div>
                    <div class="form-group">
                        <label><?php echo e(translate('Sort Order')); ?></label>
                        <input type="number" name="sort_order" class="form-control" value="0">
                    </div>
                    <div class="text-right">
                        <button type="submit" class="btn btn-primary">
                            <?php echo e(translate('Save Section')); ?>

                        </button>
                    </div>
                </form>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <!-- Right Column: Sections List -->
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0 h6"><?php echo e(translate('All Homepage Sections')); ?></h5>
            </div>
            <div class="card-body">
                <?php if($homeSections->count() > 0): ?>
                <div class="table-responsive">
                    <table class="table aiz-table mb-0">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th><?php echo e(translate('Sort')); ?></th>
                                <th><?php echo e(translate('Title & Subtitle')); ?></th>
                                <th><?php echo e(translate('Type / Source')); ?></th>
                                <th><?php echo e(translate('Limit')); ?></th>
                                <th><?php echo e(translate('Status')); ?></th>
                                <th class="text-right"><?php echo e(translate('Options')); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__currentLoopData = $homeSections; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $sec): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td><?php echo e($key + 1); ?></td>
                                <td><span class="badge badge-inline badge-soft-secondary font-weight-bold"><?php echo e($sec->sort_order); ?></span></td>
                                <td>
                                    <div class="font-weight-bold text-dark"><?php echo e($sec->title); ?></div>
                                    <?php if($sec->subtitle): ?>
                                    <small class="text-muted"><?php echo e($sec->subtitle); ?></small>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if(!empty($sec->product_ids)): ?>
                                        <span class="badge badge-inline badge-warning"><?php echo e(translate('Custom Products')); ?> (<?php echo e(count(array_filter(explode(',', $sec->product_ids)))); ?>)</span>
                                    <?php elseif($sec->type == 'featured'): ?>
                                        <span class="badge badge-inline badge-success"><?php echo e(translate('Featured Products')); ?></span>
                                    <?php elseif($sec->type == 'new_arrivals'): ?>
                                        <span class="badge badge-inline badge-primary"><?php echo e(translate('New Arrivals')); ?></span>
                                    <?php elseif($sec->type == 'category'): ?>
                                        <span class="badge badge-inline badge-info"><?php echo e(translate('Category')); ?>: <?php echo e($sec->category->name ?? 'N/A'); ?></span>
                                    <?php else: ?>
                                        <span class="badge badge-inline badge-secondary"><?php echo e(translate('Default')); ?></span>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo e($sec->limit); ?></td>
                                <td>
                                    <label class="aiz-switch aiz-switch-success mb-0">
                                        <input
                                            type="checkbox"
                                            onchange="update_section_status(this)"
                                            value="<?php echo e($sec->id); ?>"
                                            <?php echo e($sec->status ? 'checked' : ''); ?>

                                        >
                                        <span></span>
                                    </label>
                                </td>
                                <td class="text-right">
                                    <a href="<?php echo e(route('home-sections.edit', $sec->id)); ?>" class="btn btn-soft-primary btn-icon btn-circle btn-sm" title="<?php echo e(translate('Edit')); ?>">
                                        <i class="las la-edit"></i>
                                    </a>
                                    <a href="<?php echo e(route('home-sections.destroy', $sec->id)); ?>" class="btn btn-soft-danger btn-icon btn-circle btn-sm ml-1" onclick="return confirm('<?php echo e(translate('Are you sure you want to delete this section?')); ?>')" title="<?php echo e(translate('Delete')); ?>">
                                        <i class="las la-trash"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div>
                <?php else: ?>
                <div class="text-center py-5 text-muted">
                    <i class="las la-layer-group" style="font-size:3rem;"></i>
                    <p class="mt-2"><?php echo e(translate('No homepage sections found. Add your first section!')); ?></p>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('script'); ?>
<script type="text/javascript">
    $(document).ready(function() {
        if (typeof AIZ !== 'undefined' && AIZ.plugins && typeof AIZ.plugins.bootstrapSelect === 'function') {
            AIZ.plugins.bootstrapSelect('refresh');
        }
    });

    function update_section_status(el) {
        $.post('<?php echo e(route('home-sections.update', ':id')); ?>'.replace(':id', el.value), {
            _token: '<?php echo e(csrf_token()); ?>',
            status: el.checked ? 1 : 0
        }).done(function(res) {
            if (res == 1) {
                AIZ.plugins.notify('success', '<?php echo e(translate('Section status updated')); ?>');
            } else {
                AIZ.plugins.notify('danger', '<?php echo e(translate('Something went wrong')); ?>');
            }
        });
    }
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('backend.layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\laragon\www\rizikpoint\backend\resources\views/home_sections/index.blade.php ENDPATH**/ ?>