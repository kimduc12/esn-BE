<?php

namespace App\Services;

class BaseService
{
    public function _result($status = true, $message = '', $data = null)
    {
        return [
            'status'  => $status,
            'message' => $message,
            'data'    => $data
        ];
    }

    public function removeNullAttributeForProductOption($attributes, $options)
    {
        foreach ($options as $key => $option) {
            foreach ($attributes as $attribute) {
                if ($option[$attribute->slug] == null) {
                    unset($options[$key][$attribute->slug]);
                }
            }
        }
        return $options;
    }
}
