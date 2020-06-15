<?php
namespace Zoom\SMS\Drivers;

use AfricasTalking\SDK\AfricasTalking as AFT;
use Exception;
use Zoom\SMS\SMSDriverInterface;
use Zoom\SMS\SMSException;
use Zoom\SMS\SMSResult;

class AfricasTalking implements SMSDriverInterface {
    
    /**
     * A list of all possible response statuses and their meanings
     * 
     * @var array
     */
    const STATUSMAP = [
        100 => 'Processed',
        101 => 'Sent',
        102 => 'Queued',
        401 => 'RiskHold',
        402 => 'InvalidSenderId',
        403 => 'InvalidPhoneNumber',
        404 => 'UnsupportedNumberType',
        405 => 'InsufficientBalance',
        406 => 'UserInBlacklist',
        407 => 'CouldNotRoute',
        500 => 'InternalServerError',
        501 => 'GatewayError',
        502 => 'RejectedByGateway',
    ];

    /**
     * The name of the driver
     * 
     * @var string
     */
    public $name = 'africastalking';

    /**
     * An Africa's Talking Account SMS Sender ID
     * 
     * @var string
     */
    protected $senderID;

    /**
     * Africa's Talking SMS Service
     * 
     * @var \AfricasTalking\SDK\SMS
     */
    protected $service;

    /**
     * Create a new SMS Driver for Africa's Talking
     * 
     * @param   array   $settings
     * @return  void
     */
    public function __construct(array $settings) {
        $this->senderID = $settings['SENDER_ID'];
        $this->service = with(new AFT($settings['USERNAME'], $settings['API_KEY']))->sms();
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
            throw new SMSException('$recipents should be an array or string');
        }
        try {
            $response = $this->service->send([
                'from'      => $this->senderID,
                'message'   => $message,
                'to'        => implode(',', $recipients)
            ]);
            $failed = array_map(
                function($recipient) {
                    return [
                        'recipient' => $recipient['number'],
                        'reason'    => static::STATUSMAP[$recipient['statusCode']] . " - {$recipient['status']}",
                    ];
                },
                array_filter(
                    $response['Recipients'],
                    function($recipent) {
                        return $recipent['statusCode'] > 102;
                    }
                )
            );
            $sent = array_map(
                function($recipient) {
                    return [
                        'recipient' => $recipient['number'],
                        'ref'       => $recipient['messageId'],
                    ];
                },
                array_filter(
                    $response['Recipients'],
                    function($recipent) {
                        return $recipent['statusCode'] < 401;
                    }
                )
            );
            return new SMSResult($failed, $sent);
        } catch (Exception $e) {
            throw new SMSException("Africa's Talking - {$e->getMessage()}");
        }
    }
    
}
