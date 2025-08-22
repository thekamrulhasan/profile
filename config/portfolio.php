<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Portfolio Owner Information
    |--------------------------------------------------------------------------
    |
    | This configuration contains the portfolio owner's personal information
    | that will be displayed throughout the website.
    |
    */
    'owner' => [
        'name' => env('PORTFOLIO_OWNER_NAME', 'Hasan Kamrul Anik'),
        'email' => env('PORTFOLIO_OWNER_EMAIL', 'hasan.kamrul.anik@gmail.com'),
        'phone' => env('PORTFOLIO_OWNER_PHONE', '+8801729354682'),
        'location' => env('PORTFOLIO_OWNER_LOCATION', 'Dhaka, Bangladesh'),
        'title' => 'DevOps Engineer & Full Stack Developer',
        'bio' => 'Experienced DevOps Engineer with expertise in CI/CD, Infrastructure as Code, and full-stack web development. Passionate about automation, scalable systems, and modern development practices.',
    ],

    /*
    |--------------------------------------------------------------------------
    | Social Media Links
    |--------------------------------------------------------------------------
    */
    'social' => [
        'github' => env('PORTFOLIO_GITHUB_URL', 'https://github.com/hasankamrul'),
        'linkedin' => env('PORTFOLIO_LINKEDIN_URL', 'https://linkedin.com/in/hasankamrul'),
        'facebook' => env('FACEBOOK_URL'),
        'twitter' => env('TWITTER_URL'),
        'instagram' => env('INSTAGRAM_URL'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Analytics Configuration
    |--------------------------------------------------------------------------
    */
    'analytics' => [
        'google_analytics_id' => env('GOOGLE_ANALYTICS_ID'),
        'google_tag_manager_id' => env('GOOGLE_TAG_MANAGER_ID'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Content Settings
    |--------------------------------------------------------------------------
    */
    'content' => [
        'posts_per_page' => 10,
        'projects_per_page' => 12,
        'featured_projects_count' => 6,
        'featured_skills_count' => 8,
        'recent_posts_count' => 5,
    ],

    /*
    |--------------------------------------------------------------------------
    | File Upload Settings
    |--------------------------------------------------------------------------
    */
    'uploads' => [
        'max_file_size' => 10 * 1024 * 1024, // 10MB
        'allowed_image_types' => ['jpg', 'jpeg', 'png', 'gif', 'webp'],
        'allowed_document_types' => ['pdf', 'doc', 'docx'],
        'image_quality' => 85,
        'thumbnail_sizes' => [
            'small' => [150, 150],
            'medium' => [300, 300],
            'large' => [800, 600],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | SEO Settings
    |--------------------------------------------------------------------------
    */
    'seo' => [
        'default_title' => 'DevOps Engineer & Full Stack Developer',
        'title_separator' => ' | ',
        'default_description' => 'Professional DevOps Engineer and Full Stack Developer specializing in CI/CD, Infrastructure as Code, and modern web development.',
        'default_keywords' => 'DevOps, Full Stack Developer, Laravel, Python, AWS, Docker, Kubernetes, CI/CD',
        'default_image' => '/images/og-image.jpg',
    ],
];
