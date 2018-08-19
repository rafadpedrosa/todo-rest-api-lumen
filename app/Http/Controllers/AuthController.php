<?php
/**
 * Created by PhpStorm.
 * User: rafadpedrosa
 * Date: 2018-08-19
 * Time: 4:44 PM
 */

namespace App\Http\Controllers;


use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * @SWG\Post(
     *     tags={"generic"},
     *     path="/api/authenticate",
     *     summary="Authentica usuários do sistema", produces={"application/json"},
     *     @SWG\parameter(
     *      name="body",
     *      in="body",
     *      required=true,
     *      @SWG\Schema(ref="#/definitions/auth")
     *     ),
     *     @SWG\Response(response="200", description="usuário authenticado"),
     *     @SWG\Response(response="304", description="Usuário e ou senha errads"),
     *     @SWG\Response(response="401", description="Não autorizado"),
     *     @SWG\Response(
     *     response="500",
     *     description="Erro inesperado"),
     *     )
     * )
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws $e
     */
    public function authenticate(Request $request)

    {
        $this->validate($request, [
            'email' => 'required',
            'password' => 'required'
        ]);


        $user = User::where('email', $request->input('email'))->first();


        if (Hash::check($request->input('password'), $user->password)) {

            $apikey = base64_encode(str_random(40));

            User::where('email', $request->input('email'))->update(['api_key' => "$apikey"]);;

            return response()->json(['status' => 'success', 'api_key' => $apikey]);

        } else {

            return response()->json(['status' => 'fail'], 401);

        }
    }
}