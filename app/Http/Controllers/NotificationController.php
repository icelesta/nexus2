<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    /**
     * Mark one notification as read
     * and redirect to its document URL.
     */
    public function open(
        Request $request,
        string $notification
    ): RedirectResponse {

        $user = $request->user();

        $model = $user
            ->notifications()
            ->whereKey($notification)
            ->firstOrFail();

        /*
        |--------------------------------------------------------------------------
        | Mark Read
        |--------------------------------------------------------------------------
        */

        if (! $model->read_at) {
            $model->markAsRead();
        }

        /*
        |--------------------------------------------------------------------------
        | Resolve Notification URL
        |--------------------------------------------------------------------------
        */

        $url = $model->data['url'] ?? null;

        /*
        |--------------------------------------------------------------------------
        | Safety
        |--------------------------------------------------------------------------
        |
        | Only allow internal application URLs.
        |
        */

        if (
            ! is_string($url)
            || $url === ''
            || ! str_starts_with(
                $url,
                url('/')
            )
        ) {

            return redirect()
                ->route('filament.admin.pages.dashboard');
        }

        return redirect()->to($url);
    }

    /**
     * Mark all notifications as read.
     */
    public function markAllAsRead(
        Request $request
    ): RedirectResponse {

        $request
            ->user()
            ->unreadNotifications
            ->each(
                fn ($notification) =>
                    $notification->markAsRead()
            );

        return back();
    }
}