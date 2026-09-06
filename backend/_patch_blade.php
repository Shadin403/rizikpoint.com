<?php
$path = __DIR__."/resources/views/backend/uploaded_files/index.blade.php";
$content = file_get_contents($path);

$old = <<<'OLD'
				<div class="aiz-file-box">
					<div class="dropdown-file" >
						<a class="dropdown-link" data-toggle="dropdown">
							<i class="la la-ellipsis-v"></i>
						</a>
						<div class="dropdown-menu dropdown-menu-right">
OLD;

$new = <<<'NEW'
				<div class="aiz-file-box" data-file-id="{{ $file->id }}" data-file-name="{{ $file_name }}.{{ $file->extension }}">
					<div class="dropdown-file" >
						<a class="dropdown-link" data-toggle="dropdown">
							<i class="la la-ellipsis-v"></i>
						</a>
						<div class="dropdown-menu dropdown-menu-right">
NEW;

if (strpos($content, $old) !== false) {
    $content = str_replace($old, $new, $content);
    echo "Patched aiz-file-box opening tag.\n";
}

$old2 = <<<'OLD2'
					<label class="card card-file aiz-uploader-select c-default file-checkbox-card" title="{{ $file_name }}.{{ $file->extension }}">
						<div class="card-file-thumb">
							@if($file->type == 'image' && $file_exists)
								<img src="{{ $file_url }}" class="img-fit">
							@elseif($file->type == 'video')
								<i class="las la-file-video"></i>
							@else
								<i class="las la-file"></i>
							@endif
						</div>
						<div class="card-body">
							<h6 class="d-flex">
								<span class="text-truncate title">{{ $file_name }}</span>
								<span class="ext">.{{ $file->extension }}</span>
							</h6>
							<p>{{ formatBytes($file->file_size) }}</p>
						</div>
						<div class="file-checkbox-overlay">
							<input type="checkbox" class="file-checkbox" value="{{ $file->id }}" data-name="{{ $file_name }}.{{ $file->extension }}">
						</div>
					</label>
OLD2;

$new2 = <<<'NEW2'
					<div class="card card-file aiz-uploader-select c-default file-checkbox-card" title="{{ $file_name }}.{{ $file->extension }}">
						<div class="card-file-thumb">
							@if($file->type == 'image' && $file_exists)
								<img src="{{ $file_url }}" class="img-fit">
							@elseif($file->type == 'video')
								<i class="las la-file-video"></i>
							@else
								<i class="las la-file"></i>
							@endif
						</div>
						<div class="card-body">
							<h6 class="d-flex">
								<span class="text-truncate title">{{ $file_name }}</span>
								<span class="ext">.{{ $file->extension }}</span>
							</h6>
							<p>{{ formatBytes($file->file_size) }}</p>
						</div>
					</div>
					<div class="file-checkbox-overlay">
						<input type="checkbox" class="file-checkbox" value="{{ $file->id }}" data-name="{{ $file_name }}.{{ $file->extension }}" autocomplete="off">
					</div>
NEW2;

if (strpos($content, $old2) !== false) {
    $content = str_replace($old2, $new2, $content);
    echo "Patched card markup - moved checkbox outside label.\n";
}

file_put_contents($path, $content);
echo "New size: ".filesize($path)."\n";
