<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;
use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\Category;
use App\Traits\ApiResponse;


class CategoryController extends Controller
{
    use ApiResponse;

    public function index(Request $request)
    {
        $perPage = min((int) $request->get('per_page', 10), 50);

        $categories = Category::query()
            ->when(
                $request->search,
                fn($query, $search) =>
                $query->where('name', 'like', "%{$search}%")
            )
            ->when($request->sort === 'oldest', fn($query) => $query->oldest())
            ->when($request->sort !== 'oldest', fn($query) => $query->latest())
            ->paginate($perPage)
            ->withQueryString();

        return $this->paginated(
            CategoryResource::collection($categories),
            'Categories retrieved successfully'
        );
    }

    public function store(StoreCategoryRequest $request)
    {
        $category = Category::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
        ]);

        return $this->created(
            new CategoryResource($category),
            'Category created successfully'
        );
    }

    public function show(Category $category)
    {
        return $this->success(
            new CategoryResource($category),
            'Category retrieved successfully'
        );
    }

    public function update(UpdateCategoryRequest $request, Category $category)
    {
        $category->update([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
        ]);

        return $this->success(
            new CategoryResource($category),
            'Category updated successfully'
        );
    }

    public function destroy(Category $category)
    {
        $category->delete();

        return $this->deleted('Category deleted successfully');
    }
}
