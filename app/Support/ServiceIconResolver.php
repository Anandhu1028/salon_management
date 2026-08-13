<?php

namespace App\Support;

class ServiceIconResolver
{
    /**
     * Resolve the best icon key and alternatives from service text fields.
     *
     * @return array{primary: string, alternatives: array<int, string>, category: string|null, scores: array<string, int>}
     */
    public static function resolve(
        string $serviceName,
        ?string $category = null,
        ?string $subcategory = null
    ): array {
        $haystack = self::normalizeText(implode(' ', array_filter([
            $serviceName,
            $category,
            $subcategory,
        ])));

        $scores = self::scoreHaystack($haystack);

        if (empty($scores)) {
            return self::fallbackResult();
        }

        arsort($scores);
        $ranked = array_keys($scores);
        $primary = $ranked[0];

        $alternatives = self::buildAlternatives($primary, $ranked);

        return [
            'primary' => $primary,
            'alternatives' => $alternatives,
            'category' => config("service-icons.icons.{$primary}.category"),
            'scores' => $scores,
        ];
    }

    public static function normalize(?string $icon): string
    {
        $icon = strtolower(trim((string) $icon));
        $valid = self::validKeys();

        return in_array($icon, $valid, true) ? $icon : config('service-icons.default', 'default');
    }

    /**
     * @return array<int, string>
     */
    public static function validKeys(): array
    {
        return array_keys(config('service-icons.icons', []));
    }

    public static function label(string $icon): string
    {
        $icon = self::normalize($icon);

        return config("service-icons.icons.{$icon}.label", 'Salon Service');
    }

    /**
     * Export icon rules for client-side resolver (no business logic in Blade).
     *
     * @return array<string, mixed>
     */
    public static function exportForFrontend(): array
    {
        $icons = config('service-icons.icons', []);

        return [
            'default' => config('service-icons.default', 'default'),
            'icons' => collect($icons)->map(function (array $icon, string $key) {
                return [
                    'key' => $key,
                    'label' => $icon['label'],
                    'category' => $icon['category'],
                    'keywords' => $icon['keywords'],
                    'related' => $icon['related'] ?? [],
                ];
            })->values()->all(),
        ];
    }

    /**
     * @return array{primary: string, alternatives: array<int, string>, category: string|null, scores: array<string, int>}
     */
    private static function fallbackResult(): array
    {
        $default = config('service-icons.default', 'default');
        $related = config("service-icons.icons.{$default}.related", ['haircut', 'facial', 'spa']);

        return [
            'primary' => $default,
            'alternatives' => array_slice(array_values(array_unique($related)), 0, 3),
            'category' => config("service-icons.icons.{$default}.category"),
            'scores' => [],
        ];
    }

    /**
     * @return array<string, int>
     */
    private static function scoreHaystack(string $haystack): array
    {
        if ($haystack === '') {
            return [];
        }

        $scores = [];

        foreach (config('service-icons.icons', []) as $key => $icon) {
            if ($key === 'default') {
                continue;
            }

            $score = 0;

            foreach ($icon['keywords'] as $keyword) {
                $normalizedKeyword = self::normalizeText($keyword);

                if ($normalizedKeyword === '') {
                    continue;
                }

                if (str_contains($haystack, $normalizedKeyword)) {
                    $score += max(10, strlen($normalizedKeyword) * 2);

                    if (preg_match('/\b' . preg_quote($normalizedKeyword, '/') . '\b/u', $haystack)) {
                        $score += 5;
                    }
                }
            }

            if ($score > 0) {
                $scores[$key] = $score;
            }
        }

        return $scores;
    }

    /**
     * @param  array<int, string>  $ranked
     * @return array<int, string>
     */
    private static function buildAlternatives(string $primary, array $ranked): array
    {
        $alternatives = [];
        $related = config("service-icons.icons.{$primary}.related", []);

        foreach ($related as $key) {
            if ($key !== $primary && ! in_array($key, $alternatives, true)) {
                $alternatives[] = $key;
            }
        }

        foreach ($ranked as $key) {
            if ($key !== $primary && ! in_array($key, $alternatives, true)) {
                $alternatives[] = $key;
            }
        }

        $alternatives = array_values(array_filter(
            $alternatives,
            fn (string $key) => $key !== 'default' && in_array($key, self::validKeys(), true)
        ));

        if (count($alternatives) < 2) {
            foreach (['haircut', 'facial', 'spa', 'makeup'] as $fallbackKey) {
                if ($fallbackKey !== $primary && ! in_array($fallbackKey, $alternatives, true)) {
                    $alternatives[] = $fallbackKey;
                }
            }
        }

        return array_slice($alternatives, 0, 4);
    }

    private static function normalizeText(string $text): string
    {
        $text = strtolower(trim($text));
        $text = preg_replace('/[^\p{L}\p{N}\s&\-\/]+/u', ' ', $text) ?? $text;
        $text = preg_replace('/\s+/u', ' ', $text) ?? $text;

        return trim($text);
    }
}
