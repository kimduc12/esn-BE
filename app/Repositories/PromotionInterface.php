<?php
namespace App\Repositories;

interface PromotionInterface {
    public function getAllPaginate($perPage = 20, $filter = []);
    public function create($data);
    public function getByID($id);
    public function getByCode($code);
    public function updateByID($id, $data);
    public function destroyByIDs($id);
    public function getAll($filter = []);
}
