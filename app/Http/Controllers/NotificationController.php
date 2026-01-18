<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = auth()->user()->notifications()->paginate(20);
        return view('notifications.index', compact('notifications'));
    }

    public function markAsRead($id)
    {
        $notification = auth()->user()->notifications()->findOrFail($id);
        $notification->markAsRead();
        return back()->with('success', 'Bildirim okundu olarak işaretlendi.');
    }

    public function markAllAsRead()
    {
        auth()->user()->unreadNotifications->markAsRead();
        return back()->with('success', 'Tüm bildirimler okundu olarak işaretlendi.');
    }

    // Deprecated but kept for compatibility with existing AJAX calls if any
    public function checkread(Request $request)
    {
        $s = DB::table('notifications')->where('id', $request->id)->update(['read_at' => now()]);
        // Return empty or success to satisfy legacy calls
        return response()->json(['success' => true]);
    }
}
