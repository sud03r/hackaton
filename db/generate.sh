#! /bin/bash -f

rm -f generated.sql
touch generated.sql
cat setup.sql >> generated.sql
cat movieData.sql >> generated.sql
