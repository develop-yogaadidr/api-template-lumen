<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Enums\StatusCodes;
use App\Helpers\MessageParameter;
use App\Helpers\QueryGenerator;
use Kreait\Firebase\Messaging\MessageTarget;

abstract class CrudController extends Controller
{
    private $model;
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
    public function getById($id)
    {
        $result = $this->model::findOrFail($id);
        return response()->json($result);
    }

    /**
     * Create an object
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function create(Request $request)
    {
        $this->validateRequestInput($request, $this->model->getFillable());
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
        $this->validateRequestInput($request, $this->model->getFillable());
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
