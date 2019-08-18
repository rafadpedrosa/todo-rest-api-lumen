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


        if ($user && Hash::check($request->input('password'), $user->password)) {
            return $this->getAuthPayload($user);
        } else {
            return response()->json(['status' => 'fail'], 401);
        }
    }

    /**
     * @param $user
     * @return \Illuminate\Http\JsonResponse
     */
    private function getAuthPayload($user): \Illuminate\Http\JsonResponse
    {
        $apiKey = base64_encode(str_random(40));

        $user->api_key = $apiKey;
        $user->save();

        return response()->json([
            'userId' => $user->id,
            'email' => $user->email,
            'username' => $user->username,
            'api_key' => $user->api_key
        ]);
    }

    /**
     * @SWG\Post(
     *     tags={"generic"},
     *     path="/api/authenticateByToken",
     *     summary="Authentica usuários do sistema pelo token", produces={"application/json"},
     *     @SWG\parameter(
     *      name="body",
     *      in="body",
     *      required=true,
     *      @SWG\Schema(ref="#/definitions/auth_api")
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
    public function authenticateByToken(Request $request)

    {
        $this->validate($request, [
            'api_key' => 'required',
        ]);


        $user = User::where('api_key', $request->input('api_key'))->first();


        if (!empty($user)) {
            return $this->getAuthPayload($user);
        } else {
            return response()->json(['status' => 'fail'], 401);
        }
    }

    /**
     * @SWG\Post(
     *     tags={"generic"},
     *     path="/api/logout",
     *     summary="Desloga o usuários do sistema", produces={"application/json"},
     *     @SWG\parameter(
     *      name="body",
     *      in="body",
     *      required=true,
     *      @SWG\Schema(ref="#/definitions/auth_api")
     *     ),
     *     @SWG\Response(response="200", description="usuário deslogado"),
     *     @SWG\Response(
     *     response="500",
     *     description="Erro inesperado"),
     *     )
     * )
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws $e
     */
    public function logout(Request $request)

    {
        $this->validate($request, [
            'api_key' => 'required',
        ]);


        $user = User::where('api_key', $request->input('api_key'))->first();


        if (!empty($user)) {
            User::find($user->id)->update(['api_key' => null]);

            return response()->json(['status' => 'user loggedOut']);
        } else {
            return response()->json(['status' => 'fail'], 500);
        }
    }
}