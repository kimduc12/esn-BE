<?php
namespace App\Repositories;

interface SupplierInterface {
    public function getAllPaginate($perPage = 20, $filter = []);
    public function create($data);
    public function getByID($id);
    public function updateByID($id,$data);
    public function destroyByIDs($id);
    public function getListPaginate($perPage = 20, $filter = []);
    public function getAll($filter = []);
}
