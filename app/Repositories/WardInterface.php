<?php
namespace App\Repositories;

interface WardInterface {
    public function getAll($filter = []);
}
