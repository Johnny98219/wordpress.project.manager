<?php

namespace CPM\Role\Controllers;

use WP_REST_Request;
use League\Fractal;
use League\Fractal\Resource\Item as Item;
use League\Fractal\Resource\Collection as Collection;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;
use CPM\Transformer_Manager;
use CPM\Role\Models\Role;
use CPM\Role\Transformers\Role_Transformer;
use CPM\Common\Traits\Request_Filter;

class Role_Controller {
    use Transformer_Manager, Request_Filter;

    public function index( WP_REST_Request $request ) {
        $roles = Role::paginate();
        $role_collection = $roles->getCollection();
        $resource = new Collection( $role_collection, new Role_Transformer );

        $resource->setPaginator( new IlluminatePaginatorAdapter( $roles ) );

        return $this->get_response( $resource );
    }

    public function show( WP_REST_Request $request ) {
        return "show";
    }

    public function store( WP_REST_Request $request ) {
        return "store";
    }

    public function update( WP_REST_Request $request ) {
        return "update";
    }

    public function destroy( WP_REST_Request $request ) {
        return "delete";
    }
}