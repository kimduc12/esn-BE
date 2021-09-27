<?php namespace App\Constants;

class RolePermissionConst
{
    const ROLE_SUPER_ADMIN  = 'Super Admin';
    const ROLE_ADMIN        = 'Admin';
    const ROLE_CUSTOMER     = 'Customer';
    const ROLE_COLLABORATOR = 'Collaborator';

    const MANAGEMENT_ROLES = [
        RolePermissionConst::ROLE_ADMIN,
        RolePermissionConst::ROLE_COLLABORATOR,
    ];

    const ROLES = [
        RolePermissionConst::ROLE_ADMIN,
        RolePermissionConst::ROLE_CUSTOMER,
        RolePermissionConst::ROLE_COLLABORATOR,
    ];

    /*
        Role Permissions
    */
    const ROLE_LIST   = 'role.list';
    const ROLE_ADD    = 'role.add';
    const ROLE_DETAIL = 'role.detail';
    const ROLE_EDIT   = 'role.edit';
    const ROLE_DELETE = 'role.delete';

    const ALL_ROLE_PERMISSIONS = [
        RolePermissionConst::ROLE_LIST,
        RolePermissionConst::ROLE_ADD,
        RolePermissionConst::ROLE_DETAIL,
        RolePermissionConst::ROLE_EDIT,
        RolePermissionConst::ROLE_DELETE
    ];

    /*
        Section Permissions
    */
    const SECTION_LIST   = 'section.list';
    const SECTION_ADD    = 'section.add';
    const SECTION_DETAIL = 'section.detail';
    const SECTION_EDIT   = 'section.edit';
    const SECTION_DELETE = 'section.delete';

    const ALL_SECTION_PERMISSIONS = [
        RolePermissionConst::SECTION_LIST,
        RolePermissionConst::SECTION_ADD,
        RolePermissionConst::SECTION_DETAIL,
        RolePermissionConst::SECTION_EDIT,
        RolePermissionConst::SECTION_DELETE
    ];

    /*
        User Permissions
    */
    const USER_LIST   = 'user.list';
    const USER_ADD    = 'user.add';
    const USER_DETAIL = 'user.detail';
    const USER_EDIT   = 'user.edit';
    const USER_DELETE = 'user.delete';

    const ALL_USER_PERMISSIONS = [
        RolePermissionConst::USER_LIST,
        RolePermissionConst::USER_ADD,
        RolePermissionConst::USER_DETAIL,
        RolePermissionConst::USER_EDIT,
        RolePermissionConst::USER_DELETE
    ];

    /*
        Customer Permissions
    */
    const CUSTOMER_LIST   = 'customer.list';
    const CUSTOMER_ADD    = 'customer.add';
    const CUSTOMER_DETAIL = 'customer.detail';
    const CUSTOMER_EDIT   = 'customer.edit';
    const CUSTOMER_DELETE = 'customer.delete';

    const ALL_CUSTOMER_PERMISSIONS = [
        RolePermissionConst::CUSTOMER_LIST,
        RolePermissionConst::CUSTOMER_ADD,
        RolePermissionConst::CUSTOMER_DETAIL,
        RolePermissionConst::CUSTOMER_EDIT,
        RolePermissionConst::CUSTOMER_DELETE
    ];

    /*
        Gift Permissions
    */
    const GIFT_LIST     = 'gift.list';
    const GIFT_ADD      = 'gift.add';
    const GIFT_DETAIL   = 'gift.detail';
    const GIFT_EDIT     = 'gift.edit';
    const GIFT_DELETE   = 'gift.delete';
    const GIFT_EXCHANGE = 'gift.exchange';

    const ALL_GIFT_PERMISSIONS = [
        RolePermissionConst::GIFT_LIST,
        RolePermissionConst::GIFT_ADD,
        RolePermissionConst::GIFT_DETAIL,
        RolePermissionConst::GIFT_EDIT,
        RolePermissionConst::GIFT_DELETE,
        RolePermissionConst::GIFT_EXCHANGE,
    ];

    /*
        Pages Permissions
    */
    const PAGE_LIST   = 'page.list';
    const PAGE_ADD    = 'page.add';
    const PAGE_DETAIL = 'page.detail';
    const PAGE_EDIT   = 'page.edit';
    const PAGE_DELETE = 'page.delete';

    const ALL_PAGE_PERMISSIONS = [
        RolePermissionConst::PAGE_LIST,
        RolePermissionConst::PAGE_ADD,
        RolePermissionConst::PAGE_DETAIL,
        RolePermissionConst::PAGE_EDIT,
        RolePermissionConst::PAGE_DELETE
    ];

    /*
        Blog Permissions
    */
    const BLOG_LIST   = 'blog.list';
    const BLOG_ADD    = 'blog.add';
    const BLOG_DETAIL = 'blog.detail';
    const BLOG_EDIT   = 'blog.edit';
    const BLOG_DELETE = 'blog.delete';

