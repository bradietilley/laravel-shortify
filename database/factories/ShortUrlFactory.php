<?php

namespace BradieTilley\Shortify\Database\Factories;

use BradieTilley\Shortify\Models\ShortifyUrl;
use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\BradieTilley\Shortify\Models\ShortifyUrl>
 */
class ShortUrlFactory extends Factory
{
    protected $model = ShortifyUrl::class;

    public function definition()
    {
        return [
            'ulid' => (string) Str::ulid(),
            'code' => Str::random(12),
            'original_url' => 'https://localhost/long-url/'.Str::random(48),
        ];
    }

    public function original(string $original): static
    {
        return $this->state([
            'original_url' => $original,
        ]);
    }

    public function code(string $code): static
    {
        return $this->state([
            'code' => $code,
        ]);
    }

    public function expires(Carbon|CarbonImmutable $date): static
    {
        return $this->state([
            'expires_at' => $date->toDateTimeString(),
        ]);
    }
}
