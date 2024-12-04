<?php

namespace App\Http\Controllers;

use Abraham\TwitterOAuth\TwitterOAuth;
use Illuminate\Http\Request;

class TwitterController extends Controller
{
    protected $twitter;

    public function __construct()
    {
        $this->twitter = new TwitterOAuth(
            env('TWITTER_CONSUMER_KEY'),
            env('TWITTER_CONSUMER_SECRET'),
            env('TWITTER_ACCESS_TOKEN'),
            env('TWITTER_ACCESS_TOKEN_SECRET')
        );
       
    }

    public function postTweet(Request $request)
    {
        $twitter = new TwitterOAuth(
            env('TWITTER_CONSUMER_KEY'),
            env('TWITTER_CONSUMER_SECRET'),
            env('TWITTER_ACCESS_TOKEN'),
            env('TWITTER_ACCESS_TOKEN_SECRET')
        );
    
        $tweet = $request->input('tweet');
    
        if (!$tweet) {
            return response()->json(['error' => 'Tweet cannot be empty'], 400);
        }
    
        try {
            $result = $twitter->post("statuses/update", ["status" => $tweet]);
            
            if ($twitter->getLastHttpCode() == 200) {
                return response()->json(['success' => 'Tweet posted successfully']);
            } else {
                return response()->json(['error' => 'Failed to post tweet'], 500);
            }
        } catch (\Abraham\TwitterOAuth\TwitterOAuthException $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function connectTwitter()
    {
        $request_token = $this->twitter->oauth('oauth/request_token', ['oauth_callback' => route('twitter.callback')]);
        session(['oauth_token' => $request_token['oauth_token'], 'oauth_token_secret' => $request_token['oauth_token_secret']]);
        return redirect($this->twitter->url('oauth/authorize', ['oauth_token' => $request_token['oauth_token']]));
    }

    public function handleTwitterCallback(Request $request)
    {
        $oauth_verifier = $request->input('oauth_verifier');
        $token = session('oauth_token');
        $token_secret = session('oauth_token_secret');

        $connection = new TwitterOAuth(env('TWITTER_CONSUMER_KEY'), env('TWITTER_CONSUMER_SECRET'), $token, $token_secret);
        $access_token = $connection->oauth("oauth/access_token", ["oauth_verifier" => $oauth_verifier]);

        // Guardar el access_token para la cuenta del usuario
        // Puedes persistir $access_token en la base de datos para publicaciones futuras

        return redirect()->route('dashboard')->with('success', 'Twitter connected successfully');
    }
}
