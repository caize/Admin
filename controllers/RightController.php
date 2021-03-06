<?php
/**
 * Created by PhpStorm.
 * User: Krisz
 * Date: 2016.11.24.
 * Time: 15:33
 */

namespace Modules\Admin\Controllers;
use Modules\BusinessLogic\ContentSettings;
use Modules\BusinessLogic\Search\RightSearch;

class RightController extends ControllerBase
{

    public function getAction($id = false){
        $search = RightSearch::createRightSearch();
        
        $search->lang = $this->lang;
        
        $id = (int)$id!=0?(int)$id:false;
        if($id){
            $right = $search->create($id);
        }else{
            $right = false;
        }

        if($right){
            return $this->api(200,$right);
        }
        return $this->api(200,false);
    }

    
    public function listAction($type = false){
        $search = RightSearch::createRightSearch();

        $search->lang = $this->lang;
        
        if($type){
            $search->type = $type;
            $search->setCacheType($type);
        }
        $search->cacheByLogin($this->urlMakeup($this->authUser->username));
        $rights = $search->find();
        return $this->api(200,$rights);
    }

    public function saveAction(){

        $search = RightSearch::createRightSearch();

        $search->lang = $this->lang;
        
        $form = $this->request->getJsonRawBody();
        /**@var \Modules\BusinessLogic\ContentSettings\Right $right*/
        $right = $form->id?$search->create($form->id):$search->create();
        $right->name = $form->name;
        $right->parent = $form->parent;
        $right->code = $form->code?$form->code:$this->urlMakeup($form->name);
        $right->type = $form->type;
        $right->actions = $form->actions;

        $right->save();
        return $this->api(200,$right);

    }

    public function getSubAction($parent){
        $search = RightSearch::createRightSearch();

        $search->lang = $this->lang;
        
        $search->type = "subRight";
        $search->parent = $parent;
        $search->setCacheType($parent.'_subRight');
        $search->cacheByLogin($this->urlMakeup($this->authUser->username));
        $rights = $search->find();

        return $this->api(200,$rights);
    }

    public function deleteAction(){
        $id = $this->request->getJsonRawBody();
        $search = RightSearch::createRightSearch();
        /**@var \Modules\BusinessLogic\ContentSettings\Right $right*/
        $right = $search->create($id);
        $right->delete();

        return $this->api(200,"törölve");

    }
    
    public function indexAction(){}

    public function editAction(){}
}