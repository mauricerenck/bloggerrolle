<?php

namespace mauricerenck\BloggerRolle;

use Kirby\Http\Remote;
use Exception;

return [
    'page.changeStatus:after' => function ($newPage, $oldPage) {
        $kirbyUrl = kirby()->url();

        if (!$newPage->isDraft() && $oldPage->isDraft()) {

            $pingUrl = option('mauricerenck.bloggerrolle.uberblogr', false) ? 'https://ping.uberblogr.de/v2/' : 'https://ping.bloggerrolle.de';
            $blogUrl = option('mauricerenck.bloggerrolle.url', $kirbyUrl);
            $allowedTemplates = option('mauricerenck.bloggerrolle.templates.allowed', []);
            $blockedTemplates = option('mauricerenck.bloggerrolle.templates.blocked', []);

            if (count($blockedTemplates) > 0 && in_array($newPage->intendedTemplate()->name(), $blockedTemplates)) {
                return;
            }

            if (count($allowedTemplates) > 0 && !in_array($newPage->intendedTemplate()->name(), $allowedTemplates)) {
                return;
            }

            try {
                Remote::request($pingUrl . '?url=' . $blogUrl, [
                    'method' => 'POST',
                    'headers' => [
                        'Content-Type' => 'application/x-www-form-urlencoded; charset=utf-8',
                    ],
                    'data' => [
                        'url' => $blogUrl,
                    ],
                ]);
            } catch (Exception $e) {
                throw new Exception('Error sending ping: ' . $e->getMessage());
            }
        }
    },
];
