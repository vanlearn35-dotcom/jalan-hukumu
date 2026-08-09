<?php

namespace App\Handlers;

use UniSharp\LaravelFilemanager\Handlers\ConfigHandler as BaseConfigHandler;

class ConfigHandler extends BaseConfigHandler
{
    public function userField()
    {
        return null;
    }

    public function getConfig()
    {
        $config = parent::getConfig();

        $packageId = request('package_id');

        if (!$packageId) {
            abort(403, 'Package context required');
        }

        $config['disk'] = 'public';
        $config['root_folder'] = "audios/{$packageId}";

        $config['folder_categories'] = [
            'file' => [
                'folder_name' => "audios/{$packageId}",
                'startup_view' => 'list',
                'max_size' => 10240,
                'thumb' => false,
            ],
        ];

        return $config;
    }
}
