<?php

namespace App\Http\Middleware;

use App\Models\Classroom;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Lang;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {

        $user = $request->user();

        return [
            ...parent::share($request),
            'name' => config('app.name'),
            'auth' => [
                'user' => $user,
            ],
            'sidebarOpen' => ! $request->hasCookie('sidebar_state') || $request->cookie('sidebar_state') === 'true',
            'flash' => [
                'success' => session('success'),
                'error' => session('error'),
            ],

            // ✅ i18n
            'locale' => app()->getLocale(),
            'translations' => fn (): array => Lang::get('app'),

            // ✅ Teacher's assigned classroom for sidebar link
            'teacherClassroom' => $user?->role === 'teacher'
                ? Classroom::where('teacher_id', $user->id)
                    ->select('id', 'name')
                    ->first()
                : null,
        ];
    }
}
