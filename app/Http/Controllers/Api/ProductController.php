<?php
namespace App\Http\Controllers\Api;

use App\Constants\ProductConst;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

use App\Services\ProductService;
use App\Services\SupplierService;
use App\Services\BrandService;
use App\Services\CountryService;
use App\Services\ProductCategoryService;
use App\Services\AgeService;
use App\Services\MaterialService;
use App\Services\PatternService;
use App\Services\ProductTypeService;
use App\Services\TopicService;

class ProductController extends RestfulController
{
    protected $productService;
    protected $supplierService;
    protected $brandService;
    protected $countryService;
    protected $productCategoryService;
    protected $ageService;
    protected $topicService;
    protected $productTypeService;
    protected $patternService;
    protected $materialService;

    public function __construct(
        ProductService $productService,
        SupplierService $supplierService,
        BrandService $brandService,
        CountryService $countryService,
        ProductCategoryService $productCategoryService,
        AgeService $ageService,
        TopicService $topicService,
        ProductTypeService $productTypeService,
        PatternService $patternService,
        MaterialService $materialService
    ){
        parent::__construct();
        $this->productService = $productService;
        $this->supplierService = $supplierService;
        $this->brandService = $brandService;
        $this->countryService = $countryService;
        $this->productCategoryService = $productCategoryService;
        $this->ageService = $ageService;
        $this->topicService = $topicService;
        $this->productTypeService = $productTypeService;
        $this->patternService = $patternService;
        $this->materialService = $materialService;
    }

    /**
     * Get all products with paginate
     * @group Product management
     * @authenticated
     * @queryParam per_page integer Field to limit item per page. Defaults to 20.
     * @queryParam page integer Field to change current page. Defaults to 1.
     * @queryParam keyword string Field to filter. Defaults to null.
     * @queryParam supplier_id integer Field to filter. Defaults to null.
     * @queryParam brand_id integer Field to filter. Defaults to null.
     * @queryParam status integer Field to filter. Defaults to null.
     * @queryParam category_ids string Field to filter. Defaults to null.
     * @response {
        *   "status": true,
        *   "pagination": {
                *   "current_page": 1,
                *   "last_page": 1,
                *   "per_page": 3,
                *   "total": 0
            * },
        *   "products": [
                *   {
                    *   "id": 1,
                    *   "sku": "HBN-1",
                    *   "name": "product 1",
                    *   "slug": "product-1",
                    *   "image_url": "upload/images/product.png",
                    *   "image_mobile_url": "upload/images/product-mobile.png",
                    *   "summary": "Lorem sipum",
                    *   "content": "Lorem sipum",
                    *   "quote": "Lorem sipum",
                    *   "price": 10000,
                    *   "price_original": 0,
                    *   "sku": "123456",
                    *   "barcode": "123456",
                    *   "stock_address": "100 Hai Bà Trưng",
                    *   "quantity": 5000,
                    *   "star_rating": 5,
                    *   "title": "Lorem sipum",
                    *   "keyword": "Lorem sipum",
                    *   "description": "Lorem sipum",
                    *   "is_show": 0,
                    *   "is_popular": 0,
                    *   "can_promotion": 0,
                    *   "status": 1,
                    *   "total_orders": 0,
                    *   "has_options": 0,
                    *   "published_at": "2010-12-30 05:00:00",
                    *   "created_at": "2010-12-30 05:00:00",
                    *   "updated_at": "2010-12-30 05:00:00",
                    *   "categories": [
                            *   {
                                *   "id": 1,
                                *   "name": "Bé trai",
                                *   "slug": "be-trai",
                                *   "image_url": "upload/betrai.png",
                                *   "image_mobile_url": null,
                                *   "title": "Bé trai",
                                *   "keyword": "Bé trai",
                                *   "description": "Bé trai",
                                *   "parent_id": 0,
                                *   "sort": 0,
                                *   "status": 1,
                                *   "created_at": "2021-01-29T10:35:57.000000Z",
                                *   "updated_at": "2021-01-29T10:35:57.000000Z"
                            *   }
                        *   ],
                    *   "supplier": {
                            *   "id": 1,
                            *   "name": "Phong Vũ",
                            *   "sort": 0,
                            *   "status": 1,
                            *   "created_at": "2021-01-31T07:11:56.000000Z",
                            *   "updated_at": "2021-01-31T07:11:56.000000Z"
                        *   }
                *   }
            * ]
        * }
     * @response status=200 scenario="Something wrong" {
        *  "status": false,
        *  "message": "Something wrong"
        * }
     */
    public function index(Request $request){
        try{
            $perPage = $request->input("per_page", 20) ?: 20;
            $keyword      = $request->input('keyword', '');
            $brand_id     = $request->input('brand_id', '');
            $supplier_id  = $request->input('supplier_id', '');
            $status       = $request->input('status', '');
            $category_ids = $request->input('category_ids', '');
            $filter = [
                'keyword'      => $keyword,
                'status'       => $status,
                'category_ids' => $category_ids,
                'brand_id'     => $brand_id,
                'supplier_id'  => $supplier_id,
            ];
            $products = $this->productService->getAllPaginate($perPage, $filter);
            $products->appends($request->except(['page', '_token']));
            $paginator = $this->getPaginator($products);
            $pagingArr = $products->toArray();
            return $this->_response([
                'pagination' => $paginator,
                'products' => $pagingArr['data']
            ]);
        }catch(\Exception $e){
            return $this->_error($e, self::HTTP_INTERNAL_ERROR);
        }
    }

