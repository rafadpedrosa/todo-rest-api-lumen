<?php

namespace App\Http\Controllers;

use App\Http\Models\task;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Mockery\Exception;

class TaskController extends Controller
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
     *     tags={"tasks"},
     *     path="/api/tasks",
     *     summary="Busca todos os tasks paginados", produces={"application/json"},
     *     security={{"api_key": {"scope"}}},
     *     @SWG\Parameter(ref="#/parameters/pFilter"),
     *     @SWG\Parameter(ref="#/parameters/pPage"),
     *     @SWG\Parameter(ref="#/parameters/pPer_page"),
     *     @SWG\Parameter(ref="#/parameters/pSort"),
     *     @SWG\Parameter(ref="#/parameters/pColumns"),
     *     @SWG\Parameter(ref="#/parameters/pPageName"),
     *     @SWG\Response(response="200", description="Retorna tasks paginados"),
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

        $query = Task::query();

        $query = mountOrWhereRecursive($query, 'title', $filter);
        $query = mountOrWhereRecursive($query, 'description', $filter);

        return response()
            ->json($query->orderBy($sort[0], $sort[1])
                ->paginate($per_page, $columns, $pageName, $page));
    }

    /**
     * @SWG\Post(
     *     tags={"tasks"},
     *     path="/api/task",
     *     summary="Salva task",
     *     produces={"application/json"},
     *     security={{"api_key": {"scope"}}},
     *     @SWG\parameter(
     *      name="body",
     *      in="body",
     *      required=true,
     *      @SWG\Schema(ref="#/definitions/Task")
     *     ),
     *     @SWG\Response(response="200", description="task salvo com sucesso"),
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
                'title' => 'required|unique:tasks'
            ]);
            $task = new Task();
            $task->title = $request->json()->get('title');
            $task->description = $request->json()->get('description');
            $task->userId = $request->json()->get('userId');

            $task->saveOrFail();

            return response()
                ->json(["STORE " => $request->all(), "task Created" => $task]);
        } catch (Exception $e) {
            return response()
                ->json(["Unexpected error", $e->getMessage()]);
        }
    }

    /**
     * @SWG\Get(
     *     tags={"tasks"},
     *     path="/api/task/{id}",
     *     summary="Busca task",
     *     produces={"application/json"},
     *     security={{"api_key": {"scope"}}},
     *     @SWG\Parameter(ref="#/parameters/pId"),
     *     @SWG\Response(response="200", description="task encontrado"),
     *     @SWG\Response(response="500", description="Internal server Error")
     * )
     * @param Request $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        return response()
            ->json(["SHOW " => Task::findOrFail($id)]);
    }

    /**
     * @SWG\Put(
     *     tags={"tasks"},
     *     path="/api/task/{id}",
     *     summary="Altera task",
     *     produces={"application/json"},
     *     security={{"api_key": {"scope"}}},
     *     @SWG\Parameter(ref="#/parameters/pId"),
     *     @SWG\parameter(
     *      name="body",
     *      in="body",
     *      required=true,
     *      @SWG\Schema(ref="#/definitions/Task")
     *     ),
     *     @SWG\Response(response="200", description="task alterado com sucesso"),
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
                'title' => 'required|unique:tasks',
                Rule::unique('Tasks', 'title')
                    ->ignore($id)
                    ->where('deleted_at', 'NULL')
            ]);

            $task = new Task();
            $task->title = $request->json()->get('title');
            $task->description = $request->json()->get('description');
            $task->userId = $request->json()->get('userId');

            $task->saveOrFail();

            return response()
                ->json(["UPDATE " => $id, "task Updated" => $task]);
        } catch (Exception $e) {
            return response()
                ->json(["Unexpected error", $e->getMessage()]);
        }
    }

    /**
     * @SWG\Get(
     *     tags={"tasks"},
     *     path="/api/task/{id}/edit",
     *     summary="Busca task para ser editado",
     *     produces={"application/json"},
     *     security={{"api_key": {"scope"}}},
     *     @SWG\Parameter(ref="#/parameters/pId"),
     *     @SWG\Response(response="200", description="task encontrado para ser editado"),
     *     @SWG\Response(response="500", description="Internal server Error")
     * )
     * @param Request $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function edit($id)
    {
        return response()
            ->json(Task::findOrFail($id));
    }

    /**
     * @SWG\Delete(
     *     tags={"tasks"},
     *     path="/api/task/{id}",
     *     summary="Deleta task",
     *     produces={"application/json"},
     *     security={{"api_key": {"scope"}}},
     *     @SWG\Parameter(ref="#/parameters/pId"),
     *     @SWG\Response(response="200", description="task alterado com sucesso"),
     *     @SWG\Response(response="500", description="Internal server Error")
     * )
     * @param Request $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        Task::findOrFail($id)->delete();
        return response()
            ->json(["DESTROY " => $id]);
    }
}
