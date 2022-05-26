<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use App\Services\UrlNormalizer;
use App\Models\ShortUrl;
use App\Services\FolderService;
use App\Services\SafebrowsingService;
use Illuminate\Support\Str;

class ShortUrlController extends Controller
{
    //
    public function index()
    {
        $shortUrl = ShortUrl::latest()->paginate(10);
        $shortUrl->getCollection()->transform(function($value){
            $folderToParent = '';
            if($value->folder_id)
            $folderToParent = FolderService::getFolderPathtoParent($value->folder_id,'');
            $value->folders = $folderToParent;
            return $value;
        });

        return view('shorturl', [
            'urls' => $shortUrl
        ]);
    }

    public function show($short_url_hash)
    {
        $short_url = ShortUrl::where('short_url',$short_url_hash)->where('folder_id',null)->firstOrFail();
        return redirect($short_url->original_url);
    }

    public function showWithFolder($folder,$url_hash)
    {
        $folderService = new FolderService($folder);
        $folder_id = $folderService->checkFolderStructureExistance();
        if($folder_id>0)
        {
            $short_url = ShortUrl::where('short_url',$url_hash)->where('folder_id',$folder_id)->firstOrFail();
            return redirect($short_url->original_url);
        }
        else{
            abort(404);
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'original_url' => 'required|url',
            'folder_id' => '',
        ]);

        $original_url = $request->original_url;
        $folder = $request->folder_id;

        $response = SafebrowsingService::checkUrlIsSafe($original_url);

        if ($response->successful()) {
            $resultJson = $response->json();
            if (!empty($resultJson)) {
                return Redirect::back()->withErrors('The url is not safe.');
            } else {

                $normalizeUrlOb = new UrlNormalizer($original_url, true, true);
                $normalizedUrl = $normalizeUrlOb->normalize();
                
                $checkUrlResult = $this->checkExistanceOfAnUrl($normalizedUrl);
                $checkUrlExistence = $checkUrlResult[0];

                if (!$checkUrlExistence) {
                    $folderService = new FolderService($folder);
                    $folderResult = $folderService->getIfNotExistCreateFolder();
                    $folder_id = $folderResult[0];
                    $folderString = $folderResult[1];

                    $randomString = $this->getFolderWiseUniqueUrlHash($folder_id);

                    try {

                        $result = ShortUrl::create(array_merge([
                            'original_url' => $original_url,
                            'short_url' => $randomString,
                            'folder_id' => $folder_id,
                        ],$checkUrlResult[1]));

                        $createdShortUrl = $request->getHttpHost().'/'.$folderString.$result->short_url;
                        return Redirect::back()->withSuccess('Generated short url is: <a target="_blank" href="http://' . $createdShortUrl . '">' . $createdShortUrl . '</a>');
                    } catch (\Illuminate\Database\QueryException  $e) {
                        return Redirect::back()->withErrors('Please try again');
                    }
                } else {
                    $shortUrlFromPreviousEntry = $request->getHttpHost().'/'.FolderService::getFolderPathtoParent($checkUrlExistence->folder_id,'').$checkUrlExistence->short_url;
                    return Redirect::back()->withErrors('The Given url exists in the system. Short url is: <a target="_blank" href="http://' . $shortUrlFromPreviousEntry . '">' . $shortUrlFromPreviousEntry . '</a>');
                }
            }
        } else {
            return Redirect::back()->withErrors('Please try again.');
        }
    }

    public function checkExistanceOfAnUrl($url)
    {
        $parsed_url = \parse_url($url);
        $scheme = array_key_exists('scheme', $parsed_url) ? $parsed_url['scheme'] : 'http';
                $host = array_key_exists('host', $parsed_url) ? $parsed_url['host'] : '';
                $port = array_key_exists('port', $parsed_url) ? $parsed_url['port'] : '';
                $user = array_key_exists('user', $parsed_url) ? $parsed_url['user'] : '';
                $pass = array_key_exists('pass', $parsed_url) ? $parsed_url['pass'] : '';
                $path = array_key_exists('path', $parsed_url) ? $parsed_url['path'] : '';
                $query = array_key_exists('query', $parsed_url) ? $parsed_url['query'] : '';
                $fragment = array_key_exists('fragment', $parsed_url) ? $parsed_url['fragment'] : '';

        $checkUrlExistence = ShortUrl::where(function ($query) {
                                        $query->where('scheme', 'http')
                                            ->orWhere('scheme', 'https');
                                    })
                                    ->where('host', $host)
                                    ->where('port', $port)
                                    ->where('user', $user)
                                    ->where('pass', $pass)
                                    ->where('path', $path)
                                    ->where('query', $query)
                                    ->where('fragment', $fragment)
                                    ->first();

        return [$checkUrlExistence,compact("scheme","host","port","user","pass","path","query","fragment")];
    }

    public function getFolderWiseUniqueUrlHash($folder_id)
    {
        $randomString = Str::random(6);
        $shortUrlExistenceIterator = 0;

        while (true) {
            $shortUrlExistence = ShortUrl::where('short_url', $randomString)->where('folder_id',$folder_id)->first();
            if (!$shortUrlExistence) {
                break;
            } else {
                if ($shortUrlExistence % 2 == 0) {
                    $randomString = Str::random(6);
                } else {
                    $randomString = str_shuffle($randomString);
                }
            }
            if ($shortUrlExistenceIterator > 5) break;
            $shortUrlExistenceIterator++;
        }

        return $randomString;
    }

    
}
