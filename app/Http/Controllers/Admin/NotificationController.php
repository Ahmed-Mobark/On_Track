<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $notifications = Notification::whereHas('user', fn($q) => $q->whereIn('role', ['ADMIN', 'SUPER_ADMIN']))
            ->where('user_id', auth()->id())
            ->latest('created_at')
            ->paginate(20);

        return view('admin.notifications.index', compact('notifications'));
    }

    public function send()
    {
        return view('admin.notifications.send');
    }

    public function store(Request $request, NotificationService $service)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'message' => 'required|string|max:1000',
            'target' => 'required|in:all,specific',
            'user_id' => 'required_if:target,specific|nullable|exists:users,id',
        ]);

        if ($request->target === 'all') {
            $count = $service->sendToAll($request->title, $request->message, 'promo', [
                'url' => url('/'),
            ]);
            return back()->with('success', "تم إرسال الإشعار لـ {$count} عميل بنجاح");
        }

        $user = User::findOrFail($request->user_id);
        $service->send($user, $request->title, $request->message, 'promo', [
            'url' => url('/'),
        ]);

        return back()->with('success', "تم إرسال الإشعار لـ {$user->name} بنجاح");
    }

    public function markAllRead()
    {
        Notification::where('user_id', auth()->id())
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return back()->with('success', 'تم تعليم الكل كمقروء');
    }
}
