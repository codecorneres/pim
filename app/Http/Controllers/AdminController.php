<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
// use Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Input;
Use Session;
use App\Shops;
use App\Product;
use App\User;

class AdminController extends Controller
{
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
      $shops = Shops::leftJoin('users', 'users.id', '=', 'shops.user_id')->select('shops.*','users.name','users.email')->get();
      return view('admin_home', ['shops' => $shops]);
    }

    public function create(){
    	return view('add_store');
    }

    public function store(Request $request){
      if (Auth::check()) {
        $input = $request->all();
        $rules = [
            'name' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:8|confirmed',
            'password_confirmation' => 'required|min:8|same:password',
            'shopify_domain' => 'required',
            'shopify_api_key' => 'required',
            'shopify_api_password' => 'required',
            'shopify_shared_secret' => 'required'
        ];

        $validator = Validator::make($input, $rules);

        if ($validator->fails()) {
            return back()->withInput()->withErrors($validator->messages());
        }
        
        $user = User::create([
            'name' => $request->get('name'),
            'email' => $request->get('email'),
            'password' => Hash::make($request->get('password')),
        ]);

        $shop = Shops::where('shopify_domain',$request->get('shopify_domain'))->first();
        if(!$shop){
          $shop = new Shops;
          $shop->user_id = $user->id;
          $shop->shopify_domain  = $request->get('shopify_domain');
          $shop->api_key = $request->get('shopify_api_key');
          $shop->api_password  = $request->get('shopify_api_password');
          $shop->shared_secret  = $request->get('shopify_shared_secret');
          $shop->save();
        }
      }
      
      return redirect()->route('admin.home')->with('status','New Store added successfully !');
    }

    public function show(Request $request, $id){
      $shop = Shops::where('shops.id',$id)->join('users', 'users.id', '=', 'shops.user_id')->select('shops.*','users.name','users.email')->first();
      if($shop){
        return view('view_store',[ 'shop' => $shop ]);
      }
    }

    public function edit(Request $request, $id){
      $shop = Shops::where('shops.id',$id)->join('users', 'users.id', '=', 'shops.user_id')->select('shops.*','users.name','users.email')->first();
      if($shop){
        return view('edit_store',[ 'shop' => $shop ]);
      }
    }

    public function update(Request $request, $id){
      $shop = Shops::find($id);

      if($shop){
        $user = User::find($shop->user_id);
        if($user){
          $user->name = $request->get('name');
          $user->save();
        }
        $shop->shopify_domain  = $request->get('shopify_domain');
        $shop->api_key = $request->get('shopify_api_key');
        $shop->api_password  = $request->get('shopify_api_password');
        $shop->shared_secret  = $request->get('shopify_shared_secret');
        $shop->save();
        return redirect()->route('admin.shop.edit', [ 'id' => $shop->id ])->with('status', 'Store details updated successfully !');
      }
    }

    public function delete(Request $request, $id){
      $shop = Shops::find($id);

      if($shop){
        $products = Product::where('shop_id',$shop->id)->get();
        foreach ($products as $key => $product) {
          $product->delete();
        }
        $user = User::find($shop->user_id);
        if($user){
          $user->delete();
        }
        $shop->delete();
      }
      return redirect()->route('admin.home')->with('status','Store deleted successfully !');
    }

    public function settings(Request $request){
      $user = User::find(Auth::user()->id);
      return view('settings',[ 'user' => $user ]);
    }

    public function settingsPost(Request $request){
      $user = User::find(Auth::user()->id);
      if($user){
        $user->cc_email1 = $request->get('cc_email1');
        $user->cc_email2 = $request->get('cc_email2');
        $user->save();
      }
      return redirect()->route('admin.settings')->with('status','Settings saved successfully !');
    }
}
