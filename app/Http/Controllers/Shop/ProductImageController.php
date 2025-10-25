<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use File;
use App\Models\ProductImage;

class ProductImageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if ($request->hasFile('image')) {
            $pimage = new ProductImage();
            $pimage->product_id = $request['product_id'];
            $pimage->save();
            //  Let's do everything here
            if ($request->file('image')->isValid()) {
                $validated = $request->validate([
                    'image' => 'mimes:jpeg,png,webp,gif,jfif,avif|max:1014',
                ]);

                $img_path = storage_path('/prod-images/'.$pimage->img_url);
                if (File::exists($img_path)) {
                    unlink($img_path);
                }

                $extension = $request->image->extension();
                $request->image->storeAs('/prod-images', $pimage->id.'_pimage.'.$extension);
                $location = 'prod-images/'.$pimage->id.'_pimage.'.$extension;
                $pimage->img_url = $location;
                $pimage->save();

                return redirect()->route('products.show', encrypt($request['product_id']))->with('success', 'Product Image added successfully');
            }
        }else {
            return redirect()->back()->with('error', 'Please select an Image');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $pimage = ProductImage::find(decrypt($id));
        if (!is_null($pimage)) {
            $img_path = storage_path('/prod-images/'.$pimage->img_url);
            if (File::exists($img_path)) {
                unlink($img_path);
            }
            $pimage->delete();

            return redirect()->back()->with('success', 'Product Image removed successfully');
        }else{
            return redirect()->back()->with('error', 'Item not Found');
        }
    }
}
