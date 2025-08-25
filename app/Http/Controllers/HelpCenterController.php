<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\HelpArticle;
use App\Models\HelpCategory;

class HelpCenterController extends Controller
{
    public function index()
    {
        return Inertia::render('HelpCenter/Index', [
            'categories' => HelpCategory::with(['articles' => function($query) {
                $query->published()->orderBy('title');
            }])->orderBy('order')->get(),
            'featuredArticles' => HelpArticle::published()
                ->featured()
                ->orderBy('views', 'desc')
                ->limit(5)
                ->get()
        ]);
    }

    public function showCategory($slug)
    {
        $category = HelpCategory::where('slug', $slug)
            ->with(['articles' => function($query) {
                $query->where('is_published', true)
                    ->orderBy('title');
            }])
            ->firstOrFail();

        return Inertia::render('HelpCenter/Category', [
            'category' => $category,
            'articles' => $category->articles,
            'categories' => HelpCategory::orderBy('order')->get()
        ]);
    }

    public function showArticle($categorySlug, $articleSlug)
    {
        $article = HelpArticle::with('category') // Eager load the category
            ->whereHas('category', function($query) use ($categorySlug) {
                $query->where('slug', $categorySlug);
            })
            ->where('slug', $articleSlug)
            ->firstOrFail();

        $article->incrementViews();

        return Inertia::render('HelpCenter/Article', [  // Changed from 'Articles' to 'Article' to match standard naming
            'article' => $article,
            'relatedArticles' => HelpArticle::with('category') // Eager load for related articles
                ->published()
                ->where('category_id', $article->category_id)
                ->where('id', '!=', $article->id)
                ->limit(5)
                ->get(),
            'categories' => HelpCategory::orderBy('order')->get()
        ]);
    }

    public function search(Request $request)
    {
        $query = $request->input('query');

        return Inertia::render('HelpCenter/Search', [
            'results' => HelpArticle::published()
                ->where('title', 'like', "%{$query}%")
                ->orWhere('content', 'like', "%{$query}%")
                ->with('category')
                ->get(),
            'query' => $query,
            'categories' => HelpCategory::orderBy('order')->get()
        ]);
    }
}
