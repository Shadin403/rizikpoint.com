<?php
$path = __DIR__."/app/Http/Controllers/AizUploadController.php";
$bak  = __DIR__."/app/Http/Controllers/AizUploadController.php.bak";
if (!file_exists($bak)) copy($path, $bak);

$content = file_get_contents($path);

// 1) Update paginate line to support per_page
$oldPaginate = "        \$all_uploads = \$all_uploads->paginate(60)->appends(request()->query());";
$newPaginate = "        \$perPage = in_array((int) \$request->per_page, [30, 50, 100, 200, 500, 1000]) ? (int) \$request->per_page : 30;\n        \$all_uploads = \$all_uploads->paginate(\$perPage)->appends(request()->query());";

if (strpos($content, $oldPaginate) !== false) {
    $content = str_replace($oldPaginate, $newPaginate, $content);
    echo "Patched paginate line.\n";
} else {
    echo "WARN: paginate line not found.\n";
}

// 2) Pass perPage to view
$oldCompact = "            : view('backend.uploaded_files.index', compact('all_uploads', 'search', 'sort_by'));";
$newCompact = "            : view('backend.uploaded_files.index', compact('all_uploads', 'search', 'sort_by', 'perPage'));";
if (strpos($content, $oldCompact) !== false) {
    $content = str_replace($oldCompact, $newCompact, $content);
    echo "Patched compact line.\n";
}

// 3) Insert bulk_destroy method before get_preview_files
$bulkMethod = <<<'PHP'

    public function bulk_destroy(Request $request)
    {
        if (!$request->has('id') || !is_array($request->id) || count($request->id) == 0) {
            flash(translate('Please select files to delete.'))->warning();
            return back();
        }

        $ids = array_map('intval', $request->id);
        $uploadsQuery = Upload::whereIn('id', $ids);
        if (auth()->user()->user_type == 'seller') {
            $uploadsQuery = $uploadsQuery->where('user_id', auth()->user()->id);
        }
        $uploads = $uploadsQuery->get();

        $deleted = 0;
        foreach ($uploads as $upload) {
            try {
                if (env('FILESYSTEM_DRIVER') == 's3') {
                    Storage::disk('s3')->delete($upload->file_name);
                    if (file_exists(public_path().'/'.$upload->file_name)) {
                        unlink(public_path().'/'.$upload->file_name);
                    }
                } else {
                    if (file_exists(public_path().'/'.$upload->file_name)) {
                        unlink(public_path().'/'.$upload->file_name);
                    }
                }
                $upload->delete();
                $deleted++;
            } catch (\Exception $e) {
                continue;
            }
        }

        flash(translate($deleted . ' file(s) deleted successfully'))->success();
        return back();
    }

PHP;

$marker = "    public function get_preview_files(Request $request){";
if (strpos($content, $marker) !== false && strpos($content, "public function bulk_destroy") === false) {
    $content = str_replace($marker, $bulkMethod . "\n" . $marker, $content);
    echo "Inserted bulk_destroy method.\n";
} else {
    echo "bulk_destroy marker state: ". (strpos($content, "public function bulk_destroy") === false ? "absent" : "present") ."\n";
}

file_put_contents($path, $content);
echo "Done. New size: ".filesize($path)."\n";
