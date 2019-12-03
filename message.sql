-- add type
INSERT INTO type (`tid`, `type_name`)
VALUES
    (1, "friend"),
    (2, "neighbor"),
    (3, "all friends"),
    (4, "entire block"),
    (5, "entire hood");


-- Friend Message
-- 1 send message to 2
INSERT INTO message (`mid`, `title`, `subject`, `uid`, `text_body`, `lantitude`, `longitude` , `tid`)
VALUES (1, "My First Message to friend", "say hello", 1, "Hello everyone, I am Jia Shi.", 41.40338, 2.17403, 1);


INSERT INTO receive_msg (`mid`, `uid`, `is_read`)
VALUES 
    (1, 1, 1),
    (1, 2, 0);

-- 2 reply to 1
UPDATE receive_msg
SET is_read = 1
WHERE uid = 2 AND mid = 1;

INSERT INTO reply (`rid`, `mid`, `uid`, `content`)
VALUES (1, 1, 2, "Nice to meet you Jia!");

INSERT INTO receive_reply (`rid`, `uid`, `is_read`)
VALUES 
    (1, 1, 0),
    (1, 2, 1);


-- 1 reply to 2
UPDATE receive_reply rr, reply r
SET rr.is_read = 1
WHERE rr.rid = r.rid AND r.mid = 1 AND rr.uid = 1;

INSERT INTO reply (`rid`, `mid`, `uid`, `content`)
VALUES (2, 1, 1, "Nice to meet you too!");

INSERT INTO receive_reply (`rid`, `uid`, `is_read`)
VALUES 
    (2, 1, 1),
    (2, 2, 0);



-- Neighbor Message
-- 2 send to 3
INSERT INTO message (`mid`, `title`, `subject`, `uid`, `text_body`, `lantitude`, `longitude` , `tid`)
VALUES (2, "My First Message to neighbor", "say hello", 2, "Hello neighbor, I am Haochen.", 41.40338, 2.17403, 2);

INSERT INTO receive_msg (`mid`, `uid`, `is_read`)
VALUES 
    (2, 2, 1),
    (2, 3, 0);

-- 3 reply to 2
UPDATE receive_msg
SET is_read = 1
WHERE uid = 3 AND mid = 2;

INSERT INTO reply (`rid`, `mid`, `uid`, `content`)
VALUES (3, 2, 2, "Nice to meet you, Haochen! I'm Mary.");

INSERT INTO receive_reply (`rid`, `uid`, `is_read`)
VALUES 
    (3, 2, 0),
    (3, 3, 1);

-- 2 reply to 3
UPDATE receive_reply rr, reply r
SET rr.is_read = 1
WHERE rr.rid = r.rid AND r.mid = 2 AND rr.uid = 2;

INSERT INTO reply (`rid`, `mid`, `uid`, `content`)
VALUES (4, 2, 2, "Nice to meet you too!");

INSERT INTO receive_reply (`rid`, `uid`, `is_read`)
VALUES 
    (4, 2, 1),
    (4, 3, 0);

-- All Friends Message

-- 先找到所有已经和1成为朋友的人, 找到uid为 2, 4
SELECT friend_uid FROM friend WHERE uid = 1 AND is_valid = 1;

-- 1 send to all friends
INSERT INTO message (`mid`, `title`, `subject`, `uid`, `text_body`, `lantitude`, `longitude` , `tid`)
VALUES (3, "Buy a dog", "sell and buy", 1, "Hello, I want to buy a dog. Do you know someone who wanna sell one?", 41.40338, 2.17403, 3);

INSERT INTO receive_msg (`mid`, `uid`, `is_read`)
VALUES 
    (3, 1, 1),
    (3, 2, 0),
    (3, 4, 0);

-- 2 reply 
UPDATE receive_msg
SET is_read = 1
WHERE uid = 2 AND mid = 3;

UPDATE receive_reply rr, reply r
SET rr.is_read = 1
WHERE rr.rid = r.rid AND r.mid = 3 AND rr.uid = 2;


INSERT INTO reply (`rid`, `mid`, `uid`, `content`)
VALUES (5, 3, 2, "Sorry, I don't know.");

INSERT INTO receive_reply (`rid`, `uid`, `is_read`)
VALUES 
    (5, 1, 0),
    (5, 2, 1),
    (5, 4, 0);


-- Block Message
-- 1 to block 先找到所有在这个block的人, 找到user id 为 1, 2, 3, 4, 7的人
SELECT uid FROM join_block WHERE bid = 1;

