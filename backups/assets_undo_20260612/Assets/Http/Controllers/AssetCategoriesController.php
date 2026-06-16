<?php

namespace App\Modules\Assets\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Assets\Http\Requests\AssetCategoryRequest;
use App\Modules\Assets\Repositories\AssetCategoryRepository;
use Illuminate\Http\Request;

class AssetCategoriesController extends Controller
{
    private $categories;

    public function __construct(AssetCategoryRepository $categories)
    {
        $this->categories = $categories;
    }

    public function index()
    {
        $categories = $this->categories->getAll();

        return view('assets::categories.index', compact('categories'));
    }

    public function create()
    {
        return view('assets::categories.create');
    }

    public function store(AssetCategoryRequest $request)
    {
        $category = $this->categories->create($request->all());
        $request->session()->flash('success', 'Asset category created successfully.');

        return redirect()->route('assets.categories.edit', $category->id);
    }

    public function edit($id)
    {
        $category = $this->categories->getById($id);

        return view('assets::categories.edit', compact('category'));
    }

    public function update($id, AssetCategoryRequest $request)
    {
        $category = $this->categories->update($id, $request->all());
        $request->session()->flash('success', 'Asset category updated successfully.');

        return redirect()->route('assets.categories.edit', $category->id);
    }

    public function destroy($id, Request $request)
    {
        $this->categories->delete($id);
        $request->session()->flash('success', 'Asset category deleted successfully.');

        return redirect()->route('assets.categories.index');
    }
}
