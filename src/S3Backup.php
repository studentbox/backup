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

namespace Studentbox\Backup;

use Aws\S3\S3Client;

/**
 * Diese Klasse synchronisiert mehrere Ordner mit dem Cloudspeicherdienst Amazon S3.
 *
 * @author Fabian Wüthrich
 */
class S3Backup
{

    /**
     * @var string Bucketname
     */
    protected $bucketName;

    /**
     * @var array Ordner zum Upload
     */
    protected $folders;

    /**
     * @var S3Client S3-Client
     */
    protected $client;

    /**
     * Erstellt ein S3Backup wenn ein S3-Client übergeben wurde.
     *
     * @param S3Client $client
     */
    public function __construct(S3Client $client)
    {
        $this->client = $client;
    }

    /**
     * Liefert den Bucket-Name zurück.
     *
     * @return string Bucket-Name
     */
    public function getBucketName()
    {
        return $this->bucketName;
    }

    /**
     * Liefert ein Array mit den Ordnern zum Backup zurück.
     *
     * @return array Ordner-Array
     */
    public function getFolders()
    {
        return $this->folders;
    }

    /**
     * Setzt den Bucket-Namen.
     *
     * @param string $bucket
     */
    public function setBucketName($bucket)
    {
        $this->bucketName = $bucket;
    }

    /**
     * Setzt das Ordner-Array.
     *
     * @param array $folders
     */
    public function setFolders($folders)
    {
        $this->folders = $folders;
    }

    /**
     * Erstellt ein Backup und lädt es auf S3.
     *
     * @return boolean True wenn Backup erfolgreich
     */
    public function backupToS3()
    {
        $success = false;
        if (!empty($this->bucketName) and ! empty($this->folders)) {
            foreach ($this->folders as $folder) {
                $virtualfolder = 'files/' . basename($folder);
                $this->client->uploadDirectory($folder, $this->bucketName, $virtualfolder);
            }
            $success = true;
        }
        return $success;
    }
}
