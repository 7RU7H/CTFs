#!/bin/bash

mkdir tables-dump
allTables=$(cat $1)
for table in $allTables; do
	mdb-array backup.mdb $table > tables-dump/$table.c
done
