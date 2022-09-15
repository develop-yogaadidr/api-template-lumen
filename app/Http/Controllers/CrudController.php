<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Enums\StatusCodes;
use App\Helpers\QueryGenerator;

abstract class CrudController extends Controller
{
    private $model;
    public $createRules;
    public $updateRules;

    protected function __construct($model)
    {
        $this->model = $model;
    }

    /**
     * Get all the objects
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAll(Request $request)
    {
        $generatedRequest = new QueryGenerator($this->model);
        $result = $generatedRequest->searchGenerator($request);
        
        return response()->json($result);
    }

    /**
     * Get the object by the id
     *
     * @param  string $id
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function getById(Request $request, $id)
    {
        $model = $this->model;
        if ($request->join !== null && $request->join !== '') {
            $join = strtolower($request->join);
            $words = explode(",", $join);
            $model = $model->with($words);
        }

        $result = $model->findOrFail($id);

        return response()->json($result);
    }

    /**
     * Create an object
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function create(Request $request)
    {
        if ($this->createRules != null) $this->validate($request, $this->createRules);

        $result = $this->model->fill($request->all());
        $result->save();

        return response()->json($result);
    }

    /**
     * Update an object using the id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        if ($this->updateRules != null) $this->validate($request, $this->updateRules);

        $result = $this->model::findOrFail($id)->fill($request->all());
        $result->save();

        return response()->json(null, StatusCodes::NoContent);
    }

    /**
     * Delete an object using the id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete($id)
    {
        $object = $this->model::findOrFail($id);
        $object->delete();

        return response()->json(null, StatusCodes::NoContent);
    }
}
