<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{


    /**
     * @OA\Info(
     *    title="YouthConnekt",
     *    version="1.0.0",
     * )
     * 
     * @OA\Server(
     * url=L5_SWAGGER_CONST_HOST,
     * description="Serveur"
     * )
    */


    use AuthorizesRequests, ValidatesRequests, DispatchesJobs;
}
