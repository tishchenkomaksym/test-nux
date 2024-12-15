<?php

namespace App\Http\Controllers;

use App\Models\Link;
use App\Models\User;
use App\Services\PageService;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Redis;
use JsonException;

class PageController extends Controller
{
    public function __construct(public PageService $pageService)
    {}

    /**
     * @param Request $request
     *
     * @return View|Factory|Application|\Illuminate\Routing\Redirector|RedirectResponse
     * @throws JsonException
     */
    public function mainPage(Request $request): View|Factory|Application|\Illuminate\Routing\Redirector|RedirectResponse
    {
        if (!$request->hasValidSignature()) {
            abort(403, 'Link expired or invalid.');
        }

        $user = json_decode(base64_decode($request->query('user')), true, 512, JSON_THROW_ON_ERROR);
        $user = User::where('phonenumber', $user['phonenumber'])->first();

        $queryArray = [];
        parse_str(parse_url($request->fullUrl())['query'], $queryArray);

        $link = Link::where('link', 'like', '%' . $queryArray['signature'] . '%')->first();

        if (!$link || $link->deactivated_at || $link->expired_at < now()) {
            return redirect('/')->withErrors(['link' => 'The link is deactivated or has expired.']);
        }
        $history = Redis::lrange('history:' . $user->id, 0, 2);

        return view('main-page', ['user' => $user, 'history' => array_map('json_decode', $history)]);
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse
     * @throws JsonException
     */
    public function generateLink(Request $request): JsonResponse
    {
        $userId = $request->input('user');
        $user = User::find($userId);
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $newLink = $this->pageService->createLink($user);
        $this->pageService->deactivateLink($request);

        return response()->json(['link' => $newLink]);
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse|RedirectResponse
     */
    public function deactivateLink(Request $request): JsonResponse|RedirectResponse
    {
        return response()->json($this->pageService->deactivateLink($request));
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse
     * @throws JsonException
     */
    public function imfeelingLucky(Request $request): JsonResponse
    {
        return response()->json($this->pageService->getResult($request), Response::HTTP_OK);
    }

    public function history(Request $request): JsonResponse
    {
        $userId = $request->input('user');
        $history = Redis::lrange('history:' . $userId, 0, 2);
        $decodedHistory = array_map(function ($item) {
            return json_decode($item, true, 512, JSON_THROW_ON_ERROR);
        }, $history);

        return response()->json($decodedHistory);
    }
}
