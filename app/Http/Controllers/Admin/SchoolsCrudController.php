<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\SchoolsRequest;
use App\Models\Classes;
use App\Models\Schools;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;



use Gate;
use function Couchbase\defaultDecoder;

/**
 * Class SchoolsCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class SchoolsCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\BulkDeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\BulkCloneOperation;


    /**
     * Configure the CrudPanel object. Apply settings to all operations.
     *
     * @return void
     */
    public function setup()
    {
        CRUD::setModel(\App\Models\Schools::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/schools');
        CRUD::setEntityNameStrings('schools', 'schools');
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
            'label' => 'Search by name'
        ],
            false,
            function ($value) {
                $this->crud->addClause('where', 'name', 'LIKE', "%$value%");
            }
        );
        $this->crud->enableExportButtons();

        $this->crud->addFilter([
            'type' => 'text',
            'name' => 'relationship_count',
            'label' => 'total class',
        ],
            false,
            function ($value) {
                $this->crud->addClause('has', 'classes', '=', "$value");
            }
        );
        $this->crud->addFilter([
            'name' => 'status',
            'type' => 'dropdown',
            'label' => 'Status'
        ], [
            1 => 'ACTIVE',
            2 => 'In ACTIVE',
        ]
            , function ($value) { //
            $this->crud->addClause('where', 'status', $value);
        });

        $this->crud->allowAccess('status');
        $this->crud->addButtonFromView('line', 'status', 'moderate', 'beginning');
        CRUD::enableDetailsRow();
        CRUD::addColumn([
            'name' => 'name',
            'type' => 'text',
            'label' => 'Name',
            'wrapper' => [
                'href' => function ($crud, $column, $entry) {
                    return backpack_url('schools/' . $entry->id . '/classes');
                },

            ],
        ]);
        CRUD::addColumn([
            'name' => 'classes', // name of relationship method in the model
            'type' => 'relationship_count',
            'label' => 'Total class', // Model which contain FK\
            'suffix' => '',
            'searchLogic' => function ($query, $column, $searchTerm) {
                $query->orWhereHas('classes', function ($q) use ($column, $searchTerm) {
                    $q->where('name', 'like', '%' . $searchTerm . '%');
                });
            },
        ]);
    }

    /**
     * Define what happens when the Create operation is loaded.
     *
     * @see https://backpackforlaravel.com/docs/crud-operation-create
     * @return void
     */
    protected function setupCreateOperation()
    {
        if (Gate::denies('update')) {
            abort(403);
        }


        CRUD::setValidation(SchoolsRequest::class);
        CRUD::setFromDb(); // fields
        CRUD::removeField('status');
        CRUD::addField([   // select_and_order
            'name' => 'status',
            'label' => "Status",
            'type' => 'select_from_array',
            'options' => [1 => 'ACTIVE', 2 => 'INACTIVE'],
            'allows_null' => false,
            'default' => 'one',
        ]);

        /**
         * Fields can be defined using the fluent syntax or array syntax:
         * - CRUD::field('price')->type('number');
         * - CRUD::addField(['name' => 'price', 'type' => 'number']));
         */
    }

    /**
     * Define what happens when the Update operation is loaded.
     *
     * @see https://backpackforlaravel.com/docs/crud-operation-update
     * @return void
     */
    protected function setupUpdateOperation()
    {
        if (Gate::denies('update')) {
            abort(403);
        }

        $this->setupCreateOperation();
    }

    protected function showDetailsRow($id)
    {
        $data = Classes::where('schoolid', $id)->get();
        $this->crud->allowAccess('details_row');
        return view('vendor/backpack/crud/details_row', ['data' => $data]);
    }

    public function update_status($id)
    {
        $school = Schools::find($id);

        if ($school->status == 1) {
            $school->status = 2;
            $school->save();
        } else {
            $school->status = 1;
            $school->save();
        }
        return $school;
    }

    public function showClass($id)
    {
        $data = Classes::where('schoolid', $id);
        if ($data == null) {
            return view("errors/404");
        };
        return view('vendor/backpack/crud/show_classes_in_school', ['data' => $data]);
    }

    public function getSchoolList()
    {
        $data = Classes::all();
        return $data;
    }

}
