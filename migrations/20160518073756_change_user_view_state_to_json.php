<?php

use \Console\Migration\BaseMigration;

class ChangeUserViewStateToJson extends BaseMigration
{
    public function change()
    {
        $q = $this->getDb()->query('select')->table('user')
            ->fields('id', 'view_state')
            ->execute();

        foreach ($q as $row) {
            $row = (array)$row;
            $viewState = json_encode(unserialize($row['view_state']));
            $this->getDb()->query('update')->table('user')
                ->data(['view_state' => $viewState])
                ->where('id', $row['id'])
                ->execute();
        }
    }
}
