<?php
$a = App\Models\Article::latest()->first();
if($a) {
    echo "\n=========================\n";
    echo "ID Bài viết: " . $a->id . "\n";
    echo "Tiêu đề: " . $a->title . "\n";
    echo "Kích thước Content: " . number_format(strlen($a->content)) . " ký tự (bytes)\n";
    $preview = preg_replace('/<img[^>]+>/i', '[--- HÌNH ẢNH MÃ BÓNG ---]', $a->content);
    echo "\n[--- NỘI DUNG VĂN BẢN TRÍCH XUẤT TỪ DB ---]\n";
    echo substr($preview, 0, 1000) . "\n";
    echo "=========================\n";
} else {
    echo "TRONG DATABASE HIỆN KHÔNG CÓ BÀI NÀO!";
}
