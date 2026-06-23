<?php

namespace App\Services;

class ContentSanitizer
{
    public function sanitize(?string $content): string
    {
        if ($content === null) {
            return '';
        }

        return trim(strip_tags($content));
    }
}
