<?php

declare(strict_types=1);

namespace App\Http\Components\Oris;

use GuzzleHttp;
use GuzzleHttp\Client;

class GuzzleClient
{
    public const string METHOD_CREATE_ENTRY = 'createEntry';
    public const string METHOD_UPDATE_ENTRY = 'updateEntry';
    public const string METHOD_DELETE_ENTRY = 'deleteEntry';

    public function create(): Client
    {
        return new Client(['base_uri' => 'https://oris.orientacnisporty.cz']);
    }

    public function generateMultipartForm(string $method, array $context = []): array
    {

        $boundary = sprintf("--------------------------------------%u", mt_rand(1000000000, 9999999999));
        // $boundary = '--------------------------250790699679000650450122';
        $default = [
            'format' => 'json',
            'method' => $method,
        ];

        $multipartForm = array_merge($default, $this->getCredentials(), $context);

        $multipart_form = [];
        foreach ($multipartForm as $index => $value) {
            if ($value !== null) {
                $multipart_form[] = [
                    'name' =>  $index,
                    'contents' =>  $value,
                ];
            }
        }

        return [
            'headers' => [
                'Connection' => 'keep-alive',
                'Content-Type' => 'multipart/form-data; boundary='.$boundary,
            ],
            'body' => new GuzzleHttp\Psr7\MultipartStream($multipart_form, $boundary),
        ];

    }

    private function getCredentials(): array
    {
        $clubKey = config('site-config.oris_credentials.general.clubkey');

        if ($clubKey !== null) {
            return [
                'clubkey' => $clubKey,
            ];
        } else {
            return [
                'username' => config('site-config.oris_credentials.general.username'),
                'password' => config('site-config.oris_credentials.general.password'),
            ];
        }
    }

}
