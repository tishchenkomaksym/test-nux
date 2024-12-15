<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegisterRequest;
use App\Models\Link;
use App\Models\User;
use App\Services\PageService;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\URL;
use JsonException;

class UserController extends Controller
{
    /**
     * @param RegisterRequest $registerRequest
     *
     * @return Factory|View|Application
     * @throws JsonException
     */
    public function register(RegisterRequest $registerRequest, PageService $pageService): Factory|View|Application
    {
        $user = User::where('phonenumber', $registerRequest->phonenumber)->first();

        if (!$user) {
            $user = User::create([
                'username' => $registerRequest->username,
                'phonenumber' => $registerRequest->phonenumber,
            ]);
        }


        $existingLink = Link::where('user_id', $user->id)
                            ->whereNull('deactivated_at')
                            ->where('expired_at', '>', now())
                            ->first();

        if ($existingLink) {
            return view('link', ['link' => $existingLink->link]);
        }

        return view('link', ['link' => $pageService->createLink($user)]);
    }

}
