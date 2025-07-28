<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\LearningPackage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class CartController extends Controller
{
    public function __construct() 
    {
      $this->middleware('guest',['except' => ['logout']]);
    }
    public function addToCart($id)
    {
        try {
            $userId = Auth::id();

            if ($userId) {
                $cartPackage = Cart::where('package_id', $id)->first();

                if ($cartPackage) {
                    return redirect()->back()->with("success", \Lang::get('lang.package-is-already-in-cart'));
                } else {
                    $cart = new Cart();
                    $cart->user_id = $userId;
                    $cart->package_id = $id;
                    $cart->save();
                }

                return redirect()->back()->with("success", \Lang::get('lang.package-has-been-added-to-cart'));
            } else {

                $cartPackages = Session::get('cart', []);

                if (in_array($id, $cartPackages)) {
                    return redirect()->back()->with("success", \Lang::get('lang.package-is-already-in-cart'));
                } else {
                    $cartPackages[] = $id;
                    Session::put('cart', $cartPackages);
                }

                return redirect()->back()->with("success", \Lang::get('lang.package-has-been-added-to-cart'));
            }
        } catch (\Exception $exception) {
            return redirect()->back()->with('error', \Lang::get('lang.generic-error'));
        }
    }

    public function deleteCartPackage($id)
    {
        try {
            $userId = Auth::id();

            if ($userId) {
                $cartPackages = Cart::where('user_id', $userId)->where('package_id', $id);
                $cartPackages->delete();
            } else {
                $cartPackageIds = Session::get('cart', []);
                $key = array_search($id, $cartPackageIds);
                if ($key !== false) {
                    unset($cartPackageIds[$key]);
                    Session::put('cart', $cartPackageIds);
                }
            }
            return redirect()->back()->with("success", \Lang::get('lang.deleted-successfully'));
        } catch (\Exception $exception) {
            return redirect()->back()->with('error', \Lang::get('lang.generic-error'));
        }
    }
    public function checkOut(Request $request)
    {
        $userId = Auth::id();

        if ($userId) {
            $cartPackageId = Cart::where('user_id', $userId)->get('package_id');
            $cartPackages = LearningPackage::whereIn('id', $cartPackageId)->get();
        } else {
            $cartPackageIds = Session::get('cart', []);
            $cartPackages = LearningPackage::whereIn('id', $cartPackageIds)->get();
        }

        return view('public.customer-cart', compact('cartPackages'));
    }

    public function deleteAllCartPackage()
    {
        try {
            $userId = Auth::id();
            if ($userId) {
                Cart::where('user_id', $userId)->delete();
            }
            else{
                Session::forget('cart');
            }
            return redirect()->back()->with('success', \Lang::get('lang.deleted-successfully'));
        } catch (\Exception $exception) {
            return redirect()->back()->with('error', \Lang::get('lang.generic-error'));
        }
    }
}
