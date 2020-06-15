<?php
namespace Zoom\SMS;

interface SMSDriverInterface {

    /**
     * Send a message to a recipient(s)
     * 
     * This method initiates the sending of text messages to a recipient or list of recipients and returns a
     * \Zoom\SMS\SMSResult.
     *
     * @param   string|array    $recipients A single phone number or array of phone numbers
     * @param   string          $message    The text message body which will be sent to the recipient(s)
     * @return  \Zoom\SMS\SMSResult
     * 
     * @throws  \Zoom\SMS\SMSException
     */
    public function send($recipients, $message);

    /**
     * Process a status callback for an SMS we sent
     * 
     * This method processes information from SMS Providers on the delivery/sent status of SMS and returns a
     * \Zoom\SMS\SMSStatus.
     *
     * @return  \Zoom\SMS\SMSStatus
     * 
     * @throws  \Zoom\SMS\SMSException
     */
    public function status();

}