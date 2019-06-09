#!/bin/sh
# Compatible versions: 1.9.2.1, 1.9.2.0, 1.9.1.1, 1.9.1.0, 1.9.0.1, 1.8.1.0, 1.8.0.0
lockfile=/tmp/cron.lock
PHP_BIN=/usr/local/bin/php
ABSOLUTE_PATH=$(cd $(dirname "${BASH_SOURCE[0]}") && pwd)/$(basename "${BASH_SOURCE[0]}")
INSTALLDIR=${ABSOLUTE_PATH%/*}

function cleanup() {
    for cpid in $(jobs -p); do kill $cpid; done
    rm -f $lockfile
}

trap cleanup 1 2 3 6 9 15

if [ ! -f $lockfile ];then
    echo $$ > $lockfile
else
    exit 0
fi

if [ -n "$1" ] ; then
    CRONSCRIPT=$1
else
    CRONSCRIPT=cron.php
fi

for i in default always; do
        $PHP_BIN $INSTALLDIR/$CRONSCRIPT -m$i 1>/dev/null 2>&1 &
done
wait
rm -f $lockfile

exit 0
