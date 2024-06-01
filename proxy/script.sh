#!/bin/bash

# Vérifier les arguments
if [ "$#" -ne 3 ]; then
    echo "Usage: $0 <client_name> <server_addr> <port>"
    exit 1
fi

# Variables
API_ENDPOINT="http://157.159.11.199:5000/add_config"
CLIENT_NAME="$1"
SERVER_ADDR="$2:22"
PORT="$3"

# Faire la requête avec curl
curl -X POST -H "Content-Type: application/json" -d '{"client_name":"'"$CLIENT_NAME"'", "port":"'"$PORT"'", "ip":"'"$SERVER_ADDR"'"}' "$API_ENDPOINT"
