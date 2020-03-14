<?php

use \Console\Migration\BaseMigration;

class UseNamesForColors extends BaseMigration
{

    public function change()
    {
	    $this->query("
		    UPDATE `user` SET `case_color`='purple' WHERE `case_color`='#BB96CE';
		    UPDATE `user` SET `case_color`='sky-blue' WHERE `case_color`='#8FAFE0';
		    UPDATE `user` SET `case_color`='apricot' WHERE `case_color`='#ED7B7B';
		    UPDATE `user` SET `case_color`='aquamarine' WHERE `case_color`='#58F4B3';
		    UPDATE `user` SET `case_color`='gold-sand' WHERE `case_color`='#E8C188';
		    UPDATE `user` SET `case_color`='grey' WHERE `case_color`='#CECECE';
		    UPDATE `user` SET `case_color`='default-grey' WHERE `case_color`='#E0E0E0';

		    UPDATE `case_blocking` SET `color`='purple' WHERE `color`='#BB96CE';
		    UPDATE `case_blocking` SET `color`='sky-blue' WHERE `color`='#8FAFE0';
		    UPDATE `case_blocking` SET `color`='apricot' WHERE `color`='#ED7B7B';
		    UPDATE `case_blocking` SET `color`='aquamarine' WHERE `color`='#58F4B3';
		    UPDATE `case_blocking` SET `color`='gold-sand' WHERE `color`='#E8C188';
		    UPDATE `case_blocking` SET `color`='grey' WHERE `color`='#CECECE';
		    UPDATE `case_blocking` SET `color`='default-grey' WHERE `color`='#E0E0E0';

		    UPDATE `case_blocking_item` SET `color`='purple' WHERE `color`='#BB96CE';
		    UPDATE `case_blocking_item` SET `color`='sky-blue' WHERE `color`='#8FAFE0';
		    UPDATE `case_blocking_item` SET `color`='apricot' WHERE `color`='#ED7B7B';
		    UPDATE `case_blocking_item` SET `color`='aquamarine' WHERE `color`='#58F4B3';
		    UPDATE `case_blocking_item` SET `color`='gold-sand' WHERE `color`='#E8C188';
		    UPDATE `case_blocking_item` SET `color`='grey' WHERE `color`='#CECECE';
		    UPDATE `case_blocking_item` SET `color`='default-grey' WHERE `color`='#E0E0E0';
	    ");
    }
}
