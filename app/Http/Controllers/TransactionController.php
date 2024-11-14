<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Tools\Utility;
use Illuminate\Support\Facades\Auth;
use App\Models\Account;
use App\Models\Client;

class TransactionController extends Controller
{
    private $utilty;

    public function __construct(Utility $utility){
        
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        return Transaction::all();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
        $request->validate([
            'amount'=>'required|integer:100,100000',
            'client_id'=>'required'
        ]);

        //$client = Client::find($request->client_id);
        $account = Account::where('client_id', $request['client_id'])->first();
        $transaction['account_id']=$account->id;
        $transaction['amount'] = $request->amount;
        $transaction['date_paid'] = isset($request['date_paid'])? $request->date_paid : date('Y-m-d');
        isset($request['transaction_type']) && $transaction['transaction_type'];
        isset($request['purpose']) &&  $transaction['purpose']= $request['purpose'];
        $transaction['created_by'] = Auth::id();
      
        if(!$account){
            return response("Operation failed, Account not found", 402);
        }
    
        $transactionResponse = Transaction::create($transaction);
        $account->account_balance = $account->account_balance+$transactionResponse->amount;
        $updatedAccount =  $account->update([
            'account_balance'=>$account->account_balance
        ]);
        return response(
            ["message"=>"Transaction Completer",
                    "account_balance"=>$account->account_balance],
            201
        );


        
    }

    public function storeBatch(){

    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }


}
