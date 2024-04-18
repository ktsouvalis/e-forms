<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NotificationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        return view('notifications.index', ['notifications'=>auth()->user()->notifications]);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        //
        $notification = auth()->user()->notifications->find($id);
        $notification->markAsRead();
        return view('notifications.show', ['notification'=>$notification]);
    }
}
