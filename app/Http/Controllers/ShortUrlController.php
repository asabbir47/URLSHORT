<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Redirect;
use App\Services\UrlNormalizer;
use App\Models\ShortUrl;
use Illuminate\Support\Str;

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
                
                $normalizeUrlOb = new UrlNormalizer($original_url,true,true);

                $normalizedUrl = $normalizeUrlOb->normalize();
                dump($normalizedUrl);

                $parsed_url = \parse_url($normalizedUrl);
                \dump($parsed_url);

                $scheme = array_key_exists('scheme',$parsed_url)?$parsed_url['scheme'] : 'http';
                $host = array_key_exists('host',$parsed_url)?$parsed_url['host'] : '';
                $port = array_key_exists('port',$parsed_url)?$parsed_url['port'] : '';
                $user = array_key_exists('user',$parsed_url)?$parsed_url['user'] : '';
                $pass = array_key_exists('pass',$parsed_url)?$parsed_url['pass'] : '';
                $path = array_key_exists('path',$parsed_url)?$parsed_url['path'] : '';
                $query = array_key_exists('query',$parsed_url)?$parsed_url['query'] : '';
                $fragment = array_key_exists('fragment',$parsed_url)?$parsed_url['fragment'] : '';

                $checkUrlExistence = ShortUrl::where('scheme',$scheme)
                                    ->where('host',$host)
                                    ->where('port',$port)
                                    ->where('user',$user)
                                    ->where('pass',$pass)
                                    ->where('path',$path)
                                    ->where('query',$query)
                                    ->where('fragment',$fragment)
                                    ->first();

                dump($checkUrlExistence);

                if(!$checkUrlExistence)
                {
                    $randomString = Str::random(6);

                    try {
                        $result = ShortUrl::create([
                            'original_url' => $original_url,
                            'short_url' => $randomString,
                            'scheme' => $scheme,
                            'host' => $host,
                            'port' => $port,
                            'user' => $user,
                            'pass' => $pass,
                            'path' => $path,
                            'query' => $query,
                            'fragment' => $fragment,
                        ]);
                        $createdShortUrl = $request->getHttpHost().'/'.$result->short_url;
                        return Redirect::back()->withSuccess('Generated short url is: <a href="http://'.$createdShortUrl.'">'.$createdShortUrl.'</a>');

                    } catch (\Illuminate\Database\QueryException  $e) {
                        \dump($e->getMessage());
                    }

                    
                }else{
                    $shortUrlFromPreviousEntry = $request->getHttpHost().'/'.$checkUrlExistence->short_url;
                    dump($shortUrlFromPreviousEntry);
                    return Redirect::back()->withErrors('The Given url exists in the system. Short url is: <a href="http://'.$shortUrlFromPreviousEntry.'">'.$shortUrlFromPreviousEntry.'</a>');
                }
                
            }
        }else{
            return Redirect::back()->withErrors('Please try again.');
        }

    }
}
