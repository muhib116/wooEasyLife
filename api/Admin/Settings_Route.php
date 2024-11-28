<?php
    namespace WooEasyLife\API\Admin;

    use WP_REST_Controller;

    class Settings_Route extends WP_REST_Controller {
        protected $namespace;
        protected $rest_base;

        public function __construct(){
            $this->namespace = "wel/v1";
            $this->rest_base = "/settings";
        }

        /**
         * register route
         */

         public function register_route(){
            register_rest_route( 
                $this->namespace, 
                $this->rest_base,
                [
                    [
                        "methods" => \WP_REST_Server::READABLE,
                        "callback" => [ $this, 'get_items' ],
                        'permission_callback' => [ $this, 'get_route_access' ]
                    ]
                ]
            );
        }

        public function get_route_access($request) {
            return true;
        }

        public function get_items( $request ) {
            $response = [
                'firstname' => "Md.",
                'lastname' => "Muhibbullah",
                'email' => "dev.muhibbullah@gmail.com"
            ];

            return rest_ensure_response( $response );
        }
    }
