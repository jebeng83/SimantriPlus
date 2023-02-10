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

    public function swalConfirmDialog($function, $params = []) : array
    {
        return [
            'title' => 'Yakin Hapus Data ?',
            'text' => 'Data yang sudah terhapus tidak bisa dikembalikan lagi',
            'icon' => 'warning',
            'showCancelButton' => true,
            'confirmButtonText' => 'Ya',
            'cancelButtonText' => 'Tidak',
            'function' => $function,
            'params' => $params
        ];
    }
}