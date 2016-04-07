#!/bin/bash
set -e

GREEN='\033[0;32m'
BLUE='\033[0;36m'
NC='\033[0m'

echo -e "${BLUE}Building image from Dockerfile...${NC}"
cd ..
docker build -t churchis ./

echo -e "${BLUE}Running container in background with mounted volume...${NC}"
docker run -d -p 8081:80 -v ${PWD}:/app/ --name churchis-c churchis

echo -e "${GREEN}Done.${NC}"
