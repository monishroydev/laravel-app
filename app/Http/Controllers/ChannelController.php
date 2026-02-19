<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class ChannelController extends Controller
{
    public function index()
    {
        return Cache::remember('tv_channels', 3600, function () {
            $url = 'https://raw.githubusercontent.com/Free-TV/IPTV/master/playlist.m3u8';

            try {
                $response = Http::timeout(20)->get($url);

                if (!$response->ok()) {
                    return [];
                }

                return $this->parseM3U($response->body());
            } catch (\Exception $e) {
                return [];
            }
        });
    }

    private function parseM3U($text)
    {
        $lines = explode("\n", $text);
        $channels = [];
        $current = [];

        foreach ($lines as $line) {
            $line = trim($line);

            if (str_starts_with($line, '#EXTINF')) {
                preg_match('/tvg-logo="([^"]*)"/', $line, $logo);
                preg_match('/group-title="([^"]*)"/', $line, $group);
                preg_match('/,(.*)$/', $line, $name);

                $current = [
                    'name' => $name[1] ?? 'Unknown',
                    'logo' => $logo[1] ?? '',
                    'country' => $group[1] ?? 'Unknown',
                    'url' => ''
                ];
            } elseif (!empty($line) && !str_starts_with($line, '#')) {
                $current['url'] = $line;
                // Only add if we have a valid URL and name
                if (!empty($current['url']) && !empty($current['name'])) {
                    $channels[] = $current;
                }
            }
        }

        return $channels;
    }
}
