<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductVariant;
use App\Models\ProductVariantPrice;
use App\Models\Variant;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Validator;
use Intervention\Image\Facades\Image;
use File;
use Illuminate\Support\Facades\Storage;
class ProductController extends Controller
{
    const ITEM_PER_PAGE = 2;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Http\Response|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        $products = Product::orderBy('id')->paginate(static::ITEM_PER_PAGE);
        $variants = Variant::all();
        $total = Product::count();
        return view('products.index', compact('products', 'variants', 'total'));
        // $product = DB::table('product_variant_prices')
                 // ->join('products', 'product_variants.product_id', 'products.id')
                // ->join('variants', 'product_variants.variant_id', 'variants.id')
                // ->join('products', 'product_variant_prices.product_id', 'products.id')
                
                // ->join('product_variant_prices', 'products.id', 'product_variant_prices.id')
                // ->select('products.*','product_variant_prices.*,variants.title')
                // ->orderBy('product_variant_prices.id')
                // ->get();
    	// return view('products.index',compact('product'));
        // $products = product::all();
        // return view('products.index');
    }
    public function filter(Request $request)
    {
        $searchParams = $request->all();
        $title = Arr::get($searchParams, 'title', '');
        $variant = Arr::get($searchParams, 'variant', '');
        $price_from = Arr::get($searchParams, 'price_from', '');
        $price_to = Arr::get($searchParams, 'price_to', '');
        $date = Arr::get($searchParams, 'date', '');
        $products = [];
        if(!empty($title)){
            $products = Product::where('title', 'like', '%'.$title.'%')->paginate(static::ITEM_PER_PAGE);
        }
        if(!empty($date)){
            $products = Product::where('created_at', 'like', '%'.$date.'%')->paginate(static::ITEM_PER_PAGE);
        }
        if(!empty($variant)){
            $productVariant = ProductVariantPrice::where('product_variant_one', $variant)->orwhere('product_variant_two', $variant)->orwhere('product_variant_three', $variant)->first();
            if(!empty($productVariant)){
                $products = Product::where('id', $productVariant->product_id)->paginate(static::ITEM_PER_PAGE);
            }
        }
        
        if(!empty($price_from) && !empty($price_to)){
            $productVariants = ProductVariantPrice::whereBetween('price', [$price_from, $price_to])->get();
            foreach ($productVariants as $productVariant) {
                $product = Product::where('id', $productVariant->product_id)->first();
                $exists = Arr::exists($products, $product->id);
                if(!$exists){
                    $products[] = $product;
                }
            }
        }
        if(!empty($price_from) && empty($price_to)){
            $productVariants = ProductVariantPrice::where('price', '>=', $price_from)->get();
            foreach ($productVariants as $productVariant) {
                $products[] = Product::where('id', $productVariant->product_id)->first();
                $exists = Arr::exists($products, $product->id);
                if(!$exists){
                    $products[] = $product;
                }
                    
            }
        }
        if(!empty($price_to) && empty($price_from)){
            $productVariants = ProductVariantPrice::where('price', '<=', $price_from)->get();
            foreach ($productVariants as $productVariant) {
                $products[] = Product::where('id', $productVariant->product_id)->first();
                $exists = Arr::exists($products, $product->id);
                if(!$exists){
                    $products[] = $product;
                }
            }
        }
        $total = Product::count();
        $variants = Variant::all();
        return view('products.index', compact('products', 'variants', 'total'));

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Http\Response|\Illuminate\View\View
     */
    public function create()
    {
        $variants = Variant::all();
        return view('products.create', compact('variants'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {

    }


    /**
     * Display the specified resource.
     *
     * @param \App\Models\Product $product
     * @return \Illuminate\Http\Response
     */
    public function show($product)
    {

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Models\Product $product
     * @return \Illuminate\Http\Response
     */
    public function edit(Product $product)
    {
        $variants = Variant::all();
        // return view('products.edit', compact('variants'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Product $product
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Product $product)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\Product $product
     * @return \Illuminate\Http\Response
     */
    public function destroy(Product $product)
    {
        //
    }
}
