<?php

class Roomvu_Marketing_Rest_Api
{
    /**
     * api url
     * @var string
     */
    protected $baseUrl = 'https://www.roomvu.com/api';

    protected $settings;

    public function __construct($email, $api_key)
    {
        $this->settings = [
            'email' => $email,
            'api_key' => $api_key,
        ];
    }


    public function isActive(){
        try {
            $credentials = $this->getAuthCredentials();

            $endPoint = "v1/integration/wp/calendar/posts?email={$credentials['email']}&api_key={$credentials['api_key']}&v=1.3";
            $result = $this->call($endPoint, [], 'get');
            if (isset($result['error']) && $result['error']) {
                return false;
            } else {
                if (isset($result['response']) && $result['response']['status'] == 'failed') {
                    return false;
                } elseif (isset($result['response']) && $result['response']['status'] == 'success') {
                    return true;
                }
            }
        }catch (Exception $e){
            return false;
        }
    }

    /**
     * @return string[]
     */
    public function getCalendarContent() {
        $response = array_fill_keys( [ 'status', 'message', 'data' ], '' );

        try {
            $credentials = $this->getAuthCredentials();
            $startDate = date("Y-m-d", strtotime("-30 days"));
            $endDate = date("Y-m-d");
            $endPoint    = "v1/integration/wp/calendar/posts?email={$credentials['email']}&api_key={$credentials['api_key']}&v=1.3&start={$startDate}&end={$endDate}";
            $result = $this->call( $endPoint, [], 'get' );
            if ( isset( $result['error'] ) && $result['error'] ) {
                $response['status']  = 'failed';
                $response['message'] = $result['response'];
            } else {
                if ( isset( $result['response'] ) && $result['response']['status'] == 'failed' ) {
                    $response['status']  = 'failed';
                    $response['message'] = 'token expired';
                } elseif ( isset( $result['response'] ) && $result['response']['status'] == 'success' ) {
                    $response['status']  = 'success';
                    $response['message'] = 'success';
                    $response['data']    = $result['response']['data'];
                }
            }
        } catch ( Exception $e ) {
            $response['status']  = 'failed';
            $response['message'] = $e->getMessage();
        }

        return $response;
    }

    /**
     * Calls the MailChimp API
     *
     * @param string $api_endpoint
     * @param array $param
     * @param string $method
     *
     * @return array
     * @uses WP_HTTP
     */
    private function call( $api_endpoint = '', $param = [], $method = 'post' ) {
        try {

            if ( isset( $param['data'] ) ) {
                $data = $param['data'];
            } else {
                $data = array();
            }
            $http_header = [];
            if ( ! empty( $data ) ) {
                $data          = json_encode( $data );
                $http_header[] = "Content-type: application/json; charset=utf-8";
                $http_header[] = "Accept: application/json";
                $http_header[] = "Content-Length: " . strlen( $data );
            }

            $url = $this->baseUrl . '/' . $api_endpoint;
            $request_args = [
                'headers' => $http_header,
                'httpversion' => '1.1',
                'timeout' => 20,
            ];


            if ( $method == 'post' ) {
                $request_args['body'] = $data;
                $httpRequest = wp_remote_post( $url, $request_args );
            } else {
                $httpRequest = wp_remote_get( $url, $request_args);
            }
            // Request response from API
            $http_code = wp_remote_retrieve_response_code( $httpRequest );
            if ( $http_code != 200 ) {
                $response = array(
                    'error'    => true,
                    'response' => 'System not able to communicate to API',
                );
            } else {
                $body = wp_remote_retrieve_body( $httpRequest );
                $response = array(
                    'error'    => false,
                    'response' => json_decode( $body, true )
                );
            }

            return $response;
        } catch ( Exception $e ) {
            return [
                'error'    => true,
                'response' => 'Something went wrong,Please try again later',
            ];
        }
    }

    protected function getAuthCredentials() {
        return [
            'email'   => $this->settings['email'],
            'api_key' => $this->settings['api_key'],
        ];
    }

}