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
    public function login(Request $request){
        $json = $request->getContent();
        $data = json_decode($json);

        if($data){
           $validate = Validator::make(json_decode($json,true), [
               'username' => 'required',
               'password' => 'required'
           ]);
           if($validate->fails()){
               return ResponseGenerator::generateResponse("OK", 422, null, $validate->errors());
           }else{
               try{
                   $user = User::where('username', 'like', $data->username)->firstOrFail();

                   if(!Hash::check($data->password, $user->password)) {
                       return ResponseGenerator::generateResponse("KO", 404, null, "Login incorrecto, comprueba la contraseña");
                   }else{
                       $user->tokens()->delete();
   
                       $token = $user->createToken($user->username);
                       return ResponseGenerator::generateResponse("OK", 200, $token->plainTextToken, "Login correcto");
                   }
               }catch(\Exception $e){
                   return ResponseGenerator::generateResponse("KO", 404, null, "Login incorrecto, usuario erróneo");
               }
           }
       }
   }
   //User aa token: hgPUxmfA09Qr3BdGnmRgIhX9qWHkKO6NwTkQx9gX
   public function recoverPass(Request $request){
    $json = $request->getContent();
    $data = json_decode($json);

    if($data){
        $validate = Validator::make(json_decode($json,true), [
            'email' => 'required'
        ]);
        if($validate->fails()){
            return ResponseGenerator::generateResponse("OK", 422, null, $validate->errors());
        }else{
            try{
                $user = User::where('email', 'like', $data->email)->firstOrFail();
                $newPass = random_int(100000, 999999);
                $user->password = Hash::make($newPass);
                $user->save();
                return ResponseGenerator::generateResponse("OK", 200, null , "Tu nueva pass es: ".$newPass);
                
            }catch(\Exception $e){
                return ResponseGenerator::generateResponse("KO", 404, null, "Correo incorrecto");
            }
        
        }
    }
   }
}
