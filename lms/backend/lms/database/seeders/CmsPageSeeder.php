<?php

namespace Database\Seeders;

use App\Models\CmsPage;
use Illuminate\Database\Seeder;

class CmsPageSeeder extends Seeder
{
    public function run(): void
    {
        $defaults = [
            'privacy-policy' => [
                'title' => 'Privacy Policy',
                'body' => '<p>Update this page from the dashboard with your institute privacy policy.</p>',
            ],
            'terms' => [
                'title' => 'Terms & Conditions',
                'body' => '<p>Update this page from the dashboard with your terms and conditions.</p>',
            ],
            'about-us' => [
                'title' => 'About Us',
                'body' => '<p>Update this page from the dashboard with information about DSA Edu.</p>',
            ],
        ];

        foreach ($defaults as $slug => $data) {
            CmsPage::query()->updateOrCreate(
                ['slug' => $slug],
                [
                    'title' => $data['title'],
                    'body' => $data['body'],
                    'is_published' => true,
                ],
            );
        }
    }
}
