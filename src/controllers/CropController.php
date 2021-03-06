<?php namespace Jayked\Laravelfilemanager\controllers;

use Jayked\Laravelfilemanager\controllers\Controller;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\View;
use Intervention\Image\Facades\Image;

/**
 * Class CropController
 *
 * @package Jayked\Laravelfilemanager\controllers
 */
class CropController extends LfmController
{
	public $option = 'crop';

	/**
	 * Show crop page
	 *
	 * @return mixed
	 */
	public function getCrop()
	{
		$working_dir = Input::get( 'working_dir' );
		$img         = parent::getUrl( 'directory' ) . Input::get( 'img' );

		return View::make( 'laravel-filemanager::crop' )
			->with( compact( 'working_dir', 'img' ) );
	}


	/**
	 * Crop the image (called via ajax)
	 */
	public function getCropimage()
	{
		$image      = Input::get( 'img' );
		$dataX      = Input::get( 'dataX' );
		$dataY      = Input::get( 'dataY' );
		$dataHeight = Input::get( 'dataHeight' );
		$dataWidth  = Input::get( 'dataWidth' );
		
		$image = parent::getTruePath( $image );

		// crop image
		$tmp_img = Image::make( base_path( $image ) );
		$tmp_img->crop( $dataWidth, $dataHeight, $dataX, $dataY )
			->save( base_path( $image ) );

		// make new thumbnail
		$thumb_img = Image::make( base_path( $image ) );
		$thumb_img->fit( 200, 200 )
			->save( parent::getPath( 'thumb' ) . parent::getFileName( $image )['short'] );
	}

}
