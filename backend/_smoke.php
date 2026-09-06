<?php
require __DIR__."/vendor/autoload.php";
$app = require_once __DIR__."/bootstrap/app.php";
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$path = __DIR__."/resources/views/backend/uploaded_files/index.blade.php";
$content = file_get_contents($path);
$compiler = app(\Illuminate\View\Compilers\BladeCompiler::class);
try {
    $compiled = $compiler->compileString($content);
    echo "Blade compiled OK. Length: ".strlen($compiled).PHP_EOL;
    echo "Has is-selected class: ".(strpos($content, "is-selected") !== false ? "YES" : "NO").PHP_EOL;
    echo "Has refreshUI: ".(strpos($content, "refreshUI") !== false ? "YES" : "NO").PHP_EOL;
} catch (\Throwable $e) {
    echo "ERROR: ".$e->getMessage().PHP_EOL;
}
