<?php
namespace App\Repositories;

interface UserInterface {
    public function getByEmail($email);
    public function getByFacebookID($facebook_id);
    public function getByGoogleID($google_id);
    public function getByZaloID($zalo_id);

    public function getUserByPhone($phone);

    public function createByEmail($data);

    public function getListPaginate($perPage = 20,$filter);
    public function getCustomerAllPaginate($perPage = 20, $filter);

    public function create($data);

    public function getUserByID($id);
    public function getCustomerByID($id);

    public function updateUserByID($id, $data);

    public function destroyUsersByIDs($ids);
    public function destroyCustomersByIDs($ids);

    public function getBySimilarPhone($phone);

    public function getBySimilarEmail($email);

    public function getByRoleIDs($role_ids);

    // Favourite products
    public function getMyFavouriteProductsPaginate($perPage = 20, $filter);
    public function getAllMyFavouriteProducts();

    public function getMyReadProductsPaginate($perPage = 20, $filter);
    public function getAllMyReadProducts();

}
