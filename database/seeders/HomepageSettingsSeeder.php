<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class HomepageSettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            // App Branding
            ['key' => 'app_name', 'value' => 'Zonal First Timers', 'type' => 'text'],

            // Hero Section
            ['key' => 'hero_badge_text', 'value' => 'Experience Excellence in Discipleship', 'type' => 'text'],
            ['key' => 'hero_title_1', 'value' => 'Raising', 'type' => 'text'],
            ['key' => 'hero_title_1_highlight', 'value' => 'Leaders', 'type' => 'text'],
            ['key' => 'hero_title_2', 'value' => 'Building', 'type' => 'text'],
            ['key' => 'hero_title_2_highlight', 'value' => 'Legacies', 'type' => 'text'],
            ['key' => 'hero_subtitle', 'value' => 'A modern platform dedicated to tracking and nurturing the spiritual growth of every first timer in the zone.', 'type' => 'text'],
            ['key' => 'hero_button_primary_text', 'value' => 'Member Portal Access', 'type' => 'text'],
            ['key' => 'hero_button_secondary_text', 'value' => 'Our Mandate', 'type' => 'text'],
            ['key' => 'hero_background_image', 'value' => '/assets/images/hero-bg.png', 'type' => 'image'],

            // Mission Section
            ['key' => 'mission_heading_1', 'value' => 'Our Discipleship', 'type' => 'text'],
            ['key' => 'mission_heading_highlight', 'value' => 'Pillars', 'type' => 'text'],
            ['key' => 'mission_subheading', 'value' => 'We are committed to the comprehensive growth and integration of every soul that walks through our doors.', 'type' => 'text'],

            // Card 1
            ['key' => 'mission_card_1_title', 'value' => 'Nurturing Souls', 'type' => 'text'],
            ['key' => 'mission_card_1_desc', 'value' => 'Dedicated follow-up systems ensuring no one is left behind in their spiritual journey after their first visit.', 'type' => 'text'],

            // Card 2
            ['key' => 'mission_card_2_title', 'value' => 'Foundation School', 'type' => 'text'],
            ['key' => 'mission_card_2_desc', 'value' => 'A structured curriculum designed to ground new converts in the core doctrines of the faith and church vision.', 'type' => 'text'],

            // Card 3
            ['key' => 'mission_card_3_title', 'value' => 'Membership Integration', 'type' => 'text'],
            ['key' => 'mission_card_3_desc', 'value' => 'Transitioning first timers into fully integrated, productive members of the local church community and workforce.', 'type' => 'text'],

            // Footer
            ['key' => 'footer_text', 'value' => 'For Zion\'s Sake, We Will Not Rest.', 'type' => 'text'],
        ];

        foreach ($settings as $setting) {
            \App\Models\HomepageSetting::updateOrCreate(
                ['key' => $setting['key']],
                ['value' => $setting['value'], 'type' => $setting['type']]
            );
        }
    }
}
