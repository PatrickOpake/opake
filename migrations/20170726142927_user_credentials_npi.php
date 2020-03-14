<?php

use \Console\Migration\BaseMigration;

class UserCredentialsNpi extends BaseMigration
{
    public function change()
    {
	    $this->getDb()->begin_transaction();

	    try {

		    $app = $this->getPixie();

		    $users = $app->orm->get('User')->find_all();

		    foreach ($users as $user) {
			    $model = $app->orm->get('User_Credentials')
				    ->where('user_id', $user->id())
				    ->find();

			    if (!$model->loaded()) {
				    $model = $app->orm->get('User_Credentials');
				    $model->user_id = $user->id();
			    }

			    if ($model->npi_number && !$user->npi) {
				    $user->npi = substr($model->npi_number, 0, 10);
				    $user->save();
			    } else if ($user->npi && !$model->npi_number) {
				    $model->npi_number = substr($user->npi, 0, 10);
				    $model->save();
			    }
		    }

		    $this->getDb()->commit();

	    } catch (\Exception $e) {
		    $this->getDb()->rollback();
		    throw $e;
	    }
    }
}
