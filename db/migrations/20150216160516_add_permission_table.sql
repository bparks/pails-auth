create table permissions (
  id integer not null auto_increment primary key,
  user_id integer not null,
  permission text not null
);