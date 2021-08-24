<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\StudentsRequest;
use App\Models\Schools;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class StudentsCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class StudentsCrudController extends CrudController
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
        CRUD::setModel(\App\Models\Students::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/students');
        CRUD::setEntityNameStrings('students', 'students');
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
            'name' => 'classid',
            'type' => 'dropdown',
            'label' => 'Class'
        ],function() {
            return \App\Models\Classes::all()->pluck('name', 'id')->toArray();
        }, function ($value) { //
            $this->crud->addClause('where', 'classid', $value);
        });

        $this->crud->setShowView('show_classes_in_school');
        CRUD::setFromDb(); // columns
        CRUD::removeColumn('classid');
        CRUD::addColumn([
            'label' => 'Class name', // Table column heading
            'type' => 'select',
            'name' => 'classid', // the column that contains the ID of that connected entity;
            'entity' => 'Classes', // the method that defines the relationship in your Model
            'attribute' => 'name', // foreign key attribute that is shown to user
            'model' => "App\Models\Classes"]);
    }

    /**
     * Define what happens when the Create operation is loaded.
     *
     * @see https://backpackforlaravel.com/docs/crud-operation-create
     * @return void
     */
    protected function setupCreateOperation()
    {
        CRUD::setValidation(StudentsRequest::class);
        CRUD::setFromDb(); // fields
        CRUD::removeField('age');
        CRUD::removeField('classid');
        CRUD::addField([
            'label' => "Class name",
            'type' => 'select',
            'name' => 'classid', // the db column for the foreign key

            // optional
            // 'entity' should point to the method that defines the relationship in your Model
            // defining entity will make Backpack guess 'model' and 'attribute'
            'entity' => 'Classes',

            // optional - manually specify the related model and attribute
            'model' => "App\Models\Classes", // related model
            'attribute' => 'name', // foreign key attribute that is shown to user
            // optional - force the related options to be a custom query, instead of all();
            'options' => (function ($query) {
                return $query->orderBy('name', 'ASC')->get();
            }), //  you can use this to filter the results show in the select
        ]);
        CRUD::addField([
            'name' => 'age',
            'type' => 'date_picker',
            'label' => 'Age',

            // optional:
            'date_picker_options' => [
                'todayBtn' => 'linked',
                'format' => 'dd-mm-yyyy',
                'language' => 'vi'
            ],]);
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

}
