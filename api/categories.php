<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include '../DB.class.php';
$db = new DB;
try {
    // Get users from database
    $sql = "SELECT p.id, p.category, s.thumbnail, p.priority, p.display_type, p.height, s.id as subid, s.category as subcategory, s.description FROM categories as p LEFT JOIN categories as s ON p.id = s.parent_id WHERE p.parent_id = 0 AND p.status = 0 ORDER BY p.priority";
    $categories = $db->customQuery($sql);
    /* print_r($categories);
    exit; */
    $catl = array();
    $catlist = array();
    //$subcatlist = array();
    $videolist = array();
    $allinfo = array();
    foreach ($categories as $cat) {

        if (!in_array($cat['category'], $catl)) {
            $catlist[$cat['id']] = array('parent_id' => $cat['id'], 'parent_category' => $cat['category'], 'display_type' => $cat['display_type'], 'height' => $cat['height'], 'sub_category' => array());
            array_push($catl, $cat['category']);
        }
        if (!empty($cat['subcategory'])) {


            $subcat['pid'] = $cat['id'];
            $subcat['subid'] = $cat['subid'];
            $subcat['subcategory'] = $cat['subcategory'];
            $subcat['thumbnail'] = $cat['thumbnail'];
            $subcat['description'] = !empty($cat['description']) ? $cat['description'] : "";
            array_push($catlist[$cat['id']]['sub_category'], $subcat);
        }
    }
    foreach ($catlist as $c) {
        array_push($allinfo, $c);
    }

    $path = $db->getBasePath() . "/";
} catch (Exception $e) {
    $categories = '';
}

if (!empty($categories) && count($categories) > 0) {
    echo json_encode(array("status" => 200, "msg" => "Categories found.", "category_list" => $allinfo, 'cat_repeat_detail_page' => '2', 'cat_repeat_detail_page_catname' => 'Explore More', 'path' => $path));
} else {
    echo json_encode(array("status" => 201, "msg" => "No category added yet."));
}