    const ALL_BLOG_PERMISSIONS = [
        RolePermissionConst::BLOG_LIST,
        RolePermissionConst::BLOG_ADD,
        RolePermissionConst::BLOG_DETAIL,
        RolePermissionConst::BLOG_EDIT,
        RolePermissionConst::BLOG_DELETE
    ];

    /*
        Blog category Permissions
    */
    const BLOG_CATEGORY_LIST   = 'blog.category.list';
    const BLOG_CATEGORY_ADD    = 'blog.category.add';
    const BLOG_CATEGORY_DETAIL = 'blog.category.detail';
    const BLOG_CATEGORY_EDIT   = 'blog.category.edit';
    const BLOG_CATEGORY_DELETE = 'blog.category.delete';

    const ALL_BLOG_CATEGORY_PERMISSIONS = [
        RolePermissionConst::BLOG_CATEGORY_LIST,
        RolePermissionConst::BLOG_CATEGORY_ADD,
        RolePermissionConst::BLOG_CATEGORY_DETAIL,
        RolePermissionConst::BLOG_CATEGORY_EDIT,
        RolePermissionConst::BLOG_CATEGORY_DELETE
    ];

    /*
        Order Permissions
    */
    const ORDER_LIST   = 'order.list';
    const ORDER_ADD    = 'order.add';
    const ORDER_DETAIL = 'order.detail';
    const ORDER_EDIT   = 'order.edit';
    const ORDER_DELETE = 'order.delete';

    const ALL_ORDER_PERMISSIONS = [
        RolePermissionConst::ORDER_LIST,
        RolePermissionConst::ORDER_ADD,
        RolePermissionConst::ORDER_DETAIL,
        RolePermissionConst::ORDER_EDIT,
        RolePermissionConst::ORDER_DELETE
    ];

    /*
        Attribute Permissions
    */
    const ATTRIBUTE_LIST   = 'attribute.list';
    const ATTRIBUTE_ADD    = 'attribute.add';
    const ATTRIBUTE_DETAIL = 'attribute.detail';
    const ATTRIBUTE_EDIT   = 'attribute.edit';
    const ATTRIBUTE_DELETE = 'attribute.delete';

    const ALL_ATTRIBUTE_PERMISSIONS = [
        RolePermissionConst::ATTRIBUTE_LIST,
        RolePermissionConst::ATTRIBUTE_ADD,
        RolePermissionConst::ATTRIBUTE_DETAIL,
        RolePermissionConst::ATTRIBUTE_EDIT,
        RolePermissionConst::ATTRIBUTE_DELETE
    ];

    /*
        Product Permissions
    */
    const PRODUCT_LIST   = 'product.list';
    const PRODUCT_ADD    = 'product.add';
    const PRODUCT_DETAIL = 'product.detail';
    const PRODUCT_EDIT   = 'product.edit';
    const PRODUCT_DELETE = 'product.delete';

    const ALL_PRODUCT_PERMISSIONS = [
        RolePermissionConst::PRODUCT_LIST,
        RolePermissionConst::PRODUCT_ADD,
        RolePermissionConst::PRODUCT_DETAIL,
        RolePermissionConst::PRODUCT_EDIT,
        RolePermissionConst::PRODUCT_DELETE
    ];

    /*
        Product category Permissions
    */
    const PRODUCT_CATEGORY_LIST   = 'product.category.list';
    const PRODUCT_CATEGORY_ADD    = 'product.category.add';
    const PRODUCT_CATEGORY_DETAIL = 'product.category.detail';
    const PRODUCT_CATEGORY_EDIT   = 'product.category.edit';
    const PRODUCT_CATEGORY_DELETE = 'product.category.delete';

    const ALL_PRODUCT_CATEGORY_PERMISSIONS = [
        RolePermissionConst::PRODUCT_CATEGORY_LIST,
        RolePermissionConst::PRODUCT_CATEGORY_ADD,
        RolePermissionConst::PRODUCT_CATEGORY_DETAIL,
        RolePermissionConst::PRODUCT_CATEGORY_EDIT,
        RolePermissionConst::PRODUCT_CATEGORY_DELETE
    ];

    /*
        Advice Post Permissions
    */
    const ADVICE_POST_LIST   = 'advice.list';
    const ADVICE_POST_ADD    = 'advice.add';
    const ADVICE_POST_DETAIL = 'advice.detail';
    const ADVICE_POST_EDIT   = 'advice.edit';
    const ADVICE_POST_DELETE = 'advice.delete';