    /**
     * Create a product
     * @group Product management
     * @authenticated
     * @bodyParam name string required Example: product 1
     * @bodyParam image_url string Example: /upload/images/product.png
     * @bodyParam image_mobile_url string Example: /upload/images/product-mobile.png
     * @bodyParam summary string Example: Lorem sipum
     * @bodyParam content string Example: Lorem sipum
     * @bodyParam quote string Example: Lorem sipum
     * @bodyParam price integer Example: 10000
     * @bodyParam price_original integer Example: 0
     * @bodyParam sku string Example: 123456
     * @bodyParam barcode string Example: 123456
     * @bodyParam stock_address string Example: 100 Hai Bà Trưng
     * @bodyParam quantity integer Example: 0
     * @bodyParam star_rating integer required Example: 0
     * @bodyParam title string Example: Lorem sipum
     * @bodyParam keyword string Example: Lorem sipum
     * @bodyParam description string Example: Lorem sipum
     * @bodyParam is_show bool required Example: 0
     * @bodyParam is_popular bool required Example: 0
     * @bodyParam can_promotion bool required Example: 0
     * @bodyParam status integer required Example: 0
     * @bodyParam published_at datetime required Example: 2010-10-20 20:00:00
     * @bodyParam supplier_id integer Example: 0
     * @bodyParam brand_id integer Example: 0
     * @bodyParam country_id integer Example: 0
     * @bodyParam category_ids integer[] required Example: [1,2,3,4]
     * @bodyParam age_ids integer[] Example: [1,2,3,4]
     * @bodyParam topic_ids integer[] Example: [1,2,3,4]
     * @bodyParam product_type_ids integer[] Example: [1,2,3,4]
     * @bodyParam pattern_ids integer[] Example: [1,2,3,4]
     * @bodyParam material_ids integer[] Example: [1,2,3,4]
     * @bodyParam file_ids integer[] Example: [1,2,3,4]
     * @bodyParam has_options boolean required Example: 0
     * @bodyParam options object[]
     * @bodyParam options[].sku string
     * @bodyParam options[].barcode string
     * @bodyParam options[].price integer
     * @bodyParam options[].price_original integer
     * @bodyParam options[].quantity integer
     * @bodyParam options[].stock_address string
     * @bodyParam options[].image_url string
     * @bodyParam options[].image_mobile_url string
     * @bodyParam options[].size integer
     * @response {
        *   "status": true,
        *   "message": "Created"
        * }
     * @response status=200 scenario="Duplicate name by unique slug" {
        *  "status": false,
        *  "message": "Tiêu đề đã được sử dụng"
        * }
     * @response status=200 scenario="Something wrong" {
        *  "status": false,
        *  "message": "Something wrong"
        * }
     */
    public function store(Request $request){
        $this->validate($request, [
            'name'                       => 'bail|required',
            'image_url'                  => 'bail|nullable',
            'image_mobile_url'           => 'bail|nullable',
            'summary'                    => 'bail|nullable',
            'content'                    => 'bail|nullable',
            'quote'                      => 'bail|nullable',
            'price'                      => 'bail|required_if:options,null|integer',
            'price_original'             => 'bail|required_if:options,null|integer',
            'sku'                        => 'bail|required_if:options,null',
            'barcode'                    => 'bail|required_if:options,null',
            'stock_address'              => 'bail|nullable',
            'quantity'                   => 'bail|required_if:options,null|integer',
            'star_rating'                => 'bail|required|integer',
            'title'                      => 'bail|nullable',
            'keyword'                    => 'bail|nullable',
            'description'                => 'bail|nullable',
            'is_show'                    => 'bail|required|boolean',
            'is_popular'                 => 'bail|required|boolean',
            'can_promotion'              => 'bail|required|boolean',
            'status'                     => 'bail|required|in:'.implode(',', ProductConst::STATUS_VALIDATE),
            'published_at'               => 'bail|required|date_format:Y-m-d H:i:s',
            'supplier_id'                => 'bail|nullable|integer|exists:suppliers,id',
            'brand_id'                   => 'bail|nullable|integer|exists:brands,id',
            'country_id'                 => 'bail|nullable|integer|exists:countries,id',
            'category_ids'               => 'bail|required|array|min:1',
            'category_ids.*'             => 'bail|required|exists:product_categories,id',
            'age_ids'                    => 'bail|nullable|array|min:1',
            'age_ids.*'                  => 'bail|nullable|exists:ages,id',
            'topic_ids'                  => 'bail|nullable|array|min:1',
            'topic_ids.*'                => 'bail|nullable|exists:topics,id',
            'product_type_ids'           => 'bail|nullable|array|min:1',
            'product_type_ids.*'         => 'bail|nullable|exists:product_types,id',
            'pattern_ids'                => 'bail|nullable|array|min:1',
            'pattern_ids.*'              => 'bail|nullable|exists:patterns,id',
            'material_ids'               => 'bail|nullable|array|min:1',
            'material_ids.*'             => 'bail|nullable|exists:materials,id',
            'file_ids'                   => 'bail|nullable|array|min:1',
            'file_ids.*'                 => 'bail|nullable|exists:files,id',
            'has_options'                => 'bail|required|boolean',
            'options'                    => 'bail|nullable|array|min:1',
            'options.*.sku'              => 'bail|nullable',
            'options.*.barcode'          => 'bail|nullable',
            'options.*.price'            => 'bail|nullable|integer',
            'options.*.price_original'   => 'bail|nullable|integer',
            'options.*.quantity'         => 'bail|nullable|integer',
            'options.*.stock_address'    => 'bail|nullable',
            'options.*.image_url'        => 'bail|nullable',
            'options.*.image_mobile_url' => 'bail|nullable',
        ]);
        try{
            $input = $request->all();
            \Log::info("API ".\Route::current()->uri, [$input]);
            $result = $this->productService->create($input);
            if($result['status']==false){
                return $this->_error($result['message']);
            }
            return $this->_response($result['data'], $result['message']);
        }catch(\Exception $e){
            return $this->_error($e, self::HTTP_INTERNAL_ERROR);
        }
    }

