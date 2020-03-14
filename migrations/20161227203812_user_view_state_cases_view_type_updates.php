<?php

use \Console\Migration\BaseMigration;

class UserViewStateCasesViewTypeUpdates extends BaseMigration
{
    public function change()
    {
        $db = $this->getDb();
        $db->begin_transaction();
        try {
            $rows = $db->query('select')->table('user')
		    ->fields('id', 'view_state')
		    ->execute();

            foreach ($rows as $row) {
                if ($row->view_state) {
                    $viewState = json_decode($row->view_state, true);
                    if ($viewState) {
                        if (isset($viewState['dashboard_view'])) {
                            $viewState['cases_view_type'] = $viewState['dashboard_view'];
                        }
                        if(array_key_exists('dashboard_view', $viewState)) {
                            unset($viewState['dashboard_view']);
                        }
                        if(array_key_exists('schedule_view', $viewState)) {
                            unset($viewState['schedule_view']);
                        }
                        $db->query('update')->table('user')
				->data(['view_state' => json_encode($viewState)])
				->where('id', $row->id)
				->execute();
                    }
                }
            }
            $db->commit();

        } catch (\Exception $e) {
            $db->rollback();
            $this->writeln("Can't update view states for users");
            throw $e;
        }

    }
}
