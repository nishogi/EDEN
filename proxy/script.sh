#!/bin/bash

# Vérifier les arguments
if [ "$#" -ne 2 ] && [ "$#" -ne 4 ]; then
    echo "Usage: $0 <add|rm> <client_name> [<server_addr>] [<port>]"
    exit 1
fi

# Variables
USAGE="$1"
API_ENDPOINT_ADD="http://157.159.11.199:5000/add_config"
API_ENDPOINT_RM="http://157.159.11.199:5000/remove_config"
CLIENT_NAME="$2"
SERVER_ADDR="$3:22"
PORT="$4"

# Vérifier si c'est une opération d'ajout ou de suppression
if [ "$USAGE" = "add" ]; then
    # Faire la requête avec curl pour l'ajout
    curl -X POST -H "Content-Type: application/json" -d '{"client_name":"'"$CLIENT_NAME"'", "port":"'"$PORT"'", "ip":"'"$SERVER_ADDR"'"}' "$API_ENDPOINT_ADD"
elif [ "$USAGE" = "rm" ]; then
    # Faire la requête avec curl pour la suppression
    curl -X DELETE -H "Content-Type: application/json" -d '{"client_name":"'"$CLIENT_NAME"'"}' "$API_ENDPOINT_RM"
else
    echo "Usage: $0 <add|rm> <client_name> <server_addr> [<port>]"
    exit 1
fi
