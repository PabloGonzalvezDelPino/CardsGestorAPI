<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Http\Helpers\ResponseGenerator;
use App\Models\Card;
use App\Models\Collection;

class CollectionsController extends Controller
{
    public function create(Request $request){
        $json = $request->getContent();
        $data = json_decode($json);

        if($data){
            $validate = Validator::make(json_decode($json,true), [
                'name' => 'required|string',
                'image' => 'required|string',
            ]);
            if($validate->fails()){
                return ResponseGenerator::generateResponse("KO", 422, null, $validate->errors());
            }else {
                $collection = new Collection();
                $collection->name = $data->name;
                $collection->image = $data->image;
                try{
                    foreach($data->cards as $element){
                        if(isset($element->cardId)){
                            try{
                            $card = Card::find($element->cardId);
                            }catch(\Exception $e){
                                return ResponseGenerator::generateResponse("KO", 304, $e, "Error al Buscar la carta");
                            }
                            try{
                                $collection->save();
                                $collection->cards()->attach($element->cardId);
                            }catch(\Exception $e){
                                return ResponseGenerator::generateResponse("KO", 304, $e, "Error al añadir la carta a la colección");
                            }
                        }else if(isset($element->cardName) &&isset($element->cardDescription)){   
                            $card = new Card();
                            $card->name = $element->cardName;
                            $card->description = $element->cardDescription;
                            try{
                                $card->save();
                            }catch(\Exception $e){
                                return ResponseGenerator::generateResponse("KO", 304, $e, "Error al Buscar la carta");
                            }
                            try{
                                $collection->save();
                                $collection->cards()->attach($card->id);
                            }catch(\Exception $e){
                                return ResponseGenerator::generateResponse("KO", 304, $e, "Error al añadir la carta a la colección");
                            }
                        }
                    }
                    return ResponseGenerator::generateResponse("OK", 200, $collection, "Cartas añadidas a la colección"); 
                }catch(\Exception $e){
                    return ResponseGenerator::generateResponse("KO", 304, $e, "Error al añadir las cartas a la colección");
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
                'collectionId' => 'required|exists:collections,id',
                'name' => 'required|string',
                'image' => 'required|string',
            ]);
            if($validate->fails()){
                return ResponseGenerator::generateResponse("KO", 422, null, $validate->errors());
            }else {
                $collection = Collection::find($data->collectionId);
                $collection->name = $data->name;
                $collection->image = $data->image;
                try{
                    $collection->save();
                    return ResponseGenerator::generateResponse("OK", 200, $card, "Collección guardada correctamente");
                }catch(\Exception $e){
                    return ResponseGenerator::generateResponse("KO", 304, null, "Error al guardar");
                }
            }      
        }else{
            return ResponseGenerator::generateResponse("KO", 500, null, "Datos no registrados");
        }
    }
}
