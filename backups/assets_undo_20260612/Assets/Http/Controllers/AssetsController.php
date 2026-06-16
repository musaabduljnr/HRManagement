<?php

namespace App\Modules\Assets\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Assets\Http\Requests\AssetRequest;
use App\Modules\Assets\Repositories\AssetCategoryRepository;
use App\Modules\Assets\Repositories\AssetRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AssetsController extends Controller
{
    private $assets;
    private $categories;

    public function __construct(AssetRepository $assets, AssetCategoryRepository $categories)
    {
        $this->assets = $assets;
        $this->categories = $categories;
    }

    public function index()
    {
        $assets = $this->assets->getWithCategory();

        return view('assets::assets.index', compact('assets'));
    }

    public function create()
    {
        $categories = $this->categories->getAll();

        return view('assets::assets.create', compact('categories'));
    }

    public function store(AssetRequest $request)
    {
        $data = $request->all();
        if ($request->hasFile('image')) {
            $data['image'] = Storage::putFile('assets', $request->file('image'));
        }

        $asset = $this->assets->create($data);
        $request->session()->flash('success', 'Asset created successfully.');

        return redirect()->route('assets.edit', $asset->id);
    }

    public function edit($id)
    {
        $asset = $this->assets->getById($id);
        $categories = $this->categories->getAll();

        return view('assets::assets.edit', compact('asset', 'categories'));
    }

    public function update($id, AssetRequest $request)
    {
        $data = $request->all();
        if ($request->hasFile('image')) {
            $data['image'] = Storage::putFile('assets', $request->file('image'));
        }

        $asset = $this->assets->update($id, $data);
        $request->session()->flash('success', 'Asset updated successfully.');

        return redirect()->route('assets.edit', $asset->id);
    }

    public function destroy($id, Request $request)
    {
        $this->assets->delete($id);
        $request->session()->flash('success', 'Asset deleted successfully.');

        return redirect()->route('assets.index');
    }
}
