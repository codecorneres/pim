<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Shops;
use App\Product;
use App\User;
use App\Mail\ProductsImages;
use Mail;

class EmailProducts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:products';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send product CSV to store owner';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        logger("Cron is working fine!");
        
        $admin_user = User::where('is_admin', 1)->select('cc_email1','cc_email2')->first();
        $emails = array();
        if($admin_user->cc_email1 != null){
          $emails[] = $admin_user->cc_email1;
        }
        if($admin_user->cc_email2 != null){
          $emails[] = $admin_user->cc_email2;
        }
        $shops = Shops::get();
        foreach ($shops as $key => $shop) {
          $missing_img_products =  Product::where('shop_id',$shop->id)->where('status','0')->where('image' , Null)->get()->toArray();
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
            fputcsv($file, $header_columns);
            
            foreach ($csvData as $row) {
                fputcsv($file, $row);
            }
            fclose($file);
            $csv_path = asset('storage/csv/'.$filename);
            Mail::to($shop->shopowner_email)->send(new ProductsImages($csv_path,$emails,$shop));
            logger('Mail sent successfully');

            $missing_image_products =  Product::where('shop_id',$shop->id)->where('status','0')->where('image' , Null)->get();
            foreach ($missing_image_products as $key => $product) {
                $product->status = '1';
                $product->save();
            }
            logger('Products updated');
          }
        }
        
        logger('cron ended');
    }
}
