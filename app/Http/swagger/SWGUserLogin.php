<?php
/**
 * Created by PhpStorm.
 * User: rafadpedrosa
 * Date: 2018-01-16
 * Time: 12:46 AM
 */

namespace App\Http\swagger;

/**
 * @SWG\Definition(
 *     type="object",
 *     example={"email":"1@1.com", "password":"1"},
 *     @SWG\Property(type="string", property="email"),
 *     @SWG\Property(type="string", property="password"),
 * )
 */
class SWGUserLogin
{

    /**
     * * @SWG\Parameter(
     *     parameter="user_login",
     *     in="body",
     *     name="user",
     *     description="JSON obj",
     *     required=true,
     *     @SWG\Schema(ref="#/definitions/SWGUserLogin")
     *     )
     */
}