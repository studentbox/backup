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
 * Erstellt einen Dump der DB und lädt ihn auf den Cloudspeicher Amazon S3.
 *
 * @author Fabian Wüthrich
 */
class Database
{

    /**
     * @var S3Client S3-Client um den Dump zu speichern
     */
    protected $s3Client;

    /**
     * @var string Adresse des Datenbank-Hosts
     */
    protected $host;

    /**
     * @var string Benutzername des Datenbank-Benutzers
     */
    protected $username;

    /**
     * @var string Passwort des Datenbank-Benutzers
     */
    protected $password;

    /**
     * @var int Port des Datenbank-Hosts
     */
    protected $port;

    /**
     * @var string Name der Datenbank
     */
    protected $databasename;

    /**
     * @var string Pfad zu mysqldump
     */
    protected $mysqldumpPath;

    /**
     * @var string Optionen für mysqldump
     */
    protected $mysqldumpOptions;

    /**
     * @var string Name des Backup-Buckets
     */
    protected $bucketname;

    /**
     * @var array Array mit Einstellungen
     */
    protected $settings = array();

    /**
     * @param S3Client $s3Client S3-Client für das Backup
     * @param array $settings Einstellungen für die Datenbank
     */
    public function __construct(S3Client $s3Client, array $settings)
    {
        $this->s3Client = $s3Client;

        if (!empty($settings)) {
            $this->setSettings($settings);
        }
    }

    /**
     * Gibt Adresse des Datenbank-Hosts zurück.
     *
     * @return string Adresse des Datenbank-Hosts
     */
    public function getHost()
    {
        return $this->host;
    }

    /**
     * Gibt Benutzername des Datenbank-Benutzers zurück.
     *
     * @return string Benutzername des Datenbank-Benutzers
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Gibt Passwort des Datenbank-Benutzers zurück.
     *
     * @return string Passwort des Datenbank-Benutzers
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Gibt Port des Datenbank-Hosts zurück.
     *
     * @return int Port des Datenbank-Hosts
     */
    public function getPort()
    {
        return $this->port;
    }

    /**
     * Gibt Name der Datenbank zurück.
     *
     * @return string Name der Datenbank
     */
    public function getDatabasename()
    {
        return $this->databasename;
    }

    /**
     * Gibt Pfad zu mysqldump zurück.
     *
     * @return string Pfad zu mysqldump
     */
    public function getMysqldumpPath()
    {
        return $this->mysqldumpPath;
    }

    /**
     * Gibt Optionen für mysqldump zurück.
     *
     * @return string Optionen für mysqldump
     */
    public function getMysqldumpOptions()
    {
        return $this->mysqldumpOptions;
    }

    /**
     * Gibt Name des Backup-Buckets zurück.
     *
     * @return string Name des Backup-Buckets
     */
    public function getBucketname()
    {
        return $this->bucketname;
    }

    /**
     * Gibt Array mit Einstellungen zurück.
     *
     * @return array Array mit Einstellungen
     */
    public function getSettings()
    {
        return $this->settings;
    }

    /**
     * Setzt Adresse des Datenbank-Hosts.
     *
     * @param string $host Adresse des Datenbank-Hosts
     * @return \Studentbox\Backup\Database
     */
    public function setHost($host)
    {
        $this->host = $host;
        return $this;
    }

    /**
     * Setzt Benutzername des Datenbank-Benutzers.
     *
     * @param string $username Benutzername des Datenbank-Benutzers
     * @return \Studentbox\Backup\Database
     */
    public function setUsername($username)
    {
        $this->username = $username;
        return $this;
    }

    /**
     * Setzt Passwort des Datenbank-Benutzers.
     *
     * @param string $password Passwort des Datenbank-Benutzers
     * @return \Studentbox\Backup\Database
     */
    public function setPassword($password)
    {
        $this->password = $password;
        return $this;
    }

    /**
     * Setzt Port des Datenbank-Hosts.
     *
     * @param int $port Port des Datenbank-Hosts
     * @return \Studentbox\Backup\Database
     */
    public function setPort($port)
    {
        $this->port = (int) $port;
        return $this;
    }

    /**
     * Setzt Name der Datenbank.
     *
     * @param string $databasename Name der Datenbank
     * @return \Studentbox\Backup\Database
     */
    public function setDatabasename($databasename)
    {
        $this->databasename = $databasename;
        return $this;
    }

