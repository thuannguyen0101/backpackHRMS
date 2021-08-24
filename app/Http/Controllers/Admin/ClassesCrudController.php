<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\ClassesRequest;
use App\Models\Classes;
use App\Models\Students;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class ClassesCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class ClassesCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;

    /**
     * Configure the CrudPanel object. Apply settings to all operations.
     *
     * @return void
     */
    public function setup()
    {
        CRUD::setModel(\App\Models\Classes::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/classes');
        CRUD::setEntityNameStrings('classes', 'classes');
    }

    /**
     * Define what happens when the List operation is loaded.
     *
     * @see  https://backpackforlaravel.com/docs/crud-operation-list-entries
     * @return void
     */
    protected function setupListOperation()
    {

        $this->crud->addFilter([
            'type' => 'text',
            'name' => 'name',
            'label' => 'Search by name Student'
        ],
            false,
            function ($value) {
                $this->crud->addClause('whereHas','students',function($query) use ($value){
                    $query->where('name','like', '%'.$value.'%');
                } );
            }
        );
        $this->crud->addFilter([
            'name' => 'schoolid',
            'type' => 'dropdown',
            'label' => 'School'
        ],function() {
            return \App\Models\Schools::all()->pluck('name', 'id')->toArray();
        }, function ($value) { //
            $this->crud->addClause('where', 'schoolid', $value);
        });


        CRUD::addColumn([
            'name' => 'name',
            'type'=>'text',
            'label' => 'Name',
            'wrapper'   => [
                'href' => function ($crud, $column, $entry) {
                    return backpack_url('students/?classid='.$entry->id);
                },

            ],
        ]);

        CRUD::enableDetailsRow();
        CRUD::addColumn([
            'name' => 'students', // name of relationship method in the model
            'type' => 'relationship_count',
            'label' => 'Total student', // Model which contain FK\
            'suffix' => '',
            'searchLogic' => function ($query, $column, $searchTerm) {
                $query->orWhereHas('students', function ($q) use ($column, $searchTerm) {
                    $q->where('name', 'like', '%'.$searchTerm.'%');
                });
            }
        ]);
        CRUD::removeColumn('schoolid');
        CRUD::addColumn([
            'label' => 'School name', // Table column heading
            'type' => 'select',
            'name' => 'schoolid', // the column that contains the ID of that connected entity;
            'entity' => 'Schools', // the method that defines the relationship in your Model
            'attribute' => 'name', // foreign key attribute that is shown to user
            'model' => "App\Models\Schools"]);
        /**
         * Columns can be defined using the fluent syntax or array syntax:
         * - CRUD::column('price')->type('number');
         * - CRUD::addColumn(['name' => 'price', 'type' => 'number']);
         */
    }

    /**
     * Define what happens when the Create operation is loaded.
     *
     * @see https://backpackforlaravel.com/docs/crud-operation-create
     * @return void
     */
    protected function setupCreateOperation()
    {
        CRUD::setValidation(ClassesRequest::class);

        CRUD::setFromDb(); // fields
        CRUD::removeField('schoolid');
        CRUD::addField([
            'label' => "School name",
            'type' => 'select',
            'name' => 'schoolid', // the db column for the foreign key

            // optional
            // 'entity' should point to the method that defines the relationship in your Model
            // defining entity will make Backpack guess 'model' and 'attribute'
            'entity' => 'Schools',

            // optional - manually specify the related model and attribute
            'model' => "App\Models\Schools", // related model
            'attribute' => 'name', // foreign key attribute that is shown to user
            // optional - force the related options to be a custom query, instead of all();
            'options' => (function ($query) {
                return $query->orderBy('name', 'ASC')->get();
            }), //  you can use this to filter the results show in the select
        ]);
    }

    /**
     * Define what happens when the Update operation is loaded.
     *
     * @see https://backpackforlaravel.com/docs/crud-operation-update
     * @return void
     */
    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
    }
    protected function showDetailsRow($id)
    {
        $data = Students::where('classid',$id)->get();

        $this->crud->allowAccess('details_row');
        return view('vendor/backpack/crud/details_classes',['data'=>$data]);
    }
}
