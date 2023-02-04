<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Http\Helpers\ResponseGenerator;
use App\Models\Card;
use App\Models\Collection;
use App\Models\User;

class CardsController extends Controller
{
    public function create(Request $request){
        $json = $request->getContent();
        $data = json_decode($json);

        if($data){
            $validate = Validator::make(json_decode($json,true), [
                'name' => 'required|string',
                'description' => 'required|string',
                'collectionId' => 'required|exists:collections,id'
            ]);
            if($validate->fails()){
                return ResponseGenerator::generateResponse("KO", 422, null, $validate->errors());
            }else {

                $collection = Collection::find($data->collectionId);
                if($collection){
                    $card = new Card();
                    $card->name = $data->name;
                    $card->description = $data->description;
                    try{
                        $card->save();
                        $collection->cards()->attach($card->id);
                        return ResponseGenerator::generateResponse("OK", 200, $card, "Carta guardado correctamente");
                    }catch(\Exception $e){
                        return ResponseGenerator::generateResponse("KO", 304, null, "Error al guardar");
                    }
                }else{
                    return ResponseGenerator::generateResponse("KO", 404, null, "No se ha encontrado ninguna collección");
                }
            }      
        }else{
            return ResponseGenerator::generateResponse("KO", 500, null, "Datos no registrados");
        }
    }
    public function addToCollection(Request $request){
        $json = $request->getContent();
        $data = json_decode($json);

        if($data){
            $validate = Validator::make(json_decode($json,true), [
                'cardId' => 'required|exists:cards,id',
                'collectionId' => 'required|exists:collections,id'
            ]);
            if($validate->fails()){
                return ResponseGenerator::generateResponse("KO", 422, null, $validate->errors());
            }else {
                $collection = Collection::find($data->collectionId);
                $card = Card::find($data->cardId);
                try{
                    $collection->cards()->attach($card->id);
                    return ResponseGenerator::generateResponse("OK", 200, $card, "Carta añadida correctamente");
                }catch(\Exception $e){
                    return ResponseGenerator::generateResponse("KO", 304, null, "Error al añadir");
                }
            }      
        }else{
            return ResponseGenerator::generateResponse("KO", 500, null, "Datos no registrados");
        }
    }
    public function searchByName(Request $request){
        $json = $request->getContent();
        $data = json_decode($json);

        if($data){
            $validate = Validator::make(json_decode($json,true), [
                'cardName' => 'required|string'
            ]);
            if($validate->fails()){
                return ResponseGenerator::generateResponse("KO", 422, null, $validate->errors());
            }else {
                try{
                $cards = Card::where('name', 'like', '%'.$data->cardName.'%')->get();
                if(isset($cards)){
                    return ResponseGenerator::generateResponse("OK", 200, $cards, "Carta buscada correctamente");
                }else{
                    return ResponseGenerator::generateResponse("OK", 200, null, "Carta no encontrada");
                }
                }catch(\Exception $e){
                    return ResponseGenerator::generateResponse("KO", 304, null, "Erro al buscar");
                }
            }      
        }else{
            return ResponseGenerator::generateResponse("KO", 500, null, "Datos no registrados");
        }
    }
    public function publishCard(Request $request){
        $json = $request->getContent();
        $data = json_decode($json);

        if($data){
            $validate = Validator::make(json_decode($json,true), [
                'cardId' => 'required|exists:cards,id',
                'amount' => 'required|numeric',
                'price' => 'required|numeric'
            ]);
            if($validate->fails()){
                return ResponseGenerator::generateResponse("KO", 422, null, $validate->errors());
            }else {
                $actualUserId = Auth::id();
                try{
                    $card = Card::find($data->cardId);
                    $card->users()->attach($actualUserId, ['amount'=>$data->amount, 'price'=>$data->price]);
                    //$card->save();
                    return ResponseGenerator::generateResponse("OK", 200, $card, "Carta añadida correctamente");
                }catch(\Exception $e){
                    return ResponseGenerator::generateResponse("KO", 304, null, "Error al añadir");
                }
            }      
        }else{
            return ResponseGenerator::generateResponse("KO", 500, null, "Datos no registrados");
        }

    }
}
