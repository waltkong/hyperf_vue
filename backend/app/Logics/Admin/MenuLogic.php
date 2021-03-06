<?php
namespace App\Logics\Admin;

use App\Constants\ErrorCode;
use App\Exception\AdminResponseException;
use App\Logics\Common\DatabaseLogic;
use App\Logics\Common\PageLogic;
use App\Model\System\MenuModel;
use App\Tools\DataConvert\TreeTool;

class MenuLogic{

    public function __construct()
    {
    }

    public function dataList(array $input)
    {
        $qsFunc = function () use($input){
            $obj = MenuModel::query();
            if(isset($input['name']) && strlen($input['name']) > 0){
                $obj = $obj->where('name','like',"%{$input['name']}%");
            }
            if(isset($input['need_auth']) && strlen($input['need_auth']) > 0 && in_array($input['need_auth'],MenuModel::NEED_AUTH)){
                $obj = $obj->where('need_auth','=',"{$input['need_auth']}");
            }
            if(isset($input['is_menu']) && strlen($input['is_menu']) > 0 && in_array($input['is_menu'],MenuModel::IS_MENU)){
                $obj = $obj->where('is_menu','=',"{$input['is_menu']}");
            }
            $obj = PageLogic::startAndEndTimeQuerySetFilter($obj,$input);
            return $obj;
        };

        $list = PageLogic::getPaginateListQuerySet($qsFunc(),$input)->get()->toArray();

        $treeTool = new TreeTool();
        $tree = $treeTool->getTree($list,0,'parent_id','id');

        $result = $treeTool->addTitleWithTreeNode($tree,'id','name');

        $ret = [
            'data' => $result,
            'count' =>  count($result),
            'total' =>  $qsFunc()->count()
        ];
        return PageLogic::commonListDataReturn($ret);
    }

    public function update($input)
    {
        $user = auth()->guard('jwt')->user();

        $findcheck = MenuModel::query()
            ->where('name',$input['name'])
            ->where('id','<>',$input['id'])
            ->exists();
        if($findcheck){
            throw new AdminResponseException(ErrorCode::ERROR,"该菜单名称已存在");
        }
        $findcheck = MenuModel::query()
            ->where('id','<>',$input['id'])
            ->where('url',$input['url'])
            ->exists();
        if($findcheck){
            throw new AdminResponseException(ErrorCode::ERROR,"该url已存在");
        }

        $data = DatabaseLogic::filterTableData(MenuModel::FIELDS,$input);
        MenuModel::query()->where('id',$input['id'])->update($data);
    }


    public function store($input)
    {
        $user = auth()->guard('jwt')->user();

        $findcheck = MenuModel::query()
            ->where('name',$input['name'])
            ->exists();
        if($findcheck){
            throw new AdminResponseException(ErrorCode::ERROR,"该菜单名称已存在");
        }
        $findcheck = MenuModel::query()
            ->where('url',$input['url'])
            ->exists();
        if($findcheck){
            throw new AdminResponseException(ErrorCode::ERROR,"该url已存在");
        }

        $data = DatabaseLogic::filterTableData(MenuModel::FIELDS,$input);
        unset($data['id']);
        MenuModel::query()->insert($data);
    }

    public function storeOrUpdate(array $input)
    {
        $id = $input['id'] ?? '';
        $parent_id = $input['parent_id'] ?? '';
        try{

            if(empty($parent_id)){
                $input['level'] = 1;
            }else{
                $parent_row = MenuModel::query()->where('id',$parent_id)->first();
                $input['level'] = (int)($parent_row->level) + 1 ;
            }

            if(empty($id)){
                $this->store($input);
            }else{
                $this->update($input);
            }
        }catch (\Exception $e){
            throw new AdminResponseException(ErrorCode::SYSTEM_INNER_ERROR,$e->getMessage());
        }
    }

    public function getOne($input)
    {

        $row = MenuModel::query()->where('id',$input['id'])->first();
        return [
            'data' => $row,
        ];
    }

    public function deleteOne($input)
    {
        try{

            DatabaseLogic::commonCheckThisCompany(MenuModel::query()->where('id',$input['id'])->first());

            $childrowCheck =  MenuModel::query()
                ->where('parent_id',$input['id'])
                ->exists();
            if($childrowCheck){
                throw new AdminResponseException(ErrorCode::ERROR,"存在子菜单，不能删除");
            }

            MenuModel::query()->where('id',$input['id'])->delete();
        }catch (\Exception $e){
            throw new AdminResponseException(ErrorCode::SYSTEM_INNER_ERROR,$e->getMessage());
        }
    }

    public function menuParentOptions($input)
    {
        $list = MenuModel::query()->where('level','<=',2)->get()->toArray();

        $treeTool = new TreeTool();
        $tree = $treeTool->getTree($list,0,'parent_id','id');

        $options = $treeTool->convertTreeToFormOptions($tree,'id','name');

        $options = array_merge([
            'key' => 0,
            'label' => '顶级',
        ],$options);

        return [
            'data' => $options,
        ];
    }





}