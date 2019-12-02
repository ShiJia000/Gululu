
INSERT INTO USER (`firstname`, `lastname`, `user_pwd`, `state`, `city`, `zipcode`, `address`, `phone_num`, `photo`, `self_intro`, `family_intro`, `profile_timestamp`, `email`)
VALUES ('Jia', 'Shi', 'psw', 'New York', 'Brooklyn', 11201,'111 Street', 9491118218,'jia.jpg', null, null, '2019-11-01 13:10:11', 'js11182@nyu.edu'),
VALUES ('Haochen','Zhou', 'nice111','New York', 'Brooklyn',11201,'123 Street', 1314411011, null, null, null, '2019-11-02 09:10:11', 'hz2204@nyu.edu'),
VALUES ('Mary','Brown', '233','New York', 'Brooklyn',11201,'132 Street', 2123228202, null, null, null,'2019-11-20 09:10:11','mb@nyu.edu'),
VALUES ('Lily','Brown', 'ni121e','New York', 'Brooklyn',11201,'121 Street', 333333333, null, null, 'Mary Brown is my mother','2019-11-21 09:10:,11', 'lb1@nyu.edu'),
VALUES ('Lisa','Zhou', '2hdsk','New York', 'Brooklyn',11211,'40 Avenue', 323433033, null, null, null,'2019-12-02 09:10:,11', 'lz@nyu.edu'),
VALUES ('Jack','Lee', '23ls1','New York', 'Brooklyn',11211,'41 Avenue', 333139303, null, null, null,'2019-11-28 09:10:,11', 'jl@nyu.edu'),
VALUES ('Muimui','Choing','jdk13','New York', 'Brooklyn',11201,'199 Street',3248923891,null,null,null,'2019-12-01 09:10:,11','mc@nyu.edu');

INSERT INTO neighbor (`uid`, `neighbor_uid`, `is_valid`)
VALUES (2,3,1),
VALUES (3,2,1);

INSERT INTO friend (`uid`, `friend_uid`, `is_valid`)
VALUES (1,2,1),
VALUES (2,1,1),
VALUES (4,5,0);

INSERT INTO hood (`hid`,`hname`)
VALUES ('DWTN Brooklyn'),
VALUES ('Manhattan');


INSERT INTO block (`hid`, `bname`)
VALUES (1,'100-200 Street'),
VALUES (1,'10-45 Avenue'),
VALUES (1, '300-400 Street'),
VALUES (2, '14 Street');

--If a person is aproved to be added in a block, is_approved will be 1. Otherwise, wil be 0.
--For the first person in a block, his/her is_approved defaults to 1 and approve_num defaults to 0.
INSERT INTO join_block(`joinid`, `uid`, `bid`, `is_approved`, `approve_num`)
VALUES (1, 1, 1, 0),
VALUES (2, 1, 1, 1),
VALUES (3, 1, 1, 2),
VALUES (4, 1, 1, 3),
VALUES (5, 2, 1, 0),
VALUES (6, 2, 1, 1)
VALUES (7, 1, 1, 3);

--if a person is moved out
--add a new line --> VALUES (8, 4, 3, xx, xx),


INSERT INTO agree_join (`uid`, `joinid`, `is_agree`)
VALUES (1, 2, 1),
VALUES (1, 3, 1),
VALUES (2, 3, 1),
VALUES (1, 4, 1),
VALUES (2, 4, 1),
VALUES (3, 4, 1),
VALUES (5, 6, 1),
VALUES (1, 7, 1),
VALUES (2, 7, 1),
VALUES (3, 7, 1),
VALUES (4, 7, 0);



INSERT INTO message (`mid`, `title`, `subject`, `uid`, `text_body`, `timestamp`, `latitude`, `longitude`, `tid`)
VALUES ();

INSERT INTO receive_msg (`mid`, `uid`, `is_read`, `read_timestamp`)
VALUES ();

INSERT INTO reply (`rid`, `mid`, `uid`, `reply_timestamp`, `content`)
VALUES ();

INSERT INTO receive_reply (`rid`, `uid`, `is_read`, `read_timestamp`)
VALUES ();








