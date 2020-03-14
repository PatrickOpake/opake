<?php

namespace Opake\Model\Order\Outgoing\Mail;

use Opake\Model\AbstractModel;

/**
 * Receiver
 * @package Opake\Model\Order\Outgoing\Mail
 *
 * @property int $id
 * @property int $order_outgoing_mail_id
 * @property string $email
 * @property int $receiver_type
 */
class Receiver extends AbstractModel
{
    const TYPE_TO = 1;
    const TYPE_CC = 2;
    const TYPE_BCC = 3;

    public $id_field = 'id';
    public $table = 'order_outgoing_mail_receiver';
    protected $_row = [
        'id' => null,
        'order_outgoing_mail_id' => null,
        'email' => null,
        'receiver_type' => null
    ];
}