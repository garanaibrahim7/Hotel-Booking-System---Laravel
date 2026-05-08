<?php

namespace App\Traits;

trait HttpResponses {
    /**
     * Standard Success Response
     */
    protected function success($data, $message = null, $code = 200) {
        return response()->json([
            'status'  => 'Success',
            'message' => $message,
            'data'    => $data
        ], $code);
    }

    /**
     * Standard Error Response
     */
    protected function error($data, $message = null, $code = 400) {
        return response()->json([
            'status'  => 'Error',
            'message' => $message,
            'data'    => $data
        ], $code);
    }
}
