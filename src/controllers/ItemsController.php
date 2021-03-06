<?php namespace Jayked\Laravelfilemanager\controllers;

use Jayked\Laravelfilemanager\controllers\Controller;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Input;

/**
 * Class ItemsController
 * @package Jayked\Laravelfilemanager\controllers
 */
class ItemsController extends LfmController {


    /**
     * Get the images to load for a selected folder
     *
     * @return mixed
     */
    public function getItems()
    {
        $type = Input::get('type');
        $view = $this->getView($type);
        $path = parent::getPath();
        $options = Config::get('lfm.options');

        $files       = File::files($path);
        $file_info   = $this->getFileInfos($files, $type);
        $directories = parent::getDirectories($path);
        $thumb_url   = parent::getUrl('thumb');

        return view($view)
            ->with(compact('files', 'file_info', 'directories', 'thumb_url', 'options'));
    }


    private function getFileInfos($files, $type = 'Images')
    {
        $file_info = [];

        foreach ($files as $key => $file) {
            $file_name = parent::getFileName($file)['short'];
            $file_created = filemtime($file);
            $file_size = number_format((File::size($file) / 1024), 2, ".", "");

            if ($file_size > 1024) {
                $file_size = number_format(($file_size / 1024), 2, ".", "") . " Mb";
            } else {
                $file_size = $file_size . " Kb";
            }

            if ($type === 'Images') {
                $file_type = File::mimeType($file);
                $icon = '';
            } else {
                $extension = strtolower(File::extension($file_name));

                $icon_array = Config::get('lfm.file_icon_array');
                $type_array = Config::get('lfm.file_type_array');

                if (array_key_exists($extension, $icon_array)) {
                    $icon = $icon_array[$extension];
                    $file_type = $type_array[$extension];
                } else {
                    $icon = "fa-file";
                    $file_type = "File";
                }
            }

            $file_info[$key] = [
                'name'      => $file_name,
                'size'      => $file_size,
                'created'   => $file_created,
                'type'      => $file_type,
                'icon'      => $icon,
            ];
        }

        return $file_info;
    }


    private function getView($type = 'Images')
    {
        $view = 'laravel-filemanager::images';

        if ($type !== 'Images') {
            $view = 'laravel-filemanager::files-list';
        }

        if (Input::get('show_list') == 1 && $type === 'Images') {
            $view .= '-list';
        }

        return $view;
    }
}