    /**
     * Get a product by id
     * @group Product management
     * @authenticated
     * @urlParam id integer required
     * @response {
        *   "status": true,
        *   "data": {
                *   "id": 1,
                *   "name": "product 1",
                *   "slug": "product-1",
                *   "image_url": "upload/images/product.png",
                *   "image_mobile_url": "upload/images/product-mobile.png",
                *   "summary": "Lorem sipum",
                *   "content": "Lorem sipum",
                *   "quote": "Lorem sipum",
                *   "price": 10000,
                *   "price_original": 5000,
                *   "sku": "123456",
                *   "barcode": "123456",
                *   "stock_address": "100 Hai Bà Trưng",
                *   "quantity": 5000,
                *   "star_rating": 5,
                *   "title": "Lorem sipum",
                *   "keyword": "Lorem sipum",
                *   "description": "Lorem sipum",
                *   "is_popular": 1,
                *   "can_promotion": 1,
                *   "is_show": 1,
                *   "status": 1,
                *   "total_orders": 0,
                *   "has_options": 0,
                *   "created_at": "2010-12-30 05:00:00",
                *   "updated_at": "2010-12-30 05:00:00",
                *   "published_at": "2010-12-30 05:00:00",
                *   "categories": [
                        *   {
                            *   "id": 1,
                            *   "name": "Bé trai",
                            *   "slug": "be-trai",
                            *   "image_url": "/upload/images/be-trai.png",
                            *   "image_mobile_url": null,
                            *   "title": "Bé trai",
                            *   "keyword": "Bé trai",
                            *   "description": "Bé trai",
                            *   "parent_id": 0,
                            *   "sort": 0,
                            *   "status": 1
                        *   }
                    *   ],
                *   "supplier": {
                            *   "id": 1,
                            *   "name": "Phong Vũ",
                            *   "sort": 0,
                            *   "status": 1
                        *   },
                *   "brand": {
                            *   "id": 1,
                            *   "name": "Samsung",
                            *   "sort": 0,
                            *   "status": 1
                        *   },
                *   "ages": [{
                            *   "id": 1,
                            *   "name": "Sơ sinh",
                            *   "sort": 0,
                            *   "status": 1
                        *   }],
                *   "topics": [{
                            *   "id": 1,
                            *   "name": "Mùa xuân",
                            *   "sort": 0,
                            *   "status": 1
                        *   }],
                *   "product_types": [{
                            *   "id": 1,
                            *   "name": "Giày",
                            *   "sort": 0,
                            *   "status": 1
                        *   }],
                *   "patterns": [{
                            *   "id": 1,
                            *   "name": "Gấu nhồi bông",
                            *   "sort": 0,
                            *   "status": 1
                        *   }],
                *   "materials": [{
                            *   "id": 1,
                            *   "name": "Cotton",
                            *   "sort": 0,
                            *   "status": 1
                        *   }],
                *   "product_options": [{
                            *   "id": 1,
                            *   "sku": "123456789",
                            *   "barcode": "123456789",
                            *   "price": 10000,
                            *   "price_original": 10000,
                            *   "quantity": 100,
                            *   "stock_address": "100 Lê Văn Sỹ",
                            *   "image_url": "upload/images/option.png",
                            *   "image_mobile_url": "upload/images/option-mb.png",
                            *   "attribute_1": 100,
                            *   "attribute_2": 100,
                            *   "attribute_3": 100
                        *   }]
            *   }
        * }
     * @response status=200 scenario="Not found" {
        *  "status": false,
        *  "message": "Not found"
        * }
     */
    public function show($id){
        try{
            $id = (int)$id;
            $result = $this->productService->getByID($id);
            if(!$result){
                return $this->_error(trans('messages.not_found'));
            }
            return $this->_response($result);

        }catch(\Exception $e){
            return $this->_error($e, self::HTTP_INTERNAL_ERROR);
        }
    }

