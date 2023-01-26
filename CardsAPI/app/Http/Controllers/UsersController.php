<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use App\Http\Helpers\ResponseGenerator;
use App\Models\User;

class UsersController extends Controller
{
    public function register(Request $request){
        $json = $request->getContent();
        $data = json_decode($json);

        if($data){
            $validate = Validator::make(json_decode($json,true), [
                'username' => 'required|string|unique:users',
                'email' => 'required|string|email|unique:users',
                'password' => 'required|string'
            ]);
            if($validate->fails()){
                return ResponseGenerator::generateResponse("KO", 422, null, $validate->errors());
            }else {
                $user = new User();
                $user->username = $data->username;
                $user->email = $data->email;
                $user->password = Hash::make($data->password);
                $user->type = $data->type;
                try{
                    $user->save();
                    return ResponseGenerator::generateResponse("OK", 200, $user, "Usuario guardado correctamente");
                }catch(\Exception $e){
                    return ResponseGenerator::generateResponse("KO", 304, null, "Error al guardar");
                }
            }
        }else{
            return ResponseGenerator::generateResponse("KO", 500, null, "Datos no registrados");
        }
    }
}
