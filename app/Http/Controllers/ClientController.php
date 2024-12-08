<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\Client;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use App\Models\Account;

class ClientController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $request->validate([
            'search'=>'nullable|string',
            'sort_by'=>'nullable|string|in:id,name,card_no',
            'sort_order'=>'nullable|string|in:asc,desc',
            'per_page'=> 'nullable|integer|min:1|max:100',
            'page'=>'nullable|integer:min:1'
        ]);

        $query=Client::query();
        if($request->has('search')){
            $search = $request->input(key:'search');
            $query->where('name', 'LIKE',"%$search%")
            ->orWhere('card_no', 'LIKE', "%$search%");
        }
          // Sorting
          $sortBy = $request->input('sort_by', 'id'); // Default sort by 'id'
          $sortOrder = $request->input('sort_order', 'desc'); // Default sort order 'asc'
          $query->orderBy($sortBy, $sortOrder);
      
          // Pagination
          $perPage = $request->input('per_page', 20); // Default 20 items per page
          $clients = $query->paginate($perPage);
          $data = $clients->items();
          $clients_data= [];
        //   'name',
        //   'card_no',
        //   'phone_number',
        //   'address',
        //   'created_by'

          foreach($data as $client_transaction){
            $account = Account::where('client_id', $client_transaction->id)->first();
            $current_client =[
                'name'=>$client_transaction->name,
                'client_id'=>$client_transaction->id,
                'card_no'=>$client_transaction->card_no,
                'phone_number'=>$client_transaction->phone_number,
                'created_on'=>$client_transaction->created_at,
                'balance'=>$account->account_balance
            ];
            array_push($clients_data, $current_client);
          }

          return response()->json([
            'data' => $clients_data,
            'pagination'=>[
                'total' => $clients->total(),
                'per_page' => $clients->perPage(),
                'current_page' => $clients->currentPage(),
                'last_page' => $clients->lastPage(),
                'next_page_url' => $clients->nextPageUrl(),
                'prev_page_url' => $clients->previousPageUrl(),
            ]
          ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        $data= $request->validate([
            'name'=>['required'],
            'card_no'=>['required', Rule::unique(Client::class, 'card_no')],
            'phone_number'=>['required', 'max:12', 'min:11'],
            "address"=>['max:255']
        ]);

        $data['created_by'] = Auth::id();
        $client = Client::create($data);

        $account = Account::create(attributes: [
            'client_id'=>$client->id,
        ]);
        
        //Create Account 
        return response($client, 200);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return Client::find($id);
        //
    }


    public function findByCardNo(string $card_no){
        $client = Client::where('card_no', $card_no)->first();
        if($client){
            return response([
                "name"=>$client->name,
                "status"=>true,
                "msg"=>"User found"
            ]);
        }
        return response([
            "status"=>false,
            "msg"=>"No user found"
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
        $data=$request->validate([
            'name'=>['required'],
            'card_no'=>['required', Rule::unique(Client::class, 'card_no')->ignore($id)],
            'phone_number'=>['required', 'max:12', 'min:11'],
            "address"=>['max:255']
        ]);

        $client = Client::find($id);
        $client->update($data);
        return $client;

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        Client::destroy($id);
        return "Client data successfully deleted";
    }



      /**
     * Search for the specified resource in storage.
     */
    public function search($keyword)
    {
        $data =Client::where('name', 'like', '%'.$keyword.'%')
                    ->orWhere('card_no', 'like', '%'.$keyword.'%')
                    ->orWhere('phone_number', 'like', '%'.$keyword.'%')->get();
        return $data;
    }
}
