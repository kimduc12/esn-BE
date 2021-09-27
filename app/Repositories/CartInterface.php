<?php
namespace App\Repositories;

interface CartInterface {
    public function getAllPaginate($perPage = 20, $filter = []);
    public function create($data);
    public function getByID($id);
    public function getByUserID($user_id);
    public function updateByID($id,$data);
    public function destroyByIDs($id);
    public function destroyByID($id);
}
