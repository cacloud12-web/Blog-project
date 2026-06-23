<?php

namespace Tests\Unit;

use App\Helper\ApiResponse;
use Tests\TestCase;

class ApiResponseTest extends TestCase
{
    public function test_success_response_format(): void
    {
        $response = ApiResponse::success(['id' => 1], 'Created', 201);

        $this->assertSame(201, $response->getStatusCode());
        $this->assertSame([
            'success' => true,
            'message' => 'Created',
            'data' => ['id' => 1],
        ], $response->getData(true));
    }

    public function test_error_response_format(): void
    {
        $response = ApiResponse::error('Not found', 404);

        $this->assertSame(404, $response->getStatusCode());
        $this->assertSame([
            'success' => false,
            'message' => 'Not found',
            'errors' => null,
        ], $response->getData(true));
    }
}