    /**
     * Update a product
     * @group Product management
     * @authenticated
     * @urlParam id integer required
     * @bodyParam name string required Example: product 1
     * @bodyParam image_url string Example: /upload/images/product.png
     * @bodyParam image_mobile_url string Example: /upload/images/product-mobile.png
     * @bodyParam summary string Example: Lorem sipum
     * @bodyParam content string Example: Lorem sipum
     * @bodyParam quote string required Example: Lorem sipum
     * @bodyParam price integer required Example: 10000
     * @bodyParam price_original integer required Example: 0
     * @bodyParam sku string required Example: 123456
     * @bodyParam barcode string required Example: 123456
     * @bodyParam stock_address string Example: 100 Hai Bà Trưng
     * @bodyParam quantity integer required Example: 0
     * @bodyParam star_rating integer required Example: 0
     * @bodyParam title string Example: Lorem sipum
     * @bodyParam keyword string Example: Lorem sipum
     * @bodyParam description string Example: Lorem sipum
     * @bodyParam is_show bool required Example: 0
     * @bodyParam is_popular bool required Example: 0
     * @bodyParam can_promotion bool required Example: 0
     * @bodyParam status integer required Example: 0
     * @bodyParam published_at datetime required Example: 2010-10-20 20:00:00
     * @bodyParam supplier_id integer Example: 0
     * @bodyParam brand_id integer Example: 0
     * @bodyParam country_id integer Example: 0
     * @bodyParam category_ids integer[] required Example: [1,2,3,4]
     * @bodyParam age_ids integer[] Example: [1,2,3,4]
     * @bodyParam topic_ids integer[] Example: [1,2,3,4]
     * @bodyParam product_type_ids integer[] Example: [1,2,3,4]
     * @bodyParam pattern_ids integer[] Example: [1,2,3,4]
     * @bodyParam material_ids integer[] Example: [1,2,3,4]
     * @bodyParam file_ids integer[] Example: [1,2,3,4]
     * @bodyParam has_options boolean required Example: 0
     * @response {
        *   "status": true,
        *   "message": "Updated"
        * }
     * @response status=200 scenario="Duplicate name by unique slug" {
        *  "status": false,
        *  "message": "Tiêu đề đã được sử dụng"
        * }
     * @response status=200 scenario="Something wrong" {
        *  "status": false,
        *  "message": "Something wrong"
        * }
     */
    public function update(Request $request, $id){
        $this->validate($request, [
            'name'                              => 'bail|required',
            'image_url'                         => 'bail|nullable',
            'image_mobile_url'                  => 'bail|nullable',
            'summary'                           => 'bail|nullable',
            'content'                           => 'bail|nullable',
            'quote'                             => 'bail|nullable',
            'price'                             => 'bail|required_if:has_options,false|integer',
            'price_original'                    => 'bail|required_if:has_options,false|integer',
            'sku'                               => 'bail|required_if:has_options,false',
            'barcode'                           => 'bail|required_if:has_options,false',
            'stock_address'                     => 'bail|nullable',
            'quantity'                          => 'bail|required_if:has_options,false|integer',
            'star_rating'                       => 'bail|required|integer',
            'title'                             => 'bail|nullable',
            'keyword'                           => 'bail|nullable',
            'description'                       => 'bail|nullable',
            'is_show'                           => 'bail|required|boolean',
            'is_popular'                        => 'bail|required|boolean',
            'can_promotion'                     => 'bail|required|boolean',
            'status'                            => 'bail|required|in:'.implode(',', ProductConst::STATUS_VALIDATE),
            'published_at'                      => 'bail|required|date_format:Y-m-d H:i:s',
            'supplier_id'                       => 'bail|nullable|integer|exists:suppliers,id',
            'brand_id'                          => 'bail|nullable|integer|exists:brands,id',
            'country_id'                        => 'bail|nullable|integer|exists:countries,id',
            'category_ids'                      => 'bail|required|array|min:1',
            'category_ids.*'                    => 'bail|required|exists:product_categories,id',
            'age_ids'                           => 'bail|nullable|array|min:1',
            'age_ids.*'                         => 'bail|nullable|exists:ages,id',
            'topic_ids'                         => 'bail|nullable|array|min:1',
            'topic_ids.*'                       => 'bail|nullable|exists:topics,id',
            'product_type_ids'                  => 'bail|nullable|array|min:1',
            'product_type_ids.*'                => 'bail|nullable|exists:product_types,id',
            'pattern_ids'                       => 'bail|nullable|array|min:1',
            'pattern_ids.*'                     => 'bail|nullable|exists:patterns,id',
            'material_ids'                      => 'bail|nullable|array|min:1',
            'material_ids.*'                    => 'bail|nullable|exists:materials,id',
            'file_ids'                          => 'bail|nullable|array|min:1',
            'file_ids.*'                        => 'bail|nullable|exists:files,id',
            'has_options'                       => 'bail|required|boolean'
        ]);
        try{
            $id = (int)$id;
            $input = $request->all();
            \Log::info("API ".\Route::current()->uri, [$input]);
            $result = $this->productService->updateByID($id,$input);
            if($result['status']==false){
                return $this->_error($result['message']);
            }
            return $this->_success($result['message']);
        }catch(\Exception $e){
            return $this->_error($e, self::HTTP_INTERNAL_ERROR);
        }
    }

