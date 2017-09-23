<?php

namespace CPM\Comment\Transformers;

use CPM\Comment\Models\Comment;
use League\Fractal\TransformerAbstract;
use CPM\File\Transformer\File_Transformer;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;

class Comment_Transformer extends TransformerAbstract {
    /**
     * List of resources to automatically include
     *
     * @var array
     */
    protected $availableIncludes = [
        'replies'
    ];

    protected $defaultIncludes = [
        'files'
    ];

    public function transform( Comment $item ) {
        return [
            'id'         => (int) $item->id,
            'content'    => $item->content,
            'created_by' => $item->created_by,
            'meta' => [
                'total_replies' => $item->replies->count(),
            ]
        ];
    }

    /**
     * Include replies to a comment
     *
     * @param Comment $item
     * @return \League\Fractal\Resource\Collection
     */
    public function includeReplies( Comment $item ) {
        $page = isset( $_GET['reply_page'] ) ? $_GET['reply_page'] : 1;

        $replies = $item->replies()->paginate( 10, ['*'], 'reply_page', $page );

        $reply_collection = $replies->getCollection();
        $resource = $this->collection( $reply_collection, new Comment_Transformer );

        $resource->setPaginator( new IlluminatePaginatorAdapter( $replies ) );

        return $resource;
    }

    /**
     * Include files to a comment
     *
     * @param Comment $item
     * @return \League\Fractal\Resource\Collection
     */
    public function includeFiles( Comment $item ) {
        $page = isset( $_GET['file_page'] ) ? $_GET['file_page'] : 1;

        $files = $item->files()->paginate( 10, ['*'], 'file_page', $page );

        $file_collection = $files->getCollection();
        $resource = $this->collection( $file_collection, new File_Transformer );

        $resource->setPaginator( new IlluminatePaginatorAdapter( $files ) );

        return $resource;
    }
}