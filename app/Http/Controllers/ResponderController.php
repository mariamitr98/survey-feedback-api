<?php

namespace App\Http\Controllers;

use App\Models\Responder;

class ResponderController extends Controller
{

    /**
     * Get responder informations.
     */
    public function getResponderInfo()
    {
        $id = auth('api')->user()->id;
        $responder = Responder::where('id', $id)->first()->makeHidden('password');
        return response()->json(compact('responder'), 200);
    }
}
