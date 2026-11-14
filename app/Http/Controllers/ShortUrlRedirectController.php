<?php

namespace App\Http\Controllers;

use App\Models\ShortUrl;

class ShortUrlRedirectController extends Controller
{
    public function redirect($short_code)
    {
        $shortUrl = ShortUrl::where('short_code', $short_code)->first();

        if (!$shortUrl) {
            abort(404, 'Short URL not found.');
        }

        // Increase hits
        $shortUrl->increment('hits');

        // Redirect to original URL
        return redirect()->away($shortUrl->original_url);
    }
}