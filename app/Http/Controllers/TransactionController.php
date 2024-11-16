<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Tools\Utility;
use Illuminate\Support\Facades\Auth;
use App\Models\Account;
use App\Models\Client;
use Ramsey\Uuid\Type\Integer;

class TransactionController extends Controller
{
    private $utilty;

    public function __construct(Utility $utility){
        
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {

        $request->validate([
            'search' => 'nullable|string',
            'sort_by' => 'nullable|string|in:id,amount,date_paid', // Limit to valid columns
            'sort_order' => 'nullable|string|in:asc,desc',
            'per_page' => 'nullable|integer|min:1|max:100',
            'page' => 'nullable|integer|min:1',
        ]);
        
        $query = Transaction::query();
        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where('purpose', 'LIKE', "%$search%")
                  ->orWhere('transaction_type', 'LIKE', "%$search%")
                  ->orWhere('status', 'LIKE', "%$search%");
        }
    
        // Sorting
        $sortBy = $request->input('sort_by', 'id'); // Default sort by 'id'
        $sortOrder = $request->input('sort_order', 'desc'); // Default sort order 'asc'
        $query->orderBy($sortBy, $sortOrder);
    
        // Pagination
        $perPage = $request->input('per_page', 20); // Default 20 items per page
        $transactions = $query->paginate($perPage);
        $data = $transactions->items();

        $transaction_response=[];

        foreach($data as $user_transaction){
            $account = Account::where('id', $user_transaction->account_id)->first();
            $client = Client::where('id', $account->client_id)->first();
            $current_transaction = [
                'client_name'=>$client->name,
                'card_no'=>$client->card_no,
                'amount'=>$user_transaction->amount,
                'data_paid'=>$user_transaction->date_paid,
                'transaction_type'=>$user_transaction->transaction_type,
                'purpose'=>$user_transaction->purpose,
            ];
            array_push($transaction_response, $current_transaction);
        }

        return response()->json([
            'data' => $transaction_response,
            'pagination' => [
                'total' => $transactions->total(),
                'per_page' => $transactions->perPage(),
                'current_page' => $transactions->currentPage(),
                'last_page' => $transactions->lastPage(),
                'next_page_url' => $transactions->nextPageUrl(),
                'prev_page_url' => $transactions->previousPageUrl(),
            ],
        ]);


        //return $transaction_response;

    
        //return response()->json($transactions);

        //return Transaction::all();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
        $request->validate([
            'amount'=>'required|integer:100,1000000',
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


    public function storeMultipleTransaction(Request $request){
        $request->validate([
           'amount'=>'required|integer:100,1000000',
            'client_ids'=>'required'
        ]);
        $client_ids = $request['client_ids'];
        $amount = $request['amount'];
        //return $amount;
        $date_paid = isset($request['date_paid'])? $request->date_paid : date('Y-m-d');

        foreach($client_ids as $client_id){
            $this->saveTransaction($client_id, $amount, $date_paid);
        }
        return response("Transaction Saved", 201);
    }



    public function saveTransaction($client_id, $amount, $date_paid){
        $account = Account::where('client_id', $client_id)->first();
        $transaction['account_id']=$account->id;
        $transaction['amount'] = $amount;
        $transaction['date_paid'] = $date_paid; //isset($date_paid)? $date_paid: date('Y-m-d');
        isset($request['transaction_type']) && $transaction['transaction_type'];
        $transaction['created_by'] = Auth::id();
      
        if(!$account){
            return response("Operation failed, Account not found", 402);
        }
    
        $transactionResponse = Transaction::create($transaction);
        $account->account_balance = $account->account_balance+$transactionResponse->amount;
        $updatedAccount =  $account->update([
            'account_balance'=>$account->account_balance
        ]);
    }


    public function getUserTransaction(Request $request){
        $request->validate([
            'client_id'=>'required'
        ]);

//      $client= Client::where('id', $request['client_id']);
        $account = Account::where('client_id', $request->client_id)->first();

        $query = Transaction::query();
        $query->where('account_id','LIKE', $account->id);
        $sortBy = $request->input('sort_by', 'id'); // Default sort by 'id'
        $sortOrder = $request->input('sort_order', 'asc'); // Default sort order 'asc'
        $query->orderBy($sortBy, $sortOrder);
        $perPage = $request->input('per_page', 20); // Default 20 items per page
        $transactions = $query->paginate($perPage);
        $data = $transactions->items();

        $transaction_response=[];

        foreach($data as $user_transaction){
            $account = Account::where('id', $user_transaction->account_id)->first();
            $client = Client::where('id', $account->client_id)->first();
            $current_transaction = [
                'client_name'=>$client->name,
                'card_no'=>$client->card_no,
                'amount'=>$user_transaction->amount,
                'data_paid'=>$user_transaction->date_paid,
                'transaction_type'=>$user_transaction->transaction_type,
                'purpose'=>$user_transaction->purpose,
            ];
            array_push($transaction_response, $current_transaction);
        }

        return response()->json([
            'data' => $transaction_response,
            'pagination' => [
                'total' => $transactions->total(),
                'per_page' => $transactions->perPage(),
                'current_page' => $transactions->currentPage(),
                'last_page' => $transactions->lastPage(),
                'next_page_url' => $transactions->nextPageUrl(),
                'prev_page_url' => $transactions->previousPageUrl(),
            ],
        ]);

        //return $data;
    

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
