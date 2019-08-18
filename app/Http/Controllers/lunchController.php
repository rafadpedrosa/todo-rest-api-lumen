<?php

namespace App\Http\Controllers;

use App\Http\Models\lunch;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Mockery\Exception;

class lunchController extends Controller
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
     *     tags={"lunchs"},
     *     path="/api/lunchs",
     *     summary="Busca todos os lunchs paginados", produces={"application/json"},
     *     @SWG\parameter(ref="#/parameters/pAuthorization"),
     *     @SWG\Parameter(ref="#/parameters/pFilter"),
     *     @SWG\Parameter(ref="#/parameters/pPage"),
     *     @SWG\Parameter(ref="#/parameters/pPer_page"),
     *     @SWG\Parameter(ref="#/parameters/pSort"),
     *     @SWG\Parameter(ref="#/parameters/pColumns"),
     *     @SWG\Parameter(ref="#/parameters/pPageName"),
     *     @SWG\Response(response="200", description="Retorna lunchs paginados"),
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

        $query = lunch::query();

        $query = mountOrWhereRecursive($query, 'name', $filter);

        return response()
            ->json($query->orderBy($sort[0], $sort[1])
                ->paginate($per_page, $columns, $pageName, $page));
    }

    /**
     * @SWG\Post(
     *     tags={"lunchs"},
     *     path="/api/lunch",
     *     summary="Salva lunch",
     *     produces={"application/json"},
     *     @SWG\parameter(ref="#/parameters/pAuthorization"),
     *     @SWG\parameter(
     *      name="body",
     *      in="body",
     *      required=true,
     *      @SWG\Schema(ref="#/definitions/lunch")
     *     ),
     *     @SWG\Response(response="200", description="lunch salvo com sucesso"),
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
                'name' => 'required|unique:lunchs'
            ]);
            $lunch = new lunch();
            $lunch->name = $request->json()->get('name');

            $lunch->saveOrFail();

            return response()
                ->json(["STORE " => $request->all(), "lunch Created" => $lunch]);
        } catch (Exception $e) {
            return response()
                ->json(["Unexpected error", $e->getMessage()]);
        }
    }

    /**
     * @SWG\Get(
     *     tags={"lunchs"},
     *     path="/api/lunch/{id}",
     *     summary="Busca lunch",
     *     produces={"application/json"},
     *     @SWG\parameter(ref="#/parameters/pAuthorization"),
     *     @SWG\Parameter(ref="#/parameters/pId"),
     *     @SWG\Response(response="200", description="lunch encontrado"),
     *     @SWG\Response(response="500", description="Internal server Error")
     * )
     * @param Request $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        return response()
            ->json(["SHOW " => lunch::findOrFail($id)]);
    }

    /**
     * @SWG\Put(
     *     tags={"lunchs"},
     *     path="/api/lunch/{id}",
     *     summary="Altera lunch",
     *     produces={"application/json"},
     *     @SWG\parameter(ref="#/parameters/pAuthorization"),
     *     @SWG\Parameter(ref="#/parameters/pId"),
     *     @SWG\parameter(
     *      name="body",
     *      in="body",
     *      required=true,
     *      @SWG\Schema(ref="#/definitions/lunch")
     *     ),
     *     @SWG\Response(response="200", description="lunch alterado com sucesso"),
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
                'name' => 'required|unique:lunchs',
                Rule::unique('lunchs', 'name')
                    ->ignore($id)
                    ->where('deleted_at', 'NULL')
            ]);

            $lunch = new lunch();
            $lunch->name = $request->json()->get('name');
            
            $lunch->saveOrFail();

            return response()
                ->json(["UPDATE " => $id, "lunch Updated" => $lunch]);
        } catch (Exception $e) {
            return response()
                ->json(["Unexpected error", $e->getMessage()]);
        }
    }

    /**
     * @SWG\Get(
     *     tags={"lunchs"},
     *     path="/api/lunch/{id}/edit",
     *     summary="Busca lunch para ser editado",
     *     produces={"application/json"},
     *     @SWG\parameter(ref="#/parameters/pAuthorization"),
     *     @SWG\Parameter(ref="#/parameters/pId"),
     *     @SWG\Response(response="200", description="lunch encontrado para ser editado"),
     *     @SWG\Response(response="500", description="Internal server Error")
     * )
     * @param Request $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function edit($id)
    {
        return response()
            ->json(lunch::findOrFail($id));
    }

    /**
     * @SWG\Delete(
     *     tags={"lunchs"},
     *     path="/api/lunch/{id}",
     *     summary="Deleta lunch",
     *     produces={"application/json"},
     *     @SWG\parameter(ref="#/parameters/pAuthorization"),
     *     @SWG\Parameter(ref="#/parameters/pId"),
     *     @SWG\Response(response="200", description="lunch alterado com sucesso"),
     *     @SWG\Response(response="500", description="Internal server Error")
     * )
     * @param Request $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        lunch::findOrFail($id)->delete();
        return response()
            ->json(["DESTROY " => $id]);
    }
}
