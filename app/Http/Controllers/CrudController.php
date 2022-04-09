<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;

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
    public function getAll()
    {
        $result = $this->model::all();
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
        $result = $this->model::find($id);
        return response()->json($result);
    }

    /**
     * Create an object
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function create(Request $request)
    {
        $param = $this->requestToArray($request);
        $result = $this->model::create($param);
        
        return response()->json($result);
    }

    /**
     * Create an object
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        $param = $this->requestToArray($request);
        $result = $this->model::where('id',$id)->update($param);

        return response()->json($result);
    }

    /**
     * Delete an object using the id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete($id)
    {
        $object = $this->model::find($id);
        $result = $object->delete();

        return response()->json($result);
    }
}
