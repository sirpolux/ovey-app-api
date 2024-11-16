<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Client;
use Illuminate\Http\Request;

class AccountController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {

        $query = Account::query();

        $request->validate([
            'search'=>'nullable|string',
            'sort_by'=>'nullable|string|in:id,account_balance,updated_at',
            'sort_order'=>'nullable|string|in:asc,desc',
            'per_page'=>'nullable|integer|min:1|max:100',
            'page'=>'nullable|integer|min:1'
        ]);
        $sortBy = $request->input('sort_by', 'updated_at'); // Default sort by 'id'
        $sortOrder = $request->input('sort_order', 'desc'); // Default sort order 'desc'
        $query->orderBy($sortBy, $sortOrder);

        // Pagination
        $perPage = $request->input('per_page', 20); // Default 20 items per page
        $accounts = $query->paginate($perPage);
        
        $data = $accounts->items();

        $account_response =[];

        foreach($data as $account_transaction){
            $client = Client::where('id', $account_transaction->client_id)->first();
            $current_account=[
                'account_name'=>$client->name,
                'card_no'=>$client->card_no,
                'phone_number'=>$client->phone_number,
                'account_balance'=>$account_transaction->account_balance,
                'updated_at'=>$account_transaction->updated_at,
            ];
            array_push($account_response, $current_account);
        }

        return response()->json([
            'data'=>$account_response,
            'pagination'=>[
                'total'=>$accounts->total(),
                'per_page'=>$accounts->perPage(),
                'current_page'=>$accounts->currentPage(),
                'last_page'=>$accounts->lastPage(),
                'next_page_url'=>$accounts->nextPageUrl(),
                'prev_page_url'=>$accounts->previousPageUrl()
            ]
        ]);








        
        
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Client $client)
    {
        //
        $account = Account::create([
            'client_id'=>$client->id,
        ]);
    }


    public function createAccount(Client $client)
    {
        $account = Account::create(attributes: [
            'client_id'=>$client->id,
        ]);
    }


    public function fetchAllAccountBalance(Request $request){

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
