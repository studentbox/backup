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

/**
 * Testet die S3-Klasse.
 *
 * @author Fabian Wüthrich
 */
class S3BackupTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var string Name des Buckets
     */
    protected $bucketName = 'my-bucket';

    /**
     * @var array Array mit Verzeichnissen
     */
    protected $folders;

    /**
     * @var array Testkonfiguration
     */
    protected $config;

    /**
     * @var S3Client S3-Client Mock
     */
    protected $s3Client;

    /**
     * @var S3Backup Testobjekt
     */
    protected $s3Backup;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->folders = array(
            'directory/test',
            'folder/directory',
            'folder/test',
        );
        $this->config = array(
            'bucketname' => $this->bucketName,
            'folders' => $this->folders,
        );
        $this->s3Client = $this->getMockBuilder('Aws\S3\S3Client')
                ->disableOriginalConstructor()
                ->getMock();
        $this->s3Backup = new S3Backup($this->s3Client, array());
    }

    /**
     * Testet die backuptToS3-Methode mit einem Ordner.
     */
    public function testBackupToS3WithOneFolder()
    {
        $folder = array('folder/directory');
        $this->s3Client->expects($this->once())
                ->method('uploadDirectory')
                ->with($folder[0], $this->bucketName, $this->isType('string'));
        $this->s3Backup->setFolders($folder);
        $this->s3Backup->setBucketName($this->bucketName);

        $this->assertTrue($this->s3Backup->backupToS3());
    }

    /**
     * Testet die backuptToS3-Methode mit drei Ordnern.
     */
    public function testBackupToS3WithThreeFolder()
    {
        $folders = array(
            'directory/test',
            'folder/directory',
            'folder/test',
        );

        $this->s3Client->expects($this->exactly(3))
                ->method('uploadDirectory')
                ->with($this->isType('string'), $this->bucketName, $this->isType('string'));
        $this->s3Backup->setFolders($folders);
        $this->s3Backup->setBucketName($this->bucketName);

        $this->assertTrue($this->s3Backup->backupToS3());
    }

    /**
     * Testet die backupToS3-Methode wenn ein leerer Bucket-Name gesetzt wurde.
     */
    public function testBackupToS3WithEmptyBucketName()
    {
        $this->s3Backup->setFolders($this->folders);
        $this->s3Backup->setBucketName('');

        $this->assertFalse($this->s3Backup->backupToS3());
    }

    /**
     * Testet die backupToS3-Methode wenn kein Ordner-Array gesetzt wurde.
     */
    public function testBackupToS3WithoutFolderArray()
    {
        $this->assertFalse($this->s3Backup->backupToS3());
    }

    /**
     * Testet getBucketName.
     */
    public function testSetGetBucketName()
    {
        $this->s3Backup->setBucketName($this->bucketName);
        $this->assertEquals($this->bucketName, $this->s3Backup->getBucketName());
    }

    /**
     * Testet getBucketName.
     */
    public function testSetGetFolders()
    {
        $this->s3Backup->setFolders($this->folders);
        $this->assertEquals($this->folders, $this->s3Backup->getFolders());
    }

    /**
     * Testet die Getter/Setter der Einstellungen mit einem Array.
     */
    public function testSetGetSettingsWithArray()
    {
        $this->s3Backup->setSettings($this->config);
        $this->assertEquals($this->bucketName, $this->s3Backup->getBucketName());
        $this->assertEquals($this->folders, $this->s3Backup->getFolders());
        $this->assertNotEmpty($this->s3Backup->getSettings());
    }

    /**
     * Testet die Einstellungen über den Konstruktor.
     */
    public function testSettingsOverConstructor()
    {
        $s3Backup = new S3Backup($this->s3Client, $this->config);
        $this->assertEquals($this->config, $s3Backup->getSettings());
    }

}
