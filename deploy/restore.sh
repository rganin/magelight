#!/bin/bash

RESTORE_RELEASE=$1;
source deploy.conf;

if [ ! $RESTORE_RELEASE ]; then
    echo Usage : $0 release-number. Example: $0 2012-12-21-21-21-21;
    exit 1;
fi

echo Restoring release $RESTORE_RELEASE;

rm -f current;

ln -s releases/$RESTORE_RELEASE current;
touch releases/$RESTORE_RELEASE;
touch backups/backup-for-$RESTORE_RELEASE;

echo Symlink restored;

echo Restoring var directory;

unzip -o backups/backup-for-$RESTORE_RELEASE/var.zip -d .;

echo Restoring databases: $MYSQL_BACKUP_DBS;

for database in $MYSQL_BACKUP_DBS; do
    gunzip -c backups/backup-for-$RESTORE_RELEASE/$database.gz > ./$database.sql;
    echo "DROP DATABASE $database;" | mysql -u $MYSQL_USER -p$MYSQL_PASSWORD;
    echo "CREATE DATABASE $database CHARSET utf8 COLLATE utf8_general_ci;" | mysql -u $MYSQL_USER -p$MYSQL_PASSWORD;
    cat $database.sql | mysql -u $MYSQL_USER -p$MYSQL_PASSWORD $database;
    rm -f $database.sql;
done

echo Restored to release $RESTORE_RELEASE;
