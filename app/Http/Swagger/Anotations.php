<?php

/**
 * @OA\Info(
 *      version="1.0.0",
 *      title="Object Access Api",
 *      @OA\Contact(
 *          name="Zaw Myo Latt",
 *          email="zawmyolatt.ucsy@gmail.com",
 *          url="http://zawmyolatt.github.io"
 *      )
 * )
 */

/**
 *  @OA\Server(
 *      url=L5_SWAGGER_CONST_HOST,
 *      description="Key Api server"
 *  )
 */

/**
 * @OA\Get(
 *      path="/object/get_all_records",
 *      operationId="getObjectsList",
 *      tags={"Objects"},
 *      summary="Get list of objects",
 *      description="Returns list of objects",
 *      @OA\Parameter(
 *          name="page",
 *          in="query",
 *          description="Page to return",
 *          required=false,
 *          @OA\Schema(
 *              type="integer"
 *          )
 *      ),
 *      @OA\Parameter(
 *          name="limit",
 *          in="query",
 *          description="Maximum number of items to return per page",
 *          required=false,
 *          @OA\Schema(
 *              type="integer"
 *          )
 *      ),
 *      @OA\Response(response=200, description="Success"),
 *      @OA\Response(response=400, description="Bad request")
 *     )
 *
 * Returns list of objects
 */

/**
 * @OA\Get(
 *      path="/object/{key}",
 *      operationId="getObjectByKey",
 *      tags={"Objects"},
 *      summary="Get object information",
 *      description="Returns object data",
 *      @OA\Parameter(
 *          name="key",
 *          description="Object Key",
 *          required=true,
 *          in="path",
 *          @OA\Schema(
 *              type="string"
 *          )
 *      ),
 *      @OA\Parameter(
 *          name="timestamp",
 *          in="query",
 *          description="Request Timestamp",
 *          required=false,
 *          @OA\Schema(
 *              type="integer"
 *          )
 *      ),
 *      @OA\Response(response=200, description="Success"),
 *      @OA\Response(response=400, description="Bad request"),
 *      @OA\Response(response=404, description="Not Found")
 * )
 */