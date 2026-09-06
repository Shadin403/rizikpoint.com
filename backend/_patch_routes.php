<?php
$path = __DIR__."/routes/admin.php";
$c = file_get_contents($path);

$insert = "\n    Route::post('/uploaded-files/bulk-destroy', 'AizUploadController@bulk_destroy')->name('uploaded-files.bulk_destroy');";

$marker = "    Route::resource('/uploaded-files', 'AizUploadController');";
if (strpos($c, "uploaded-files.bulk_destroy") === false && strpos($c, $marker) !== false) {
    $c = str_replace($marker, $marker . $insert, $c);
    file_put_contents($path, $c);
    echo "Route added.\n";
} else {
    echo "Route already exists or marker missing.\n";
}
