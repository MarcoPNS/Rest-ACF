<?php

if (!defined('ABSPATH')) {
    exit;
}


if (!class_exists('REST_ACF_Controller')) {
    class REST_ACF_Controller extends WP_REST_Controller
    {

        protected $acf = null;
        protected $type = null;
        protected $controller = null;

        protected static $default_params = array(
            'page' => 1,
            'per_page' => 10,
            'orderby' => 'id',
        );

        public function __construct($type = null)
        {
            $this->namespace = 'acf/V4';
            $this->acf = new REST_ACF_API($this->type, get_class($this));
        }

        public function register_hooks()
        {
            add_action('rest_insert_' . $this->type, array($this, 'rest_insert'), 10, 3);
        }

        public function register_routes()
        {
            register_rest_route($this->namespace, '/' . $this->rest_base . '/(?P<id>[\d]+)/?(?P<field>[\w\-\_]+)?', array(
                array(
                    'methods' => WP_REST_Server::READABLE,
                    'callback' => array($this, 'get_item'),
                    'permission_callback' => array($this, 'get_item_permissions_check'),
                ),
                array(
                    'methods' => WP_REST_Server::EDITABLE,
                    'callback' => array($this, 'update_item'),
                    'permission_callback' => array($this, 'update_item_permissions_check'),
                ),
            ));

            register_rest_route($this->namespace, '/' . $this->rest_base, array(
                array(
                    'methods' => WP_REST_Server::READABLE,
                    'callback' => array($this, 'get_items'),
                    'permission_callback' => array($this, 'get_items_permissions_check'),
                ),
            ));
        }

        public function register_field()
        {
            register_rest_field($this->type, 'acf', array(
                'get_callback' => array($this, 'register_field_callback'),
                'schema' => array(
                    'description' => __('Expose advanced custom fields.', 'acf-to-rest-api'),
                    'type' => 'object',
                ),
            ));
        }

        public function register_field_callback($data)
        {
            $fields = $this->acf->get_fields($data);
            return $fields['acf'];
        }

        public function register()
        {
            $this->register_routes();
            $this->register_hooks();
            $this->register_field();
        }

        public function get_item($request)
        {
            $fields = $this->acf->get_fields($request);
            return rest_ensure_response($fields);
        }

        public function get_item_permissions_check($request)
        {
            return apply_filters('acf/rest_api/item_permissions/get', true, $request, $this->type);
        }

        public function get_items($request)
        {
            if (!method_exists($this->controller, 'get_items')) {
                return new WP_Error('cant_get_items', __('Cannot get items', 'acf-to-rest-api'), array('status' => 500));
            }

            $this->set_default_parameters($request);
            $data = $this->controller->get_items($request)->get_data();

            $response = array();
            if (!empty($data)) {
                foreach ($data as $v) {
                    if (isset($v['acf'])) {
                        $response[] = array(
                            'id' => $v['id'],
                            'acf' => $v['acf'],
                        );
                    }
                }
            }

            return apply_filters('acf/rest_api/' . $this->type . '/get_items', rest_ensure_response($response), rest_ensure_request($request));
        }

        public function get_items_permissions_check($request)
        {
            return apply_filters('acf/rest_api/items_permissions/get', true, $request, $this->type);
        }

        public function update_item_permissions_check($request)
        {
            return apply_filters('acf/rest_api/item_permissions/update', current_user_can('edit_posts'), $request, $this->type);
        }

        public function update_item($request)
        {
        	$postId = $request->get_param('id');
            $item = $this->prepare_item_for_acf($request);
            if (is_array($item) && count($item) > 0) {
                foreach ($item as $key => $value) {
	                acf_update_metadata($postId,$key, $value);
	                acf_flush_value_cache( $postId, $key);
                }
                return new WP_REST_Response($this->acf->get_fields($request), 200);
            }
            return new WP_Error('cant_update_item', __('Something went wrong. Please check if the fields exists and your first key is **fields**', 'acf-to-rest-api'), array('status' => 500));
        }

        public function rest_insert($object, $request, $creating)
        {
            if ($request instanceof WP_REST_Request) {
                $id = $this->acf->get_id($object);
                if (!$id) {
                    $id = $this->acf->get_id($request);
                }
                $request->set_param('id', $id);
            }

            return $this->update_item($request);
        }

        protected $items = [];

        public function prepare_item_for_acf($request)
        {
            global $arrayResult;
            $item = false;
            if ($request instanceof WP_REST_Request) {
                $data = $request->get_param('fields');
                $id = $this->acf->get_id($request);
                if ($id && is_array($data)) {
                    $this->generateKeysOfJson($data);
                    $item = $this->items;
                }
            }
            return apply_filters('acf/rest_api/' . $this->type . '/prepare_item', $item, $request);
        }


        protected function set_default_parameters(&$request)
        {
            if ($request instanceof WP_REST_Request) {
                $params = $request->get_params();
                foreach (self::$default_params as $k => $v) {
                    if (!isset($params[$k])) {
                        $request->set_param($k, $v);
                    }
                }
            }
        }

        protected function generateKeysOfJson($array, $level = 0, $keyString = '', $keys = [])
        {
            foreach ($array as $key => $value) {
                //If $value is an array.
                if (is_array($value)) {
                    //save the key or add it till we have a value.
                    $keys[$level] = $key;
                    if ($keyString == '') {
                        $keyString = $key;
                    } else {
                        $keyString = $keyString . '_' . $key;
                    }
                    //We need to loop through it.
                    $this->generateKeysOfJson($value, $level + 1, $keyString, $keys);
                } else {
                    if ($keyString == '' || $level == 0) {
                        $finalKey = $key;
                    } else {
                        $finalKey = implode('_', array_slice($keys, 0, $level)) . '_' . $key;
                    }

                    //clean it
                    if (var_export($value, true) !== 'NULL') {
                        $this->items[$finalKey] = $value;
                    }

                }
            }
        }
    }
}

