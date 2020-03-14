<?php

use \Console\Migration\BaseMigration;

class UserActivitySearchParams extends BaseMigration
{
    public function change()
    {
        $this->query("
            CREATE TABLE `user_activity_search_params` (
                `id` INT(10) NOT NULL AUTO_INCREMENT,
                `user_activity_id` INT(10) NULL,
                `case_id` INT(10) NULL,
                PRIMARY KEY (`id`),
                INDEX `IDX_user_activity_id` (`user_activity_id`)
            )
            ENGINE=InnoDB;
        ");

        $rows = $this->getDb()->query('select')
            ->table('user_activity')
            ->execute();

        $this->getDb()->begin_transaction();

        try {

            foreach ($rows as $row) {
                if ($row->details) {
                    $data = unserialize($row->details);
                    if (isset($data['case'])) {
                        $this->getDb()->query('insert')
                            ->table('user_activity_search_params')
                            ->data([
                                'user_activity_id' => $row->id,
                                'case_id' => $data['case']
                            ])->execute();
                    }
                }
            }

            $this->getDb()->commit();

        } catch (\Exception $e) {
            $this->getDb()->rollback();
            throw $e;

        }

    }
}
