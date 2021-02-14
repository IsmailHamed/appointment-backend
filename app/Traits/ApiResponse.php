<?php

namespace App\Traits;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Model;
use League\Fractal\Manager;
use Symfony\Component\HttpFoundation\Response;
use League\Fractal\Resource\Collection;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;

trait ApiResponse
{
    public function successResponse($data, $code): \Illuminate\Http\JsonResponse
    {
        return response()->json($data, $code);
    }

    protected function errorMessage($message, $code)
    {
        return response()->json(['message' => $message, 'status_code' => $code], $code);
    }

    protected function successMessage($message, $code = Response::HTTP_OK)
    {
        return $this->successResponse(['message' => $message, 'status_code' => $code], $code);
    }

    protected function showOne(Model $model, $transformer, $code = 200)
    {
        if ($model != null) {
            $model = $this->transformData($model, $transformer);
        }
        return $this->successResponse($model, $code);
    }

    protected function showAll($data, $transformer, $code = 200)
    {
        $per_page = request('per_page', 15);
        $paginator = $data->paginate($per_page);
        $collection = $this->transformCollection($paginator, $transformer);

        return $this->successResponse($collection, $code);
    }

    protected function transformData($data, $transformer)
    {
        $transformation = fractal($data, new $transformer);
        return $transformation->toArray();

    }


    /**
     * @param LengthAwarePaginator $paginator
     * @param $transformer
     * @return \Illuminate\Support\Collection
     */
    protected function transformCollection(LengthAwarePaginator $paginator, $transformer): \Illuminate\Support\Collection
    {
        $collection = new Collection($paginator->getCollection(), $transformer);
        $collection->setPaginator(new IlluminatePaginatorAdapter($paginator));
        $fractal = new Manager();
        if (request()->has('include')) {
            $fractal->parseIncludes(request('include'));
        }
        $collection = collect($fractal->createData($collection)->toArray());
        return $collection;
    }

    protected function IsNullOrEmptyString($str): bool
    {
        return (!isset($str) || trim($str) === '');
    }

}
