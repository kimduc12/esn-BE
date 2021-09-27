<?php
use App\Http\Controllers\Api\AdvicePostController;

Route::get('advice-posts/product-category/{product_category_id}', [
    AdvicePostController::class, 'getAdvicePostByProductCategoryID'
]);
