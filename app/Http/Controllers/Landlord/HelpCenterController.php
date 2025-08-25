<?php

namespace App\Http\Controllers\Landlord;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\HelpArticle;
use App\Models\HelpCategory;
use Inertia\Inertia;
use Illuminate\Support\Str;

class HelpCenterController extends Controller
{
    // Categories
    public function categoriesIndex()
    {
        return Inertia::render('Landlord/HelpCenter/Categories', [
            'categories' => HelpCategory::orderBy('order')->get()
        ]);
    }

    public function storeCategory(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'order' => 'nullable|integer'
        ]);

        $validated['slug'] = Str::slug($validated['name']);

        HelpCategory::create($validated);

        return redirect()->back()->with('success', 'Category created successfully');
    }

    public function updateCategory(Request $request, HelpCategory $category)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:help_categories,slug,'.$category->id,
            'description' => 'nullable|string',
            'order' => 'nullable|integer'
        ]);

        $category->update($validated);

        return redirect()->back()->with('success', 'Category updated successfully');
    }

    public function destroyCategory(HelpCategory $category)
    {
        $category->delete();
        return redirect()->back()->with('success', 'Category deleted successfully');
    }

    // Articles
    public function articlesIndex()
    {
        return Inertia::render('Landlord/HelpCenter/Article', [
            'articles' => HelpArticle::with('category')->latest()->get(),
            'categories' => HelpCategory::orderBy('name')->get()
        ]);
    }

    public function storeArticle(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'category_id' => 'required|exists:help_categories,id',
            'is_featured' => 'boolean',
            'is_published' => 'boolean'
        ]);

        $validated['slug'] = Str::slug($validated['title']);

        HelpArticle::create($validated);

        return redirect()->back()->with('success', 'Article created successfully');
    }

    public function updateArticle(Request $request, HelpArticle $article)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:help_articles,slug,'.$article->id,
            'content' => 'required|string',
            'category_id' => 'required|exists:help_categories,id',
            'is_featured' => 'boolean',
            'is_published' => 'boolean'
        ]);

        $article->update($validated);

        return redirect()->back()->with('success', 'Article updated successfully');
    }

    public function destroyArticle(HelpArticle $article)
    {
        $article->delete();
        return redirect()->back()->with('success', 'Article deleted successfully');
    }
}
