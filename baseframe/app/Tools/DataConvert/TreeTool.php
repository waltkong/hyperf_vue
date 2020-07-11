<?php
namespace App\Tools\DataConvert;

class TreeTool{

    /**
     * 递归生成树
     */
    public function getTree($data, $pidStart=0,$pidFieldName='parent_id',$primaryKey='id'){
        $tree = array();                                //每次都声明一个新数组用来放子元素
        foreach ($data as $v) {
            if ($v[$pidFieldName] == $pidStart) {                      //匹配子记录
                $v['children'] = $this->getTree($data, $v[$primaryKey]); //递归获取子记录
                if ($v['children'] == null) {
                    unset($v['children']);             //如果子元素为空则unset()
                }
                $tree[] = $v;                           //将记录存入新数组
            }
        }
        return $tree;                                  //返回新数组
    }


}