#!/usr/bin/env bash

ide-helper(){
    php artisan clear-compiled
	php artisan ide-helper:generate
	php artisan ide-helper:meta
	php artisan ide-helper:models
	php artisan config:clear
}

start(){
    echo "Starting..."
    if [ $1 ]
    then
       docker-compose up -d
       docker ps
       docker exec -it ${PWD##*/}_php_1 bash
    else
       docker-compose up
    fi
}

stop(){
    echo "Stopping..."
    docker-compose stop
    echo "END"
}


restart(){
    stop
    start
}

pull(){
    stop
    git pull origin dev
    php artisan apidoc
    start
}


update(){
    stop
    docker stop `docker ps -a -q`
    docker rm -f `docker ps -a -q`
    docker rmi -f `docker images -q`
    start
}

apidoc(){
	php artisan apidoc
}

cache(){
#	php artisan api:cache
	php artisan cache:table
	php artisan config:cache
#	php artisan route:cache
	php artisan optimize
}

clear(){
	php artisan auth:clear-resets
	php artisan cache:clear
	php artisan config:clear
	php artisan route:clear
	php artisan view:clear
}
style(){
	./vendor/bin/phpcbf app/ --standard=PSR2
	./vendor/bin/phpcbf vendor/songshenzong --standard=PSR2
}
echo ""
echo "--------------- Docker Helper ---------------"
echo "1: IDE Helper"
echo "2: Start Docker (Interactive Mode)"
echo "3: Start Docker (Daemon Model)"
echo "4: Stop Docker"
echo "5: Restart Docker"
echo "6: Pull & Update Code (Not Front End)"
echo "7: Update Docker"
echo "8: ApiDoc"
echo "9: Cache"
echo "10: Clear Cache"
echo "11: Code Style"
if [ $1 ]
then
    echo "12: Interactive Mode (Docker Print Information)"

else
    echo "13: Daemon Model (Docker Hide Information)"
fi
echo "--------------------------------------"
echo -n "Enter the Number:"
read code
case $code in
    1)  ide-helper
    ;;
    2)  start
    ;;
    3)  start -d
    ;;
    4)  stop
    ;;
    5)  restart
    ;;
    6)  pull
    ;;
    7)  update
    ;;
    8)  apidoc
    ;;
    9)  cache
    ;;
    10)  clear
    ;;
    11)  style
    ;;
    12)  $0 -d
    ;;
    13)  $0
    ;;
    *)  echo 'Enter the Error'
    ;;
esac