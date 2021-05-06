#!/usr/bin/env bash

cd docker/dev

if [[ "$1" == 'bash' ]]; then
docker-compose -p testapp exec php bash

elif [[ "$1" == 'exec' ]]; then
shift;
docker-compose -p testapp exec php $@

elif [[ "$1" == 'up' ]]; then
shift;
docker-compose -p testapp up -d $@

elif [[ "$1" == 'down' ]]; then
docker-compose -p testapp down

elif [[ "$1" == 'ps' ]]; then
docker-compose -p testapp ps

elif [[ "$1" == 'restart' ]]; then
docker-compose -p testapp restart

elif [[ "$1" == 'logs' ]]; then
docker-compose -p testapp logs -f --tail=100 php

elif [[ "$1" == 'redis' ]]; then
docker-compose -p testapp exec redis bash

fi

cd ../../
