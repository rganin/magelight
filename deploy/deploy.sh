#!/bin/bash

if [ ! -d releases ]; then
    mkdir releases;
fi

if [ ! -d backups ]; then
    mkdir backups;
fi

RELEASE=`date +"%Y-%m-%d-%H-%M-%S"`;

PREVIOUS_RELEASE=`ls releases -t | head -n +1`;

RELEASE_DIR=releases/$RELEASE;
source deploy.conf;

echo Deploy started;


echo Creating release directory;
if [ ! -d $RELEASE_DIR ]; then
    mkdir $RELEASE_DIR;
fi
echo Creating backup directory;
if [ ! -d $BACKUP_DIR ]; then
    mkdir $BACKUP_DIR;
fi




echo Dumping current mysql databases $MYSQL_BACKUP_DBS  to files:;
for database in $MYSQL_BACKUP_DBS; do
    $MYSQL_DUMP -u $MYSQL_USER -p$MYSQL_PASSWORD $database | $GZIP -9 > "$BACKUP_DIR/$database.gz";
    echo $BACKUP_DIR/$database.gz dump complete;
done



echo Backup directories:;
for directory in $BACKUP_DIRECTORIES; do
    zip -r $BACKUP_DIR/$directory.zip $directory;
done






echo "Cloning git repo";
$GIT clone $GIT_REPOSITORY $RELEASE_DIR;
source pull-magelight.sh;

echo "Removing .git directory from relelase";
rm -Rf $RELEASE_DIR/.git;



echo "Cleaning var directories";
for files in $REMOVE_FILES; do
    rm -Rf $files;
done


echo "Recreating directories";
for directory in $CREATE_DIRECTORIES; do
    if [ ! -d $directory ]; then
        mkdir $directory;
    fi
done



echo Creading symlinks;
rm -f current;
ln -s $RELEASE_DIR current;
ln -s ../../var current/var;

echo Rendering config templates;
php -f deploy_templates.php

echo Creating symlink to private modules;
$POST_DEPLOY_COMMAND;

echo Running upgrade process;
php -f $RELEASE_DIR/upgrade.php;



echo Removing too old releases and backups;
CLEAR_DIRS="backups releases";
for directory in $CLEAR_DIRS; do
    for subdir in `ls $directory -t | tail -n +4`; do
        rm -Rf $directory/$subdir;
    done
done

echo DEPLOY COMPLETE;
