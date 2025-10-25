<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\QuoteRequest;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductUnit;
use App\Models\OrderDetail;
use App\Models\OrderItem;
use App\Models\DeliveryRate;

class WelcomeController extends Controller
{
    public function index()
    {
        return redirect('login');
    }

    public function productDetail($id)
    {
        $product = Product::find(decrypt($id));
        $images = ProductImage::where('product_id', $product->id)->select('img_url')->get();
        $dunit = ProductUnit::where('product_id', $product->id)->select('id', 'unit_name', 'unit_price')->first();
        $punits = ProductUnit::where('product_id', $product->id)->select('id', 'unit_name', 'unit_price')->get();
        $reviews = [];
        $relatedproducts = [];
        $upsellproducts = [];
        return response()->json(['product' => $product, 'images' => $images, 'dunit' => $dunit, 'punits' => $punits, 'reviews' => $reviews, 'relatedproducts' => $relatedproducts, 'upsellproducts' => $upsellproducts]);
    }

    public function quoteRequest(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'phone' => 'required|numeric',
            'message' => 'required'
        ]);

        QuoteRequest::create($request->all());
    }

    public function clientDashboard($id)        
    {
        $totalorders = OrderDetail::where('user_id', $id)->count();
        $pendings = OrderDetail::where('user_id', $id)->where('status', 'Pending')->count();
        $orders = OrderItem::join('order_details', 'order_details.id', '=', 'order_items.order_detail_id')->where('user_id', $id)->join('products', 'products.id', '=', 'order_items.product_id')->select('order_details.id as id', 'name', 'image_url', 'uom', 'quantity', 'price', 'status')->get();

        return response()->json(['totalorders' => $totalorders, 'pendings' => $pendings, 'orders' => $orders]);
    }

    public function deliveryRate()
    {
        $drate = DeliveryRate::find(1);

        return response()->json(['drate' => $drate]);
    }
}
