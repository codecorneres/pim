<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Shops;
use App\Product;
use App\User;
use Osiset\BasicShopifyAPI\BasicShopifyAPI;
use Osiset\BasicShopifyAPI\Options;
use Osiset\BasicShopifyAPI\Session;
use Illuminate\Support\Facades\Auth;
use App\Mail\ProductsImages;
use Mail;
use DB;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {   
      if (Auth::check()) {
        $shop = Shops::where('user_id',Auth::user()->id)->first();

        $vendor_type = !empty(session('vendor_type')) ? session('vendor_type') : '';

        $filterVendors = $this->filterVendors();

        if($shop){

            if($vendor_type != ''){
                $products = Product::where('shop_id',$shop->id)
                                    ->where('vendor_type', $vendor_type)
                                    ->paginate(100);
            }else{
                $products = Product::where('shop_id',$shop->id)->paginate(100);
            }
          
          return view('home', ['products' => $products,'shop' => $shop, 'filterVendors' => $filterVendors, 'vendorType' => $vendor_type]);
        }
        return view('disabled');
      }else{
        return view('welcome');
      }
    }

    public function withsessionindex()
    {   
      if (Auth::check()) {
        echo 'has_img='.session('has_img');
        if(session()->get('has_img') == 'true'){
            echo 'true in if';
            $products = Product::whereNotNull('image')->paginate(100);
        }else if(session()->get('has_img') == 'false'){
            echo 'false in if';
            $products = Product::where('image' , Null)->paginate(100);
            // $products = Product::paginate(100);
        }else{
            echo 'still else';
            $products = Product::paginate(100);
        }
        return view('home', ['products' => $products]);
      }else{
        return view('welcome');
      }
    }

    public function filteredProducts(Request $request)
    {
        if (Auth::check()) {
            // session('has_img', $request->get('query'));
            session()->put('has_img', $request->get('query'));
            // session(['has_img' => $request->get('query')]);
            return response()->json([
                                'status' => 'success',
                                'redirect' => 'reload'
            ]);
        }
    }

    public function productsWithImage(Request $request){
        if (Auth::check()) {
          $shop = Shops::where('user_id',Auth::user()->id)->first();

          $vendor_type = !empty(session('vendor_type')) ? session('vendor_type') : '';

          $filterVendors = $this->filterVendors();

          if($shop){
            if($vendor_type != ''){
                $products = Product::where('shop_id',$shop->id)
                                    ->where('vendor_type', $vendor_type)
                                    ->whereNotNull('image')
                                    ->paginate(100);
            }else{
                $products = Product::where('shop_id',$shop->id)->whereNotNull('image')->paginate(100);
            }

            
            return view('home', ['products' => $products,'shop' => $shop, 'filterVendors' => $filterVendors, 'vendorType' => $vendor_type]);
          }
        }else{
          return view('welcome');
        }
    }

    public function productsWithoutImage(Request $request){
        if (Auth::check()) {
            $shop = Shops::where('user_id',Auth::user()->id)->first();
            $vendor_type = !empty(session('vendor_type')) ? session('vendor_type') : '';

            $filterVendors = $this->filterVendors();

            if($shop){

                if($vendor_type != ''){
                    $products = Product::where('shop_id',$shop->id)
                                        ->where('vendor_type', $vendor_type)
                                        ->where('image', Null)
                                        ->paginate(100);
                }else{
                    $products = Product::where('shop_id',$shop->id)->where('image' , Null)->paginate(100);
                }

              return view('home', ['products' => $products,'shop' => $shop, 'filterVendors' => $filterVendors, 'vendorType' => $vendor_type]);
            }
        }else{
            return view('welcome');
        }
    }

    public function sendProductNotification(Request $request){ 
      if (Auth::check()) {
        $admin_user = User::where('is_admin', 1)->select('cc_email1','cc_email2')->first();
        $emails = array();
        if($admin_user->cc_email1 != null){
          $emails[] = $admin_user->cc_email1;
        }
        if($admin_user->cc_email2 != null){
          $emails[] = $admin_user->cc_email2;
        }
        $shop = Shops::where('user_id',Auth::user()->id)->first();
        $missing_img_products =  Product::where('shop_id',$shop->id)->where('image' , Null)->get()->toArray();

        if(count($missing_img_products) > 0){
            foreach($missing_img_products as $product){
              $burl = 'https://'.$shop->shopify_domain.'/admin/products/'.$product['product_id'];
              $furl = 'https://'.$shop->shopify_domain.'/products/'.$product['handle'];

              $csvData[] = array($product['product_id'], $product['title'], $product['handle'], $furl, $burl, '');
            }

            $header_columns = array('Product ID','Title', 'Handle', 'Frontend URL', 'Backend URL', 'Image URL');
            $path = storage_path('/app/public/csv/');
            $filename = 'products-'.date('Y-m-d').'_'.time().'.csv';
            $file = fopen($path . $filename, 'w');
            $csv_path = asset('storage/csv/'.$filename);
          //   chmod($_SERVER['DOCUMENT_ROOT'].'/product-img-manager/'.$csv_path,0755);
          //   echo 'tr'.$_SERVER['DOCUMENT_ROOT'].'/product-img-manager/'.$csv_path;
            fputcsv($file, $header_columns);
            
            foreach ($csvData as $row) {
                fputcsv($file, $row);
            }
            fclose($file);
            Mail::to($shop->shopowner_email)->send(new ProductsImages($csv_path,$emails,$shop));

            return redirect()->route('home')->with('status','Email sent successfully. Please check your inbox !');
        }else{
          return redirect()->route('home')->with('status','All products are having images !');
        }
      }
    }

    public function sync(){
      $shop = Shops::where('user_id',Auth::user()->id)->first();
      if($shop){
        $api = $this->apiConnect();
        if($shop->shopowner_email == '' || $shop->shopowner_email == null){
          $shop_response = $api->rest('GET', '/admin/api/'.env('SHOPIFY_API_VERSION').'/shop.json');
          if(isset($shop_response['body']['shop'])){
            $shop->shopowner_email = $shop_response['body']['shop']['email'];
            $shop->save();
          }
        }
        
        $this->createWebhooks();
        
        $totalCount = $response = $api->rest('GET', '/admin/api/'.env('SHOPIFY_API_VERSION').'/products/count.json')['body']['count'];
        $pages = ceil((int)$totalCount / 250);
        for ($i=1; $i<=$pages; $i++) {
            if($i ==1){
               sleep(2);
               $response = $api->rest('GET', '/admin/api/'.env('SHOPIFY_API_VERSION').'/products.json', ['limit' => 250]);             
            }else{
               if (!empty($response['link']['next'])) {
                   $link = $response['link']['next'];
               }else{
                   $link = $response['link']['previous'];
               }
              $response = $api->rest('GET', '/admin/api/'.env('SHOPIFY_API_VERSION').'/products.json', ['limit' => 250, 'page_info' => $link]);
            }
            $pro = $response['body']['products'];
            foreach ($pro as $key => $value) {
              $product = Product::where('shop_id',$shop->id)->where('product_id', $value['id'])->first();
              if(!$product){
                $product = new Product();
              }
              $this->storeData($shop, $product, $value['id'], $value['title'], $value['handle'], $value['image']['src'], $value['vendor']);
            }
        }

        $products = Product::where('shop_id',$shop->id)->where('image' , Null)->get()->toArray();
        return redirect()->route('home')->with('status','Store products saved successfully !');  
      }
    }

    public function filter(){
        $vendor_type = $_POST['vendor_type'];
        session(['vendor_type' => $vendor_type]);
    }

    public function bulk_import(Request $request){
        $api = $this->apiConnect();

        $file = $request->file('file');

        if($request->hasFile('file')) {
            // File Details 
            $filename  = $file->getClientOriginalName();
            $extension = $file->getClientOriginalExtension();

            // Valid File Extensions
            $valid_extension = array("csv");
            $fileNameToStore = time().'_'.$filename;   
            $location = 'imports';

            if(in_array( $extension, $valid_extension)) {

                $file->move($location,$fileNameToStore);

                $filepath = public_path($location."/".$fileNameToStore);

                $file = fopen($filepath,"r");

                $i = 0;

                if (($file = fopen($filepath, "r")) !== FALSE) {

                    while (($filedata = fgetcsv($file, 1000, ",")) !== FALSE) {
                        $num = count($filedata );

                        // Skip first row 
                        if($i == 0){
                            $i++;
                            continue; 
                        }

                        $response = $api->rest('GET', '/admin/api/'.env('SHOPIFY_API_VERSION').'/products/'.$filedata[0].'.json');

                        $allSrcs = $allImages = array();

                        $allImages = explode(',', $filedata[5]);

                        foreach($allImages as $img){
                            $allSrcs[] = array('src' => $img);
                        }

                        $images = array( 'product' => 
                            array(
                            'id' => $filedata[0],
                            'images' => $allSrcs
                        ));

                        $api->rest('PUT', '/admin/api/'.env('SHOPIFY_API_VERSION').'/products/'.$filedata[0].'.json', $images);

                        $i++;
                    }

                    fclose($file);

                return redirect()->route('home')->with('status','Images imported successfully !');  
            }
            
        }
        else
        {       
            return redirect()->route('home')->withErrors('status','Invalid file format!');   
        }
    }
    else
    {
            return redirect()->route('home')->withErrors('status','Please upload file!');  
    }
    }

    public function createWebhooks(){
        $webhooks = array(
          array(
            'webhook' => array(
                "topic"     => 'products/create',
                "address"   => 'https://devworktdmc.com/product-img-manager/public/webhook/product-create',
                "format"    => "json"
            )
          ),
          array(
              'webhook' => array(
                  "topic"     => 'products/update',
                  "address"   => 'https://devworktdmc.com/product-img-manager/public/webhook/product-update',
                  "format"    => "json"
              )
          ),
          array(
              'webhook' => array(
                  "topic"     => 'products/delete',
                  "address"   => 'https://devworktdmc.com/product-img-manager/public/webhook/product-delete',
                  "format"    => "json"
              )
          ),
          array(
            'webhook' => array(
                "topic"     => 'shop/update',
                "address"   => 'https://devworktdmc.com/product-img-manager/public/webhook/shop-update',
                "format"    => "json"
            )
          )  
        );

        foreach ($webhooks as $key => $webhook) {
          $api = $this->apiConnect();
          $response = $api->rest('POST', '/admin/api/'.env('SHOPIFY_API_VERSION').'/webhooks.json', $webhook)['body'];
        }
    }

    private function apiConnect(){
        $shop = Shops::where('user_id',Auth::user()->id)->first();
        $options = new Options();
        $options->setType(true); // Makes it private
        $options->setVersion('2021-01');
        $options->setApiKey($shop->api_key);
        $options->setApiPassword($shop->api_password);

        // Create the client and session
        $api = new BasicShopifyAPI($options);
        $api->setSession(new Session($shop->shopify_domain));
        return $api;
    }

    public function storeData($shop, $product, $pro_id, $title, $handle, $image, $vendor)
    {
        $product->product_id  = (string) $pro_id;
        $product->shop_id     = $shop->id;
        $product->title       = $title;
        $product->handle      = $handle;
        $product->image       = $image;
        $product->vendor_type = $vendor;
        if($image == '' || $image == null){
            $product->status  = '0';
        }else{
            $product->status  = '1';
        }
        $product->save();
        return $product;
    }

    public function filterVendors(){
      $shop = Shops::where('user_id',Auth::user()->id)->first();
      if($shop){
        return DB::table('products')->where('shop_id', $shop->id)
        ->select('vendor_type')
        ->whereNotNull('vendor_type')
        ->groupBy('vendor_type')
        ->get();
      }
    }

}
