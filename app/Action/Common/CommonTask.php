<?php

namespace App\Action\Common;

class CommonTask
{

    public static function pushData($data, $model)
    {
        if (!is_array($data)) {
            throw new \InvalidArgumentException("The first argument must be an associative array.");
        }

        foreach ($data as $key => $value) {
            if (!in_array($key, $model->getGuarded())) {
                $model->$key = $value;
            }
        }

        $model->save();

        return $model;
    }
}
