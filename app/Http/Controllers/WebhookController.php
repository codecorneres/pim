<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Osiset\BasicShopifyAPI\BasicShopifyAPI;
use Osiset\BasicShopifyAPI\Options;
use Osiset\BasicShopifyAPI\Session;
use App\Shops;
use App\Product;
use App\User;

class WebhookController extends Controller
{
	public function index()
    {
        // $response1 = $api->rest('DELETE', '/admin/api/'.env('SHOPIFY_API_VERSION').'/webhooks/1012929003702.json');
        // $response2 = $api->rest('DELETE', '/admin/api/'.env('SHOPIFY_API_VERSION').'/webhooks/1012912947382.json');
        // $response3 = $api->rest('DELETE', '/admin/api/'.env('SHOPIFY_API_VERSION').'/webhooks/990337204406.json');
        // $shops = Shops::get();
        // foreach ($shops as $key => $shop) {
        //     $api = $this->apiConnect($shop);
        //     $response = $api->rest('GET', '/admin/api/'.env('SHOPIFY_API_VERSION').'/webhooks.json')['body']['webhooks'];
        //     echo "Shop domain=".$shop->shopify_domain;
        //     echo "<pre>";
        //     print_r($response);
        //     echo "</pre>";
        // }
    }

    public function productCreate(Request $request){
    	$data = $request->all();
        $data_string = request()->getContent();
        $hmac_header = request()->header('x-shopify-hmac-sha256') ?: '';
        $shop_domain = request()->header('x-shopify-shop-domain');
        if(!empty($data)){
            $shop = Shops::where('shopify_domain',$shop_domain)->first();

            if($shop){
                $verified = $this->verifyWebhook($data_string,$hmac_header,$shop->shared_secret);
                // logger('Webhook verified=: '.$verified);
                if($verified){
                    $product = new Product();
                    $product->shop_id     = $shop->id;
                    $product->product_id  = (string) $data['id'];
                    $product->title       = $data['title'];
                    $product->handle      = $data['handle'];
                    $product->image       = $data['image']['src'];
                    if($data['image']['src'] == '' || $data['image']['src'] == null){
                        $product->status  = '0';
                    }else{
                        $product->status  = '1';
                    }
                    $product->save();
                }
            }else{
                logger('Shop does not exist in DB on product create webhook: '.$shop);
            }
        }
    }

    public function productUpdate(Request $request){
    	$data = $request->all();
        $data_string = request()->getContent();
        $hmac_header = request()->header('x-shopify-hmac-sha256') ?: '';
        $shop_domain = request()->header('x-shopify-shop-domain');
        if(!empty($data)){
            $shop = Shops::where('shopify_domain',$shop_domain)->first();

            if($shop){
                $verified = $this->verifyWebhook($data_string,$hmac_header,$shop->shared_secret);
                // logger('Webhook verified=: '.$verified);
                if($verified){
                    $product = Product::where('product_id', (string) $data['id'])->first();
                    if($product){
                        $product->title       = $data['title'];
                        $product->handle      = $data['handle'];
                        $product->image       = $data['image']['src'];
                        if($data['image']['src'] == '' || $data['image']['src'] == null){
                            $product->status  = '0';
                        }else{
                            $product->status  = '1';
                        }
                        $product->save();
                    }else{
                        logger('Product does not exist in DB on update webhook: '.$data['id']);
                    }
                }
            }
        }
    }

    public function productDelete(Request $request){
        $data = $request->all();
        $data_string = request()->getContent();
        $hmac_header = request()->header('x-shopify-hmac-sha256') ?: '';
        $shop_domain = request()->header('x-shopify-shop-domain');
        if(!empty($data)){
            $shop = Shops::where('shopify_domain',$shop_domain)->first();

            if($shop){
                $verified = $this->verifyWebhook($data_string,$hmac_header,$shop->shared_secret);
                // logger('Webhook verified=: '.$verified);
                if($verified){
                    $product = Product::where('product_id', (string) $data['id'])->first();
                    if($product){
                        $product->delete();
                    }else{
                        logger('Product does not exist in DB on delete webhook: '.$data['id']);
                    }
                }
            }
        }
    }

    public function shopUpdate(Request $request){
        $data = $request->all();
        $data_string = request()->getContent();
        $hmac_header = request()->header('x-shopify-hmac-sha256') ?: '';
        $shop_domain = request()->header('x-shopify-shop-domain');
        if(!empty($data)){
            $shop = Shops::where('shopify_domain',$shop_domain)->first();

            if($shop){
                $verified = $this->verifyWebhook($data_string,$hmac_header,$shop->shared_secret);
                // logger('Webhook verified=: '.$verified);
                if($verified){
                    $shop->shopowner_email = $data['email'];
                    $shop->save();
                }
            }else{
                logger('Shop does not exist in DB on update webhook: '.$data['id']);
            }
        }
    }

    private function verifyWebhook($data, $hmac_header,$shared_secret)
    {
        $calculated_hmac = base64_encode(hash_hmac('sha256', $data, $shared_secret, true));
        return hash_equals($hmac_header, $calculated_hmac);
    }

    private function apiConnect($shop){
        $options = new Options();
        $options->setType(true); // Makes it private
        $options->setVersion(env('SHOPIFY_API_VERSION'));
        $options->setApiKey($shop->api_key);
        $options->setApiPassword($shop->api_password);

        // Create the client and session
        $api = new BasicShopifyAPI($options);
        $api->setSession(new Session($shop->shopify_domain));
        return $api;
    }

}
