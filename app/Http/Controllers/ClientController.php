<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Client;
use Illuminate\Validation\Rule;

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
        //
        $data= $request->validate([
            'name'=>['required'],
            'card_no'=>['required', Rule::unique(Client::class, 'card_no')],
            'phone_number'=>['required', 'max:12', 'min:11'],
            "address"=>['max:255']
        ]);

        return Client::create($data);
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
        //
    }
}
