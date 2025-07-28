<?php

namespace App\Http\Controllers\Creator\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\BankAccount;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class BankAccountController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        Log::info("Entering index page of Bank Account");
        $id = Auth::user()->id;
        try {
            Log::debug("Finding bank accounts for the superadmin");

            $bankAccounts = BankAccount::where('is_primary', '=', 1)->get();

            Log::debug("Sending response for index page of bank account for the superadmin");

            return view('creator.superadmin.bank-account.index', compact('bankAccounts'));
        } catch (Exception $ex) {
            Log::error($ex->getMessage());
            return back()->with('flash_error', "Some error occured. Unable to show bank accounts");
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('creator.superadmin.bank-account.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'account_holder_name' => 'required',
            'routing_number' => 'required',
            'account_number' => 'required'
        ]);

        $user = Auth::user();

        try {
            $stripe = new \Stripe\StripeClient(env('STRIPE_SECRET'));

            try {
                $account = $stripe->accounts->create([
                    'type' => 'custom',
                    'country' => 'US',
                    'email' => $user->email,
                    'capabilities' => [
                        'transfers' => ['requested' => true],
                    ],
                    'individual' => [
                        'first_name' => $user->name,
                    ],
                    'business_profile' => [
                        "url" => "https://reallylesson.infoxen.com"
                    ],
                    'business_type' => 'individual',
                    'tos_acceptance' => [
                        'date' => Carbon::now()->timestamp,
                        'ip' => $request->ip()
                    ],
                    'external_account' => [
                        'object' => 'bank_account',
                        'country' => 'US',
                        'currency' => 'usd',
                        'account_holder_name' => $request->account_holder_name,
                        'account_holder_type' => 'individual',
                        'routing_number' => $request->routing_number,
                        'account_number' => $request->account_number,
                    ]
                ]);
                $bankAccount = new BankAccount();
                $bankAccount->account_id = $account->id;
                $bankAccount->last_four = $account->external_accounts->data[0]->last4;
                $bankAccount->name = $account->external_accounts->data[0]->bank_name;
                $count = BankAccount::count();
                if ($count) {
                    $bankAccount->is_primary = false;
                } else {
                    $bankAccount->is_primary = true;
                }
                $bankAccount->save();
            } catch (Exception $ex) {
                Log::error($ex->getMessage());
                return back()->with('flash_error', "Unable to add bank account. Please try again.");
            }

            return redirect('/superadmin/bank-account')->with(['flash_success', 'Bank Account Added']);
        } catch (Exception $e) {
            Log::error($e);
            return back()->with('flash_error', $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
       //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Log::debug("Going to delete bank account for the superadmin");
        BankAccount::destroy($id);
        Log::debug("Successfully deleted bank account for the superadmin");
        return back()->with('flash_success', "Bank account deleted successfully");
    }
}
