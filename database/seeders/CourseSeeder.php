<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Course;

class CourseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $courses = [
            [
                'name' => 'Flutter Mobile Development',
                'slug' => 'flutter-mobile-development',
                'desc' => 'Build beautiful cross-platform mobile apps with Flutter and Dart',
                'price' => 1500,
                'stripe_price_id' => null
            ],
            [
                'name' => 'Backend Development Mastery',
                'slug' => 'backend-development-mastery',
                'desc' => 'Master backend development with Node.js, databases, and APIs',
                'price' => 2500,
                'stripe_price_id' => null
            ],
            [
                'name' => 'Frontend Development Pro',
                'slug' => 'frontend-development-pro',
                'desc' => 'Learn modern frontend with React, Vue, and advanced CSS techniques',
                'price' => 2000,
                'stripe_price_id' => null
            ],
            [
                'name' => 'AI & Machine Learning Fundamentals',
                'slug' => 'ai-machine-learning-fundamentals',
                'desc' => 'Discover AI and ML concepts with Python, TensorFlow, and practical projects',
                'price' => 2000,
                'stripe_price_id' => null
            ],
            [
                'name' => 'Full Stack Web Development',
                'slug' => 'full-stack-web-development',
                'desc' => 'Complete full stack course covering frontend, backend, and deployment',
                'price' => 4000,
                'stripe_price_id' => null
            ],
            [
                'name' => 'Software Testing & QA',
                'slug' => 'software-testing-qa',
                'desc' => 'Learn testing strategies, automation, and quality assurance best practices',
                'price' => 1000,
                'stripe_price_id' => null
            ]
        ];

        foreach ($courses as $course) {
            Course::create($course);
        }
    }
}
