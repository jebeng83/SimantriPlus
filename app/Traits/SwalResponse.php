<?php

namespace App\Traits;

trait SwalResponse
{
    public function toastResponse($title="", $icon="success", $position="top-right") : array
    {
        return [
            'title' => $title,
            'timer'=>3000,
            'icon'=> $icon,
            'toast'=>true,
            'position'=> $position,
            'showConfirmButton'=>false,
        ];
    }
}