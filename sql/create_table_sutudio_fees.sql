use studio;

drop table if exists studio_fees;

create table studio_fees (
  id int not null primary key AUTO_INCREMENT comment 'ID',
  fee_type varchar(50) not null comment '料金種類',
  fees int not null comment '料金',
  fee_name varchar(50) not null comment '料金名称',
  create_date_time datetime not null default CURRENT_TIMESTAMP comment '登録日時',
  update_date_time datetime not null default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP comment '更新日時'
) engine = InnoDB default charset = utf8mb4 comment = '料金情報';

insert into
  studio_fees (fee_type, fees, fee_name)
values
  ('a_1018', '1900', 'Aスタジオ 平日10～18時'),
  ('a_1824', '2200', 'Aスタジオ 平日18～24時'),
  ('a_holiday', '2200', 'Aスタジオ 土日祝'),
  ('b_1018', '1800', 'Bスタジオ 平日10～18時'),
  ('b_1824', '2000', 'Bスタジオ 平日18～24時'),
  ('b_holiday', '2000', 'Bスタジオ 土日祝'),
  ('student', '1600', '高校生料金 A・Bスタジオ 全日時共通');