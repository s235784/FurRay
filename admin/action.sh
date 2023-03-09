#!/bin/bash
# author: NuoTian

action=$1
passwd=$2
case "$action" in
  "restart") echo "$passwd" | sudo systemctl restart xray
  echo '重启完成'
  ;;
  "sync") echo "$passwd" | sudo php sync.php
  echo "$passwd" | sudo cp test/config.json /usr/local/etc/xray/config.json
  echo "$passwd" | sudo systemctl restart xray
  echo '同步完成'
  ;;
  "init") echo "$passwd" | sudo cp /usr/local/etc/xray/config.json test/config.json
  echo "$passwd" | sudo php init.php
  echo '初始化完成'
  ;;
  *) echo '参数有误！'
  ;;
esac