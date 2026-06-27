<?php

namespace Tests\Unit;

use App\Services\CookingConsultationService;
use PHPUnit\Framework\TestCase;
use ReflectionMethod;
use RuntimeException;

class CookingConsultationServiceTest extends TestCase
{
    public function test_response_text_returns_text_from_valid_gemini_payload(): void
    {
        // Arrange
        $service = new CookingConsultationService();
        $payload = $this->geminiPayload('{"in_scope":true,"answer":"Siap.","related_recipe_ids":[]}');

        // Act
        $text = $this->responseText($service, $payload);

        // Assert
        $this->assertSame('{"in_scope":true,"answer":"Siap.","related_recipe_ids":[]}', $text);
    }

    public function test_response_text_throws_exception_when_payload_has_no_candidate_text(): void
    {
        // Arrange
        $service = new CookingConsultationService();
        $payload = ['candidates' => []];

        // Act & Assert
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Respons layanan konsultasi AI tidak memiliki teks.');

        $this->responseText($service, $payload);
    }

    public function test_response_text_throws_exception_when_candidate_text_is_empty(): void
    {
        // Arrange
        $service = new CookingConsultationService();
        $payload = $this->geminiPayload('   ');

        // Act & Assert
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Respons layanan konsultasi AI tidak memiliki teks.');

        $this->responseText($service, $payload);
    }

    private function responseText(CookingConsultationService $service, array $payload): string
    {
        $method = new ReflectionMethod($service, 'responseText');

        return $method->invoke($service, $payload);
    }

    private function geminiPayload(string $text): array
    {
        return [
            'candidates' => [[
                'content' => [
                    'parts' => [[
                        'text' => $text,
                    ]],
                ],
            ]],
        ];
    }
}
