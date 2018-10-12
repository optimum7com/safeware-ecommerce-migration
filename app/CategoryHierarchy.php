<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CategoryHierarchy extends Model{
    protected $table = 'item_category_hierarchy';

    public static function  ReverseCategory_find($category_id){
        $db = $this->table($this->table)->where('parent_item_category_uid','=',$category_id);//s
        dd($db);
        return $db;
    }

}
