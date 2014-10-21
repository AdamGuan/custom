#!/bin/bash
mysql -uroot -p66978fa501 -e"drop database custom"
mysql -uroot -p66978fa501 -e"create database custom"

mysql -uroot -p66978fa501 custom -e"create table custom.t_captcha (
  F_captcha_id int(10) unsigned AUTO_INCREMENT COMMENT '主键',
  F_captcha_time char(15) NOT NULL COMMENT '创建时间',
  F_word char(4) NOT NULL,
  F_ip_address varchar(15) NOT NULL,
  PRIMARY KEY (F_captcha_id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='验证码'"

mysql -uroot -p66978fa501 custom -e"CREATE TABLE custom.t_custom (
  F_id int(10) unsigned NOT NULL AUTO_INCREMENT,
  F_custom_id VARCHAR(50) NOT NULL COMMENT '客服ID',
  F_custom_name varchar(250) NOT NULL COMMENT '客服用户名',
  F_custom_nickname VARCHAR(250) NOT NULL COMMENT '客服昵称',
  F_custom_pwd VARCHAR(100) NOT NULL COMMENT '客服密码',
  F_custom_createtime DATETIME NOT NULL COMMENT '客服创建时间',
  F_custom_modifytime DATETIME NOT NULL COMMENT '客服修改时间',
  F_groupid INT(10) NOT NULL COMMENT '所属组ID',
  F_custom_key VARCHAR(250) NOT NULL COMMENT '客服key',
  PRIMARY KEY (F_id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='客服表(有与环信关联)'"

mysql -uroot -p66978fa501 custom -e"CREATE TABLE custom.t_custom_score (
  F_id int(10) unsigned NOT NULL AUTO_INCREMENT,
  F_from varchar(250) NOT NULL COMMENT '学生用户名',
  F_to varchar(250) NOT NULL COMMENT '客服用户名',
  F_score float NOT NULL COMMENT '分数',
  F_content text NOT NULL COMMENT '评价内容',
  F_createtime DATETIME NOT NULL COMMENT '评价时间',
  PRIMARY KEY (F_id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='客户评分表'"

mysql -uroot -p66978fa501 custom -e"CREATE TABLE custom.t_role (
  F_role_id int(10) NOT NULL AUTO_INCREMENT,
  F_role_name varchar(50) NOT NULL COMMENT '角色名',
  F_role_privilege TEXT NOT NULL COMMENT '权限(例如: "all"    "index/index,login/index")',
  F_groupid INT(10) NOT NULL COMMENT '所属组ID',
  PRIMARY KEY (F_role_id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='角色'"

mysql -uroot -p66978fa501 custom -e"CREATE TABLE custom.t_user (
  F_user_id int(10) unsigned NOT NULL AUTO_INCREMENT,
  F_user_login_name varchar(50) NOT NULL COMMENT '登录名',
  F_user_login_pwd char(40) NOT NULL COMMENT '登录密码',
  F_user_nickname varchar(50) NOT NULL COMMENT '用户昵称',
  F_role_id int(10) unsigned NOT NULL COMMENT '角色ID',
  F_group_id INT(10) UNSIGNED NOT NULL COMMENT '组ID',
  F_create_time datetime NOT NULL COMMENT '创建时间',
  F_update_time datetime NOT NULL COMMENT '更新时间',
  F_user_key VARCHAR(250) NOT NULL COMMENT '用户key',
  PRIMARY KEY (F_user_id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='用户表'"

mysql -uroot -p66978fa501 custom -e"CREATE TABLE custom.t_student (
  t_id int(10) unsigned NOT NULL AUTO_INCREMENT,
  t_student_name varchar(100) NOT NULL COMMENT '登录名',
  t_student_pwd char(100) NOT NULL COMMENT '登录密码',
  t_status TINYINT(1) NOT NULL DEFAULT '1' COMMENT '状态(0:无效，1有效)',
  t_student_key VARCHAR(250) NOT NULL COMMENT '用户key',
  PRIMARY KEY (t_id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='学生表'"

mysql -uroot -p66978fa501 custom -e"CREATE TABLE custom.t_group (
  F_id INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  F_groupname VARCHAR(100) NOT NULL COMMENT '组名',
  PRIMARY KEY (F_id),
  UNIQUE INDEX F_groupname (F_groupname)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='群组'"

mysql -uroot -p66978fa501 custom -e"CREATE TABLE custom.t_custom_relation (
  F_id INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  F_custom_name VARCHAR(250) NOT NULL COMMENT '客服用户名',
  F_student_name VARCHAR(250) NOT NULL COMMENT '学生用户名',
  F_add_time DATETIME NOT NULL COMMENT '添加时间',
  PRIMARY KEY (F_id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='客服对用户的关系表'"

for i in {0..99}; do
  if [ "$i" -lt "10" ]; then
    i="0"$i
  fi  
  mysql -uroot -p66978fa501 custom -e"CREATE TABLE custom.t_msg_custom_receive_$i (
  F_id int(10) unsigned NOT NULL AUTO_INCREMENT,
  F_from_name varchar(250) NOT NULL COMMENT '学生用户名',
  F_to_name varchar(250) NOT NULL COMMENT '客服用户名',
  F_msg_id varchar(20) NOT NULL COMMENT '环信msg_id',
  F_timestamp char(13) NOT NULL COMMENT '消息时间戳',
  PRIMARY KEY (F_id)
  ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='客服接收消息记录表(根据to_name分表,即:客服用户名)'"

  mysql -uroot -p66978fa501 custom -e"CREATE TABLE custom.t_msg_custom_send_$i (
  F_id int(10) unsigned NOT NULL AUTO_INCREMENT,
  F_from_name varchar(250) NOT NULL COMMENT '客服用户名',
  F_to_name varchar(250) NOT NULL COMMENT '学生用户名',
  F_msg_id varchar(20) NOT NULL COMMENT '环信msg_id',
  F_timestamp char(13) NOT NULL COMMENT '消息时间戳',
  PRIMARY KEY (F_id)
  ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='客服发送消息记录表(根据from_name分表,即:客服用户名)'"
done

for i in {2014..2019}; do
  for j in {1..12}; do
    if [ "$j" -lt "10" ]; then
      j="0"$j
    fi
    mysql -uroot -p66978fa501 custom -e"CREATE TABLE custom.t_msg_stduent_send_$i$j (
    F_id int(10) unsigned NOT NULL AUTO_INCREMENT,
    F_from_name varchar(250) NOT NULL COMMENT '学生用户名',
    F_to_name varchar(250) NOT NULL COMMENT '客服用户名',
    F_msg_id varchar(20) NOT NULL COMMENT '环信msg_id',
    F_timestamp char(13) NOT NULL COMMENT '消息时间戳',
    PRIMARY KEY (F_id)
    ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='学生发送消息记录表(根据消息时间分表)'"
  done
done
