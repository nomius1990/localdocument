#!/bin/bash
while true
do
    if [ -f "/Users/boli/Documents/test/m2.sh" ];then
		echo "正在执行..."
		sh /Users/boli/Documents/test/m2.sh
		rm -f /Users/boli/Documents/test/m2.sh
		echo "执行完毕"
    fi 
done
