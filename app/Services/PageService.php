<?php

namespace App\Services;

use App\Models\Link;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\URL;

class PageService
{
    public function deactivateLink(Request $request): array
    {
        $cleanLink = str_replace('&amp;', '&', $request->input('link'));
        $queryArray = [];
        parse_str(parse_url($cleanLink)['query'], $queryArray);
        $link = Link::where('link', 'like', '%' . $queryArray['signature'] . '%')->first();

        if ($link) {
            $link->update(['deactivated_at' => now()]);
            return ['message' => 'Link deactivated'];
        }

        return ['message' => 'Link not found'];
    }

    public function createLink($user): string
    {
        $newLink = URL::temporarySignedRoute(
            'main-page',
            now()->addDays(7),
            [
                'user' => base64_encode(
                    json_encode([
                        'username' => $user->username,
                        'phonenumber' => $user->phonenumber,
                    ], JSON_THROW_ON_ERROR)
                )
            ]
        );

        Link::create([
            'link' => $newLink,
            'user_id' => $user->id,
            'expired_at' => now()->addDays(7),
        ]);

        return $newLink;
    }

    public function getResult(Request $request)
    {
        $userId = $request->input('user');
        $randomNumber = random_int(1, 1000);
        $result = $randomNumber % 2 === 0 ? 'Win' : 'Lose';

        $winAmount = match (true) {
            $randomNumber > 900 => $randomNumber * 0.7,
            $randomNumber > 600 => $randomNumber * 0.5,
            $randomNumber > 300 => $randomNumber * 0.3,
            default => $randomNumber * 0.1,
        };

        $winAmount = $result === 'Win' ? $winAmount : 0;

        $entry = [
            'number' => $randomNumber,
            'result' => $result,
            'winAmount' => $winAmount,
        ];

        Redis::lpush('history:' . $userId, json_encode($entry, JSON_THROW_ON_ERROR));
        Redis::ltrim('history:' . $userId, 0, 2);

        return $entry;
    }
}
