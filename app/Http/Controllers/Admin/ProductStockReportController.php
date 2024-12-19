<?php

namespace App\Http\Controllers\Admin;

use App\Services\AdditionalServices;
use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

// For CSV File Download
use League\Csv\Writer;
use SplTempFileObject;

class ProductStockReportController extends Controller
{
    public function index(Request $request)
    {
        if ($request->has('seller_id') == false || $request['seller_id'] == 'all') {
            $query = Product::whereIn('added_by', ['admin', 'seller']);
        } elseif ($request['seller_id'] == 'in_house') {
            $query = Product::where(['added_by' => 'admin']);
        } else {
            $query = Product::where(['added_by' => 'seller', 'user_id' => $request['seller_id']]);
        }

        $query_param = ['seller_id' => $request['seller_id']];
        $seller_is = $request['seller_id'];

        // Check for CSV download request
        if ($request->has('download') && $request['download'] === 'csv') {
            $products = $query->get(); 
            return $this->downloadCsv($products);
        } 

        $products = $query->paginate(AdditionalServices::pagination_limit())->appends($query_param);
       


        return view('admin-views.report.product-stock', compact('products', 'seller_is'));
    }

    private function downloadCsv($products)
    {
        // Create CSV file
        $csv = Writer::createFromFileObject(new SplTempFileObject());

        // Insert header
        $csv->insertOne([
            'Product ID',
            'Product Name',
            // 'Category',
            'Brand',
            'Current Stock',
            'Unit Price',
            'Added By',
            'Seller Name',
            'Created At',
            'Updated At'
        ]);

        // Insert rows
        foreach ($products as $product) {
            $created_at = $product->created_at ? $product->created_at->format('Y-m-d H:i:s') : 'N/A';
            $updated_at = $product->updated_at ? $product->updated_at->format('Y-m-d H:i:s') : 'N/A';

            $seller_name = $product->seller ? ($product->seller->fullname ?? 'N/A') : 'N/A';

            $csv->insertOne([
                $product->id,
                $product->name,
                // $product->category ? $product->category->name : 'N/A',
                $product->brand ? $product->brand->name : 'N/A',
                $product->current_stock,
                $product->unit_price,
                $product->added_by,
                $seller_name,
                $created_at,
                $updated_at,
            ]);
        }

        // Return CSV file as response
        $csv->output('products.csv');
        exit;
    }

}
