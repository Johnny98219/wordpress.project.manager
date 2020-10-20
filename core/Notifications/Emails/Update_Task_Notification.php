<?php 

namespace WeDevs\PM\Core\Notifications\Emails;

/**
* Email Notification When a new project created
*/
use WeDevs\PM\Core\Notifications\Email;

class Update_Task_Notification extends Email {
    
    function __construct() {

        add_action('pm_after_update_task_notification', array($this, 'trigger'), 10, 2 );
    }

    public function trigger( $task, $data ) {

        $task_raw = pm_get_tasks( [ 'id' => $task->id ] );
        $task_raw = $task_raw['data'];

        $task->load('assignees.assigned_user', 'projects.managers', 'updater');
        $users = array();
        
        foreach ($task->assignees->toArray() as $assignee ) {
            if( $this->is_enable_user_notification( $assignee['assigned_to'] ) ){
                if( $this->is_enable_user_notification_for_notification_type( $assignee['assigned_to'] , '_cpm_email_notification_update_task' ) ){
                    $users[] = $assignee['assigned_user']['user_email'];
                }
            }
        }

        if( $this->notify_manager() ){
            foreach ( $task->projects->managers->toArray() as $u ) {
                if( !in_array($u['user_email'], $users )){
                    $users[] = $u['user_email'];
                }
            }
        }

        if( !$users ){
            return ; 
        }

        $template_name = apply_filters( 'pm_new_task_email_template_path', $this->get_template_path( '/html/update-task.php' ) );
        $subject = sprintf( __( '[%s][%s] Update Task Assigned: %s', 'wedevs-project-manager' ), $this->get_blogname(), $task_raw['project_title'], $task_raw['title'] );


        $message = $this->get_content_html( $template_name, $task_raw );

        $this->send( 'joy.mishu@gmail.com', $subject, $message );

    }

}
