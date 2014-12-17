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

use Aws\S3\Exception\S3Exception;

/**
 * Testet die Database-Klasse.
 *
 * @author Fabian Wüthrich
 */
class DatabaseTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Aws\S3\S3Client
     */
    protected $s3Client;

    /**
     * @var Database
     */
    protected $database;

    /**
     * @var string Adresse des Datenbank-Hosts
     */
    protected $host = 'localhost';

    /**
     * @var string Benutzername des Datenbank-Benutzers
     */
    protected $username = 'root';

    /**
     * @var string Passwort des Datenbank-Benutzers
     */
    protected $password = 'secret1234';

    /**
     * @var int Port des Datenbank-Hosts
     */
    protected $port = 3306;

    /**
     * @var string Name der Datenbank
     */
    protected $databasename = 'testdatabase';

    /**
     * @var string Pfad zu mysqldump
     */
    protected $mysqldumpPath = '/opt/bin/';

    /**
     * @var string Name des Backup-Buckets
     */
    protected $bucketname = 'test.bucket/sql/';

    /**
     * @var string Optionen für mysqldump
     */
    protected $mysqldumpOptions = '--opt';

    /**
     * @var string Datenbank-Dump als Zeichenkette
     */
    protected $databasedump = 'somesqlstatemants';

    /**
     * @var array Array mit Einstellungen
     */
    protected $testarray;

    /**
     * Sets up the fixture.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->testarray = array(
            'host' => $this->host,
            'username' => $this->username,
            'password' => $this->password,
            'port' => $this->port,
            'databasename' => $this->databasename,
            'mysqldumppath' => $this->mysqldumpPath,
            'mysqldumpoptions' => $this->mysqldumpOptions,
            'bucketname' => $this->bucketname,
        );

        $this->s3Client = $this->getMockBuilder('Aws\S3\S3Client')
                ->disableOriginalConstructor()
                ->setMethods(['putObject'])
                ->getMock();

        $this->database = new Database($this->s3Client, array());
    }

    /**
     * Testet Getter/Setter von Adresse des Datenbank-Hosts.
     */
    public function testSetGetHost()
    {
        $this->database->setHost($this->host);
        $this->assertEquals($this->host, $this->database->getHost());
    }

    /**
     * Testet Getter/Setter von Benutzername des Datenbank-Benutzers.
     */
    public function testSetGetUsername()
    {
        $this->database->setUsername($this->username);
        $this->assertEquals($this->username, $this->database->getUsername());
    }

    /**
     * Testet Getter/Setter von Benutzername des Datenbank-Benutzers.
     */
    public function testSetGetPassword()
    {
        $this->database->setPassword($this->password);
        $this->assertEquals($this->password, $this->database->getPassword());
    }

    /**
     * Testet Getter/Setter von Port des Datenbank-Hosts.
     */
    public function testSetGetPort()
    {
        $this->database->setPort($this->port);
        $this->assertEquals($this->port, $this->database->getPort());
    }

    /**
     * Testet Getter/Setter von Name der Datenbank.
     */
    public function testSetGetDatabasename()
    {
        $this->database->setDatabasename($this->databasename);
        $this->assertEquals($this->databasename, $this->database->getDatabasename());
    }

    /**
     * Testet Getter/Setter von Pfad zu mysqldump
     */
    public function testSetGetMysqldumpPath()
    {
        $this->database->setMysqldumpPath($this->mysqldumpPath);
        $this->assertEquals($this->mysqldumpPath, $this->database->getMysqldumpPath());
    }

    /**
     * Testet Getter/Setter von Optionen für mysqldump
     */
    public function testSetGetMysqldumpOptions()
    {
        $this->database->setMysqldumpOptions($this->mysqldumpOptions);
        $this->assertEquals($this->mysqldumpOptions, $this->database->getMysqldumpOptions());
    }

    /**
     * Testet Getter/Setter von Name des Backup-Buckets
     */
    public function testSetGetBucketname()
    {
        $this->database->setBucketname($this->bucketname);
        $this->assertEquals($this->bucketname, $this->database->getBucketname());
    }

    /**
     * Testet die Getter/Setter der Einstellungen mit einem Array.
     */
    public function testSetGetSettingsWithArray()
    {
        $this->database->setSettings($this->testarray);
        $this->assertEquals($this->host, $this->database->getHost());
        $this->assertEquals($this->username, $this->database->getUsername());
        $this->assertEquals($this->password, $this->database->getPassword());
        $this->assertEquals($this->port, $this->database->getPort());
        $this->assertEquals($this->databasename, $this->database->getDatabasename());
        $this->assertEquals($this->mysqldumpPath, $this->database->getMysqldumpPath());
        $this->assertEquals($this->mysqldumpOptions, $this->database->getMysqldumpOptions());
        $this->assertEquals($this->bucketname, $this->database->getBucketname());
        $this->assertNotEmpty($this->database->getSettings());
    }

    /**
     * Testet die Einstellungen über den Konstruktor.
     */
    public function testSettingsOverConstructor()
    {
        $database = new Database($this->s3Client, $this->testarray);
        $this->assertEquals($this->testarray, $database->getSettings());
    }

    /**
     * Testet writeDumpToS3 wenn erfolgreich
     */
    public function testWriteDumpToS3Success()
    {
        $result = array('testkey' => 'testvalue');
        $this->s3Client->expects($this->once())
                ->method('putObject')
                ->willReturn($result);
        $this->assertEquals($result, $this->database->writeDumpToS3($this->databasedump));
    }

    /**
     * Testet writeDumpToS3 wenn ein Fehler mit S3 aufgetreten ist.
     * @expectedException \Aws\S3\Exception\S3Exception
     */
    public function testWriteDumpToS3Error()
    {
        $this->s3Client->expects($this->once())
                ->method('putObject')
                ->willThrowException(new S3Exception);
        $this->assertEmpty($this->database->writeDumpToS3($this->databasedump));
    }

}
