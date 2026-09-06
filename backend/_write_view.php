<?php
$path = __DIR__."/resources/views/backend/uploaded_files/index.blade.php";
$bak  = __DIR__."/resources/views/backend/uploaded_files/index.blade.php.bak";
if (!file_exists($bak)) copy($path, $bak);

$blade = <<<'BLADE'
@extends('backend.layouts.app')

@section('content')
<div class="aiz-titlebar text-left mt-2 mb-3">
	<div class="row align-items-center">
		<div class="col-md-6">
			<h1 class="h3">{{translate('All uploaded files')}}</h1>
		</div>
		<div class="col-md-6 text-md-right">
			<a href="{{ route('uploaded-files.create') }}" class="btn btn-primary">
				<span>{{translate('Upload New File')}}</span>
			</a>
		</div>
	</div>
</div>

<form id="bulk-action-form" action="{{ route('uploaded-files.bulk_destroy') }}" method="POST" style="display:none;">
    @csrf
</form>

<div class="card">
    <form id="sort_uploads" action="">
        <div class="card-header row gutters-5">
            <div class="col-md-3">
                <h5 class="mb-0 h6">{{translate('All files')}}</h5>
            </div>
            <div class="col-md-2">
                <select class="form-control form-control-xs aiz-selectpicker" name="per_page" onchange="sort_uploads()">
                    <option value="30"  @if($perPage == 30)  selected="" @endif>{{ translate('30 per page') }}</option>
                    <option value="50"  @if($perPage == 50)  selected="" @endif>{{ translate('50 per page') }}</option>
                    <option value="100" @if($perPage == 100) selected="" @endif>{{ translate('100 per page') }}</option>
                    <option value="200" @if($perPage == 200) selected="" @endif>{{ translate('200 per page') }}</option>
                    <option value="500" @if($perPage == 500) selected="" @endif>{{ translate('500 per page') }}</option>
                    <option value="1000"@if($perPage == 1000) selected="" @endif>{{ translate('1000 per page') }}</option>
                </select>
            </div>
            <div class="col-md-2">
                <select class="form-control form-control-xs aiz-selectpicker" name="sort" onchange="sort_uploads()">
                    <option value="newest" @if($sort_by == 'newest') selected="" @endif>{{ translate('Sort by newest') }}</option>
                    <option value="oldest" @if($sort_by == 'oldest') selected="" @endif>{{ translate('Sort by oldest') }}</option>
                    <option value="smallest" @if($sort_by == 'smallest') selected="" @endif>{{ translate('Sort by smallest') }}</option>
                    <option value="largest" @if($sort_by == 'largest') selected="" @endif>{{ translate('Sort by largest') }}</option>
                </select>
            </div>
            <div class="col-md-3">
                <input type="text" class="form-control form-control-xs" name="search" placeholder="{{ translate('Search your files') }}" value="{{ $search }}">
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-primary">{{ translate('Search') }}</button>
            </div>
        </div>
    </form>

    <div class="card-header row gutters-5 bulk-actions-bar" id="bulk-actions-bar" style="display:none;">
        <div class="col-md-6">
            <span class="selected-count">{{ translate('Selected') }}: <strong id="selected-count">0</strong></span>
        </div>
        <div class="col-md-6 text-md-right">
            <button type="button" class="btn btn-danger btn-sm" id="bulk-delete-btn">
                <i class="las la-trash mr-2"></i>{{ translate('Delete Selected') }}
            </button>
            <button type="button" class="btn btn-light btn-sm" id="bulk-clear-btn">
                {{ translate('Clear') }}
            </button>
        </div>
    </div>

    <div class="card-body">
    	<div class="row gutters-5">
    		@foreach($all_uploads as $key => $file)
    			@php
    				if($file->file_original_name == null){
    				    $file_name = translate('Unknown');
    				}else{
    					$file_name = $file->file_original_name;
	    			}
	    			$file_url = my_asset($file->file_name);
	    			$file_exists = file_exists(public_path($file->file_name));
    			@endphp
    			<div class="col-auto w-140px w-lg-220px">
    				<div class="aiz-file-box">
    					<div class="dropdown-file" >
    						<a class="dropdown-link" data-toggle="dropdown">
    							<i class="la la-ellipsis-v"></i>
    						</a>
    						<div class="dropdown-menu dropdown-menu-right">
    							<a href="javascript:void(0)" class="dropdown-item" onclick="detailsInfo(this)" data-id="{{ $file->id }}">
    								<i class="las la-info-circle mr-2"></i>
    								<span>{{ translate('Details Info') }}</span>
    							</a>
    							@if($file_exists)
    							<a href="{{ $file_url }}" target="_blank" download="{{ $file_name }}.{{ $file->extension }}" class="dropdown-item">
    								<i class="la la-download mr-2"></i>
    								<span>{{ translate('Download') }}</span>
    							</a>
    							<a href="javascript:void(0)" class="dropdown-item" onclick="copyUrl(this)" data-url="{{ $file_url }}">
    								<i class="las la-clipboard mr-2"></i>
    								<span>{{ translate('Copy Link') }}</span>
    							</a>
    							@endif
    							<a href="javascript:void(0)" class="dropdown-item confirm-alert" data-href="{{ route('uploaded-files.destroy', $file->id ) }}" data-target="#delete-modal">
    								<i class="las la-trash mr-2"></i>
    								<span>{{ translate('Delete') }}</span>
    							</a>
    						</div>
    					</div>
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
    				</div>
    			</div>
    		@endforeach
    	</div>
		<div class="aiz-pagination mt-3">
			{{ $all_uploads->appends(request()->input())->links() }}
		</div>
    </div>
