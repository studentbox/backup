[![Build Status](https://travis-ci.org/studentbox/backup.svg?branch=master)](https://travis-ci.org/studentbox/backup)
[![Code Coverage](https://scrutinizer-ci.com/g/studentbox/backup/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/studentbox/backup/?branch=master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/studentbox/backup/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/studentbox/backup/?branch=master)

# StudentBox Backup

Dieses Skript sichert die Dateien und die Datenbank in den Cloudspeicher Amazon S3.

## Installation

Um das Skript zu installieren clone das Repo und installiere die Abhängikeiten mit composer.

```
git clone https://github.com/studentbox/backup
url -sS https://getcomposer.org/installer | php
php composer.phar install
```

## S3 Berechtigungen

Das Backup-Skript benötigt folgende Berechtigungen um korrekt zu funktionieren:

```json
{
  "Version": "<Version>",
  "Statement": [
    {
      "Effect": "Allow",
      "Action": [
        "s3:PutObject",
        "s3:ListBucket"
      ],
      "Resource": [
        "arn:aws:s3:::<Bucket-Name>",
        "arn:aws:s3:::<Bucket-Name>/*"
      ]
    }
  ]
}
```

## S3 Lifecycle

Die Datenbank wird täglich gesichert. Nach einem Tag ist das Datenbank-Backup abgelaufen und nach 30 Tagen wird es permanent gelöscht. 

Dateien werden täglich auf Änderungen überprüft. Kommt eine neue Version der Datei hinzu (auch Löschungen) wird die alte Version nach 30 Tagen gelöscht. Um diese Strategie umzusetzen wurden folgende Regeln definiert:

```
Datenbank
---------
Prefix:                       dbdumbs/
Action on Current Version:    Expire 1 days after the object's creation date 
Action on Previous Versions:  Permanently Delete 30 days after overwrite/expiration date.

Dateien
-------
Prefix:                       files/
Action on Current Version:    None
Action on Previous Versions:  Permanently Delete 30 days after overwrite/expiration date.   
```
