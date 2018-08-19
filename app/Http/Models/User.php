<?php

namespace App\Http\Models;

use Illuminate\Auth\Authenticatable as AuthenticableTrait;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @SWG\Definition(
 *     type="object",
 *     example={"name":"User name","description":"User 1"},
 *     @SWG\Property(type="integer", property="id"),
 *     @SWG\Property(type="string", property="username"),
 *     @SWG\Property(type="string", property="email"),
 *     @SWG\Property(type="string", property="password")
 * )
 */
class User extends Model implements Authenticatable
{

    use AuthenticableTrait;
    use SoftDeletes;

    protected $fillable = ['username', 'email', 'password']; // , 'userimage'

    protected $hidden = ['password'];

    protected $dates = ['deleted_at'];

}
