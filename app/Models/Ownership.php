<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ownership extends Model
{
    /** Legal documents flow (TR Tanzania): company fleet vehicles. */
    public const SLUG_COMPANY_OWNED = 'company_owned';

    protected $guarded = [];

    protected $casts = [
        'active' => 'boolean',
        'is_system' => 'boolean',
    ];

    /**
     * @return array<int, array{slug: string, type: string, description: string, sort_order: int}>
     */
    public static function coreDefinitions(): array
    {
        return [
            [
                'slug' => 'company_owned',
                'sort_order' => 1,
                'type' => 'Company-Owned',
                'description' => 'Vehicles fully owned by your company. Most important (your main fleet).',
            ],
            [
                'slug' => 'leased',
                'sort_order' => 2,
                'type' => 'Leased',
                'description' => 'Long-term vehicles but not owned. Common for scaling without big capital.',
            ],
            [
                'slug' => 'rented_short_term',
                'sort_order' => 3,
                'type' => 'Rented (Short-Term Hire)',
                'description' => 'Temporary vehicles (daily/monthly). Useful during peak demand.',
            ],
            [
                'slug' => 'contractor_third_party',
                'sort_order' => 4,
                'type' => 'Contractor / Third-Party',
                'description' => 'External vehicle owners working with you. Very important in logistics (outsourcing).',
            ],
            [
                'slug' => 'partner_company',
                'sort_order' => 5,
                'type' => 'Partner Company',
                'description' => 'Vehicles from another company under agreement. For long-term collaboration.',
            ],
            [
                'slug' => 'owner_operator',
                'sort_order' => 6,
                'type' => 'Owner-Operator',
                'description' => 'Individual owns and drives the vehicle. Common in real logistics operations.',
            ],
            [
                'slug' => 'client_owned',
                'sort_order' => 7,
                'type' => 'Client-Owned',
                'description' => 'Vehicle belongs to client but used by your company. Important for contract logistics.',
            ],
        ];
    }

    public static function syncDefaultsForCompany(int $companyId): void
    {
        foreach (self::coreDefinitions() as $def) {
            self::firstOrCreate(
                [
                    'company_id' => $companyId,
                    'slug' => $def['slug'],
                ],
                [
                    'type' => $def['type'],
                    'description' => $def['description'],
                    'active' => true,
                    'is_system' => true,
                    'sort_order' => $def['sort_order'],
                ]
            );
        }

        // Deactivate legacy placeholder ownerships that shouldn't be used anymore.
        // We keep them in DB to avoid breaking historical vehicle references.
        self::where('company_id', $companyId)
            ->whereNull('slug')
            ->whereIn('type', ['COMPANY', 'OTHERS'])
            ->update(['active' => false]);
    }

    public static function ensureDefaultsForCompany(int $companyId): void
    {
        $expected = count(self::coreDefinitions());
        $have = self::where('company_id', $companyId)->whereNotNull('slug')->count();
        if ($have < $expected) {
            self::syncDefaultsForCompany($companyId);
        }

        // Also enforce deactivation of legacy placeholders.
        self::where('company_id', $companyId)
            ->whereNull('slug')
            ->whereIn('type', ['COMPANY', 'OTHERS'])
            ->update(['active' => false]);
    }

    public function requiresLegalDocuments(): bool
    {
        return $this->slug === self::SLUG_COMPANY_OWNED;
    }
}
