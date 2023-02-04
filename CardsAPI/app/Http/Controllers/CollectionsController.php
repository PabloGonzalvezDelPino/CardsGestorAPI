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
                if(isset($data->card_id)){
                    $card = Card::find($data->card_id);
                    $collection->cards()->attach($data->card_id);
                    try{
                        $collection->save();
                        return ResponseGenerator::generateResponse("OK", 200, $card, "Collección guardada correctamente");
                    }catch(\Exception $e){
                        return ResponseGenerator::generateResponse("KO", 304, null, "Error al guardar");
                    }
                }else if(isset($data->card_name)&&isset($data->card_description)){
                    $card = new Card();
                    $card->name = $data->card_name;
                    $card->description = $data->card_description;
                    try{
                        $card->save();
                        $collection->save(); 
                        $collection->cards()->attach($card->id);
                        
                        return ResponseGenerator::generateResponse("OK", 200, $collection, "Collección guardada correctamente");
                    }catch(\Exception $e){
                        return ResponseGenerator::generateResponse("KO", 304, $e, "Error al crear y guardar");
                    }
                }else{
                    return ResponseGenerator::generateResponse("KO", 404, null, "No se ha encontrado ninguna carta");
                }
            }      
        }else{
            return ResponseGenerator::generateResponse("KO", 500, null, "Datos no registrados");
        }
    }
}
