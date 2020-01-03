-- create database open_court;

-- 試合情報
-- drop table game_info; 
create table game_info (
    id serial primary key
    , game_date date
    , start_time varchar(10)
    , end_time varchar(10)
    , place varchar(50)
);

-- テストデータ
insert into game_info (game_date, start_time, end_time, place) values ('2020-01-01', '17:00', '19:00', '那覇市民体育館');

-- 参加者
create table participant (
    id serial primary key
    , game_id int
    , occupation int   -- 1：社会、2：大学生、3：高校生
    , sex int -- 1：男、2：女
    , name varchar(50)
);

-- テストデータ
insert into participant (game_id, occupation, sex, name) values (1, 1, 1, 'aaa');
insert into participant (game_id, occupation, sex, name) values (1, 2, 1, 'bbb');
insert into participant (game_id, occupation, sex, name) values (1, 1, 2, 'ccc');

-- 参加者確認用ビュー
-- drop view v_participant;
create view v_participant as 
(select 
  max(g.id) game_id
, max(game_date) game_date
, max(start_time) start_time
, max(end_time) end_time
, max(place) place
, count(*) count
, sum(
    case 
        when occupation = 1 and sex = 1 then 1
        else 0
    end
) sya_men  -- 社会人男
,  sum(
    case 
        when occupation = 1 and sex = 2 then 1
        else 0
    end
) sya_women  -- 社会人女
, sum(
    case 
        when occupation = 2 and sex = 1 then 1
        else 0
    end
) dai_men  -- 大学生男
,  sum(
    case 
        when occupation = 2 and sex = 2 then 1
        else 0
    end
) dai_women  -- 大学生女
, sum(
    case 
        when occupation = 3 and sex = 1 then 1
        else 0
    end
) kou_men  -- 高校生男
,  sum(
    case 
        when occupation = 3 and sex = 2 then 1
        else 0
    end
) kou_women  -- 高校生女
  from game_info g
  inner join participant p
  on g.id = p.game_id
  group by g.id);