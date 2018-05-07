#!/bin/bash
LG='\033[0;37m'  # Light Gray
GR='\033[0;32m'  # Green
NC='\033[0m'     # No Color

echo "${LG}[INFO] Shuting down running containers. Please wait...${NC}"
docker-compose down

echo "${LG}[INFO] Installing modules. Please wait...${NC}"
composer install

echo "${LG}[INFO] Building containers. Please wait...${NC}"
docker-compose build

echo "${LG}[INFO] Launching containers. Please wait...${NC}"
docker-compose up -d --force-recreate

echo "${LG}[INFO] Waiting for Postgres to run. Please wait...${NC}"
sleep 20
echo "${LG}[INFO] Postgres is ready.${NC}"
echo "${LG}[INFO] Running phpunit tests. Please wait...${NC}"

docker exec -it php vendor/bin/phpunit

echo "${GR}Recipe REST API is ready !!!!!${NC}"
echo "Application URI: http://localhost/recipes"
