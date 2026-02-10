<?php

class GeminiAPI {
    // Use the latest stable or flash model
    private string $apiUrl = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-3-flash-preview:generateContent';

    /**
     * @param string $apiKey Your Google API Key
     */
    public function __construct(
        private readonly string $apiKey
    ) {}

    public function analyzeCV(string $cvText): array {
        $endpoint = $this->apiUrl . '?key=' . $this->apiKey;

        $payload = [
            // Modern approach: Separate instructions from the data
            "system_instruction" => [
                "parts" => [
                    ["text" => "You are a strict career evaluator. You must output valid JSON only. Do not wrap in markdown tags. Structure: {score:int, strengths:[], weaknesses:[], missing_skills:[], roadmap:[], suggestions:[]}"]
                ]
            ],
            "contents" => [
                [
                    "parts" => [
                        ["text" => "Analyze this CV: " . mb_substr($cvText, 0, 30000)]
                    ]
                ]
            ],
            // Enforce JSON output at the API level
            "generationConfig" => [
                "response_mime_type" => "application/json",
                "temperature" => 0.2 // Lower temperature for more consistent evaluation
            ]
        ];

        $maxRetries = 3;
        $attempt = 0;

        do {
            $attempt++;
            $ch = curl_init($endpoint);
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POST           => true,
                CURLOPT_HTTPHEADER     => ['Content-Type: application/json'],
                CURLOPT_POSTFIELDS     => json_encode($payload),
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_TIMEOUT        => 30
            ]);

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $error    = curl_error($ch);
            curl_close($ch);

            if ($error) {
                file_put_contents(__DIR__ . '/../debug_gemini_curl.log', date('Y-m-d H:i:s') . " - CURL Error: " . $error . "\n", FILE_APPEND);
                return ['error' => "Connection Error: $error"];
            }

            if ($httpCode === 429) {
                // Rate limit hit - wait and retry
                sleep(2 * $attempt); // Exponential backoff: 2s, 4s, 6s...
                continue;
            }

            if ($httpCode !== 200) {
                file_put_contents(__DIR__ . '/../debug_gemini_response.log', date('Y-m-d H:i:s') . " - API Error ($httpCode): " . $response . "\n", FILE_APPEND);
                return ['error' => "API Error (Status $httpCode)", 'details' => json_decode($response, true)];
            }

            break; // Success, exit loop

        } while ($attempt < $maxRetries);

        if ($httpCode === 429) {
             return ['error' => "Quota Exceeded. Please try again in a minute."];
        }

        $result = json_decode($response, true);
        
        // Modern Gemini response path
        $content = $result['candidates'][0]['content']['parts'][0]['text'] ?? null;

        if ($content) {
            return json_decode($content, true);
        }

        file_put_contents(__DIR__ . '/../debug_gemini_response.log', date('Y-m-d H:i:s') . " - Empty/Invalid Response: " . $response . "\n", FILE_APPEND);
        return ['error' => 'Empty response from AI', 'raw' => $result];
    }
}
?>
