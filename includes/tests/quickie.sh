#!/bin/bash

for f in *.php
do
	echo -e "\033[33;34m -- Running file $f: \033[0m"
	php $f | grep "PHP"
done

