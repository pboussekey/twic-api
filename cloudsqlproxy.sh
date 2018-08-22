#!/bin/bash

INSTANCE=""
PORT=3307
CREDENTIAL=""

while getopts "i:p:c:" option
do
    case $option in  
        i) INSTANCE=$OPTARG ;;
	p) PORT=$OPTARG ;;
	c) CREDENTIAL="-credential_file $OPTARG" ;;
    esac
done

LPATH=`dirname $0`
if pgrep cloud_sql_proxy 1>/dev/null ; then pgrep cloud_sql_proxy | xargs kill -15 ; fi
rm $LPATH/.cloud 2>/dev/null
chmod +x $LPATH/cloud_sql_proxy
$LPATH/cloud_sql_proxy $CREDENTIAL -instances=$INSTANCE=tcp:$PORT 1>$LPATH/.cloud 2>$LPATH/.cloud & 
is_connect=0
while test "$is_connect" != 1
do
   if grep -q "Ready for new connections" $LPATH/.cloud
   then
	is_connect=1
   fi
   cat $LPATH/.cloud
done
rm $LPATH/.cloud 2>/dev/null

exit 0