<?php

namespace CPM\Discussion_Board\Controllers;

use WP_REST_Request;
use CPM\Discussion_Board\Models\Discussion_Board;
use League\Fractal;
use League\Fractal\Resource\Item as Item;
use League\Fractal\Resource\Collection as Collection;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;
use CPM\Transformer_Manager;
use CPM\Discussion_Board\Transformer\Discussion_Board_Transformer;
use CPM\Common\Models\Boardable;

class Discussion_Board_Controller {

    use Transformer_Manager;

    public function index( WP_REST_Request $request ) {
        $per_page = $request->get_param( 'per_page' );
        $per_page = $per_page ? $per_page : 15;

        $page = $request->get_param( 'page' );
        $page = $page ? $page : 1;

        $discussion_boards = Discussion_Board::orderBy( 'created_at', 'DESC' )
            ->paginate( $per_page, ['*'], 'page', $page );

        $discussion_board_collection = $discussion_boards->getCollection();

        $resource = new Collection( $discussion_board_collection, new Discussion_Board_Transformer );
        $resource->setPaginator( new IlluminatePaginatorAdapter( $discussion_boards ) );

        return $this->get_response( $resource );
    }

    public function show( WP_REST_Request $request ) {
        $project_id = $request->get_param( 'project_id' );
        $discussion_board_id = $request->get_param( 'discussion_board_id' );

        $discussion_board = Discussion_Board::where( 'id', $discussion_board_id )
            ->where( 'project_id', $project_id )
            ->first();

        $resource = new Item( $discussion_board, new Discussion_Board_Transformer );

        return $this->get_response( $resource );
    }

    public function store( WP_REST_Request $request ) {
        $data = [
            'title' => $request->get_param( 'title' ),
            'description' => $request->get_param( 'description' ),
            'order' => $request->get_param( 'order' ),
            'project_id' => $request->get_param( 'project_id' )
        ];
        $data = array_filter( $data );

        $discussion_board = Discussion_Board::create( $data );

        $resource = new Item( $discussion_board, new Discussion_Board_Transformer );

        return $this->get_response( $resource );
    }

    public function update( WP_REST_Request $request ) {
        $project_id = $request->get_param( 'project_id' );
        $discussion_board_id = $request->get_param( 'discussion_board_id' );

        $discussion_board = Discussion_Board::where( 'id', $discussion_board_id )
            ->where( 'project_id', $project_id )
            ->first();

        $data = [
            'title' => $request->get_param( 'title' ),
            'description' => $request->get_param( 'description' ),
            'order' => $request->get_param( 'order' ),
        ];
        $data = array_filter( $data );

        $discussion_board->update( $data );

        $resource = new Item( $discussion_board, new Discussion_Board_Transformer );

        return $this->get_response( $resource );
    }

    public function destroy( WP_REST_Request $request ) {
        $project_id = $request->get_param( 'project_id' );
        $discussion_board_id = $request->get_param( 'discussion_board_id' );

        $discussion_board = Discussion_Board::where( 'id', $discussion_board_id )
            ->where( 'project_id', $project_id )
            ->first();

        $comments = $discussion_board->comments;
        foreach ($comments as $comment) {
            $comment->replies()->delete();
            $comment->files()->delete();
        }
        $discussion_board->comments()->delete();
        $discussion_board->files()->delete();
        $discussion_board->users()->delete();

        $discussion_board->delete();
    }

    public function attach_users( WP_REST_Request $request ) {
        $project_id = $request->get_param( 'project_id' );
        $discussion_board_id = $request->get_param( 'discussion_board_id' );

        $discussion_board = Discussion_Board::where( 'id', $discussion_board_id )
            ->where( 'project_id', $project_id )
            ->first();

        $user_ids = explode( ',', $request->get_param( 'users' ) );

        if ( !empty( $user_ids ) ) {
            foreach ( $user_ids as $user_id ) {
                $data = [
                    'board_id' => $discussion_board->id,
                    'board_type' => 'discussion-board',
                    'boardable_id' => $user_id,
                    'boardable_type' => 'user'
                ];
                Boardable::firstOrCreate( $data );
            }
        }

        $resource = new Item( $discussion_board, new Discussion_Board_Transformer );

        return $this->get_response( $resource );
    }

    public function detach_users( WP_REST_Request $request ) {
        $project_id = $request->get_param( 'project_id' );
        $discussion_board_id = $request->get_param( 'discussion_board_id' );

        $discussion_board = Discussion_Board::where( 'id', $discussion_board_id )
            ->where( 'project_id', $project_id )
            ->first();

        $user_ids = explode( ',', $request->get_param( 'users' ) );

        $discussion_board->users()->whereIn( 'boardable_id', $user_ids )->delete();

        $resource = new Item( $discussion_board, new Discussion_Board_Transformer );

        return $this->get_response( $resource );
    }
}