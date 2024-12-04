<?php

namespace App\Http\Controllers;

use Facebook\Facebook;
use Illuminate\Http\Request;

class FacebookController extends Controller
{
    protected $facebook;

    public function __construct()
    {
        $this->facebook = new Facebook([
            'app_id' => env('FACEBOOK_APP_ID'),
            'app_secret' => env('FACEBOOK_APP_SECRET'),
            'default_graph_version' => 'v16.0',
        ]);
    }

    public function connectFacebook()
    {
        $helper = $this->facebook->getRedirectLoginHelper();
        $permissions = ['email', 'pages_manage_posts', 'pages_read_engagement', 'publish_to_groups'];
        $loginUrl = $helper->getLoginUrl(env('FACEBOOK_REDIRECT_URI'), $permissions);
        
        return redirect($loginUrl);
    }

    public function handleFacebookCallback(Request $request)
    {
        $helper = $this->facebook->getRedirectLoginHelper();

        try {
            $accessToken = $helper->getAccessToken();
        } catch (\Facebook\Exceptions\FacebookResponseException $e) {
            return redirect()->route('dashboard')->with('error', 'Graph returned an error: ' . $e->getMessage());
        } catch (\Facebook\Exceptions\FacebookSDKException $e) {
            return redirect()->route('dashboard')->with('error', 'Facebook SDK returned an error: ' . $e->getMessage());
        }

        if (!isset($accessToken)) {
            return redirect()->route('dashboard')->with('error', 'User denied access.');
        }

        // Guardar el token de acceso en la base de datos para uso posterior
        session(['facebook_access_token' => (string) $accessToken]);

        return redirect()->route('dashboard')->with('success', 'Facebook connected successfully');
    }

    public function postToFacebook(Request $request)
    {
        $message = $request->input('message');

        if (!$message) {
            return response()->json(['error' => 'Message cannot be empty'], 400);
        }

        try {
            $response = $this->facebook->post('/me/feed', ['message' => $message], session('facebook_access_token'));
        } catch (\Facebook\Exceptions\FacebookResponseException $e) {
            return response()->json(['error' => 'Graph returned an error: ' . $e->getMessage()], 500);
        } catch (\Facebook\Exceptions\FacebookSDKException $e) {
            return response()->json(['error' => 'Facebook SDK returned an error: ' . $e->getMessage()], 500);
        }

        return response()->json(['success' => 'Post published successfully']);
    }
}
