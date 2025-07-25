<?php

namespace App\Http\Controllers\Landlord;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use App\Models\Category;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class BlogController extends Controller
{
    public function index()
    {
        $blogs = Blog::latest()
            ->with('user','categories')
            ->paginate(10);
       

        return Inertia::render('Landlord/Blogs/Index', [
            'blogs' => $blogs,
            
        ]);
    }

    public function create()
    {
        return Inertia::render('Landlord/Blogs/Create', [
            'categories' => Category::all(),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'featured_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_published' => 'boolean'
        ]);

        $imagePath = null;
        if ($request->hasFile('featured_image')) {
            $imagePath = $request->file('featured_image')->store('blog_images', 'public');
        }


        $blog = Blog::create([
            'title' => $request->title,
            'content' => $request->content,
            'featured_image' => $imagePath,
            'is_published' => $request->is_published ?? false,
            'published_at' => $request->is_published ? now() : null,
            'user_id' => auth()->id()
        ]);

        $blog->categories()->sync($request->category_ids);

        return redirect()->route('landlord.blogs.index')
            ->with('success', 'Blog post created successfully.');
    }

    public function edit(Blog $blog)
    {

        return Inertia::render('Landlord/Blogs/Edit', [
            'blog' => $blog->load('categories'),
            'categories' => Category::all(),
        ]);
    }

    public function update(Request $request, Blog $blog)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'featured_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_published' => 'boolean',
            'remove_image' => 'boolean'
        ]);

        $imagePath = $blog->featured_image;
        
        if ($request->remove_image) {
            if ($imagePath) {
                Storage::disk('public')->delete($imagePath);
            }
            $imagePath = null;
        } elseif ($request->hasFile('featured_image')) {
            if ($imagePath) {
                Storage::disk('public')->delete($imagePath);
            }
            $imagePath = $request->file('featured_image')->store('blog_images', 'public');
        }

        $blog->update([
            'title' => $request->title,
            'content' => $request->content,
            'featured_image' => $imagePath,
            'is_published' => $request->is_published,
            'published_at' => $request->is_published ? ($blog->published_at ?? now()) : null
        ]);

        $blog->categories()->sync($request->category_ids);

        return redirect()->route('landlord.blogs.index')
            ->with('success', 'Blog post updated successfully.');
    }

    public function destroy(Blog $blog)
    {
        if ($blog->featured_image) {
            Storage::disk('public')->delete($blog->featured_image);
        }
        
        $blog->delete();

        return redirect()->route('landlord.blogs.index')
            ->with('success', 'Blog post deleted successfully.');
    }
}
