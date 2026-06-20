<?php

namespace App\Http\Controllers;

use App\Models\Podcast;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PodcastProxyController extends Controller
{
    public function stream(Request $request, Podcast $podcast)
    {
        // Extraemos el ID del archivo de Drive desde url_audio
        if (! preg_match('/drive\.google\.com\/file\/d\/([a-zA-Z0-9_-]+)/', $podcast->url_audio, $matches)) {
            abort(404, 'Enlace de audio inválido.');
        }

        $fileId = $matches[1];
        $driveUrl = "https://drive.google.com/uc?export=download&id={$fileId}";

        // Reenviamos el header Range del navegador (necesario para seek/avance)
        $headers = [];
        if ($request->hasHeader('Range')) {
            $headers['Range'] = $request->header('Range');
        }

        $response = Http::withHeaders($headers)
            ->withOptions(['stream' => true])
            ->get($driveUrl);

        return new StreamedResponse(function () use ($response) {
            $body = $response->toPsrResponse()->getBody();
            while (! $body->eof()) {
                echo $body->read(8192);
                flush();
            }
        }, $response->status(), [
            'Content-Type' => $response->header('Content-Type') ?: 'audio/mpeg',
            'Content-Length' => $response->header('Content-Length'),
            'Content-Range' => $response->header('Content-Range'),
            'Accept-Ranges' => 'bytes',
            'Cache-Control' => 'public, max-age=86400',
        ]);
    }
}