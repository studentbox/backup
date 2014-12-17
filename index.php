<?php

/*
 * Copyright (C) 2014 StudentBox
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

require 'vendor/autoload.php';

use Aws\S3\S3Client;
use Studentbox\Backup\S3Backup;
use Studentbox\Backup\Database;

$config = require 'config/local.php';

$s3Client = S3Client::factory(array(
            'key' => $config['s3backup']['key'],
            'secret' => $config['s3backup']['secret'],
            'region' => $config['s3backup']['region'],
        ));

$s3Backup = new S3Backup($s3Client);
$s3Backup->setBucketName($config['s3backup']['bucketname']);
$s3Backup->setFolders($config['s3backup']['folders']);

if($s3Backup->backupToS3()) {
    echo 'Backup der Verzeichnisse ' . implode(", ", $config['s3backup']['folders']) . ' erfolgreich';
} else {
    echo 'Backup fehlgeschlagen';
}

$database = new Database($s3Client, $config['database']);
$database->backupDatabase();