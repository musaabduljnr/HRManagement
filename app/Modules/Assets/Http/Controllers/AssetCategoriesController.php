<?php

namespace App\Modules\Assets\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Assets\Repositories\Interfaces\AssetCategoryRepositoryInterface as AssetCategoryRepository;
use App\Modules\Assets\Http\Requests\AssetCategoryRequest;
use Datatables;
use Illuminate\Http\Request;

class AssetCategoriesController extends Controller
{
    private $assetCategoryRepository;

    public function __construct(AssetCategoryRepository $assetCategoryRepository)
    {
        $this->assetCategoryRepository = $assetCategoryRepository;
    }

    public function index()
    {
        return view('assets::categories.index');
    }

    public function getDatatable()
    {
        return Datatables::of($this->assetCategoryRepository->getCollection([], ['id', 'name', 'description', 'status']))
            ->addColumn('actions', function ($category) {
                return view('includes._datatable_actions', [
                    'deleteUrl' => route('assets.categories.destroy', $category->id),
                    'editUrl' => route('assets.categories.edit', $category->id)
                ]);
            })
            ->escapeColumns([])
            ->make(true);
    }

    public function create()
    {
        return view('assets::categories.create');
    }

    public function store(AssetCategoryRequest $request)
    {
        $data = $request->all();
        $this->assetCategoryRepository->create($data);
        $request->session()->flash('success', 'Asset category created successfully.');
        return redirect()->route('assets.categories.index');
    }

    public function edit($id)
    {
        $category = $this->assetCategoryRepository->getById($id);
        $breadcrumb = ['title' => $category->name, 'id' => $category->id];
        return view('assets::categories.edit', compact('category', 'breadcrumb'));
    }

    public function update($id, AssetCategoryRequest $request)
    {
        $data = $request->all();
        $this->assetCategoryRepository->update($id, $data);
        $request->session()->flash('success', 'Asset category updated successfully.');
        return redirect()->route('assets.categories.index');
    }

    public function destroy($id, Request $request)
    {
        $this->assetCategoryRepository->delete($id);
        $request->session()->flash('success', 'Asset category deleted successfully.');
        return redirect()->route('assets.categories.index');
    }
}
