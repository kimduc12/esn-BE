<?php
use App\Models\Permission;
/**
     * Get all permissions
     * @group Permission management
     * @authenticated
     * @response {
        *   "status": true,
        *   "data": [
                    *   {
                        *   "id": 1,
                        *   "name": "role.list",
                        *   "guard_name": "web",
                        *   "created_at": "2021-01-24T12:18:27.000000Z",
                        *   "updated_at": "2021-01-24T12:18:27.000000Z"
                    *   }
                *   ]
        * }
     */
Route::get('permissions', function(){
    return [
        "status" => true,
        "data" => Permission::get()
    ];
});
