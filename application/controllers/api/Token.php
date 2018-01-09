<?php

defined('BASEPATH') OR exit('No direct script access allowed');

// This can be removed if you use __autoload() in config.php OR use Modular Extensions
/** @noinspection PhpIncludeInspection */
require APPPATH . 'libraries/REST_Controller.php';
require_once APPPATH . '/libraries/JWT.php';
use \Firebase\JWT\JWT;
/**
 * This is an example of a few basic user interaction methods you could use
 * all done with a hardcoded array
 *
 * @package         CodeIgniter
 * @subpackage      Rest Server
 * @category        Controller
 * @author          Phil Sturgeon, Chris Kacerguis
 * @license         MIT
 * @link            https://github.com/chriskacerguis/codeigniter-restserver
 */
class Token extends REST_Controller {

    function __construct()
    {   

        try {

                // Construct the parent class
            parent::__construct();

            // Configure limits on our controller methods
            // Ensure you have created the 'limits' table and enabled 'limits' within application/config/rest.php
            $this->methods['users_get']['limit'] = 500; // 500 requests per hour per user/key
            $this->methods['users_post']['limit'] = 100; // 100 requests per hour per user/key
            $this->methods['users_delete']['limit'] = 50; // 50 requests per hour per user/key


            $this->load->database();
            $this->load->library('session');
            $this->load->library('form_validation');

            /* cache control */
            $this->output->set_header('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
            $this->output->set_header('Pragma: no-cache');

            
        } catch (\Firebase\JWT\ExpiredException $e) {
                $this->generate_put();             
                exit();

        } catch (\Firebase\JWT\BeforeValidException $e) {
            $this->response([
                        'status' => FALSE,
                        'message' => "Before Valid Exception"
                    ], REST_Controller::HTTP_UNAUTHORIZED);                    
            exit();

        } catch (\Firebase\JWT\SignatureInvalidException $e) {
            $this->response([
                        'status' => FALSE,
                        'message' => "SignatureInvalid Exception"
                    ], REST_Controller::HTTP_UNAUTHORIZED);                
            exit();

        } catch (Exception $e) {
            $this->response([
                        'status' => FALSE,
                        'message' => 'Unauthorised'
                    ], REST_Controller::HTTP_UNAUTHORIZED);         
            exit();

        }

    }

        //Add a new Category
    public function generate_post()
    {
                // Invalid request, set the response and exit.
        $this->response([
                            'status' => FALSE,
                            'message' => 'Bad Request'
                        ], REST_Controller::HTTP_BAD_REQUEST); // BAD_REQUEST (400) being the HTTP response code
        return ;
    }

            //Update a Category
    public function generate_get()
    {
                // Invalid request, set the response and exit.
        $this->response([
                            'status' => FALSE,
                            'message' => 'Bad Request'
                        ], REST_Controller::HTTP_BAD_REQUEST); // BAD_REQUEST (400) being the HTTP response code
        return ;
    }

            //Delete a  Category
    public function generate_delete()
    {
                // Invalid request, set the response and exit.
        $this->response([
                            'status' => FALSE,
                            'message' => 'Bad Request'
                        ], REST_Controller::HTTP_BAD_REQUEST); // BAD_REQUEST (400) being the HTTP response code
        return ;
    }

        //Get One or All Categories
    public function generate_put()
    {
        
        $headers = $this->input->request_headers();

        if (array_key_exists('Authorization', $headers) && !empty($headers['Authorization'])) {
            // $decodedToken = AUTHORIZATION::validateToken($headers['Authorization']);
            

            $tks = explode('.', $headers['Authorization']);
            
            list($headb64, $bodyb64, $cryptob64) = $tks;

            $payload = JWT::jsonDecode(JWT::urlsafeB64Decode($bodyb64));
                 // TODO: test if token is blacklisted
            $date = new DateTime();

            $expired_Token['iat'] = $date->getTimestamp();
            $expired_Token['exp'] = $date->getTimestamp() + JWT::OFFSET;

            $expired_Token['id'] = $payload->id;
            $expired_Token['username'] = $payload->username;



            $new_Token['new_id_token'] = JWT::encode($expired_Token, JWT::SECRET_KEY);
            // $result_data["id"] =  $payload->id;

            // $result_data["name"] = $result_data["name"];
            // $result_data["login_type"] = $decoded['login_type'];


            $this->response($new_Token, REST_Controller::HTTP_OK); // OK (200) being the HTTP response code 
            return ;

        } else {
            $this->response([
                        'status' => FALSE,
                        'message' => 'Unauthorised'
                    ], REST_Controller::HTTP_UNAUTHORIZED);         
            return;

        
        }

    }


}
