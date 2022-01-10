use studio;

drop table if exists reservations;

create table reservations (
  id int not null primary key AUTO_INCREMENT comment 'ID',
  member_id int not null comment '会員ID',
  reservation_date date not null comment '予約日',
  fee_type varchar(100) not null comment '料金種類',
  discount_type varchar(100) not null comment '割引種類',
  usage_fee int not null comment '利用料金',
  is_cancel tinyint not null default '0' comment 'キャンセル',
  create_date_time datetime not null default CURRENT_TIMESTAMP comment '登録日時',
  update_date_time datetime not null default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP comment '更新日時'
) engine = InnoDB default charset = utf8mb4 comment = '予約情報';