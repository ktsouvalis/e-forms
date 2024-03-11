<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class EnrollmentController extends Controller
{
    //
    public function save($select, Request $request){
        dd($select);
    }

    public function download_file($file){
        $username = Auth::check() ? Auth::user()->username : (Auth::guard('school')->check() ? Auth::guard('school')->user()->name : Auth::guard('teacher')->user()->afm);
        $directory = "enrollments";
        $fileHandler = new FilesController();
        $download = $fileHandler->download_file($directory, $file, 'local');
        if($download->getStatusCode() == 500){
            Log::channel('files')->error($username." File $file failed to download");
            return back()->with('failure', 'Δοκιμάστε ξανά');
        }
        Log::channel('files')->info($username." File $file successfully downloaded");
        return $download;
    }
}
