<?php

use WeDevs\PM\Core\Router\Router;
use WeDevs\PM\Core\Permissions\Administrator;

$router = Router::singleton();

$router->get( 'projects/{project_id}/tasks', 'WeDevs/PM/Task/Controllers/Task_Controller@index' );
$router->post( 'projects/{project_id}/tasks', 'WeDevs/PM/Task/Controllers/Task_Controller@store' );
$router->get( 'projects/{project_id}/tasks/{task_id}', 'WeDevs/PM/Task/Controllers/Task_Controller@show' );
$router->put( 'projects/{project_id}/tasks/{task_id}', 'WeDevs/PM/Task/Controllers/Task_Controller@update' );
$router->delete( 'projects/{project_id}/tasks/{task_id}', 'WeDevs/PM/Task/Controllers/Task_Controller@destroy' );

$router->put( 'projects/{project_id}/tasks/{task_id}/attach-users', 'WeDevs/PM/Task/Controllers/Task_Controller@attach_users' );
$router->put( 'projects/{project_id}/tasks/{task_id}/detach-users', 'WeDevs/PM/Task/Controllers/Task_Controller@detach_users' );

$router->put( 'projects/{project_id}/tasks/{task_id}/boards', 'WeDevs/PM/Task/Controllers/Task_Controller@attach_to_board' );
$router->delete( 'projects/{project_id}/tasks/{task_id}/boards', 'WeDevs/PM/Task/Controllers/Task_Controller@detach_from_board' );

$router->put( 'projects/{project_id}/tasks/reorder', 'WeDevs/PM/Task/Controllers/Task_Controller@reorder' );