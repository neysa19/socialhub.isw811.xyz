<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class InstagramController extends Controller
{
    public function connectInstagram()
    {
        $instagramLoginUrl = "https://api.instagram.com/oauth/authorize"
            . "?client_id=" . env('INSTAGRAM_APP_ID')
            . "&redirect_uri=" . env('INSTAGRAM_REDIRECT_URI')
            . "&scope=user_profile,user_media"
            . "&response_type=code";

        return redirect($instagramLoginUrl);
    }

    public function handleInstagramCallback(Request $request)
    {
        $code = $request->input('code');

        $response = Http::asForm()->post('https://api.instagram.com/oauth/access_token', [
            'client_id' => env('INSTAGRAM_APP_ID'),
            'client_secret' => env('INSTAGRAM_APP_SECRET'),
            'grant_type' => 'authorization_code',
            'redirect_uri' => env('INSTAGRAM_REDIRECT_URI'),
            'code' => $code,
        ]);

        $accessToken = $response->json()['access_token'];

        // Guardar el token de acceso para el usuario
        session(['instagram_access_token' => $accessToken]);

        return redirect()->route('dashboard')->with('success', 'Instagram connected successfully');
    }

    public function postToInstagram(Request $request)
    {
        $caption = $request->input('caption');
        $imageUrl = $request->input('image_url');

        if (!$caption || !$imageUrl) {
            return response()->json(['error' => 'Caption and Image URL cannot be empty'], 400);
        }

        $instagramBusinessAccountId = 'el_id_de_cuenta_comercial'; // Obtenlo a través de la integración con la Graph API

        $response = Http::post("https://graph.facebook.com/v16.0/{$instagramBusinessAccountId}/media", [
            'image_url' => $imageUrl,
            'caption' => $caption,
            'access_token' => session('instagram_access_token'),
        ]);

        if ($response->failed()) {
            return response()->json(['error' => 'Failed to create media'], 500);
        }

        $creationId = $response->json()['id'];

        // Publicar el contenido
        $publishResponse = Http::post("https://graph.facebook.com/v16.0/{$instagramBusinessAccountId}/media_publish", [
            'creation_id' => $creationId,
            'access_token' => session('instagram_access_token'),
        ]);

        if ($publishResponse->failed()) {
            return response()->json(['error' => 'Failed to publish media'], 500);
        }

        return response()->json(['success' => 'Post published successfully']);
    }
}
