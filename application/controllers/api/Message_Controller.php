<?php

defined('BASEPATH') OR exit('No direct script access allowed');

// This can be removed if you use __autoload() in config.php OR use Modular Extensions
require APPPATH . '/libraries/REST_Controller.php';
require APPPATH . '/libraries/PHPSerial.php';

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
class Message_Controller extends REST_Controller {

    //Define class variable for serial Port
    var $COM_PORT = "COM4";

    function __construct()
    {
        // Construct the parent class
        parent::__construct();

        // Configure limits on our controller methods
        // Ensure you have created the 'limits' table and enabled 'limits' within application/config/rest.php
        $this->methods['message_get']['limit'] = 500; // 500 requests per hour per user/key
        $this->methods['message_post']['limit'] = 100; // 100 requests per hour per user/key
        $this->methods['message_delete']['limit'] = 50; // 50 requests per hour per user/key
    }

    public function messages_get()
    {
        //use model for execute database query
        $movements = $this->MessageModel->getAllMovements();

        if ($movements) {
            // Set the response and exit
            $this->response($movements, REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
        } else {
            // Set the response and exit
            $this->response([
                'status' => FALSE,
                'message' => 'No movements were found'
                    ], REST_Controller::HTTP_NOT_FOUND); // NOT_FOUND (404) being the HTTP response code
        }
    }

    public function message_post()
    {
        //get message from parameters
        $message = $this->post('message');

        //check if message isn't empty

        if (trim($message) == "") {
            $this->response([
                'status' => FALSE,
                'message' => 'Message text cannot be empty'
                    ], REST_Controller::HTTP_BAD_REQUEST);
        }

        //use model function for save message into database
        $new_message = $this->MessageModel->addMovement($message);

        if ($new_message) {

            //TODO: Check if serial port is available
            //try to write into Arduino Serial Port
            $serial = new PhpSerial;

            //check if serial is available
            if ($serial->deviceSet($this->COM_PORT)) {

                // We can change the baud rate, parity, length, stop bits, flow control
                $serial->confBaudRate(9600);
                $serial->confParity("none");
                $serial->confCharacterLength(8);
                $serial->confStopBits(1);
                $serial->confFlowControl("none");
                $serial->deviceOpen();

                sleep(2);

                // To write into serial port
                $serial->sendMessage($message);

                $message_status = [
                    'status' => TRUE,
                    'message' => 'Message send to Arduino with success',
                    'created' => $new_message
                ];
            } else {

                $message_status = [
                    'status' => FALSE,
                    'message' => 'Message can\'t be send to Arduino',
                    'created' => $new_message
                ];
            }

            $this->response($message_status, REST_Controller::HTTP_CREATED); // CREATED (201) being the HTTP response code
        } else {
            $this->response([
                'status' => FALSE,
                'message' => 'Database insertion failded'
                    ], REST_Controller::HTTP_INTERNAL_SERVER_ERROR); // NOT_FOUND (404) being the HTTP response code
        }
    }

}
