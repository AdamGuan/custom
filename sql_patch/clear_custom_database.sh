#!/bin/bash
mysql -uroot -p66978fa501 -e"TRUNCATE custom.t_custom"
mysql -uroot -p66978fa501 -e"TRUNCATE custom.t_captcha"
mysql -uroot -p66978fa501 -e"TRUNCATE custom.t_custom_score"
mysql -uroot -p66978fa501 -e"TRUNCATE custom.t_student"
mysql -uroot -p66978fa501 -e"TRUNCATE custom.t_custom_relation"

for i in {0..99}; do
  if [ "$i" -lt "10" ]; then
    i="0"$i
  fi  
  mysql -uroot -p66978fa501 -e"TRUNCATE custom.t_msg_custom_receive_$i"
  mysql -uroot -p66978fa501 -e"TRUNCATE custom.t_msg_custom_send_$i"
done

for i in {2014..2019}; do
  for j in {1..12}; do
    if [ "$j" -lt "10" ]; then
      j="0"$j
    fi
    mysql -uroot -p66978fa501 -e"TRUNCATE custom.t_msg_stduent_send_$i$j"
  done
done
