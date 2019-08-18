<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @SWG\Definition(
 *     type="object",
 *     example={"name":"lunch name","description":"lunch 1"},
 *     @SWG\Property(type="integer", property="lunch_id"),
 *     @SWG\Property(type="string", property="description"),
 * )
 */
class lunch extends Model
{

    use SoftDeletes;

    protected $dates = ['deleted_at'];

}
