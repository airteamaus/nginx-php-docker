#!/bin/sh

service php7.3-fpm stop 2>&1 > /dev/null 
service php7.3-fpm start
