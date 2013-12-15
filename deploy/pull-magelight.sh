#!/bin/sh

echo Checkouting magelight framework;
WORKING_DIR=`pwd`;
cd /var/magelight;
GIT_ORIGINS=`git remote`;
if [[ $GIT_ORIGINS != *secure-origin* ]]; then
    git remote add secure-origin http://git.ganin.pp.ua:8080/git/framework.git;
fi
git reset --hard HEAD;
git checkout master;
git pull secure-origin master;
cd $WORKING_DIR;
