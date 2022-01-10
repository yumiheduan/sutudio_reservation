use studio;

drop table if exists cancels;

create table cancels (
  id int not null primary key AUTO_INCREMENT comment 'ID',
  reservation_id int not null comment '予約ID',
  cancel_fee int not null comment 'キャンセル料',
  is_received tinyint not null default '0' comment '領収',
  create_date_time datetime not null default CURRENT_TIMESTAMP comment '登録日時',
  update_date_time datetime not null default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP comment '更新日時'
) engine = InnoDB default charset = utf8mb4 comment = 'キャンセル情報';