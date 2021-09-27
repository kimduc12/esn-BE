<?php
namespace App\Repositories\Eloquent;

use App\Repositories\FileInterface;
use App\Models\File;

class FileRepository implements FileInterface {
    protected $model;
    function __construct(File $file){
        $this->model = $file;
    }


    public function create($data){
        return $this->model->create($data);
    }


}
