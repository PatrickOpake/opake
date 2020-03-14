<?php

use \Console\Migration\BaseMigration;

class UploadedFilesUpdateTables extends BaseMigration
{
	public function change()
	{
		$this->updateInventory();
		$this->updateOtherTables();
	}

	protected function updateInventory()
	{

		$q = $this->getDb()->query('select')->table('inventory')
			->fields('id', 'image')
			->execute();

		$photoIds = [];
		foreach ($q as $row) {
			$matches = [];
			if (preg_match('/(\/uploads\/master\/inventory\/)(.+)/', $row->image, $matches)) {
				$share = $this->getPixie()->config->get('app.share');
				$settings = $this->getPixie()->config->get('image.default_settings');
				$fullPath = $share . $row->image;


				if (!is_file($fullPath)) {
					print $fullPath . " is not a file, skipped\r\n";
					continue;
				}

				$model = $this->getPixie()->orm->get('UploadedFile_Image');
				$model->initImageSettings('inventory');
				/*$model->storeLocalFile($fullPath, [
					'is_assigned' => true
				]);*/

				$model->path = '/master/inventory/';
				$model->original_filename = basename($fullPath);
				$model->extension = 'jpg';
				$model->name = preg_replace('/\.[^.]+$/', '', $model->original_filename);
				$model->mime_type = 'image/jpeg';
				$model->system = false;
				$model->assigned = false;

				$model->save();
				$model->createThumbnails();
				$photoIds[$row->id] = $model->id();
			}
		}

		$this->query("
            ALTER TABLE `inventory`
                ADD COLUMN `image_id` INT NULL DEFAULT NULL AFTER `time_create`,
                CHANGE COLUMN `image` `image_path` VARCHAR(255) NULL DEFAULT NULL;
        ");

		foreach ($photoIds as $invId => $photoId) {
			$this->getDb()->query('update')->table('inventory')
				->data([
					'image_id' => $photoId
				])->where('id', $invId)->execute();
		}
	}

	protected function updateOtherTables()
	{
		$this->query("
            ALTER TABLE `user`
                ADD COLUMN `photo_id` INT NULL DEFAULT NULL AFTER `comment`,
                DROP COLUMN `photo`;
        ");

		$this->query("
            ALTER TABLE `inventory_type`
                ADD COLUMN `image_id` INT NULL DEFAULT NULL,
                DROP COLUMN `image`;
        ");

		$this->query("
            ALTER TABLE `master_inventory`
                ADD COLUMN `image_id` INT NULL DEFAULT NULL,
                DROP COLUMN `image`;
        ");

		$this->query("
            ALTER TABLE `order_image`
                ADD COLUMN `image_id` INT NULL DEFAULT NULL,
                DROP COLUMN `image`;
        ");

		$this->query("
            ALTER TABLE `organization`
                ADD COLUMN `logo_id` INT NULL DEFAULT NULL,
                DROP COLUMN `logo`;
        ");

		$this->query("
            ALTER TABLE `vendor`
                ADD COLUMN `logo_id` INT NULL DEFAULT NULL,
                DROP COLUMN `logo`;
        ");
	}
}
