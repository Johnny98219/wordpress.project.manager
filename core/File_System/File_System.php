<?php

namespace CPM\Core\File_System;

require_once( ABSPATH . 'wp-admin/includes/file.php' );

Class File_System {

    public static function ajax_upload_file() {
        check_ajax_referer( 'cpm_ajax_upload', 'nonce' );
        
        $attachment_id  = self::upload( $_FILES );

        if ( intval( $attachment_id ) ) {
            $file = self::get_file( $attachment_id );

            wp_send_json_success([ 
                'file' => [ 
                    'name'  => esc_attr( $file['name'] ),
                    'id'    => $file['id'],
                    'url'   => $file['url'],
                    'thumb' => $file['thumb'],
                    'type'  => $file['type']
                ]
            ]);

        } else {
            wp_send_json_error( array( 'error' => 'Something wrong!' ) );
        }
    }

    /**
     * Upload a file and insert as attachment
     *
     * @param int $post_id
     *
     * @return int|bool
     */
    public static function upload( $files ) {
        if ( $files['cpm_attachment']['error'] > 0 ) {
            return false;
        }
        
        $upload = array(
            'name'     => $files['cpm_attachment']['name'],
            'type'     => $files['cpm_attachment']['type'],
            'tmp_name' => $files['cpm_attachment']['tmp_name'],
            'error'    => $files['cpm_attachment']['error'],
            'size'     => $files['cpm_attachment']['size']
        );

        if ( ! function_exists( 'wp_handle_upload' ) ) {
            require_once( ABSPATH . 'wp-admin/includes/file.php' );
        }

        if ( ! function_exists( 'wp_generate_attachment_metadata' ) ) {
            require_once( ABSPATH . 'wp-admin/includes/image.php' );
        }
        
        $uploaded_file = wp_handle_upload( $upload, array( 'test_form' => false ) );

        if ( isset( $uploaded_file['file'] ) ) {
            $file_loc  = $uploaded_file['file'];
            $file_name = basename( $files['cpm_attachment']['name'] );
            $file_type = wp_check_filetype( $file_name );

            $attachment = array(
                'post_mime_type' => $file_type['type'],
                'post_title'     => preg_replace( '/\.[^.]+$/', '', basename( $file_name ) ),
                'post_content'   => '',
                'post_status'    => 'inherit'
            );

            $attach_id   = wp_insert_attachment( $attachment, $file_loc );
            $attach_data = wp_generate_attachment_metadata( $attach_id, $file_loc );

            wp_update_attachment_metadata( $attach_id, $attach_data );
            
            return $attach_id;
        }

        return false;
    }

    /**
     * Get an attachment file
     *
     * @param int $attachment_id
     *
     * @return array
     */
    public static function get_file( $attachment_id ) {
        $file = get_post( $attachment_id );

        if ( $file ) {
            $response = array(
                'id'   => $attachment_id,
                'name' => get_the_title( $attachment_id ),
                'url'  => wp_get_attachment_url( $attachment_id ),
            );

            if ( wp_attachment_is_image( $attachment_id ) ) {

                $thumb             = wp_get_attachment_image_src( $attachment_id, 'thumbnail' );
                $response['thumb'] = $thumb[0];
                $response['type']  = 'image';
            } else {
                $response['thumb'] = wp_mime_type_icon( $file->post_mime_type );
                $response['type']  = 'file';
            }

            return $response;
        }

        return false;
    }

    /**
     * Delete attachment file
     *
     * @param  int  $file_id
     * @param  boolean $force
     *
     * @return void
     */
    public static function delete( $file_id, $force = true ) {
        wp_delete_attachment( $file_id, $force );
        do_action( 'cpm_delete_attachment', $file_id, $force );
    }

    /**
     * Update attachment file
     *
     * @param  int  $file_id
     * @param  array $attach_data
     *
     * @return void
     */
    public static function update( $attach_id, $attach_data ) {
        $args = array(
            'ID'           => $attach_id,
            'post_title'   => $attach_data['name'],
        );

        // Update the post into the database
        wp_update_post( $args );
    }

}

