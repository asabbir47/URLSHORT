<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Redirect;

class ShortUrlController extends Controller
{
    //

    public function store(Request $request)
    {
        $request->validate([
            'original_url' => 'required|url'
        ]);

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
                    ['url' => $request->all()['original_url']],
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
                dd('ok');
            }
        }else{
            return Redirect::back()->withErrors('Please try again.');
        }

    }
}
