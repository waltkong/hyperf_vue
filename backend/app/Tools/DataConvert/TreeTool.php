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


    public function addTitleWithTreeNode($tree,$keyField='id',$labelField='name'){
        $result = [];
        foreach ($tree as $k => $v){
            $result[] = $v;
            if(isset($v['children'])){
                $children1 = $v['children'];
                foreach ($children1 as $k1 => $v1){
                    $v1[$labelField] = '|-- '.$v1[$labelField];
                    $result[] = $v1;
                    if(isset($v1['children'])){
                        $children2 = $v1['children'];
                        foreach ($children2 as $k2 => $v2){
                            $v2[$labelField] = '|---- '.$v2[$labelField];
                            $result[] = $v2;
                        }
                    }
                }
            }
        }
        return $result;
    }

    //支持两层吧
    public function convertTreeToFormOptions($tree,$keyField='id',$labelField='name'){
        $result = [];
        foreach ($tree as $k => $v){
            $result[] = [
                'key' => $v[$keyField],
                'label' => $v[$labelField],
            ];
            if(isset($v['children'])){
                $children1 = $v['children'];
                foreach ($children1 as $k1 => $v1){
                    $result[] = [
                        'key' => $v1[$keyField],
                        'label' => '|-- '.$v1[$labelField],
                    ];
                    if(isset($v1['children'])){
                        $children2 = $v1['children'];
                        foreach ($children2 as $k2 => $v2){
                            $result[] = [
                                'key' => $v2[$keyField],
                                'label' => '|---- '.$v2[$labelField],
                            ];
                        }
                    }
                }
            }

        }
        return $result;
    }


}