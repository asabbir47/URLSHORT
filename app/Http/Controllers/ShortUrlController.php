<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Redirect;
use App\Services\Normalizer;

class ShortUrlController extends Controller
{
    //

    public function store(Request $request)
    {
        $request->validate([
            'original_url' => 'required|url'
        ]);

        $original_url = $request->original_url;

        // dd($original_url);

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
                    ['url' => $original_url],
                ]
            ]
        ]);

        if($response->successful())
        {
            $resultJson = $response->json();
            // dd($resultJson);
            if(!empty($resultJson))
            {
                return Redirect::back()->withErrors('The url is not safe.');
            }else{
                
                $normalizeUrlOb = new Normalizer($original_url,true,true);

                $normalizedUrl = $normalizeUrlOb->normalize();
                dump($normalizedUrl);

                $parsed_url = \parse_url($normalizedUrl);
                \dump($parsed_url);
            }
        }else{
            return Redirect::back()->withErrors('Please try again.');
        }

    }
}
