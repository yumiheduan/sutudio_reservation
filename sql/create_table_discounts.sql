use studio;

drop table if exists discounts;

create table discounts (
  id int not null primary key AUTO_INCREMENT comment 'ID',
  discount_type varchar(50) not null comment '割引種類',
  discount_rate float not null comment '掛率',
  discount_name varchar(50) not null comment '割引名称',
  discount_rate_name varchar(50) not null comment '掛率名称',
  create_date_time datetime not null default CURRENT_TIMESTAMP comment '登録日時',
  update_date_time datetime not null default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP comment '更新日時'
) engine = InnoDB default charset = utf8mb4 comment = '割引情報';

insert into
  discounts (
    discount_type,
    discount_rate,
    discount_name,
    discount_rate_name
  )
values
  ('no_discount', '1', '割引なし', ''),
  ('3hours', '0.9', '3時間以上割', '10%OFF'),
  ('3piece', '0.9', '3ピース割', '10%OFF'),
  ('few_count_1', '0.25', '個人練習割1人', '75%OFF'),
  ('few_count_2', '0.5', '個人練習割2人', '50%OFF'),
  ('few_count_3', '0.75', '個人練習割3人', '25%OFF');