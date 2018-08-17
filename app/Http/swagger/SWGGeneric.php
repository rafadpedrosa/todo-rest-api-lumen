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
 *     example={"name":"test"},
 *     @SWG\Property(type="string", property="email"),
 *     @SWG\Property(type="string", property="password"),
 * )
 */
class SWGGeneric
{

    /**
     * * @SWG\Parameter(
     *     parameter="generic_def",
     *     in="body",
     *     name="generic",
     *     description="JSON obj",
     *     required=true,
     *     @SWG\Schema(ref="#/definitions/SWGGeneric")
     *     )
     */
}