use studio;

drop table if exists admins;

create table admins (
  id int not null primary key AUTO_INCREMENT comment 'ID',
  email varchar(255) not null comment 'メールアドレス',
  password varchar(255) not null comment 'パスワード',
  create_date_time datetime not null default CURRENT_TIMESTAMP comment '登録日時',
  update_date_time datetime not null default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP comment '更新日時'
) engine = InnoDB default charset = utf8mb4 comment = '管理者情報';

insert into
  admins
 (email, password)
values
  ("sample@test.com", "$2y$10$02zyQar6lOZvjBxa/8jZ2uRJJS.qWFBeZWWAHu1KfR4TEJMEFpwwq");