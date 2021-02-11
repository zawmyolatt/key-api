<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\ObjectRequest;
use App\Models\ObjectData;
use App\Transformers\ObjectDataTransformer;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ObjectController extends ApiController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(ObjectRequest $request)
    {
        $page = ($request->page) ?? 1;
        $limit = ($request->limit) ?? env('DEFAULT_PAGINATION_LIMIT');
        $sortedObjects = ObjectData::orderBy('created_at', 'desc');
        $objects = DB::table(DB::raw("({$sortedObjects->toSql()}) as sub"))
                    ->groupBy('key')
                    ->simplePaginate($limit, ['*'], 'page', $page);
        return $this->respondWithCollection($objects, new ObjectDataTransformer());
    }

    /**
     * Store a item of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function store(ObjectRequest $request)
    {
        $inputKey = array_key_first($request->all());
        $object = ObjectData::create([
            'key' => $inputKey,
            'value' => $request->$inputKey
        ]);
        return $this->respondWithItem($object, new ObjectDataTransformer(), null, [], 201);
    }

    /**
     * Display a item of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function show(ObjectRequest $request)
    {
        $object = ObjectData::where('key', $request->key);
        if($request->filled('timestamp')) {
            $object = $object->where('created_at', '<=', Carbon::createFromTimestamp($request->timestamp));
        }
        $object = $object->orderBy('created_at', 'desc')->first();
        return $object ? $this->respondWithItem($object, new ObjectDataTransformer()): $this->respondWithError(404, trans('errors.no_data'));
    }
}
