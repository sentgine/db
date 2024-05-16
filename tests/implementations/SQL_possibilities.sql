/*  
ALL THE POSSIBLE SQL's listed here should be possible on the Query Builder!
Please don't mind table names and columns. 
   */


SELECT *
from users;

SELECT *
from `users`;

SELECT col1,col2
from `users`;

SELECT `col1`,`col2`
from `users`;

SELECT t1.`col1`, t1.`col2`
from `users` as t1;

SELECT `col1`,`col2`
from `users` as t1;

SELECT t1.`colA`, t1.`colB`,  t1.`colA`,t1.`colB`,
from `table1` as t1
    JOIN table2 as t2 ON t1.colH = t2.colH
;

SELECT t1.`colA`, t1.`colB`, "test string" as `display1`
from `table1` as t1
    JOIN table2 as t2 ON t1.colH = t2.colH
;

SELECT t1.`colA`, t1.`colB` 
from `table1` as t1
WHERE  (
        1 = 1  OR 2 = 1
    )
    AND
    (    0 = 1    )
;



