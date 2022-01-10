use studio;

drop table if exists members;

create table members (
  id int not null primary key AUTO_INCREMENT comment 'ID',
  kana_name varchar(100) not null comment '氏名かな',
  phone varchar(100) not null comment '電話番号',
  email varchar(255) not null comment 'メールアドレス',
  create_date_time datetime not null default CURRENT_TIMESTAMP comment '登録日時',
  update_date_time datetime not null default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP comment '更新日時'
) engine = InnoDB default charset = utf8mb4 comment = '会員情報';