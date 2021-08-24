<?php


namespace App\Http\Controllers\Admin;

use App\Models\Schools;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use App\Models\Classes;
use function Couchbase\defaultDecoder;

class SystemProjectController extends CrudController
{
    public  function index($id){
        $classes = Classes::where('schoolid',$id)->get();
        return view('custom.detail_project',[
            'data'=>$classes
        ]);
    }

}
