<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 *  * @SWG\Definition(
 *     definition="Task",
 *     type="object",
 *     example={"title":"this is a title",
 *              "description":"this is a description",
 *              "userId":"8"
 *             },
 *     @SWG\Property(type="string", property="title"),
 *     @SWG\Property(type="string", property="description"),
 *     @SWG\Property(type="string", property="userId")
 * )
 */
class Task extends Model
{

    use SoftDeletes;

    protected $dates = ['deleted_at'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