    /**
     * Delete a product
     * @group Product management
     * @authenticated
     * @bodyParam ids integer[] required
     * @response {
        *   "status": true,
        *   "message": "Deleted"
        * }
     * @response status=200 scenario="Something wrong" {
        *  "status": false,
        *  "message": "Something wrong"
        * }
     */

    public function destroy(Request $request){
        $this->validate($request, [
            'ids' => 'required|array|min:1',
        ]);
        try{
            $arrID= $request->input('ids');
            $result = $this->productService->destroyByIDs($arrID);
            if($result['status']==false){
                return $this->_error($result['message']);
            }
            return $this->_success($result['message']);

        }catch(\Exception $e){
            return $this->_error($e, self::HTTP_INTERNAL_ERROR);
        }
    }

    /**
     * Get products with paginate (only active)
     * @group Product management
     * @queryParam per_page integer Field to limit item per page. Defaults to 20.
     * @queryParam page integer Field to change current page. Defaults to 1.
     * @queryParam keyword string Field to filter. Defaults to null.
     * @queryParam star_rating integer Field to filter. Defaults to 0.
     * @queryParam star_rating_gte integer Field to filter. Defaults to 0.
     * @queryParam star_rating_lte integer Field to filter. Defaults to 0.
     * @queryParam category_id integer Field to filter. Defaults to 0.
     * @queryParam supplier_id integer Field to filter. Defaults to 0.
     * @queryParam brand_id integer Field to filter. Defaults to 0.
     * @queryParam country_id integer Field to filter. Defaults to 0.
     * @queryParam from_price integer Field to filter. Defaults to 0.
     * @queryParam to_price integer Field to filter. Defaults to 0.
     * @queryParam age_id integer Field to filter. Defaults to 0.
     * @queryParam topic_id integer Field to filter. Defaults to 0.
     * @queryParam product_type_id integer Field to filter. Defaults to 0.
     * @queryParam pattern_id integer Field to filter. Defaults to 0.
     * @queryParam material_id integer Field to filter. Defaults to 0.
     * @queryParam sort_by string Field to filter (popular|newest|selling|price_asc|price_desc). Defaults to 0.
     * @queryParam attribute_color string Field to filter (blue|red|white|black). Defaults to null.
     * @queryParam attribute_size string Field to filter (XL|M|S|SL). Defaults to null.
     * @queryParam from_date date Field to filter Ex:2010-10-20 . Defaults to null
     * @queryParam to_date date Field to filter Ex:2022-10-20. Defaults to null
     * @response {
        *   "status": true,
        *   "pagination": {
                *   "current_page": 1,
                *   "last_page": 1,
                *   "per_page": 3,
                *   "total": 0
            * },
        *   "products": [
                *   {
                    *   "id": 1,
                    *   "sku": "HBN-1",
                    *   "name": "product 1",
                    *   "slug": "product-1",
                    *   "image_url": "upload/images/product.png",
                    *   "image_mobile_url": "upload/images/product-mobile.png",
                    *   "summary": "Lorem sipum",
                    *   "content": "Lorem sipum",
                    *   "quote": "Lorem sipum",
                    *   "price": 10000,
                    *   "price_original": 0,
                    *   "star_rating": 5,
                    *   "title": "Lorem sipum",
                    *   "keyword": "Lorem sipum",
                    *   "description": "Lorem sipum",
                    *   "is_popular": 0,
                    *   "can_promotion": 0,
                    *   "is_show": 0,
                    *   "status": 1,
                    *   "created_at": "2010-12-30 05:00:00",
                    *   "updated_at": "2010-12-30 05:00:00",
                    *   "published_at": "2010-12-30 05:00:00"
                *   }
            * ]
        * }
     * @response status=200 scenario="Something wrong" {
        *  "status": false,
        *  "message": "Something wrong"
        * }
     */
    public function getActiveListPaginate(Request $request){
        $this->validate($request, [
            'from_date' => 'bail|nullable|date_format:Y-m-d',
            'to_date'   => 'bail|nullable|date_format:Y-m-d',
        ]);
        try{
            $perPage     = $request->input("per_page", 20) ?: 20;
            $filter      = $request->all();
            $products = $this->productService->getActiveListPaginate($perPage, $filter);
            $products->appends($request->except(['page', '_token']));
            $paginator = $this->getPaginator($products);
            $pagingArr = $products->toArray();
            return $this->_response([
                'pagination' => $paginator,
                'products' => $pagingArr['data']
            ]);
        }catch(\Exception $e){
            return $this->_error($e, self::HTTP_INTERNAL_ERROR);
        }
    }

