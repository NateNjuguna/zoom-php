<?php
namespace Zoom\SMS\Drivers;

use Plivo\Exceptions\PlivoResponseException;
use Plivo\Exceptions\PlivoValidationException;
use Plivo\RestClient;
use Zoom\SMS\SMSDriverInterface;
use Zoom\SMS\SMSException;
use Zoom\SMS\SMSResult;

class Plivo implements SMSDriverInterface {

    /**
     * The name of the driver
     * 
     * @var string
     */
    public $name = 'plivo';

    /**
     * A callback url for status updates
     * 
     * @var string
     */
    protected $callbackUrl;

    /**
     * A Plivo issued phone number
     * 
     * @var string
     */
    protected $number;

    /**
     * Plivo's messaging resource service
     * 
     * @var \Plivo\Resources\Message\MessageInterface
     */
    protected $service;

    /**
     * Create a new SMS Driver for Africa's Talking
     * 
     * @param   array   $settings
     * @return  void
     */
    public function __construct(array $settings) {
        $this->callbackUrl = $settings['CALLBACK'];
        $this->number = $settings['NUMBER'];
        $this->service = with(new RestClient($settings['AUTH_ID'], $settings['AUTH_TOKEN']))->messages;
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
            $response = $this->service->create($this->number, $recipients, $message, [
                'method'    => 'POST',
                'type'      => 'sms',
                /**
                 * The following parameters are sent to the URL:
                 * 
                 * MessageUUID  The unique ID for the message.
                 * To	        Phone number of the recipient.
                 * From	        The sender ID used as the source address for the message.
                 * Status	    Status of the message including “queued”, “sent”, “failed”, “delivered”, “undelivered”, or “rejected”.
                 * Units	    Number of units into which a long SMS was split
                 * TotalRate	This is the charge applicable per outbound SMS unit.
                 * TotalAmount	Total charge for sending the SMS (TotalRate * No. of Units)
                 * MCC	        Mobile Country Code of the To number.
                 * MNC	        Mobile Network Code of the To number.
                 * ErrorCode	The Plivo error code which identifies the reason for the message delivery failure. This parameter is only defined for ‘failed’ or ‘undelivered’ messages.
                 * ParentMessageUUID (reserved for future use)	Same as the MessageUUID. This parameter is reserved for future use, and should be ignored for now.
                 * PartInfo (reserved for future use)	This parameter is reserved for future use, and should be ignored for now.
                 * 
                 */
                'url'       => $this->callbackUrl,
            ]);
            $invalid_numbers = isset($response->invalid_number) ? $response->invalid_number : [];
            $failed = array_map(
                function($recipient) {
                    return [
                        'recipient' => $recipient['number'],
                        'reason'    => 'Invalid Phone Number',
                    ];
                },
                $invalid_numbers
            );
            $sent = array_map(
                function($uuid, $recipient) {
                    return [
                        'recipient' => $recipient,
                        'ref'       => $uuid,
                    ];
                },
                $response->getMessageUuid(),
                array_diff(
                    $recipients,
                    $invalid_numbers
                )
            );
            return new SMSResult($failed, $sent);
        } catch (PlivoResponseException $e) {
            
        } catch (PlivoValidationException $e) {
            throw new SMSException("Plivo - The from/src phone number is required");
        }
    }
    
}
