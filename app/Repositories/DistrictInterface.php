<?php
namespace App\Repositories;

interface DistrictInterface {
    public function getAll($filter = []);
}
