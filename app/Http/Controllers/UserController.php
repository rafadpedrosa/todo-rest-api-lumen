<?php

namespace App\Http\Controllers;

use App\Http\Models\user;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Mockery\Exception;

class UserController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
    }


    /**
     * @SWG\Get(
     *     tags={"users"},
     *     path="/api/users",
     *     summary="Busca todos os users paginados", produces={"application/json"},
     *     security={{"api_key": {"scope"}}},
     *     @SWG\Parameter(ref="#/parameters/pFilter"),
     *     @SWG\Parameter(ref="#/parameters/pPage"),
     *     @SWG\Parameter(ref="#/parameters/pPer_page"),
     *     @SWG\Parameter(ref="#/parameters/pSort"),
     *     @SWG\Parameter(ref="#/parameters/pColumns"),
     *     @SWG\Parameter(ref="#/parameters/pPageName"),
     *     @SWG\Response(response="200", description="Retorna users paginados"),
     *     @SWG\Response(
     *     response="500",
     *     description="Erro inesperado"),
     *     )
     * )
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws $e
     */
    public function listAll(Request $request)
    {
        $page = $request->get('page');
        $per_page = $request->get('per_page');
        $sort = $request->get('sort');
        $columns = $request->get('columns');
        $pageName = $request->get('pageName');
        $filter = empty($request->get('filter')) ? null : '%' . $request->get('filter') . '%';
        if (empty($sort)) {
            $sort = [];
            $sort[0] = 'updated_at';
            $sort[1] = 'asc';
        } else {
            $sort = explode('|', $request->get('sort'));
        }

        $query = User::query();

        $query = mountOrWhereRecursive($query, 'username', $filter);
        $query = mountOrWhereRecursive($query, 'email', $filter);

        return response()
            ->json($query->orderBy($sort[0], $sort[1])
                ->paginate($per_page, $columns, $pageName, $page));
    }

    /**
     * @SWG\Post(
     *     tags={"users"},
     *     path="/api/user",
     *     summary="Salva user",
     *     produces={"application/json"},
     *     security={{"api_key": {"scope"}}},
     *     @SWG\parameter(
     *      name="body",
     *      in="body",
     *      required=true,
     *      @SWG\Schema(ref="#/definitions/User")
     *     ),
     *     @SWG\Response(response="200", description="user salvo com sucesso"),
     *     @SWG\Response(response="500", description="Internal server Error"),
     * )
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws $e
     */
    public function store(Request $request)
    {
        try {
            $this->validate($request, [
                'name' => 'required|unique:users'
            ]);
            $user = new User();
            $user->name = $request->json()->get('name');

            $user->saveOrFail();

            return response()
                ->json(["STORE " => $request->all(), "user Created" => $user]);
        } catch (Exception $e) {
            return response()
                ->json(["Unexpected error", $e->getMessage()]);
        }
    }

    /**
     * @SWG\Get(
     *     tags={"users"},
     *     path="/api/user/{id}",
     *     summary="Busca user",
     *     produces={"application/json"},
     *     security={{"api_key": {"scope"}}},
     *     @SWG\Parameter(ref="#/parameters/pId"),
     *     @SWG\Response(response="200", description="user encontrado"),
     *     @SWG\Response(response="500", description="Internal server Error")
     * )
     * @param Request $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        return response()
            ->json(["SHOW " => User::findOrFail($id)]);
    }

    /**
     * @SWG\Put(
     *     tags={"users"},
     *     path="/api/user/{id}",
     *     summary="Altera user",
     *     produces={"application/json"},
     *     security={{"api_key": {"scope"}}},
     *     @SWG\Parameter(ref="#/parameters/pId"),
     *     @SWG\parameter(
     *      name="body",
     *      in="body",
     *      required=true,
     *      @SWG\Schema(ref="#/definitions/User")
     *     ),
     *     @SWG\Response(response="200", description="user alterado com sucesso"),
     *     @SWG\Response(response="500", description="Internal server Error")
     * )
     * @param Request $request
     * @param Request $id
     * @return \Illuminate\Http\JsonResponse
     * @throws $e
     */
    public function update(Request $request, $id)
    {
        try {
            $this->validate($request, [
                'name' => 'required|unique:users',
                Rule::unique('Users', 'name')
                    ->ignore($id)
                    ->where('deleted_at', 'NULL')
            ]);

            $user = new User();
            $user->name = $request->json()->get('name');

            $user->saveOrFail();

            return response()
                ->json(["UPDATE " => $id, "user Updated" => $user]);
        } catch (Exception $e) {
            return response()
                ->json(["Unexpected error", $e->getMessage()]);
        }
    }

    /**
     * @SWG\Get(
     *     tags={"users"},
     *     path="/api/user/{id}/edit",
     *     summary="Busca user para ser editado",
     *     produces={"application/json"},
     *     security={{"api_key": {"scope"}}},
     *     @SWG\Parameter(ref="#/parameters/pId"),
     *     @SWG\Response(response="200", description="user encontrado para ser editado"),
     *     @SWG\Response(response="500", description="Internal server Error")
     * )
     * @param Request $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function edit($id)
    {
        return response()
            ->json(User::findOrFail($id));
    }

    /**
     * @SWG\Delete(
     *     tags={"users"},
     *     path="/api/user/{id}",
     *     summary="Deleta user",
     *     produces={"application/json"},
     *     security={{"api_key": {"scope"}}},
     *     @SWG\Parameter(ref="#/parameters/pId"),
     *     @SWG\Response(response="200", description="user alterado com sucesso"),
     *     @SWG\Response(response="500", description="Internal server Error")
     * )
     * @param Request $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        User::findOrFail($id)->delete();
        return response()
            ->json(["DESTROY " => $id]);
    }
}
