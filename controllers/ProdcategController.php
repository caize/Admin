<?php
/**
 * Created by PhpStorm.
 * User: Krisz
 * Date: 2016.12.14.
 * Time: 10:16
 */

namespace Modules\Admin\Controllers;


use Modules\BusinessLogic\ContentSettings\Input;
use Modules\BusinessLogic\ContentSettings\Prodcateg;
use Modules\BusinessLogic\Search\InputSearch;
use Modules\BusinessLogic\Search\ProdcategSearch;

class ProdcategController extends ControllerBase
{
    public function listAction(){
        $search = ProdcategSearch::createProdcategSearch();
        $search->lang = $this->lang;
        $prodcategs = $search->find();
        if($prodcategs){
            return $this->api(200,$prodcategs);
        }else{
            return $this->api(200,false);
        }
    }

    public function getAction($id = false){
        $search = ProdcategSearch::createProdcategSearch();
        $search->lang = $this->lang;
        $id = (int)$id!=0?(int)$id:false;
        if($id){
            $prodcateg = $search->create($id);
        }else{
            $prodcateg = false;
        }

        if($prodcateg){
            return $this->api(200,$prodcateg);
        }
        return $this->api(200,false);
    }

    public function getInputsAction(){
        $search = InputSearch::createInputSearch();
        $inputs = $search->find();
        return $this->api(200,$inputs);
    }

    public function saveAction(){

        $form = $this->request->getJsonRawBody();
        $inputIds = [];

        foreach ($form->inputs as $input){
            $inputSearch = InputSearch::createInputSearch();
            /** @var Input $cInput */
            $cInput = $inputSearch->create();
            $cInput->name = $input->name;
            $cInput->url = $this->urlMakeup($input->name);
            $cInput->type = $input->type;
            foreach ($input->children as &$child){
                $child->url = $this->urlMakeup($child->name);
            }
            $cInput->children = $input->children;
            $cInput->length = $input->length;
            $inputIds[] = $cInput->id;
            $cInput->save();
        }

        $categSearch = ProdcategSearch::createProdcategSearch();
        /** @var Prodcateg $prodcateg */
        $prodcateg = $categSearch->create($form->data->id);
        $prodcateg->name = $form->data->name;
        $prodcateg->url = $this->urlMakeup($form->data->name);
        $prodcateg->inputs = array_merge($form->data->inputs,$inputIds);
        $prodcateg->save();
        return $this->api(200,[$prodcateg->id,$inputIds]);
    }

    public function deleteAction(){
        $id = $this->request->getJsonRawBody();
        $search = ProdcategSearch::createProdcategSearch();
        /** @var Prodcateg $prodcateg */
        $prodcateg = $search->create($id);
        $prodcateg->delete();

        return $this->api(200,"törölve");
    }

    public function uploadAction(){}
    public function editAction(){}
    public function indexAction(){}
}