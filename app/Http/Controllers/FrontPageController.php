<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Blog;
use App\Models\SubscriptionPlan;
use Inertia\Inertia;

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
        ]);
    }
    
    public function features()
    {
        return Inertia::render('FrontPages/Features');
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
            'plans' => $plans
        ]);
    }

    public function aboutUs()
    {
        return Inertia::render('FrontPages/About');
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
        return Inertia::render('FrontPages/Contact');
    }

}
