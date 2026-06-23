<?php

namespace Tests\Unit;

use App\Services\ContentSanitizer;
use Tests\TestCase;

class ContentSanitizerTest extends TestCase
{
    public function test_strips_html_tags(): void
    {
        $sanitizer = new ContentSanitizer;

        $result = $sanitizer->sanitize('<p>Hello</p><script>alert(1)</script>');

        $this->assertSame('Helloalert(1)', $result);
    }

    public function test_handles_null_content(): void
    {
        $sanitizer = new ContentSanitizer;

        $this->assertSame('', $sanitizer->sanitize(null));
    }
}
