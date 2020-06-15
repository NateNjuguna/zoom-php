<?php
namespace Zoom\SMS\Drivers;

use Zoom\SMS\SMSDriverInterface;
use Zoom\SMS\SMSException;
use Zoom\SMS\SMSResult;
use Twilio\Exceptions\TwilioException;
use Twilio\Rest\Client;

class Twilio implements SMSDriverInterface {

    /**
     * The name of the driver
     * 
     * @var string
     */
    public $name = 'twilio';

    /**
     * A Twilio issued sender phone number
     * 
     * @var string
     */
    protected $number;

    /**
     * Twilio's Messaging Service
     * 
     * @var \Twilio\Rest\Api\V2010\Account\MessageList
     */
    protected $service;

    /**
     * Create a new SMS Driver for Africa's Talking
     * 
     * @param   array   $settings
     * @return  void
     */
    public function __construct(array $settings) {
        $this->number = $settings['SENDER_ID'];
        $this->service = with(new Client($settings['SID'], $settings['AUTH_TOKEN']))->messages;
    }

    /**
     * Send a message to a recipient(s)
     * 
     * This method initiates the sending of text messages to a recipient or list of recipients and returns an
     * array with the count of sent and unsent messages, and also the errors if any per recipient.
     *
     * @param   string|array    $recipients A single phone number or array of phone numbers
     * @param   string          $message    The text message body which will be sent to the recipient(s)
     * @return  array
     * 
     * @throws  \Zoom\SMS\SMSException
     */
    public function send($recipients, $message) {
        if (is_string($recipients)) {
            $recipients = [$recipients];
        }
        if (!is_array($recipients)) {
            throw new SMSException('$recipients should be an array or string');
        }
        $failed = [];
        $sent = [];
        foreach ($recipients as $recipient) {
            try {
                $message = $this->service->create($recipient, [
                    'from' => $this->number,
                    'body' => $message,
                ]);
                array_push($sent, [
                    'recipient' => $recipient,
                    'ref'       => $message->sid,
                ]);
            } catch (TwilioException $e) {
                array_push($failed, [
                    'recipient' => $recipient,
                    'reason'    => $e->getMessage(),
                ]);
            }
        }
        return new SMSResult($failed, $sent);
    }
    
}
