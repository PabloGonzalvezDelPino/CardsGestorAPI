<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use App\Http\Helpers\ResponseGenerator;
use App\Models\Card;
use App\Models\Collection;
use App\Models\User;
use App\Models\Sale;

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
            Log::info('Obtención de datos', ['data' => $data]);
            $validate = Validator::make(json_decode($json,true), [
                'cardName' => 'required|string'
            ]);
            if($validate->fails()){
                Log::error('Error al validarlos datos', ['errors' => $validate->errors()]);
                return ResponseGenerator::generateResponse("KO", 422, null, $validate->errors());
            }else {
                Log::info('Correcta validación de datos', ['data' => $data->cardName]);
                try{
                    $cards = Card::where('name', 'like', '%'.$data->cardName.'%')->select('cards.id','cards.name','cards.description')->get();
                    Log::info('Busqueda de cartas', ['cartas' => $cards]);
                    return ResponseGenerator::generateResponse("OK", 200, $cards, "Carta buscada correctamente");
                }catch(\Exception $e){
                    Log::error('Error en la búsqueda', ['error' => $e]);
                    return ResponseGenerator::generateResponse("KO", 304, null, "Erro al buscar");
                }
            }      
        }else{
            Log::error('No hay filtro');
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
                $id = Auth::id(); 
                try{
                    $card = Card::find($data->cardId);
                    $card->users()->attach($id, ['amount'=>$data->amount, 'price'=>$data->price]);
                    return ResponseGenerator::generateResponse("OK", 200, $id, "Carta añadida correctamente");
                }catch(\Exception $e){
                    return ResponseGenerator::generateResponse("KO", 304, null, "Error al añadir");
                }
                
            }      
        }else{
            return ResponseGenerator::generateResponse("KO", 500, null, "Datos no registrados");
        }

    }
    public function searchToBuy(Request $request){
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
                    $cards = Card::where('name', 'like', '%'.$data->cardName.'%')
                        ->has('users')
                        ->join('card_user', 'cards.id', '=', 'card_user.card_id')
                        ->join('users', 'card_user.user_id', '=', 'users.id')
                        ->orderBy('card_user.price','asc')
                        ->select('cards.*','card_user.user_id', 'card_user.price','card_user.amount')
                        ->get();

                    if(isset($cards)){
                        return ResponseGenerator::generateResponse("OK", 200, $cards, "Carta buscada correctamente");
                    }else{
                        return ResponseGenerator::generateResponse("OK", 200, null, "Carta no encontrada");
                    }
                }catch(\Exception $e){
                    return ResponseGenerator::generateResponse("KO", 304, $e, "Error al buscar");
                }
            }      
        }else{
            return ResponseGenerator::generateResponse("KO", 500, null, "Datos no registrados");
        }
    }
    public function edit(Request $request){
        $json = $request->getContent();
        $data = json_decode($json);

        if($data){
            $validate = Validator::make(json_decode($json,true), [
                'cardId' => 'required|exists:cards,id',
                'name' => 'required|string',
                'description' => 'required|string'
            ]);
            if($validate->fails()){
                return ResponseGenerator::generateResponse("KO", 422, null, $validate->errors());
            }else {

                $card = Card::find($data->cardId);
                $card->name = $data->name;
                $card->description = $data->description;
                try{
                    $card->save();
                    return ResponseGenerator::generateResponse("OK", 200, $card, "Carta actualizada correctamente");
                }catch(\Exception $e){
                    return ResponseGenerator::generateResponse("KO", 304, null, "Error al actualizar");
                }
            }      
        }else{
            return ResponseGenerator::generateResponse("KO", 500, null, "Datos no registrados");
        }
    }
    public function addFromMagic(){
        $response = Http::get('https://api.magicthegathering.io/v1/cards');
        if($response->object()){
            try{
                foreach($response->object()->cards as $card){
                    $existCard = Card::where('number', 'LIKE' ,$card->number)->first();
                    $collection = Collection::where('code','LIKE',$card->set)->get();
                    if($existCard){
                        $existCard->name = $card->name;
                        $existCard->number = $card->number;
                        $existCard->description = $card->text;
                        try{
                            $existCard->save();
                            $existCard->collections()->attach($collection[0]->id);
                        }catch(\Exception $e){
                            return ResponseGenerator::generateResponse("KO", 304, $e, "Error al guardar existente");
                        }
                    }else{
                        $newCard = new Card();
                        $newCard->name = $card->name;
                        $newCard->number = $card->number;
                        $newCard->description = $card->text;
                        try{
                            $newCard->save();
                            $newCard->collections()->attach($collection->id);
                        }catch(\Exception $e){
                            return ResponseGenerator::generateResponse("KO", 304, $collection, "Error al guardar nueva");
                        }
                    } 
                }
                return ResponseGenerator::generateResponse("OK", 200, null, "Cartas guardadas correctamente");
            }catch(\Exception $e){
                return ResponseGenerator::generateResponse("KO", 304, $response, "Error con lel bucle for");
            }
        }

    }
}
