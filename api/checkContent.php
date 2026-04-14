<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$a = \App\Models\Article::find(7);
echo "Has <img>: " . (str_contains($a->content, '<img') ? 'YES' : 'NO') . PHP_EOL;
echo "Has [IMAGE:]: " . (str_contains($a->content, '[IMAGE:') ? 'YES' : 'NO') . PHP_EOL;
preg_match('/src="([^"]+)"/', $a->content, $m);
echo "First img src: " . ($m[1] ?? 'none') . PHP_EOL;
