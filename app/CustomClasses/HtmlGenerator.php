<?php
namespace App\CustomClasses;

class HtmlGenerator {
    public static function href($path, $file, $downloadFileName) {
        $href = '<a href="'.$path.$file.'" download="'.$downloadFileName.'" >'.$file.'</a>';
    
        return $href;
    }
}