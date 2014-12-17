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
     * @var S3Client S3-Client
     */
    protected $s3Client;

    /**
     * @var string Bucketname
     */
    protected $bucketName;

    /**
     * @var array Ordner zum Upload
     */
    protected $folders;

    /**
     * @var array Array mit Einstellungen
     */
    protected $settings = array();

    /**
     * Erstellt ein S3Backup wenn ein S3-Client übergeben wurde.
     *
     * @param S3Client $s3Client
     */
    public function __construct(S3Client $s3Client, array $settings)
    {
        $this->s3Client = $s3Client;

        if (!empty($settings)) {
            $this->setSettings($settings);
        }
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
     * Liefert ein Array mit Einstellungen zurück.
     * 
     * @return array Einstellungs-Array
     */
    public function getSettings()
    {
        return $this->settings;
    }

    /**
     * Setzt den Bucket-Namen.
     *
     * @param string $bucket
     * @return \Studentbox\Backup\S3Backup
     */
    public function setBucketName($bucket)
    {
        $this->bucketName = $bucket;
        return $this;
    }

    /**
     * Setzt das Ordner-Array.
     *
     * @param array $folders
     * @return \Studentbox\Backup\S3Backup
     */
    public function setFolders($folders)
    {
        $this->folders = $folders;
        return $this;
    }

    /**
     * Setzt das Einstellungs-Array.
     * 
     * @param array $settings
     * @return \Studentbox\Backup\S3Backup
     */
    public function setSettings($settings)
    {
        if (isset($settings['bucketname'])) {
            $this->setBucketName($settings['bucketname']);
        }
        if (isset($settings['folders'])) {
            $this->setFolders($settings['folders']);
        }
        $this->settings = $settings;
        return $this;
    }

    /**
     * Erstellt ein Backup und lädt es auf S3.
     *
     * @return boolean True wenn Backup erfolgreich
     */
    public function backupToS3()
    {
        try {
            if (empty($this->bucketName) or empty($this->folders)) {
                throw new \InvalidArgumentException('Invalid bucketname or foldername given');
            }
            foreach ($this->folders as $folder) {
                $virtualfolder = 'files/' . basename($folder);
                $this->s3Client->uploadDirectory($folder, $this->bucketName, $virtualfolder);
            }
            $success = true;
        } catch (\Exception $ex) {
            echo 'Backup der Verzeichnisse fehlgeschlagen: ' . $ex->getMessage();
            $success = false;
        }
        return $success;
    }

}