    const ALL_ADVICE_POST_PERMISSIONS = [
        RolePermissionConst::ADVICE_POST_LIST,
        RolePermissionConst::ADVICE_POST_ADD,
        RolePermissionConst::ADVICE_POST_DETAIL,
        RolePermissionConst::ADVICE_POST_EDIT,
        RolePermissionConst::ADVICE_POST_DELETE
    ];

    /*
        Topic Permissions
    */
    const TOPIC_LIST   = 'topic.list';
    const TOPIC_ADD    = 'topic.add';
    const TOPIC_DETAIL = 'topic.detail';
    const TOPIC_EDIT   = 'topic.edit';
    const TOPIC_DELETE = 'topic.delete';

    const ALL_TOPIC_PERMISSIONS = [
        RolePermissionConst::TOPIC_LIST,
        RolePermissionConst::TOPIC_ADD,
        RolePermissionConst::TOPIC_DETAIL,
        RolePermissionConst::TOPIC_EDIT,
        RolePermissionConst::TOPIC_DELETE
    ];

    /*
        Age Permissions
    */
    const AGE_LIST   = 'age.list';
    const AGE_ADD    = 'age.add';
    const AGE_DETAIL = 'age.detail';
    const AGE_EDIT   = 'age.edit';
    const AGE_DELETE = 'age.delete';

    const ALL_AGE_PERMISSIONS = [
        RolePermissionConst::AGE_LIST,
        RolePermissionConst::AGE_ADD,
        RolePermissionConst::AGE_DETAIL,
        RolePermissionConst::AGE_EDIT,
        RolePermissionConst::AGE_DELETE
    ];

    /*
        Product type Permissions
    */
    const PRODUCT_TYPE_LIST   = 'product-type.list';
    const PRODUCT_TYPE_ADD    = 'product-type.add';
    const PRODUCT_TYPE_DETAIL = 'product-type.detail';
    const PRODUCT_TYPE_EDIT   = 'product-type.edit';
    const PRODUCT_TYPE_DELETE = 'product-type.delete';

    const ALL_PRODUCT_TYPE_PERMISSIONS = [
        RolePermissionConst::PRODUCT_TYPE_LIST,
        RolePermissionConst::PRODUCT_TYPE_ADD,
        RolePermissionConst::PRODUCT_TYPE_DETAIL,
        RolePermissionConst::PRODUCT_TYPE_EDIT,
        RolePermissionConst::PRODUCT_TYPE_DELETE
    ];

    /*
        Pattern Permissions
    */
    const PATTERN_LIST   = 'pattern.list';
    const PATTERN_ADD    = 'pattern.add';
    const PATTERN_DETAIL = 'pattern.detail';
    const PATTERN_EDIT   = 'pattern.edit';
    const PATTERN_DELETE = 'pattern.delete';

    const ALL_PATTERN_PERMISSIONS = [
        RolePermissionConst::PATTERN_LIST,
        RolePermissionConst::PATTERN_ADD,
        RolePermissionConst::PATTERN_DETAIL,
        RolePermissionConst::PATTERN_EDIT,
        RolePermissionConst::PATTERN_DELETE
    ];

     /*
        Material Permissions
    */
    const MATERIAL_LIST   = 'material.list';
    const MATERIAL_ADD    = 'material.add';
    const MATERIAL_DETAIL = 'material.detail';
    const MATERIAL_EDIT   = 'material.edit';
    const MATERIAL_DELETE = 'material.delete';

    const ALL_MATERIAL_PERMISSIONS = [
        RolePermissionConst::MATERIAL_LIST,
        RolePermissionConst::MATERIAL_ADD,
        RolePermissionConst::MATERIAL_DETAIL,
        RolePermissionConst::MATERIAL_EDIT,
        RolePermissionConst::MATERIAL_DELETE
    ];

    /*
        Supplier Permissions
    */
    const SUPPLIER_LIST   = 'supplier.list';
    const SUPPLIER_ADD    = 'supplier.add';
    const SUPPLIER_DETAIL = 'supplier.detail';
    const SUPPLIER_EDIT   = 'supplier.edit';
    const SUPPLIER_DELETE = 'supplier.delete';

    const ALL_SUPPLIER_PERMISSIONS = [
        RolePermissionConst::SUPPLIER_LIST,
        RolePermissionConst::SUPPLIER_ADD,
        RolePermissionConst::SUPPLIER_DETAIL,
        RolePermissionConst::SUPPLIER_EDIT,
        RolePermissionConst::SUPPLIER_DELETE
    ];

    /*
        Brand Permissions
    */
    const BRAND_LIST   = 'brand.list';
    const BRAND_ADD    = 'brand.add';
    const BRAND_DETAIL = 'brand.detail';
    const BRAND_EDIT   = 'brand.edit';
    const BRAND_DELETE = 'brand.delete';

    const ALL_BRAND_PERMISSIONS = [
        RolePermissionConst::BRAND_LIST,
        RolePermissionConst::BRAND_ADD,
        RolePermissionConst::BRAND_DETAIL,
        RolePermissionConst::BRAND_EDIT,
        RolePermissionConst::BRAND_DELETE
    ];