    /**
     * Get products (only active)
     * @group Product management
     * @queryParam keyword string Field to filter. Defaults to null.
     * @queryParam category_id integer Field to filter. Defaults to 0
     * @queryParam supplier_id integer Field to filter. Defaults to 0
     * @queryParam brand_id integer Field to filter. Defaults to 0
     * @queryParam country_id integer Field to filter. Defaults to 0
     * @queryParam product_type_id integer Field to filter. Defaults to 0
     * @queryParam from_date date Field to filter Ex:2010-10-20 . Defaults to null
     * @queryParam to_date date Field to filter Ex:2022-10-20. Defaults to null
     * @response {
        *   "status": true,
        *   "data": [
                *   {
                    *   "id": 1,
                    *   "sku": "HBN-1",
                    *   "name": "product 1",
                    *   "slug": "product-1",
                    *   "image_url": "upload/images/product.png",
                    *   "image_mobile_url": "upload/images/product-mobile.png",
                    *   "summary": "Lorem sipum",
                    *   "content": "Lorem sipum",
                    *   "quote": "Lorem sipum",
                    *   "price": 10000,
                    *   "price_original": 0,
                    *   "star_rating": 5,
                    *   "title": "Lorem sipum",
                    *   "keyword": "Lorem sipum",
                    *   "description": "Lorem sipum",
                    *   "is_popular": 0,
                    *   "is_show": 1,
                    *   "can_promotion": 0,
                    *   "status": 1,
                    *   "created_at": "2010-12-30 05:00:00",
                    *   "updated_at": "2010-12-30 05:00:00"
                    *   "published_at": "2010-12-30 05:00:00"
                *   }
            * ]
        * }
     * @response status=200 scenario="Something wrong" {
        *  "status": false,
        *  "message": "Something wrong"
        * }
     */
    public function getAllActive(Request $request){
        $this->validate($request, [
            'from_date' => 'bail|nullable|date_format:Y-m-d',
            'to_date'   => 'bail|nullable|date_format:Y-m-d',
        ]);
        try{
            $keyword         = $request->input('keyword', '');
            $category_id     = $request->input('category_id', 0);
            $supplier_id     = $request->input('supplier_id', 0);
            $brand_id        = $request->input('brand_id', 0);
            $country_id      = $request->input('country_id', 0);
            $product_type_id = $request->input('product_type_id', 0);
            $from_date       = $request->input('from_date', '');
            $to_date         = $request->input('to_date', '');
            $filter = [
                'keyword'         => $keyword,
                'category_id'     => $category_id,
                'supplier_id'     => $supplier_id,
                'brand_id'        => $brand_id,
                'country_id'      => $country_id,
                'product_type_id' => $product_type_id,
                'from_date'       => $from_date,
                'to_date'         => $to_date,
            ];
            $products = $this->productService->getAllActive($filter);
            return $this->_response($products);
        }catch(\Exception $e){
            return $this->_error($e, self::HTTP_INTERNAL_ERROR);
        }
    }

