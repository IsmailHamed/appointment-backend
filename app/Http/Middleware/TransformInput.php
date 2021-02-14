<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\ParameterBag;

class TransformInput
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next, $transformer)
    {
        $this->transformeInputs($request, $transformer);
        $this->transformeQueryParameters($request, $transformer);
        $response = $next($request);
        $this->transformeErrors($response, $transformer);
        return $response;
    }

    private function transformeQueryParameters($request, $transformer)
    {
        $oldQueryString = $request->query->all(); // To not lose other params
        if (count($oldQueryString) > 0) {
            $transformedQuery = [];
            foreach ($oldQueryString as $input => $value) {
                $originalInput = $transformer::originalAttribute($input);
                if (!is_null($originalInput)) {
                    $transformedQuery[$originalInput] = $value;
                    unset($oldQueryString[$input]);
                }
            }
            $newQueryString = array_merge($oldQueryString, $transformedQuery);
            $request->query = new ParameterBag($newQueryString);
        }
    }

    private function transformeInputs($request, $transformer)
    {
        $transformedInput = [];
        foreach ($request->request->all() as $input => $value) {
            $transformedInput[$transformer::originalAttribute($input)] = $value;
        }
        $request->replace($transformedInput);

        $this->transformeQueryParameters($request, $transformer);
    }

    private function transformeErrors($response, $transformer): void
    {
        if (isset($response->exception) && $response->exception instanceof ValidationException) {
            $data = json_decode($response->content());
            $transformedErrors = [];
            foreach ($data->errors as $field => $error) {
                $transformedField = $transformer::transformedAttribute($field);
                $transformedErrors[$transformedField] = str_replace($field, $transformedField, $error);
            }
            $data->errors = $transformedErrors;
            $response->setContent(json_encode($data));
        }
    }
}
