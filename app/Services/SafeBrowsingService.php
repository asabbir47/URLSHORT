<?php 

namespace App\Services;
use Illuminate\Support\Facades\Http;

class SafebrowsingService
{

    public static function checkUrlIsSafe($url)
    {
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'key' => 'AIzaSyBpuqvlwZlS5k3ix1hhCU3I93oa14weum4'
        ])->post('https://safebrowsing.googleapis.com/v4/threatMatches:find?key=AIzaSyBpuqvlwZlS5k3ix1hhCU3I93oa14weum4', [
            'client' => [
                'clientId' => 'URLSHORT',
                'clientVersion' => '1.1'
            ],
            "threatInfo" => [
                "threatTypes" => ["MALWARE", "SOCIAL_ENGINEERING"],
                "platformTypes" =>  ["WINDOWS"],
                "threatEntryTypes" => ["URL"],
                "threatEntries" => [
                    ['url' => $url],
                ]
            ]
        ]);

        return $response;
    }

}