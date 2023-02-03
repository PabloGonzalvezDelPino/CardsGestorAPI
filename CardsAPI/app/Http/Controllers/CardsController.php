<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use App\Http\Helpers\ResponseGenerator;
use App\Models\Card;
use App\Models\Collection;

class CardsController extends Controller
{
    public function add(Request $request){
        $json = $request->getContent();
        $data = json_decode($json);

        if($data){
            $validate = Validator::make(json_decode($json,true), [
                'name' => 'required|string',
                'description' => 'required|string',
                'collection_id' => 'required|exists:collection,id'
            ]);
            if($validate->fails()){
                return ResponseGenerator::generateResponse("KO", 422, null, $validate->errors());
            }else {

            $collection = Collection::find($data->collection_id);

                if($collection){
                    $card = new Card();
                    $card->name = $data->name;
                    $card->description = $data->description;
                    $card->collection_id = $data->collection_id;

                    try{
                        $card->save();
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
}
