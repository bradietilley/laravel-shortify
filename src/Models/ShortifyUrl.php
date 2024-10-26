<?php

namespace BradieTilley\Shortify\Models;

use BradieTilley\Shortify\Database\Factories\ShortifyUrlFactory;
use BradieTilley\Shortify\Events\ExpiredUrlAttempted;
use BradieTilley\Shortify\Events\InvalidUrlAttempted;
use BradieTilley\Shortify\Events\UrlCreated;
use BradieTilley\Shortify\Events\UrlDeleted;
use BradieTilley\Shortify\Events\UrlExpired;
use BradieTilley\Shortify\Events\UrlVisited;
use BradieTilley\Shortify\Exceptions\ShortifyException;
use BradieTilley\Shortify\Shortify;
use BradieTilley\Shortify\ShortifyConfig;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * @property-read int $id
 *
 * @property string $code
 * @property string $original_url
 * @property int $visit_count
 * @property ?CarbonImmutable $expires_at
 * @property bool $expired
 *
 * @property-read string $url
 *
 * @property Collection<int, ShortifyVisit> $visits
 *
 * @method static ShortifyUrlFactory factory(callable|array|int|null $count = null, callable|array $state = [])
 */
class ShortifyUrl extends Model
{
    /** @use HasFactory<ShortifyUrlFactory> */
    use HasFactory;

    public $table = 'shortify_urls';

    protected $guarded = [];

    public $hidden = [
    ];

    /** @var array<string, class-string> */
    protected $dispatchesEvents = [
        'created' => UrlCreated::class,
        'deleted' => UrlDeleted::class,
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'expired' => 'boolean',
            'visit_count' => 'integer',
            'expires_at' => 'immutable_datetime',
        ];
    }

    /**
     * @return HasMany<ShortifyVisit, $this>
     */
    public function visits(): HasMany
    {
        return $this->hasMany(ShortifyConfig::getShortUrlVisitModel(), 'shortify_url_id');
    }

    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory(): ShortifyUrlFactory
    {
        return new ShortifyUrlFactory();
    }

    public function getRouteKeyName()
    {
        return 'code';
    }

    /**
     * @param Builder<static>
     * @return Builder<static>
     */
    public function scopeByCode(Builder $query, string $code): Builder
    {
        $query = $query->withoutGlobalScopes();

        if (DB::getDriverName() === 'mysql') {
            return $query->whereRaw('BINARY code = ?', [ $code ]);
        }

        return $query->where('code', $code);
    }

    public static function byCode(string $code): ?ShortifyUrl
    {
        return static::query()->byCode($code)->first();
    }

    public function isExpired(): bool
    {
        $this->expireIfExpired();

        return $this->expired;
    }

    public function expireIfExpired(): static
    {
        if ($this->expired) {
            return $this;
        }

        $this->expired = $this->expires_at && $this->expires_at->lt(now());

        if ($this->expired) {
            $this->save();

            UrlExpired::dispatch($this);
        }

        return $this;
    }

    public function getRedirect(): RedirectResponse
    {
        return Shortify::make()->redirectToOriginalUrl($this);
    }

    public static function redirectTo(string $code): RedirectResponse
    {
        $url = static::byCode($code);

        $user = Shortify::make()->user();

        if ($url === null) {
            InvalidUrlAttempted::dispatch($code, $user);

            throw ShortifyException::notFound($code);
        }

        if ($url->isExpired()) {
            ExpiredUrlAttempted::dispatch($url, $user);

            throw ShortifyException::expired($url);
        }

        return $url->visit()->getRedirect();
    }

    public function url(): Attribute
    {
        return Attribute::get(
            fn () => Shortify::make()->getShortenedUrl($this),
        );
    }

    public function visit(): static
    {
        $user = Shortify::make()->user();
        /** @var Request $request */
        $request = request();

        $model = ShortifyConfig::getShortUrlVisitModel();
        $visit = new $model([
            'shortify_url_id' => $this->getKey(),
            'user_id' => $user?->getKey(),
            'ip' => $request->ip(),
            'user_agent' => $request->header('User-Agent'),
        ]);
        $visit->save();

        $this->increment('visit_count');

        UrlVisited::dispatch($this, $visit);

        return $this;
    }
}