    /**
     * Get product detail by slug (only active)
     * @group Product management
     * @urlParam slug string required
     * @response {
        *   "status": true,
        *   "data": {
                *   "id": 1,
                *   "name": "product 1",
                *   "slug": "product-1",
                *   "image_url": "upload/images/product.png",
                *   "image_mobile_url": "upload/images/product-mobile.png",
                *   "summary": "Lorem sipum",
                *   "content": "Lorem sipum",
                *   "quote": "Lorem sipum",
                *   "price": 10000,
                *   "price_original": 5000,
                *   "sku": "123456",
                *   "barcode": "123456",
                *   "stock_address": "100 Hai Bà Trưng",
                *   "quantity": 5000,
                *   "star_rating": 5,
                *   "title": "Lorem sipum",
                *   "keyword": "Lorem sipum",
                *   "description": "Lorem sipum",
                *   "is_popular": 1,
                *   "can_promotion": 1,
                *   "is_show": 1,
                *   "status": 1,
                *   "created_at": "2010-12-30 05:00:00",
                *   "updated_at": "2010-12-30 05:00:00",
                *   "published_at": "2010-12-30 05:00:00",
                *   "files": [{
                            *   "id": 1,
                            *   "file_path": "/storage/product/img.png"
                        *   }],
                *   "supplier": {
                            *   "id": 1,
                            *   "name": "Phong Vũ",
                            *   "sort": 0,
                            *   "status": 1
                        *   },
                *   "country": {
                            *   "id": 1,
                            *   "name": "Viet Nam",
                            *   "sort": 0,
                            *   "status": 1
                        *   },
                *   "brand": {
                            *   "id": 1,
                            *   "name": "Samsung",
                            *   "sort": 0,
                            *   "status": 1
                        *   },
                *   "product_options": [{
                            *   "id": 1,
                            *   "sku": "123456789",
                            *   "barcode": "123456789",
                            *   "price": 10000,
                            *   "price_original": 10000,
                            *   "quantity": 100,
                            *   "stock_address": "100 Lê Văn Sỹ",
                            *   "image_url": "upload/images/option.png",
                            *   "image_mobile_url": "upload/images/option-mb.png",
                            *   "attribute_1": 100,
                            *   "attribute_2": 100,
                            *   "attribute_3": 100
                        *   }]
            *   }
        * }
     * @response status=200 scenario="Not found" {
        *  "status": false,
        *  "message": "Not found"
        * }
     */
    public function getBySlug(Request $request, $slug){
        try{
            $result = $this->productService->getBySlug($slug);
            if($result['status']==false){
                return $this->_error($result['message']);
            }
            return $this->_response($result['data']);
        }catch(\Exception $e){
            return $this->_error($e, self::HTTP_INTERNAL_ERROR);
        }
    }

    /**
     * Create a product option
     * @group Product management
     * @authenticated
     * @urlParam id integer required
     * @bodyParam sku string
     * @bodyParam barcode string
     * @bodyParam price integer
     * @bodyParam price_original integer
     * @bodyParam quantity integer
     * @bodyParam stock_address string
     * @bodyParam image_url string
     * @bodyParam image_mobile_url string
     * @bodyParam size integer
     * @response {
        *   "status": true,
        *   "message": "Created",
        *   "data": {
                *   "id": 1,
                *   "sku": "123456789",
                *   "barcode": "123456789",
                *   "price": 10000,
                *   "price_original": 10000,
                *   "quantity": 100,
                *   "stock_address": "100 Lê Văn Sỹ",
                *   "image_url": "upload/images/option.png",
                *   "image_mobile_url": "upload/images/option-mb.png",
                *   "attribute_1": 100,
                *   "attribute_2": 100,
                *   "attribute_3": 100
        *   }
        * }
     * @response status=200 scenario="Duplicate name by unique slug" {
        *  "status": false,
        *  "message": "Tiêu đề đã được sử dụng"
        * }
     * @response status=200 scenario="Something wrong" {
        *  "status": false,
        *  "message": "Something wrong"
        * }
     */
    public function storeOption(Request $request, $id){
        $this->validate($request, [
            'sku'              => 'bail|nullable',
            'barcode'          => 'bail|nullable',
            'price'            => 'bail|nullable|integer',
            'price_original'   => 'bail|nullable|integer',
            'quantity'         => 'bail|nullable|integer',
            'stock_address'    => 'bail|nullable',
            'image_url'        => 'bail|nullable',
            'image_mobile_url' => 'bail|nullable',
        ]);
        try{
            $input = $request->all();

            $result = $this->productService->createOption($id, $input);
            if($result['status']==false){
                return $this->_error($result['message']);
            }
            return $this->_response($result['data'], trans('messages.created_success'));
        }catch(\Exception $e){
            return $this->_error($e, self::HTTP_INTERNAL_ERROR);
        }
    }