</div>
@endsection
@section('modal')
<div id="delete-modal" class="modal fade">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title h6">{{ translate('Delete Confirmation') }}</h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
            </div>
            <div class="modal-body text-center">
                <p class="mt-1">{{ translate('Are you sure to delete this file?') }}</p>
                <button type="button" class="btn btn-link mt-2" data-dismiss="modal">{{ translate('Cancel') }}</button>
                <a href="" class="btn btn-primary mt-2 comfirm-link">{{ translate('Delete') }}</a>
            </div>
        </div>
    </div>
</div>
<div id="bulk-delete-modal" class="modal fade">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title h6">{{ translate('Bulk Delete Confirmation') }}</h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
            </div>
            <div class="modal-body text-center">
                <p class="mt-1" id="bulk-delete-msg">{{ translate('Are you sure to delete selected files?') }}</p>
                <button type="button" class="btn btn-link mt-2" data-dismiss="modal">{{ translate('Cancel') }}</button>
                <button type="button" class="btn btn-danger mt-2" id="bulk-delete-confirm">{{ translate('Delete All') }}</button>
            </div>
        </div>
    </div>
</div>
<div id="info-modal" class="modal fade">
	<div class="modal-dialog modal-dialog-right">
			<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title h6">{{ translate('File Info') }}</h5>
				<button type="button" class="close" data-dismiss="modal">
				</button>
			</div>
			<div class="modal-body c-scrollbar-light position-relative" id="info-modal-content">
				<div class="c-preloader text-center absolute-center">
                    <i class="las la-spinner la-spin la-3x opacity-70"></i>
                </div>
			</div>
		</div>
	</div>
</div>

@endsection
@section('script')
	<script type="text/javascript">
		function detailsInfo(e){
            $('#info-modal-content').html('<div class="c-preloader text-center absolute-center"><i class="las la-spinner la-spin la-3x opacity-70"></i></div>');
			var id = $(e).data('id')
			$('#info-modal').modal('show');
			$.post('{{ route('uploaded-files.info') }}', {_token: AIZ.data.csrf, id:id}, function(data){
                $('#info-modal-content').html(data);
			});
		}
		function copyUrl(e) {
			var url = $(e).data('url');
			var $temp = $("<input>");
		    $("body").append($temp);
		    $temp.val(url).select();
		    try {
			    document.execCommand("copy");
			    AIZ.plugins.notify('success', '{{ translate('Link copied to clipboard') }}');
			} catch (err) {
			    AIZ.plugins.notify('danger', '{{ translate('Oops, unable to copy') }}');
			}
		    $temp.remove();
		}
        function sort_uploads(el){
            $('#sort_uploads').submit();
        }

        // Bulk action logic
        (function(){
            var $form = $('#bulk-action-form');
            var $bar  = $('#bulk-actions-bar');
            var $count = $('#selected-count');

            function updateCount(){
                var n = $('.file-checkbox:checked').length;
                $count.text(n);
                if (n > 0) { $bar.show(); } else { $bar.hide(); }
            }

            $(document).on('change', '.file-checkbox', updateCount);
            $(document).on('click', function(e){
                // ignore clicks inside the dropdown-menu or label checkbox itself
                if ($(e.target).closest('.dropdown-menu').length) return;
                if ($(e.target).closest('.file-checkbox').length) return;
            });

            $('#bulk-clear-btn').on('click', function(){
                $('.file-checkbox').prop('checked', false);
                updateCount();
            });

            $('#bulk-delete-btn').on('click', function(){
                var n = $('.file-checkbox:checked').length;
                if (n === 0) {
                    AIZ.plugins.notify('warning', '{{ translate('Please select at least one file.') }}');
                    return;
                }
                $('#bulk-delete-msg').text('{{ translate('Are you sure to delete') }} ' + n + ' {{ translate('file(s)? This cannot be undone.') }}');
                $('#bulk-delete-modal').modal('show');
            });

            $('#bulk-delete-confirm').on('click', function(){
                var ids = $('.file-checkbox:checked').map(function(){ return this.value; }).get();
                if (ids.length === 0) return;

                // Remove any old hidden inputs
                $form.find('input[name="id[]"]').remove();
                ids.forEach(function(id){
                    $('<input>').attr({type:'hidden', name:'id[]', value:id}).appendTo($form);
                });

                $('#bulk-delete-confirm').prop('disabled', true).text('{{ translate('Deleting...') }}');
                $form.off('submit').on('submit', function(e){ e.preventDefault(); });
                $form.trigger('submit');

                $.ajax({
                    url: $form.attr('action'),
                    method: 'POST',
                    data: $form.serialize(),
                    headers: { 'X-CSRF-TOKEN': AIZ.data.csrf },
                    success: function(){
                        AIZ.plugins.notify('success', '{{ translate('Selected files deleted.') }}');
                        setTimeout(function(){ location.reload(); }, 800);
                    },
                    error: function(xhr){
                        AIZ.plugins.notify('danger', '{{ translate('Delete failed. Please try again.') }}');
                        $('#bulk-delete-confirm').prop('disabled', false).text('{{ translate('Delete All') }}');
                    }
                });
            });
        })();
	</script>
@endsection
BLADE;

file_put_contents($path, $blade);
echo "Wrote blade. Size: ".filesize($path)."\n";
