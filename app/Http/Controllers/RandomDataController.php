<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;

class RandomDataController extends Controller
{
    public function generateRandomData(): JsonResponse
    {
        $targetSize = 1024 * 1024; // 1MB in bytes
        $data = [];
        $currentSize = 0;
        $counter = 0;

        // Keep adding data until we reach approximately 1MB
        while ($currentSize < $targetSize) {
            $item = [
                'id' => $counter++,
                'uuid' => Str::uuid()->toString(),
                'name' => $this->generateRandomString(50),
                'description' => $this->generateRandomString(200),
                'data' => $this->generateRandomString(300),
                'metadata' => [
                    'created_at' => now()->toISOString(),
                    'random_number' => rand(1, 1000000),
                    'random_float' => mt_rand() / mt_getrandmax(),
                    'tags' => $this->generateRandomArray(10),
                    'attributes' => $this->generateRandomAttributes()
                ],
                'content' => $this->generateRandomString(400),
                'additional_field_' . $counter => $this->generateRandomString(100)
            ];

            $data[] = $item;

            // Estimate current JSON size (approximate)
            $currentSize = strlen(json_encode($data));

            // Safety break to prevent infinite loop
            if ($counter > 2000) {
                break;
            }
        }

        // Final adjustment - if we're under 1MB, add some padding
        if ($currentSize < $targetSize) {
            $remaining = $targetSize - $currentSize;
            $paddingSize = max(100, $remaining - 200); // Leave some room for JSON structure
            $data['_padding'] = $this->generateRandomString($paddingSize);
        }

        return response()->json([
            'message' => 'Random 1MB JSON payload',
            'size_info' => [
                'approximate_size_bytes' => strlen(json_encode($data)),
                'item_count' => count($data) - (isset($data['_padding']) ? 1 : 0),
                'generated_at' => now()->toISOString()
            ],
            'data' => $data
        ]);
    }

    private function generateRandomString(int $length): string
    {
        $characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789 .,!?';
        $result = '';

        for ($i = 0; $i < $length; $i++) {
            $result .= $characters[rand(0, strlen($characters) - 1)];
        }

        return $result;
    }

    private function generateRandomArray(int $count): array
    {
        $array = [];
        for ($i = 0; $i < $count; $i++) {
            $array[] = $this->generateRandomString(rand(5, 20));
        }
        return $array;
    }

    private function generateRandomAttributes(): array
    {
        return [
            'color' => $this->generateRandomString(10),
            'size' => rand(1, 100),
            'weight' => mt_rand() / mt_getrandmax() * 1000,
            'category' => $this->generateRandomString(15),
            'subcategory' => $this->generateRandomString(20),
            'status' => ['active', 'inactive', 'pending'][rand(0, 2)],
            'priority' => rand(1, 10),
            'flags' => $this->generateRandomArray(5)
        ];
    }
}