    /**
     * Update a product option
     * @group Product management
     * @authenticated
     * @urlParam id integer required
     * @urlParam option_id integer required
     * @bodyParam sku string
     * @bodyParam barcode string
     * @bodyParam price integer
     * @bodyParam price_original integer
     * @bodyParam quantity integer
     * @bodyParam stock_address string
     * @bodyParam image_url string
     * @bodyParam image_mobile_url string
     * @bodyParam size integer
     * @response {
        *   "status": true,
        *   "message": "Updated"
        * }
     * @response status=200 scenario="Something wrong" {
        *  "status": false,
        *  "message": "Something wrong"
        * }
     */
    public function updateOption(Request $request, $id, $option_id){
        $this->validate($request, [
            'sku'              => 'bail|nullable',
            'barcode'          => 'bail|nullable',
            'price'            => 'bail|nullable|integer',
            'price_original'   => 'bail|nullable|integer',
            'quantity'         => 'bail|nullable|integer',
            'stock_address'    => 'bail|nullable',
            'image_url'        => 'bail|nullable',
            'image_mobile_url' => 'bail|nullable',
        ]);
        try{
            $id = (int)$id;
            $option_id = (int)$option_id;
            $input = $request->all();
            $result = $this->productService->updateOptionByID($id, $option_id, $input);
            if($result['status']==false){
                return $this->_error($result['message']);
            }
            return $this->_success($result['message']);
        }catch(\Exception $e){
            return $this->_error($e, self::HTTP_INTERNAL_ERROR);
        }
    }

    /**
     * Delete product options
     * @group Product management
     * @authenticated
     * @urlParam id integer required
     * @bodyParam option_ids integer[] required
     * @response {
        *   "status": true,
        *   "message": "Deleted"
        * }
     * @response status=200 scenario="Something wrong" {
        *  "status": false,
        *  "message": "Something wrong"
        * }
     */

    public function destroyOptions(Request $request, $id){
        $this->validate($request, [
            'option_ids'   => 'bail|required|array|min:1',
            'option_ids.*' => 'bail|required|exists:product_options,id'
        ]);
        try{
            $option_ids= $request->input('option_ids');
            $result = $this->productService->destroyOptionByIDs($id, $option_ids);
            if($result['status']==false){
                return $this->_error($result['message']);
            }
            return $this->_success($result['message']);

        }catch(\Exception $e){
            return $this->_error($e, self::HTTP_INTERNAL_ERROR);
        }
    }

    /**
     * Get all relations (only active)
     * @group Product management
     * @response {
        *   "status": true,
        *   "data": {
                *   "suppliers": [],
                *   "brands": [],
                *   "countries": [],
                *   "productCategories": [],
                *   "ages": [],
                *   "topics": [],
                *   "types": [],
                *   "patterns": [],
                *   "materials": []
            *   }
        * }
     * @response status=200 scenario="Something wrong" {
        *  "status": false,
        *  "message": "Something wrong"
        * }
     */
    public function getAllRelations(Request $request){
        try{
            $suppliers = $this->supplierService->getAll();
            $brands = $this->brandService->getAll();
            $countries = $this->countryService->getAllActive();
            $productCategories = $this->productCategoryService->getAll([
                'parent_id' => 0
            ]);
            $ages = $this->ageService->getAll();
            $topics = $this->topicService->getAll();
            $types = $this->productTypeService->getAll();
            $patterns = $this->patternService->getAll();
            $materials = $this->materialService->getAll();
            return $this->_response([
                'suppliers'         => $suppliers,
                'brands'            => $brands,
                'countries'         => $countries,
                'productCategories' => $productCategories,
                'ages'              => $ages,
                'topics'            => $topics,
                'types'             => $types,
                'patterns'          => $patterns,
                'materials'         => $materials,
            ]);
        }catch(\Exception $e){
            return $this->_error($e, self::HTTP_INTERNAL_ERROR);
        }
    }

    /**
     * Update product files
     * @group Product management
     * @authenticated
     * @urlParam id integer required
     * @bodyParam file_ids integer[] required Example: [1,2,3,4]
     * @response {
        *   "status": true,
        *   "message": "Updated"
        * }
     * @response status=200 scenario="Something wrong" {
        *  "status": false,
        *  "message": "Something wrong"
        * }
     */
    public function updateFiles(Request $request, $id){
        $this->validate($request, [
            'file_ids'   => 'bail|required|array|min:1',
            'file_ids.*' => 'bail|nullable|exists:files,id'
        ]);
        try{
            $id = (int)$id;
            $input = $request->all();
            $result = $this->productService->updateFiles($id, $input);
            if($result['status']==false){
                return $this->_error($result['message']);
            }
            return $this->_success($result['message']);
        }catch(\Exception $e){
            return $this->_error($e, self::HTTP_INTERNAL_ERROR);
        }
    }
}
