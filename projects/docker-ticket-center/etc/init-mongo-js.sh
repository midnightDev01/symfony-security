#!/bin/bash

mongosh <<EOF
db = connect("localhost:27017","admin","changeme");
use myra-app;
db.createCollection("cars")
EOF