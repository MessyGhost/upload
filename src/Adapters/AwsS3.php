<?php

/*
 * This file is part of fof/upload.
 *
 * Copyright (c) 2020 FriendsOfFlarum.
 * Copyright (c) 2016 - 2019 Flagrow
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace FoF\Upload\Adapters;

use FoF\Upload\Contracts\UploadAdapter;
use FoF\Upload\File;
use FoF\Upload\Helpers\Settings;
use Illuminate\Support\Arr;
use League\Flysystem\Config;

class AwsS3 extends Flysystem implements UploadAdapter
{
    protected function getConfig()
    {
        /** @var Settings $settings */
        $settings = app()->make(Settings::class);

        $config = new Config();
        if ($acl = $settings->get('awsS3ACL')) {
            $config->set('ACL', $acl);
        }

        return $config;
    }

    protected function generateUrl(File $file)
    {
        /** @var Settings $settings */
        $settings = app()->make(Settings::class);

        $cdnUrl = $settings->get('cdnUrl');

        if (!$cdnUrl) {
            $region = $this->adapter->getClient()->getRegion();
            $bucket = $this->adapter->getBucket();

            $cdnUrl = sprintf('https://%s.s3.%s.amazonaws.com', $bucket, $region ?: 'us-east-1');
        }

        $file->url = sprintf('%s/%s', $cdnUrl, Arr::get($this->meta, 'path', $file->path));
    }
}
