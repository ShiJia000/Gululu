-- view all message

-- From friend;
SELECT m.*
FROM receive_msg rm, message m
WHERE rm.mid = m.mid
	AND rm.uid = 2
	AND (m.tid = 3 or m.tid = 1);

-- From neighbor
SELECT m.*
FROM receive_msg rm, message m
WHERE rm.mid = m.mid
	AND rm.uid = 3
	AND m.tid = 2;

-- From block
SELECT m.*
FROM receive_msg rm, message m
WHERE rm.mid = m.mid
	AND rm.uid = 3
	AND m.tid = 4;

-- From hood
SELECT m.*
FROM receive_msg rm, message m
WHERE rm.mid = m.mid
	AND rm.uid = 3
	AND m.tid = 5;