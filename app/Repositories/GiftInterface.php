<?php
namespace App\Repositories;

interface GiftInterface {
    public function getAllPaginate($perPage = 20, $filter = []);
    public function getTotalPrice($filter = []);
    public function create($data);
    public function getByID($id);
    public function updateByID($id,$data);
    public function destroyByIDs($id);
    public function getActiveListPaginate($perPage = 20, $filter = []);
}
