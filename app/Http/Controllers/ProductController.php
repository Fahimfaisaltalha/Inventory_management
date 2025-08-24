<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;

class ProductController extends Controller
{


    function ProductPage(): View
    {
        return view('pages.dashboard.product-page');
    }


    function CreateProduct(Request $request)
    {
        $user_id = $request->header('user_id');

        // Accept both correct field names and the ones mistakenly used in Postman (productName, productPrice, etc.)
        // so that a mismatch doesn't crash with a 500 when $request->file('img') is null.
        $fallbackName        = $request->input('productName');
        $fallbackPrice       = $request->input('productPrice');
        $fallbackUnit        = $request->input('productUnit');
        $fallbackCategoryId  = $request->input('productCategory');

        $name        = $request->input('name', $fallbackName);
        $price       = $request->input('price', $fallbackPrice);
        $unit        = $request->input('unit', $fallbackUnit);
        $category_id = $request->input('category_id', $fallbackCategoryId);

        // Basic validation (you can replace with FormRequest later)
        $rules = [
            'name'        => 'required|string|max:255',
            'price'       => 'required|numeric',
            'unit'        => 'required|string|max:100',
            'category_id' => 'required|integer',
            'img'         => 'required|file|mimes:jpg,jpeg,png,webp|max:2048'
        ];

        // Manually build a data array for validation using resolved names
        $validationData = [
            'name'        => $name,
            'price'       => $price,
            'unit'        => $unit,
            'category_id' => $category_id,
            'img'         => $request->file('img') ?? $request->file('productImg')
        ];

        $validator = Validator::make($validationData, $rules);
        if ($validator->fails()) {
            return response()->json([
                'status'  => 'fail',
                'errors'  => $validator->errors()
            ], 422);
        }

        // Resolve the uploaded file (either correct key 'img' or mistaken 'productImg')
        $img = $request->file('img') ?? $request->file('productImg');

        // Prepare File Name & Path
        $t         = time();
        $file_name = $img->getClientOriginalName();
        $img_name  = "{$user_id}-{$t}-{$file_name}";
        $img_url   = "uploads/{$img_name}";

        // Upload File
        $img->move(public_path('uploads'), $img_name);

        // Save To Database
        $product = Product::create([
            'name'        => $name,
            'price'       => $price,
            'unit'        => $unit,
            'img_url'     => $img_url,
            'category_id' => $category_id,
            'user_id'     => $user_id
        ]);

        return response()->json([
            'status'  => 'success',
            'message' => 'Product created successfully',
            'data'    => $product
        ], 201);
    }


    function DeleteProduct(Request $request)
    {
        $user_id = $request->header('user_id');
        $product_id = $request->input('id');
        $filePath = $request->input('file_path');
        File::delete($filePath);
        return Product::where('id', $product_id)->where('user_id', $user_id)->delete();
    }


    function ProductByID(Request $request)
    {
        $user_id = $request->header('user_id');
        $product_id = $request->input('id');
        return Product::where('id', $product_id)->where('user_id', $user_id)->first();
    }


    function ProductList(Request $request)
    {
        $user_id = $request->header('user_id');
        return Product::where('user_id', $user_id)->get();
    }




    function UpdateProduct(Request $request)
    {
        $user_id = $request->header('user_id');
        $product_id = $request->input('id');

        if ($request->hasFile('img')) {

            // Upload New File
            $img = $request->file('img');
            $t = time();
            $file_name = $img->getClientOriginalName();
            $img_name = "{$user_id}-{$t}-{$file_name}";
            $img_url = "uploads/{$img_name}";
            $img->move(public_path('uploads'), $img_name);

            // Delete Old File
            $filePath = $request->input('file_path');
            File::delete($filePath);

            // Update Product

            return Product::where('id', $product_id)->where('user_id', $user_id)->update([
                'name' => $request->input('name'),
                'price' => $request->input('price'),
                'unit' => $request->input('unit'),
                'img_url' => $img_url,
                'category_id' => $request->input('category_id')
            ]);
        } else {
            return Product::where('id', $product_id)->where('user_id', $user_id)->update([
                'name' => $request->input('name'),
                'price' => $request->input('price'),
                'unit' => $request->input('unit'),
                'category_id' => $request->input('category_id'),
            ]);
        }
    }
}
