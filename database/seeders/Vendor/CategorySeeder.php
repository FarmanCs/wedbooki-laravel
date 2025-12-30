<?php

namespace Database\Seeders\Vendor;

use App\Models\Vendor\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'type' => 'Photography',
                'description' => 'Professional wedding photography services to capture your special moments beautifully.',
                'image' => 'https://via.placeholder.com/640x480/FF6B6B/ffffff?text=Photography',
            ],
            [
                'type' => 'Videography',
                'description' => 'Cinematic wedding videography to preserve memories of your big day forever.',
                'image' => 'https://via.placeholder.com/640x480/4ECDC4/ffffff?text=Videography',
            ],
            [
                'type' => 'Catering',
                'description' => 'Exquisite catering services offering diverse cuisines for your wedding celebration.',
                'image' => 'https://via.placeholder.com/640x480/FFE66D/ffffff?text=Catering',
            ],
            [
                'type' => 'Venue',
                'description' => 'Beautiful and elegant venues perfect for hosting your dream wedding ceremony.',
                'image' => 'https://via.placeholder.com/640x480/95E1D3/ffffff?text=Venue',
            ],
            [
                'type' => 'Decoration',
                'description' => 'Creative decoration services to transform your venue into a magical space.',
                'image' => 'https://via.placeholder.com/640x480/F38181/ffffff?text=Decoration',
            ],
            [
                'type' => 'Music & Entertainment',
                'description' => 'Live music, DJs, and entertainment to keep your guests dancing all night.',
                'image' => 'https://via.placeholder.com/640x480/AA96DA/ffffff?text=Music',
            ],
            [
                'type' => 'Makeup & Hair',
                'description' => 'Professional makeup artists and hairstylists to make you look stunning.',
                'image' => 'https://via.placeholder.com/640x480/FCBAD3/ffffff?text=Makeup',
            ],
            [
                'type' => 'Wedding Planner',
                'description' => 'Expert wedding planners to coordinate every detail of your perfect day.',
                'image' => 'https://via.placeholder.com/640x480/A8D8EA/ffffff?text=Planner',
            ],
            [
                'type' => 'Florist',
                'description' => 'Stunning floral arrangements and bouquets for your wedding celebration.',
                'image' => 'https://via.placeholder.com/640x480/FFAAA5/ffffff?text=Florist',
            ],
            [
                'type' => 'Transportation',
                'description' => 'Luxury transportation services for the bride, groom, and wedding party.',
                'image' => 'https://via.placeholder.com/640x480/B4A7D6/ffffff?text=Transport',
            ],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }

        // Create additional random categories
        Category::factory()->count(10)->create();

        $this->command->info('Categories seeded successfully!');
        $this->command->info('Total categories: ' . Category::count());
    }
}
