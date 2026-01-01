<?php

namespace Database\Factories\Vendor;

use App\Models\SubCategory;
use App\Models\Vendor\Business;
use App\Models\Admin\Category;
use App\Models\Vendor\Vendor;
use Illuminate\Database\Eloquent\Factories\Factory;

class BusinessFactory extends Factory
{
    protected $model = Business::class;

    public function definition(): array
    {
        return [
            'company_name' => fake()->company(),
            'business_desc' => fake()->paragraph(5),

            'category_id' => Category::factory(),
            'subcategory_id' => null,
            'vendor_id' => Vendor::factory(),

            //  Location fields (NEW)
            'street_address' => fake()->streetAddress(),
            'city' => fake()->city(),
            'country' => fake()->country(),
            'postal_code' => fake()->postcode(),

            'venue_type' => fake()->randomElement(['Indoor', 'Outdoor', 'Both', null]),
            'member_type' => fake()->randomElement(['Premium', 'Standard', 'Basic']),
            'business_registration' => fake()->numerify('REG-######'),
            'business_license_number' => fake()->numerify('LIC-######'),
            'rating' => fake()->randomFloat(1, 3.0, 5.0),
            'is_featured' => fake()->boolean(20),

            'business_type' => fake()->randomElement([
                'partnership', 'llc', 'corporation', 'Service', 'Product', 'Venue'
            ]),

            'website' => fake()->boolean(60) ? fake()->url() : null,

            'social_links' => [
                'facebook' => fake()->url(),
                'instagram' => fake()->url(),
                'twitter' => fake()->url(),
            ],

            'business_email' => fake()->companyEmail(),
            'business_phone' => fake()->numerify('##########'),

            'features' => fake()->randomElements([
                'Parking Available',
                'Wheelchair Accessible',
                'WiFi Available',
                'Air Conditioned',
                'Outdoor Space',
                'Kitchen Facilities'
            ], rand(2, 4)),

            'profile_verification' => fake()->randomElement([
                'pending', 'verified', 'approved', 'under_review', 'rejected', 'banned'
            ]),

            'services' => fake()->randomElements([
                'Full Day Coverage',
                'Photo Album',
                'Video Editing',
                'Drone Photography',
                'Raw Files',
                'Online Gallery'
            ], rand(2, 4)),

            'faqs' => [
                [
                    'question' => 'What is your cancellation policy?',
                    'answer' => 'We require 30 days notice for cancellations.',
                ],
                [
                    'question' => 'Do you offer packages?',
                    'answer' => 'Yes, we have multiple packages available.',
                ],
            ],

            'portfolio_images' => [
                fake()->imageUrl(800, 600, 'business', true),
                fake()->imageUrl(800, 600, 'business', true),
                fake()->imageUrl(800, 600, 'business', true),
            ],

            'videos' => fake()->boolean(40) ? [fake()->url(), fake()->url()] : [],

            'capacity' => fake()->numberBetween(50, 1000),
            'view_count' => fake()->numberBetween(0, 5000),
            'social_count' => fake()->numberBetween(0, 10000),
            'last_login' => fake()->dateTimeBetween('-1 month', 'now'),

            'payment_days_advance' => fake()->numberBetween(7, 30),
            'payment_days_final' => fake()->numberBetween(1, 7),
            'services_radius' => fake()->numberBetween(10, 100),
            'advance_percentage' => fake()->randomFloat(1, 10.0, 50.0),

            'profile_image' => fake()->imageUrl(400, 400, 'business', true),
            'cover_image' => fake()->imageUrl(1200, 400, 'business', true),
            'chat_image' => fake()->imageUrl(200, 200, 'business', true),
            'chat_video' => fake()->boolean(30) ? fake()->url() : null,
            'chat_document' => fake()->boolean(20) ? fake()->url() : null,
        ];
    }


    /**
     * Indicate that the business is featured.
     */
    public function featured(): static
    {
        return $this->state(fn(array $attributes) => [
            'is_featured' => true,
            'member_type' => 'Premium',
        ]);
    }

    /**
     * Indicate that the business is verified.
     */
    public function verified(): static
    {
        return $this->state(fn(array $attributes) => [
            'profile_verification' => 'verified',
        ]);
    }

    /**
     * Indicate that the business is a venue.
     */
    public function venue(): static
    {
        return $this->state(fn(array $attributes) => [
            'business_type' => 'Venue',
            'venue_type' => fake()->randomElement(['Indoor', 'Outdoor', 'Both']),
            'capacity' => fake()->numberBetween(100, 1000),
        ]);
    }

    /**
     * Indicate that the business has high ratings.
     */
    public function highRated(): static
    {
        return $this->state(fn(array $attributes) => [
            'rating' => fake()->randomFloat(1, 4.5, 5.0),
        ]);
    }
}
