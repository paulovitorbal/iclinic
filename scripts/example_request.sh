#!/usr/bin/env bash
#remember to check the host address and port
curl -X POST \
  http://webserver/prescriptions \
  -H 'Content-Type: application/json' \
  -d '{
  "clinic": {
    "id": 1
  },
  "physician": {
    "id": 1
  },
  "patient": {
    "id": 1
  },
  "text": "Dipirona 1x ao dia"
}'
