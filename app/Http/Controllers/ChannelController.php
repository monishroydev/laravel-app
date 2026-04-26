<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class ChannelController extends Controller
{
    public function index()
    {
        return Cache::remember('tv_channels_v3', 3600, function () {
            $urls = [
                'https://iptv-org.github.io/iptv/index.country.m3u',
                'https://iptv-org.github.io/iptv/countries/bd.m3u',
                'https://raw.githubusercontent.com/Free-TV/IPTV/master/playlist.m3u8'
            ];

            $allChannels = [];

            foreach ($urls as $url) {
                try {
                    $response = Http::timeout(20)->get($url);

                    if ($response->ok()) {
                        $allChannels = array_merge($allChannels, $this->parseM3U($response->body(), $url));
                    }
                } catch (\Exception $e) {
                    continue; // Skip failed playlists
                }
            }

            // Remove duplicates by URL to ensure we don't have repeating channels
            $uniqueChannels = [];
            foreach ($allChannels as $channel) {
                if (!isset($uniqueChannels[$channel['url']])) {
                    $uniqueChannels[$channel['url']] = $channel;
                }
            }

            return array_values($uniqueChannels);
        });
    }

    private function parseM3U($text, $sourceUrl)
    {
        $lines = explode("\n", $text);
        $channels = [];
        $current = [];

        $isBdPlaylist = str_contains($sourceUrl, 'bd.m3u');

        foreach ($lines as $line) {
            $line = trim($line);

            if (str_starts_with($line, '#EXTINF')) {
                preg_match('/tvg-logo="([^"]*)"/', $line, $logo);
                preg_match('/group-title="([^"]*)"/', $line, $group);
                preg_match('/,(.*)$/', $line, $name);

                $country = $group[1] ?? 'Unknown';
                if ($isBdPlaylist) {
                    $country = 'Bangladesh';
                }

                $current = [
                    'name' => trim($name[1] ?? 'Unknown'),
                    'logo' => $logo[1] ?? '',
                    'country' => trim($country),
                    'url' => ''
                ];
            } elseif (!empty($line) && !str_starts_with($line, '#')) {
                $current['url'] = $line;
                if (!empty($current['url']) && !empty($current['name'])) {
                    $channels[] = $current;
                }
            }
        }

        return $channels;
    }
}
