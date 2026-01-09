<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QmaticSetting extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'health_center_id',
        'key',
        'value',
    ];

    protected $casts = [
        'value' => 'array',
    ];

    /**
     * Relation avec le centre de santé
     */
    public function healthCenter(): BelongsTo
    {
        return $this->belongsTo(HealthCenter::class);
    }

    /**
     * Obtenir une valeur de paramètre
     */
    public static function get(string $healthCenterId, string $key, $default = null)
    {
        $setting = self::where('health_center_id', $healthCenterId)
                      ->where('key', $key)
                      ->first();

        return $setting ? $setting->value : $default;
    }

    /**
     * Définir une valeur de paramètre
     */
    public static function set(string $healthCenterId, string $key, $value): void
    {
        self::updateOrCreate(
            [
                'health_center_id' => $healthCenterId,
                'key' => $key,
            ],
            [
                'value' => $value,
            ]
        );
    }
}
