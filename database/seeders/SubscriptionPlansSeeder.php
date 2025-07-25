<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SubscriptionPlan;
use App\Models\SubscriptionFeature;

class SubscriptionPlansSeeder extends Seeder
{
    public function run()
    {
        // Create Features
        $features = [
            ['name' => 'AI Question Generation', 'slug' => 'ai_question_generation', 'description' => 'Access to AI-powered question generation'],
            ['name' => 'Quiz Attempts Limit', 'slug' => 'quiz_attempts_limit', 'description' => 'Maximum number of quiz attempts per user'],
            ['name' => 'Question Pool Limit', 'slug' => 'question_pool_limit', 'description' => 'Maximum number of question pools'],
            ['name' => 'Questions Limit', 'slug' => 'questions_limit', 'description' => 'Maximum number of questions'],
            ['name' => 'Users Limit', 'slug' => 'users_limit', 'description' => 'Maximum number of users'],
            ['name' => 'Storage Space', 'slug' => 'storage_space', 'description' => 'Storage space in MB'],
        ];

        foreach ($features as $feature) {
            SubscriptionFeature::create($feature);
        }

        // Create Plans
        $plans = [
            [
                'name' => 'Basic',
                'slug' => 'basic',
                'monthly_price' => 5000,
                'quarterly_price' => 13500, // 10% discount
                'yearly_price' => 48000, // 20% discount
                'trial_days' => 14,
                'is_active' => true,
                'description' => 'Basic plan with essential features',
                'features' => [
                    'ai_question_generation' => false,
                    'quiz_attempts_limit' => 5,
                    'question_pool_limit' => 3,
                    'questions_limit' => 50,
                    'users_limit' => 10,
                    'storage_space' => 100,
                ]
            ],
            [
                'name' => 'Standard',
                'slug' => 'standard',
                'monthly_price' => 14000,
                'quarterly_price' => 37800, // 10% discount
                'yearly_price' => 134400, // 20% discount
                'trial_days' => 14,
                'is_active' => true,
                'description' => 'Standard plan with more features',
                'features' => [
                    'ai_question_generation' => false,
                    'quiz_attempts_limit' => 15,
                    'question_pool_limit' => 10,
                    'questions_limit' => 200,
                    'users_limit' => 30,
                    'storage_space' => 500,
                ]
            ],
            [
                'name' => 'Premium',
                'slug' => 'premium',
                'monthly_price' => 20000,
                'quarterly_price' => 54000, // 10% discount
                'yearly_price' => 192000, // 20% discount
                'trial_days' => 14,
                'is_active' => true,
                'is_default' => true,
                'description' => 'Premium plan with all features',
                'features' => [
                    'ai_question_generation' => true,
                    'quiz_attempts_limit' => 9999, // Unlimited
                    'question_pool_limit' => 9999, // Unlimited
                    'questions_limit' => 9999, // Unlimited
                    'users_limit' => 9999, // Unlimited
                    'storage_space' => 2000,
                ]
            ],
        ];

        foreach ($plans as $planData) {
            $features = $planData['features'];
            unset($planData['features']);
            
            $plan = SubscriptionPlan::create($planData);
            
            foreach ($features as $slug => $value) {
                $feature = SubscriptionFeature::where('slug', $slug)->first();
                if ($feature) {
                    $plan->features()->attach($feature->id, ['value' => $value]);
                }
            }
        }
    }
}
