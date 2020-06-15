<?php
namespace Zoom\SMS\Drivers;

use Exception;
use Zoom\SMS\SMSDriverInterface;
use Zoom\SMS\SMSException;
use Zoom\SMS\SMSResult;
use Textlocal as GlobalTextlocal;

class TextLocal implements SMSDriverInterface {

    /**
     * A list of error codes and their descriptions
     * 
     * @var array
     */
    const ERROR_CODES = [
        1	=> 'No command specified.',
        2	=> 'Unrecognised command.',
        3	=> 'Invalid login details.',
        4   => 'No recipients specified.',
        5	=> 'No message content.',
        6	=> 'Message too long.',
        7	=> 'Insufficient credits.',
        8	=> 'Invalid schedule date.',
        9	=> 'Schedule date is in the past.',
        10	=> 'Invalid group ID.',
        11	=> 'Selected group is empty.',
        32	=> 'Invalid number format.',
        33	=> 'You have supplied too many numbers.',
        34	=> 'You have supplied both a group ID and a set of numbers.',
        43	=> 'Invalid sender name.',
        44	=> 'No sender name specified.',
        51	=> 'No valid numbers specified.',
    ];

    /**
     * A list of error codes and their descriptions
     * 
     * @var array
     */
    const WARNING_CODES = [
        1	=> 'Unrecognised response format.',
        3   => 'Invalid number.',
    ];

    /**
     * The name of the driver
     * 
     * @var string
     */
    public $name = 'textlocal';

    /**
     * A textlocal sender
     * 
     * @var string
     */
    protected $sender;

    /**
     * Textlocal's SMS Service
     * 
     * @var \Textlocal
     */
    protected $service;

    /**
     * Create a new SMS Driver for Textlocal
     * 
     * @param   array   $settings
     * @return  void
     */
    public function __construct(array $settings) {
        $this->sender = $settings['SENDER'];
        $this->service = new GlobalTextlocal(false, false, $settings['API_KEY']);
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
            $result = $this->service->sendSms($recipients, $message, $this->sender);
            $sent = array_map(
                function($message) {
                    return [
                        'recipient' => $message['recipient'],
                        'ref'       => $message['id'],
                    ];
                },
                $result['messages']
            );
            $failed = array_map(
                function($recipient, $warning) {
                    return [
                        'recipient' => $recipient,
                        'reason'    => "{$warning['code']} - {$warning['message']}",
                    ];
                },
                array_diff(
                    $recipients,
                    array_map(
                        function($message) {
                            return $message['recipient'];
                        },
                        $result['messages']
                    )
                ),
                $result['warnings'] ?: []
            );
            return new SMSResult($failed, $sent);
        } catch (Exception $e) {
            $error_message = $e->getMessage();
            if (in_array($error_message, static::ERROR_CODES, true)) {
                $error_code = array_pop(array_keys(static::ERROR_CODES, $e->getMessage(), true));
                $sent = [];
                $failed = array_map(
                    function($recipient) use($error_code, $error_message) {
                        return [
                            'recipient' => $recipient,
                            'reason'    => "{$error_code} - {$error_message}",
                        ];
                    },
                    $recipients
                );
                return new SMSResult($failed, $sent);
            }
            throw new SMSException("Textlocal - {$e->getMessage()}");
        }
    }
    
}
