<?php

namespace Database\Seeders\Admin;

use App\Models\Admin\Feature;
use Illuminate\Database\Seeder;

class FeatureSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create predefined features
//        $features = [
//            [
//                'name' => 'Advanced Analytics',
//                'key' => 'advanced_analytics',
//                'description' => 'Access to advanced analytics and reporting features',
//                'is_active' => true,
//            ],
//            [
//                'name' => 'Priority Support',
//                'key' => 'priority_support',
//                'description' => '24/7 priority customer support',
//                'is_active' => true,
//            ],
//            [
//                'name' => 'Custom Branding',
//                'key' => 'custom_branding',
//                'description' => 'Customize the platform with your brand colors and logo',
//                'is_active' => true,
//            ],
//            [
//                'name' => 'API Access',
//                'key' => 'api_access',
//                'description' => 'Full API access for integrations',
//                'is_active' => true,
//            ],
//            [
//                'name' => 'Unlimited Users',
//                'key' => 'unlimited_users',
//                'description' => 'Add unlimited team members',
//                'is_active' => true,
//            ],
//            [
//                'name' => 'Multi-Location Support',
//                'key' => 'multi_location_support',
//                'description' => 'Manage multiple business locations',
//                'is_active' => true,
//            ],
//            [
//                'name' => 'Advanced Security',
//                'key' => 'advanced_security',
//                'description' => 'Enhanced security features and compliance',
//                'is_active' => true,
//            ],
//            [
//                'name' => 'White Label',
//                'key' => 'white_label',
//                'description' => 'Complete white label solution',
//                'is_active' => true,
//            ],
//        ];
//
//        foreach ($features as $feature) {
//            Feature::create($feature);
//        }

        // Create additional random features
        Feature::factory()->count(10)->create();
    }
}
