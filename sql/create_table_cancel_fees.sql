use studio;

drop table if exists cancel_fees;

create table cancel_fees (
  id int not null primary key AUTO_INCREMENT comment 'ID',
  what_days_ago int not null comment '日数',
  cancel_markup_rate float not null comment '掛率',
  cancel_name varchar(50) not null comment 'キャンセル名称',
  cancel_rate_name varchar(50) not null comment '掛率名称',
  create_date_time datetime not null default CURRENT_TIMESTAMP comment '登録日時',
  update_date_time datetime not null default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP comment '更新日時'
) engine = InnoDB default charset = utf8mb4 comment = 'キャンセル料';

insert into
  cancel_fees (
    what_days_ago,
    cancel_markup_rate,
    cancel_name,
    cancel_rate_name
  )
values
  (0, 1, '当日', '100%'),
  (1, 0.65, '前日', '65%'),
  (2, 0.6, '2日前', '60%'),
  (3, 0.55, '3日前', '55%'),
  (4, 0.5, '4日前', '50%'),
  (5, 0.45, '5日前', '45%'),
  (6, 0.4, '6日前', '40%'),
  (7, 0, '7日前', '無料');