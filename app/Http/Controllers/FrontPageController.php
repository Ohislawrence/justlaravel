<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Blog;
use App\Models\SubscriptionPlan;
use Inertia\Inertia;
use Illuminate\Support\Str;

class FrontPageController extends Controller
{
    public function index()
    {
        // Get recent blogs (excluding the featured one)
        $recentBlogs = Blog::with('categories')
            ->where('is_published', true)
            ->latest()
            ->take(3)
            ->get();

        $plans = SubscriptionPlan::active()
            ->with('features')
            ->orderBy('monthly_price')
            ->get()
            ->map(function ($plan) {
                // Add computed properties
                $plan->can_use_ai = (bool) $plan->getFeatureValue('ai_question_generation');
                return $plan;
            });

        return Inertia::render('FrontPages/Index', [
            'recentBlogs' => $recentBlogs,
            'plans' => $plans,
            'meta' => [
                'title' => 'Revolutionize Assessment with AI-Powered Quiz/Test Creation',
                'description' => 'The complete exam platform designed for Nigerian educational institutions, corporations, and training organizations. Create, customize, and deploy intelligent assessments that drive real learning outcomes.',
                'image' => asset('images/examportalonlineimage.png'),
                'url' => url()->current()
        ]
        ]);
    }

    public function allBlogs()
    {
        $blogs = Blog::with(['categories', 'user'])
            ->where('is_published', true)
            ->latest()
            ->paginate(9);

        return Inertia::render('FrontPages/Blogs/Index', [
            'blogs' => $blogs,
            'meta' => [
                'title' => 'Updates and News',
                'description' => 'Tips, insights, and best practices for Nigerian educators to enhance learning outcomes and student engagement.',
                'image' => asset('images/examportalonlineimage.png'),
                'url' => url()->current()
        ]
        ]);
    }

    public function show(Blog $blog)
    {
        if (!$blog->is_published) {
            abort(404);
        }

        $relatedBlogs = Blog::with('categories')
            ->where('is_published', true)
            ->where('id', '!=', $blog->id)
            ->whereHas('categories', function($query) use ($blog) {
                $query->whereIn('id', $blog->categories->pluck('id'));
            })
            ->latest()
            ->take(3)
            ->get();

        return Inertia::render('FrontPages/Blogs/Show', [
            'blog' => $blog->load(['categories', 'user']),
            'relatedBlogs' => $relatedBlogs,
            'meta' => [
                'title' => $blog->title,
                'description' => Str::of(strip_tags($blog->content))->limit(40),
                'image' => url('/storage/' . $blog->featured_image),
                'url' => url()->current()
        ]
        ]);
    }
    
    public function features()
    {
        return Inertia::render('FrontPages/Features',[
            'meta' => [
                'title' => 'Features',
                'description' => 'Discover the comprehensive set of tools designed to make creating, delivering, and analyzing quizzes effortless for Nigerian educators.',
                'image' => asset('images/examportalonlineimage.png'),
                'url' => url()->current()
        ]
            ]);
    }
    public function pricing()
    {
        $plans = SubscriptionPlan::active()
            ->with('features')
            ->orderBy('monthly_price')
            ->get()
            ->map(function ($plan) {
                // Add computed properties
                $plan->can_use_ai = (bool) $plan->getFeatureValue('ai_question_generation');
                return $plan;
            });
            return Inertia::render('FrontPages/Pricing',[
            'plans' => $plans,
            'meta' => [
                'title' => 'Pricing',
                'description' => 'Flexible pricing options designed for individual teachers, schools, and large institutions. All plans are priced in Nigerian Naira (₦).',
                'image' => asset('images/examportalonlineimage.png'),
                'url' => url()->current()
        ]
        ]);
    }

    public function aboutUs()
    {
        return Inertia::render('FrontPages/About',[
            'meta' => [
                'title' => 'About Us',
                'description' => 'We are on a mission to revolutionize education in Nigeria by providing innovative, accessible, and culturally relevant assessment tools.',
                'image' => asset('images/examportalonlineimage.png'),
                'url' => url()->current()
        ]
            ]);
    }

    public function tos()
    {
        return Inertia::render('FrontPages/OtherPages/Tos');
    }

    public function cookie()
    {
        return Inertia::render('FrontPages/OtherPages/Cookies');
    }

    public function policy()
    {
        return Inertia::render('FrontPages/OtherPages/Policy');
    }

    public function contact()
    {
        return Inertia::render('FrontPages/Contact',[
            'meta' => [
                'title' => 'Contact Us',
                'description' => 'Reach out to us, we are available.',
                'image' => asset('images/examportalonlineimage.png'),
                'url' => url()->current()
        ]
            ]);
    }

    //who it's for
    public function hr()
    {
        return Inertia::render('FrontPages/Whoitsfor/HumanResource',[
            'meta' => [
                'title' => 'HR Solutions',
                'description' => 'Streamline hiring, training, and employee development with our powerful assessment platform designed specifically for HR teams.',
                'image' => asset('images/examportalonlineimage.png'),
                'url' => url()->current()
        ]
            ]);
    }

    public function ld()
    {
        return Inertia::render('FrontPages/Whoitsfor/LearningDevelopment',[
            'meta' => [
                'title' => 'Learning & Development',
                'description' => 'Create engaging training programs, measure learning outcomes, and develop talent with our powerful assessment platform.',
                'image' => asset('images/examportalonlineimage.png'),
                'url' => url()->current()
        ]
            ]);
    }

    public function certifications()
    {
        return Inertia::render('FrontPages/Whoitsfor/Certifications',[
            'meta' => [
                'title' => 'Certifications',
                'description' => 'Create, deliver, and manage professional certification exams with our secure, scalable platform.',
                'image' => asset('images/examportalonlineimage.png'),
                'url' => url()->current()
        ]
            ]);
    }

    public function schools()
    {
        return Inertia::render('FrontPages/Whoitsfor/Schools',[
            'meta' => [
                'title' => 'Schools & Educational Centers',
                'description' => 'Empower educators with smart assessment tools that save time, engage students, and improve learning outcomes.',
                'image' => asset('images/examportalonlineimage.png'),
                'url' => url()->current()
        ]
            ]);
    }

}
