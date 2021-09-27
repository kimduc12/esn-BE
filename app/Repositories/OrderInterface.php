<?php
namespace App\Repositories;

interface OrderInterface {
    public function getAllPaginate($perPage = 20, $filter = []);
    public function create($data);
    public function getByID($id);
    public function updateByID($id,$data);
    public function destroyByIDs($id);
}
