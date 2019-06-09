#!/bin/sh

rm -fR var/cache/*
rm -fR var/session/*
rm -fR var/log/*

find . -type f -exec chmod 644 {} \;
find . -type d -exec chmod 755 {} \;

chmod o+w ./var ./app/etc
chmod 550 ./mage
chmod -R o+w ./media
chmod -R 755 ./var

chmod o+w ./var ./app/etc && chmod 550 ./mage && chmod -R o+w ./media && chmod 755 -R ./var

chattr -R +A var/session/
chattr -R +A var/cache/
