<?php

namespace Opake\Model\Order\Outgoing;

use Opake\Model\AbstractModel;
use Opake\Model\Order\Outgoing\Mail\Receiver;

/**
 * Class Mail
 * @package Opake\Model\Order\Outgoing
 *
 * @property int $id
 * @property int $order_outgoing_item_id
 * @property string $subject
 * @property string $body
 */
class Mail extends AbstractModel
{
    public $id_field = 'id';
    public $table = 'order_outgoing_mail';

    protected $_row = [
        'id' => null,
        'order_outgoing_id' => null,
        'subject' => null,
        'body' => null
    ];

    protected $has_many = array(
        'receivers' => array(
            'model' => 'Order_Outgoing_Mail_Receiver',
            'key' => 'order_outgoing_mail_id',
            'cascade_delete' => true
        ),
    );

    public function toArray()
    {
        $array = parent::toArray();
        $array['receivers'] = [
            'to' => [],
            'cc' => [],
            'bcc' => []
        ];

        $receivers = $this->receivers->find_all();

        foreach ($receivers as $receiver) {
            if ($receiver->receiver_type == Receiver::TYPE_TO) {
                $array['receivers']['to'][] = $receiver->email;
            } else if ($receiver->receiver_type == Receiver::TYPE_CC) {
                $array['receivers']['cc'][] = $receiver->email;
            } else if ($receiver->receiver_type == Receiver::TYPE_BCC) {
                $array['receivers']['bcc'][] = $receiver->email;
            }
        }

        return $array;
    }


}