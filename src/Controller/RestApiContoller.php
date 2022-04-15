<?php


namespace TodoListApp\Controller;

use WP_REST_Server;

class RestApiContoller
{

  private $namespace = "todoList_ejt/v2";
  private $meta = 'todos_EJT';

  function __construct()
  {
    add_action('rest_api_init', [$this, 'rest_routes']);
  }


  /**
   * Check permission for user to use rest on front
   * 
   * @param WP_REST_Request $request
   * 
   * @return Bool/WP_Error
   */
  public function check_data_permissions(\WP_REST_Request $request)
  {
    if (
      is_user_logged_in() &&
      wp_verify_nonce($request->get_header('X-WP-Nonce'), 'wp_rest')
    )
      return true;


    $this->returnRestError();
  }

  /**
   * Check permission for user to use rest on back
   * 
   * @param WP_REST_Request $request
   * 
   * @return Bool/WP_Error
   */
  public function  check_admin_permissions(\WP_REST_Request $request)
  {
    if (
      current_user_can('administrator') &&
      wp_verify_nonce($request->get_header('X-WP-Nonce'), 'wp_rest')
    )
      return true;


    $this->returnRestError();
  }

  /**
   * Routes for rest
   */
  function rest_routes()
  {
    register_rest_route(
      $this->namespace,
      '/tasks',
      array(
        array(
          'methods' => WP_REST_Server::READABLE,
          'callback' => [$this, 'get_list'],
          'permission_callback' => [$this, 'check_data_permissions'],

        ),
        array(
          'methods' => WP_REST_Server::EDITABLE,
          'callback' => [$this, 'updateTasks'],
          'permission_callback' => [$this, 'check_data_permissions'],
        )
      )
    );

    register_rest_route(
      $this->namespace,
      '/tasks/(?P<id>\d+)',
      array(
        array(
          'methods' => WP_REST_Server::READABLE,
          'callback' => [$this, 'get_list'],
          'permission_callback' => [$this, 'check_admin_permissions'],
          'args' => [
            'id'
          ]
        ),
        array(
          'methods' => WP_REST_Server::EDITABLE,
          'callback' => [$this, 'updateTasks'],
          'permission_callback' => [$this, 'check_admin_permissions'],
        )
      )

    );

    register_rest_route(
      $this->namespace,
      '/users',
      array(
        'methods' => WP_REST_Server::READABLE,
        'callback' => [$this, 'get_user_list'],
        'permission_callback' => [$this, 'check_admin_permissions'],
      )
    );
  }

  /**
   * Get all users in DB
   * 
   * @return WP_REST_Response
   */
  function get_user_list()
  {
    $users_query = get_users();
    $users = [];
    foreach ($users_query as $user) {
      $users[] = [
        "ID" => $user->ID,
        "display_name" => $user->display_name
      ];
    }

    return $this->returnSucess($users);
  }

  /**
   * Get user id from request or set current user id
   * 
   * @param $request
   * 
   * @return Int $id
   */
  function get_user_ID($request)
  {
    $id =  get_current_user_id();

    if ($this->check_admin_permissions($request) && $request->get_param('id')) {
      $id = $request->get_param('id');
    }

    return $id;
  }

  /**
   * Get Tasks list
   * 
   * @param WP_REST_Request $request
   * 
   * @return WP_REST_Response
   */
  function get_list($request)
  {
    $meta = get_user_meta($this->get_user_ID($request), $this->meta);
    return $this->returnSucess($meta && count($meta) > 0 ?  json_decode($meta[0]) : []);
  }

  /**
   * Update tasks list by user id
   * 
   * @param WP_REST_Request $request
   * 
   * @return WP_REST_Response
   */

  function updateTasks($request)
  {
    $requestData = $request->get_body();
    update_user_meta($this->get_user_ID($request), $this->meta, $requestData);

    return $this->returnSucess(json_decode($requestData));
  }


  /**
   * Return Success reponse
   * 
   * @param Array|Object $params
   * 
   * @return WP_REST_Response
   */
  function returnSucess($params)
  {
    return new \WP_REST_Response([
      "response" => [
        "code" => 200
      ],
      'body' =>  $params
    ]);
  }

  /**
   * Return error Object after validation if errors exists
   * 
   * @param String|Array $message
   * 
   * @return JSON
   */
  function return_error($message = null)
  {
    header('Content-type:application/json; charset=utf-8');

    echo
    json_encode([
      "response" => [
        "code" => 400,
        'message' => $message ? $message : ''
      ]
    ]);
    exit;
  }

  /**
   * Return error response on rest not authenticated
   * 
   * @return WP_Error
   */
  private function returnRestError()
  {
    $message = apply_filters('disable_wp_rest_api_error', __('REST API restricted to authenticated users.'));
    return new \WP_Error('rest_login_required', $message, array('status' => rest_authorization_required_code()));
  }
}
