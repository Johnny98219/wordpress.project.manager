<?php

namespace CPM\User\Controllers;

use WP_REST_Request;
use League\Fractal;
use League\Fractal\Resource\Item as Item;
use League\Fractal\Resource\Collection as Collection;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;
use CPM\Transformer_Manager;
use CPM\Common\Traits\Request_Filter;
use CPM\User\Models\User;
use CPM\User\Transformers\User_Transformer;

class User_Controller {
    use Transformer_Manager, Request_Filter;

    public function index( WP_REST_Request $request ) {
        $users = User::paginate();
        $user_collection = $users->getCollection();
        $resource = new Collection( $user_collection, new User_Transformer );

        $resource->setPaginator( new IlluminatePaginatorAdapter( $users ) );

        return $this->get_response( $resource );
    }

    public function show( WP_REST_Request $request ) {
        $id       = $request->get_param( 'id' );
        $user     = User::find( $id );
        $resource = new Item( $user, new User_Transformer );

        return $this->get_response( $resource );
    }

    public function store( WP_REST_Request $request ) {
        // Extraction of user data from inputs
        $user_data = [
            'user_login'           => $request->get_param( 'username' ),
            'user_email'           => $request->get_param( 'email' ),
            'user_pass'            => $request->get_param( 'password' ),
            'user_nicename'        => $request->get_param( 'nicename' ),
            'display_name'         => $request->get_param( 'display_name' ),
            'first_name'           => $request->get_param( 'first_name' ),
            'last_name'            => $request->get_param( 'last_name' ),
            'nickname'             => $request->get_param( 'nickname' ),
            'user_url'             => $request->get_param( 'user_url' ),
            'description'          => $request->get_param( 'description' ),
            'locale'               => $request->get_param( 'locale' ),
            'rich_editing'         => $request->get_param( 'rich_editing' ),
            'comment_shortcuts'    => $request->get_param( 'comment_shortcuts' ),
            'admin_color'          => $request->get_param( 'admin_color' ),
            'show_admin_bar_front' => $request->get_param( 'show_admin_bar_front' ),
            'user_registered'      => $request->get_param( 'user_registered' ),
            'use_ssl'              => $request->get_param( 'use_ssl' ),
        ];
        $user_data = array_filter( $user_data );

        // User password insertion
        if ( !array_key_exists( 'user_pass', $user_data ) ) {
            $user_data['user_pass'] = wp_generate_password(
                $length = 12,
                $include_standard_special_chars = false
            );
        }

        // User creation
        $user_id = wp_insert_user( $user_data );
        $user    = User::find( $user_id );

        // Transforming database model instance
        $resource = new Item( $user, new User_Transformer );

        return $this->get_response( $resource );
    }

    public function search( WP_REST_Request $request ) {
        $query_string = $request->get_param( 'q' );
        $users = User::where( 'user_login', 'LIKE', '%' . $query_string . '%' )
            ->orWhere( 'user_nicename', 'LIKE', '%' . $query_string . '%' )
            ->orWhere( 'user_email', 'LIKE', '%' . $query_string . '%' )
            ->orWhere( 'user_url', 'LIKE', '%' . $query_string . '%')
            ->paginate();

        $user_collection = $users->getCollection();
        $resource = new Collection( $user_collection, new User_Transformer );

        $resource->setPaginator( new IlluminatePaginatorAdapter( $users ) );

        return $this->get_response( $resource );
    }
}