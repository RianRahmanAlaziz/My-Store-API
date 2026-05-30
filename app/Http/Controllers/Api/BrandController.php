<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\BrandResource;
use App\Http\Requests\StoreBrandRequest;
use App\Http\Requests\UpdateBrandRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\Brand;
use App\Traits\ApiResponse;

class BrandController extends Controller
{
    use ApiResponse;

    public function index(Request $request)
    {
        $perPage = min((int) $request->get('per_page', 10), 50);

        $brands = Brand::query()
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
            BrandResource::collection($brands),
            'Brands retrieved successfully'
        );
    }

    public function store(StoreBrandRequest $request)
    {
        $brand = Brand::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
        ]);

        return $this->created(new BrandResource($brand), 'Brand created successfully');
    }

    public function show(Brand $brand)
    {
        return $this->success(
            new BrandResource($brand),
            'Brand retrieved successfully'
        );
    }

    public function update(UpdateBrandRequest $request, Brand $brand)
    {
        $brand->update([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
        ]);

        return $this->success(
            new BrandResource($brand),
            'Brand updated successfully'
        );
    }

    public function destroy(Brand $brand)
    {
        $brand->delete();

        return $this->deleted(
            'Brand deleted successfully'
        );
    }
}
