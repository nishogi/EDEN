#!/bin/bash

# Configuration
KEY="ddns-key2"
ZONE="atlantis.int-evry.fr"
SERVER="157.159.11.3"
DB_FILE="db"

# Fonctions utilitaires
function add_entry {
    DOMAIN="$1"
    IP="$2"
    ENTRY="${DOMAIN}.${ZONE} 3600 IN A ${IP}"
    echo "$DOMAIN $IP" >> "$DB_FILE"
    echo "Added $DOMAIN with IP $IP to $DB_FILE"
    nsupdate_entry "add" "$ENTRY"
}

function delete_entry {
    DOMAIN="$1"
    IP="$2"
    ENTRY="${DOMAIN}.${ZONE} 3600 IN A ${IP}"
    sed -i "/^$DOMAIN $IP$/d" "$DB_FILE"
    echo "Deleted $DOMAIN with IP $IP from $DB_FILE"
    nsupdate_entry "delete" "$ENTRY"
}

function nsupdate_entry {
    ACTION="$1"
    ENTRY="$2"
    echo "server ${SERVER}" > /tmp/nsupdate.tmp
    echo "update ${ACTION} ${ENTRY}" >> /tmp/nsupdate.tmp
    echo "send" >> /tmp/nsupdate.tmp
    nsupdate -k ${KEY} -v /tmp/nsupdate.tmp
}

# Main logic
if [ "$1" == "add" ]; then
    DOMAIN="$2"
    IP="$3"
    add_entry "$DOMAIN" "$IP"
elif [ "$1" == "delete" ]; then
    DOMAIN="$2"
    IP="$3"
    delete_entry "$DOMAIN" "$IP"
else
    echo "Usage: $0 [add|delete] <domain> <IP>"
    exit 1
fi
