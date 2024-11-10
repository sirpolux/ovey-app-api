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
    public function index()
    {
        return Client::all();
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