    /*
        Country Permissions
    */
    const COUNTRY_LIST   = 'country.list';
    const COUNTRY_ADD    = 'country.add';
    const COUNTRY_DETAIL = 'country.detail';
    const COUNTRY_EDIT   = 'country.edit';
    const COUNTRY_DELETE = 'country.delete';

    const ALL_COUNTRY_PERMISSIONS = [
        RolePermissionConst::COUNTRY_LIST,
        RolePermissionConst::COUNTRY_ADD,
        RolePermissionConst::COUNTRY_DETAIL,
        RolePermissionConst::COUNTRY_EDIT,
        RolePermissionConst::COUNTRY_DELETE
    ];

    /*
        Banner Permissions
    */
    const BANNER_LIST   = 'banner.list';
    const BANNER_ADD    = 'banner.add';
    const BANNER_DETAIL = 'banner.detail';
    const BANNER_EDIT   = 'banner.edit';
    const BANNER_DELETE = 'banner.delete';

    const ALL_BANNER_PERMISSIONS = [
        RolePermissionConst::BANNER_LIST,
        RolePermissionConst::BANNER_ADD,
        RolePermissionConst::BANNER_DETAIL,
        RolePermissionConst::BANNER_EDIT,
        RolePermissionConst::BANNER_DELETE
    ];

    /*
        Promotion Permissions
    */
    const PROMOTION_LIST   = 'promotion.list';
    const PROMOTION_ADD    = 'promotion.add';
    const PROMOTION_DETAIL = 'promotion.detail';
    const PROMOTION_EDIT   = 'promotion.edit';
    const PROMOTION_DELETE = 'promotion.delete';

    const ALL_PROMOTION_PERMISSIONS = [
        RolePermissionConst::PROMOTION_LIST,
        RolePermissionConst::PROMOTION_ADD,
        RolePermissionConst::PROMOTION_DETAIL,
        RolePermissionConst::PROMOTION_EDIT,
        RolePermissionConst::PROMOTION_DELETE
    ];

    /*
        Setting Permissions
    */
    const SETTING_EDIT   = 'settings.edit';

    const ALL_SETTING_PERMISSIONS = [
        RolePermissionConst::SETTING_EDIT
    ];

    public static function getPermissionByRole($role) {
        $collaborator_permissions = [];
        $client_permissions = [
            RolePermissionConst::GIFT_EXCHANGE
        ];
        $super_admin_permissions = array_unique(array_merge(
            $client_permissions,
            RolePermissionConst::ALL_ROLE_PERMISSIONS,
            RolePermissionConst::ALL_SECTION_PERMISSIONS,
            RolePermissionConst::ALL_USER_PERMISSIONS,
            RolePermissionConst::ALL_CUSTOMER_PERMISSIONS,
            RolePermissionConst::ALL_GIFT_PERMISSIONS,
            RolePermissionConst::ALL_PAGE_PERMISSIONS,
            RolePermissionConst::ALL_BLOG_PERMISSIONS,
            RolePermissionConst::ALL_BLOG_CATEGORY_PERMISSIONS,
            RolePermissionConst::ALL_ATTRIBUTE_PERMISSIONS,
            RolePermissionConst::ALL_PRODUCT_PERMISSIONS,
            RolePermissionConst::ALL_PRODUCT_CATEGORY_PERMISSIONS,
            RolePermissionConst::ALL_ADVICE_POST_PERMISSIONS,
            RolePermissionConst::ALL_AGE_PERMISSIONS,
            RolePermissionConst::ALL_TOPIC_PERMISSIONS,
            RolePermissionConst::ALL_PRODUCT_TYPE_PERMISSIONS,
            RolePermissionConst::ALL_PATTERN_PERMISSIONS,
            RolePermissionConst::ALL_MATERIAL_PERMISSIONS,
            RolePermissionConst::ALL_SUPPLIER_PERMISSIONS,
            RolePermissionConst::ALL_COUNTRY_PERMISSIONS,
            RolePermissionConst::ALL_BRAND_PERMISSIONS,
            RolePermissionConst::ALL_BANNER_PERMISSIONS,
            RolePermissionConst::ALL_ORDER_PERMISSIONS,
            RolePermissionConst::ALL_PROMOTION_PERMISSIONS
        ));
        switch($role) {
            case RolePermissionConst::ROLE_SUPER_ADMIN:
            case RolePermissionConst::ROLE_ADMIN:
                return $super_admin_permissions;
            case RolePermissionConst::ROLE_CUSTOMER:
                return $client_permissions;
            case RolePermissionConst::ROLE_COLLABORATOR:
                return $collaborator_permissions;
        }
    }
}
