<?php

namespace App\Helpers;

class QueryGenerator
{

    protected $model;

    public function __construct($model)
    {
        $this->model = $model;
    }

    public function searchGenerator($request)
    {
        $per_page = $request->per_page ?  $request->per_page : 'all';
        $filter = $request->filter ? $request->filter : [];
        $sort = $request->sort ? $request->sort : 'created_at,ASC';
        $join = $request->join ? $request->join : '';
        $count = $request->count ? $request->count : '';
        $whereHas = $request->where_has ? $request->where_has : [];
        $limit = $request->limit ? $request->limit : '';
        $between = $request->between ? $request->between : '';

        $model = $this->model;

        if ($between) {
            $words = explode(",", $between);
            $model = $model->whereBetween($words[0], [$words[1], $words[2]]);
        }

        if (is_array($whereHas)) {
            foreach ($whereHas as $item => $value) {
                $words = explode(",", $value);
                $model = $model->whereHas($words[0], function ($query) use ($words) {
                    $query->where($words[1], $words[2]);
                });
            }
        }

        if ($join !== '') {
            $join = strtolower($join);
            $words = explode(",", $join);
            $model = $model->with($words);
        }

        if ($count !== '') {
            $count = strtolower($count);
            $words = explode(",", $count);
            $model = $model->withCount($words);
        }

        if (is_array($filter)) {
            foreach ($filter as $item => $value) {
                $words = explode(",", $value);
                if (array_key_exists(2, $words)) {
                    if ($words[2] || $words[2] == 'AND') {
                        $model = $model->orWhere($words[0], 'LIKE', '%' . $words[1] . '%');
                    } else {
                        $model = $model->where($words[0], 'LIKE', '%' . $words[1] . '%');
                    }
                } else {
                    $model = $model->where($words[0], 'LIKE', '%' . $words[1] . '%');
                }
            }
        }

        $sortItem = array_map('trim', explode(",", $sort));
        if (strtoupper($sortItem[1]) == 'ASC' || strtoupper($sortItem[1]) == 'DESC') {
            $model = $model->orderBy($sortItem[0], $sortItem[1]);
        }

        if ($limit != '') {
            $model = $model->limit($limit)->get();
        } else {
            if ($per_page !== 'all') {
                $model = $model->paginate($per_page);
            } else {
                $model = $model->get();
            }
        }

        return $model;
    }
}
