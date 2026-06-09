<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CmsPage extends Model
{
    protected $fillable = [
        'slug',
        'title',
        'body',
        'is_published',
    ];

    protected function casts(): array
    {
        return [
            'is_published' => 'boolean',
        ];
    }

    public static function slugs(): array
    {
        return [
            'privacy-policy' => 'Privacy Policy',
            'terms' => 'Terms & Conditions',
            'about-us' => 'About Us',
        ];
    }
}
