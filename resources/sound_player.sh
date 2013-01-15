#!/bin/bash
#ps -ef |egrep -v "egrep -v" |grep mplayer 
#ps -ef |egrep -v "egrep -i " |egrep -i "mplayer .*resources/sounds" | awk -F ' ' '{print $2}' |xargs kill
mplayer $1 >/dev/null 2>&1 &

