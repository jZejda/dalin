<?php

namespace App\Http\Controllers\Notification;

use DB;
use App\Http\Controllers\Controller;

class DiscordHooksController extends Controller
{
    public function notifyPost()
    {
        $url = 'https://discordapp.com/api/webhooks/587729177608716322/BZN59R6YwSx5Kxujp0IyzOJycVZqqft-W-e9FfJUj7UErVDZXoptLd5PM1Zx-WOSZ1d0';

        $hookObject = json_encode([

            'content' => 'A message will go here',
            /*
             * The username shown in the message
             */
            'username' => 'MyUsername',
            /*
             * The image location for the senders image
             */
            'avatar_url' => 'https://pbs.twimg.com/profile_images/972154872261853184/RnOg6UyU_400x400.jpg',
            /*
             * Whether or not to read the message in Text-to-speech
             */
            'tts' => false,
            /*
             * File contents to send to upload a file
             */
            // "file" => "",
            /*
             * An array of Embeds
             */
            'embeds' => [
                /*
                 * Our first embed
                 */
                [
                    // Set the title for your embed
                    'title' => 'Google.com',

                    // The type of your embed, will ALWAYS be "rich"
                    'type' => 'rich',

                    // A description for your embed
                    'description' => '',

                    // The URL of where your title will be a link to
                    'url' => 'https://www.google.com/',

                    /* A timestamp to be displayed below the embed, IE for when an an article was posted
                     * This must be formatted as ISO8601
                     */
                    'timestamp' => '2018-03-10T19:15:45-05:00',

                    // The integer color to be used on the left side of the embed
                    'color' => hexdec('FFFFFF'),

                    // Footer object
                    'footer' => [
                        'text' => 'Google TM',
                        'icon_url' => 'https://pbs.twimg.com/profile_images/972154872261853184/RnOg6UyU_400x400.jpg',
                    ],

                    // Image object
                    'image' => [
                        'url' => 'https://www.google.com/images/branding/googlelogo/1x/googlelogo_color_272x92dp.png',
                    ],

                    // Thumbnail object
                    'thumbnail' => [
                        'url' => 'https://pbs.twimg.com/profile_images/972154872261853184/RnOg6UyU_400x400.jpg',
                    ],

                    // Author object
                    'author' => [
                        'name' => 'Alphabet',
                        'url' => 'https://www.abc.xyz',
                    ],

                    // Field array of objects
                    'fields' => [
                        // Field 1
                        [
                            'name' => 'Data A',
                            'value' => 'Value A',
                            'inline' => false,
                        ],
                        // Field 2
                        [
                            'name' => 'Data B',
                            'value' => 'Value B',
                            'inline' => true,
                        ],
                        // Field 3
                        [
                            'name' => 'Data C',
                            'value' => 'Value C',
                            'inline' => true,
                        ],
                    ],
                ],
            ],

        ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

        $ch = curl_init();

        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $hookObject,
            CURLOPT_HTTPHEADER => [
                'Length' => strlen($hookObject),
                'Content-Type' => 'application/json',
            ],
        ]);

        $response = curl_exec($ch);
        curl_close($ch);
    }
}
