<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    /**
     * Generate a standardized success message for a given model action.
     *
     * @param  string|object  $model  Either the model class name or an instance.
     * @param  string         $action One of 'added', 'updated', etc.
     * @return string
     */
    protected function successMessage($model, $action = 'added')
    {
        $name = is_object($model) ? class_basename(get_class($model)) : class_basename($model);
        return "{$name} has been {$action} successfully.";
    }
}