    /**
     * Setzt Optionen für mysqldump.
     *
     * @param string $mysqldumpoptions Optionen für mysqldump
     * @return \Studentbox\Backup\Database
     */
    public function setMysqldumpOptions($mysqldumpoptions)
    {
        $this->mysqldumpOptions = $mysqldumpoptions;
        return $this;
    }

    /**
     * Setzt Pfad zu mysqldump.
     *
     * @param string $mysqldumpPath Pfad zu mysqldump
     * @return \Studentbox\Backup\Database
     */
    public function setMysqldumpPath($mysqldumpPath)
    {
        $this->mysqldumpPath = $mysqldumpPath;
        return $this;
    }

    /**
     * Setzt Name des Backup-Buckets.
     *
     * @param string $bucketname Name des Backup-Buckets
     * @return \Studentbox\Backup\Database
     */
    public function setBucketname($bucketname)
    {
        $this->bucketname = $bucketname;
        return $this;
    }

    /**
     * Setzt Array mit Einstellungen.
     *
     * @param array $settings Array mit Einstellungen
     */
    public function setSettings(array $settings)
    {
        if (isset($settings['host'])) {
            $this->setHost($settings['host']);
        }
        if (isset($settings['username'])) {
            $this->setUsername($settings['username']);
        }
        if (isset($settings['password'])) {
            $this->setPassword($settings['password']);
        }
        if (isset($settings['port'])) {
            $this->setPort($settings['port']);
        }
        if (isset($settings['databasename'])) {
            $this->setDatabasename($settings['databasename']);
        }
        if (isset($settings['mysqldumppath'])) {
            $this->setMysqldumpPath($settings['mysqldumppath']);
        }
        if (isset($settings['mysqldumpoptions'])) {
            $this->setMysqldumpOptions($settings['mysqldumpoptions']);
        }
        if (isset($settings['bucketname'])) {
            $this->setBucketname($settings['bucketname']);
        }
        $this->settings = $settings;
        return $this;
    }

    /**
     * Erstellt ein Backup einer Datenbank und speichert das Backup im Cloudspeicher S3.
     */
    public function backupDatabase()
    {
        try {
            $databasedump = $this->generateDatabaseDump();
            $this->writeDumpToS3($databasedump);
        } catch (\Exception $ex) {
            echo 'Backup der Datenbank fehlgeschlagen: ' . $ex->getMessage();
        }
    }

    /**
     * Erstellt ein Dump der Datenbank.
     *
     * @return string Datenbank-Dump als Zeichenkette
     */
    public function generateDatabaseDump()
    {
        $mysqldumpPath = escapeshellarg($this->mysqldumpPath . DIRECTORY_SEPARATOR . 'mysqldump');
        $mysqldumpParameters = $this->prepareParameter();

        $databasedumparray = array();
        exec($mysqldumpPath . ' ' . $mysqldumpParameters, $databasedumparray, $return);
        if ($return != 0) {
            throw new \Exception();
        }
        $databasedump = implode("\n", $databasedumparray);

        return $databasedump;
    }

    /**
     * Verbindet die Parameter für mysqldump und gibt sie als String zurück.
     *
     * @return string Parameter für mysqldump
     */
    private function prepareParameter()
    {
        $host = '--host ' . escapeshellcmd($this->host);
        $username = '--user=' . escapeshellcmd($this->username);
        $password = '--password=' . escapeshellcmd($this->password);
        $port = '--port=' . escapeshellcmd($this->port);
        $otheroptions = escapeshellcmd($this->mysqldumpOptions);
        $databasename = escapeshellcmd($this->databasename);
        return $host . ' ' . $username . ' ' . $password . ' ' . $port . ' ' . $otheroptions . ' ' . $databasename;
    }

    /**
     * Speichert den übergebenen Datenbank-Dump in den Cloudspeicher S3.
     *
     * @param string $databasedump Datenbank-Dump als Zeichenkette
     * @return array Resultat der S3-Anfrage
     */
    public function writeDumpToS3($databasedump)
    {
        $key = 'dbdumbs/db_dumb_' . $this->databasename . '_' . date('Ymd') . '.sql';
        $bucket = $this->getBucketname();
        $result = $this->s3Client->putObject(array(
            'Bucket' => $bucket,
            'Key' => $key,
            'Body' => $databasedump,
        ));
        return $result;
    }

}
