<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class HolidayService
{
    /**
     * Get upcoming public holidays or holidays around surgical schedule.
     * Gracefully fails if offline or API is unreachable.
     *
     * @param string $countryCode
     * @return array
     */
    public function getUpcomingHolidays(string $countryCode = 'US'): array
    {
        $cacheKey = "public_holidays_{$countryCode}_" . now()->format('Y-m');

        return Cache::remember($cacheKey, 3600, function () use ($countryCode) {
            $baseUrl = config('services.nager_date.url', env('NAGER_DATE_API_URL', 'https://date.nager.at/api/v3'));
            $year = now()->year;

            try {
                $response = Http::timeout(3)
                    ->retry(1, 100)
                    ->get("{$baseUrl}/PublicHolidays/{$year}/{$countryCode}");

                if ($response->successful()) {
                    return $response->json();
                }

                Log::warning("Nager.Date API returned HTTP status: " . $response->status());
            } catch (\Throwable $e) {
                Log::notice("Nager.Date public holiday API unreachable: " . $e->getMessage());
            }

            // Graceful fallback dummy holidays for offline/test environments
            return [
                [
                    'date' => now()->format('Y') . '-01-01',
                    'localName' => "New Year's Day",
                    'name' => "New Year's Day",
                    'countryCode' => $countryCode,
                ],
                [
                    'date' => now()->format('Y') . '-12-25',
                    'localName' => 'Christmas Day',
                    'name' => 'Christmas Day',
                    'countryCode' => $countryCode,
                ],
            ];
        });
    }

    /**
     * Check if a specific date falls on a public holiday.
     *
     * @param string|null $date
     * @param string $countryCode
     * @return array|null
     */
    public function checkDateHoliday(?string $date, string $countryCode = 'US'): ?array
    {
        if (blank($date)) {
            return null;
        }

        $holidays = $this->getUpcomingHolidays($countryCode);

        foreach ($holidays as $holiday) {
            if (isset($holiday['date']) && $holiday['date'] === $date) {
                return $holiday;
            }
        }

        return null;
    }
}
