<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FeatureUsage extends Model
{
    protected $fillable = [
        'organization_id',
        'feature_slug',
        'used',
        'period_start',
        'period_end',
    ];

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    public static function recordUsage($organizationId, $featureSlug, $amount = 1)
    {
        $now = now();
        $usage = self::firstOrCreate([
            'organization_id' => $organizationId,
            'feature_slug' => $featureSlug,
            'period_start' => $now->startOfMonth()->toDateString(),
            'period_end' => $now->endOfMonth()->toDateString(),
        ], [
            'used' => 0,
        ]);

        $usage->increment('used', $amount);
        
        return $usage;
    }

    public static function getUsage($organizationId, $featureSlug)
    {
        $now = now();
        return self::where('organization_id', $organizationId)
            ->where('feature_slug', $featureSlug)
            ->where('period_start', $now->startOfMonth()->toDateString())
            ->where('period_end', $now->endOfMonth()->toDateString())
            ->first();
    }
}
