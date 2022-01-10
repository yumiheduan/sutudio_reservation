use studio;

drop table if exists time_tables;

create table time_tables (
  id int not null primary key AUTO_INCREMENT comment 'ID',
  reservation_id int not null comment '予約ID',
  member_id int not null comment '会員ID',
  reservation_date date not null comment '予約日',
  start_time char(2) not null comment '開始時間',
  room character(3) not null comment 'スタジオA、B',
  create_date_time datetime not null default CURRENT_TIMESTAMP comment '登録日時',
  update_date_time datetime not null default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP comment '更新日時'
) engine = InnoDB default charset = utf8mb4 comment = '予約時間割';