INSERT INTO message (`mid`, `title`, `subject`, `uid`, `text_body`, `lantitude`, `longitude` , `tid`)
VALUES (4, "Introduce my self", "introduce", 1, "Hello, I'm jia. I'm new to this block.", 41.40338, 2.17403, 4);

INSERT INTO receive_msg (`mid`, `uid`, `is_read`)
VALUES 
    (4, 1, 1),
    (4, 2, 0),
    (4, 3, 0),
    (4, 4, 0),
    (4, 7, 0);

-- 4 reply
UPDATE receive_msg
SET is_read = 1
WHERE uid = 4 AND mid = 4;

UPDATE receive_reply rr, reply r
SET rr.is_read = 1
WHERE rr.rid = r.rid 
	AND r.mid = 4 
	AND rr.uid = 4;

INSERT INTO reply (`rid`, `mid`, `uid`, `content`)
VALUES (6, 3, 2, "Hi, I am Lily. Welcome to our block.If you have any concerns, feel free to contact me.");

INSERT INTO receive_reply (`rid`, `uid`, `is_read`)
VALUES 
    (6, 1, 0),
    (6, 2, 0),
    (6, 3, 0),
    (6, 4, 1),
    (6, 7, 0);


-- 1 reply
UPDATE receive_reply rr, reply r
SET rr.is_read = 1
WHERE rr.rid = r.rid
	AND r.mid = 4 
	AND rr.uid = 4;

INSERT INTO reply (`rid`, `mid`, `uid`, `content`)
VALUES (7, 3, 2, "Hi, Lily. Thank you so much!");

INSERT INTO receive_reply (`rid`, `uid`, `is_read`)
VALUES 
    (7, 1, 0),
    (7, 2, 0),
    (7, 3, 0),
    (7, 4, 1),
    (7, 7, 0);

-- hood Message
-- 6 to hood 先找到所有在这个hood的人, 找到user id 为 1, 2, 3, 4, 5, 6, 7的人
SELECT uid FROM join_block jb, block b
WHERE jb.bid = b.bid AND b.hid = 1;

INSERT INTO message (`mid`, `title`, `subject`, `uid`, `text_body`, `lantitude`, `longitude` , `tid`)
VALUES (5, "I want to sell my laptop.", "sell and buy", 6, "Hello, I'm Jack. I want to sell my labtop. It is brand new. If you want it. Please call me at 3777777777.", 41.40338, 2.17403, 5);

INSERT INTO receive_msg (`mid`, `uid`, `is_read`)
VALUES (5, 1, 0),
    (5, 2, 0),
    (5, 3, 0),
    (5, 4, 0),
    (5, 5, 0),
    (5, 6, 1),
    (5, 7, 0);

-- 1 reply
UPDATE receive_msg
SET is_read = 1
WHERE uid = 6 AND mid = 5;

UPDATE receive_reply rr, reply r
SET rr.is_read = 1
WHERE rr.rid = r.rid
	AND r.mid = 5 
	AND rr.uid = 6;

INSERT INTO reply (`rid`, `mid`, `uid`, `content`)
VALUES (8, 3, 2, "Hi, Jack. How much is your laptop?");

INSERT INTO receive_reply (`rid`, `uid`, `is_read`)
VALUES 
    (8, 1, 1),
    (8, 2, 0),
    (8, 3, 0),
    (8, 4, 0),
    (8, 5, 0),
    (8, 6, 0),
    (8, 7, 0);


-- bicycle accident
INSERT INTO message (`mid`, `title`, `subject`, `uid`, `text_body`, `lantitude`, `longitude` , `tid`)
VALUES (6, "Bicycle accident", "accident", 1, "A bicycle accident happened in downtown brooklyn.", 41.40338, 2.17403, 5);

INSERT INTO receive_msg (`mid`, `uid`, `is_read`)
VALUES 
    (6, 1, 1),
    (6, 2, 0),
    (6, 3, 0),
    (6, 4, 0),
    (6, 5, 0),
    (6, 6, 0),
    (6, 7, 0);

-- other data
INSERT INTO message (`mid`, `title`, `subject`, `uid`, `text_body`, `lantitude`, `longitude` , `tid`)
VALUES (7, "My Second Message to neighbor", "say hello", 2, "Hello neighbor, I am Haochen.", 41.40338, 2.17403, 2);

INSERT INTO receive_msg (`mid`, `uid`, `is_read`)
VALUES 
    (7, 2, 1),
    (7, 3, 0);

-- msg_setting
INSERT INTO msg_setting (`uid`, `tid`)
VALUES (1, 1),(1, 2),(1, 3),(1, 4),(1, 5);
