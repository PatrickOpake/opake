<?php

namespace Opake\Image;

use Opake\Helper\Config;

class ImageUtils extends \PHPixie\Image
{
	const ACCURACY_CROP = 'crop';
	const ACCURACY_FILL = 'fill';

	public $accuracyFillColor = 0xffffff;

	/**
	 * Возвращает картинку в нужных размерах
	 *
	 * Если $accuracy=false - просто ставит размер по минимальному координате
	 * Если $accuracy='crop' - устанавливает заданные размеры, и обрезает всё что не влезло
	 * Если $accuracy='fill' - устанавливает заданные размеры, и заполняет всё лишнее цветом $this->accuracyFillColor
	 *
	 * @param \PHPixie\Image\Driver $im
	 * @param int $width
	 * @param int $height
	 * @param bool|string $accuracy
	 * @return \PHPixie\Image\Driver
	 */
	public function resize($im, $width, $height, $accuracy = false)
	{
		if ($accuracy) {
			/* @var $canvas \PHPixie\Image\Driver */
			$canvas = $this->pixie->image->create($width, $height, $this->accuracyFillColor, 1);
			$fit = ($accuracy == self::ACCURACY_FILL);
			$im = $im->resize($width, $height, $fit);
			$newX = -0.5 * ($im->width - $width);
			$newY = -0.5 * ($im->height - $height);
			$canvas->overlay($im, $newX, $newY);
			return $canvas;
		} else {
			$im = $im->resize($width, $height);
			return $im;
		}
	}


	/**
	 * @param string $filename
	 */
	public function setExifOrientation($filename)
	{
		try {
			$exif = @exif_read_data($filename);
			$img = $this->read($filename);

			if (!empty($exif['Orientation'])) {
				switch ($exif['Orientation']) {
					case 3:
						$img->rotate(180);
						break;

					case 6:
						$img->rotate(-90);
						break;

					case 8:
						$img->rotate(90);
						break;
				}

				$img->save($filename);
			}
		} catch (\Exception $e) {

		}
	}

	/**
	 * Копирует картинку из временного каталога в нужный каталог
	 *
	 * @param string $source Путь к файлу с исходной картинкой
	 * @param string $target Путь к целевому каталогу
	 * @return string Путь к файлу с картинкой в целевом каталоге
	 */
	public function move($source, $target)
	{
		$basePath = Config::get('app.share');
		/* @var $im \PHPixie\Image\Driver */
		$im = $this->load(file_get_contents($basePath . $source));
		$newDir = $basePath . $target;
		if (!is_dir($newDir)) {
			mkdir($newDir, 0755, true);
		}
		$newName = $target . '/' . basename($source);
		$im->save($basePath . $newName, 'jpeg');
		return $newName;
	}

	/**
	 * Удаляет картинку
	 *
	 * @param string $image Путь и имя картинки
	 * @return boolean
	 */
	public function remove($image)
	{
		$image = Config::get('app.share') . '/' . $image;
		if (is_file($image)) {
			return unlink($image);
		}
		return false;
	}
}