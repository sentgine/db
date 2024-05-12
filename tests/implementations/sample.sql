SELECT t1.col1, t2.col2, t3.col3
	
FROM
    t1
	JOIN  t2.col6 = t1.col8
	JOIN  t3.col3 = t2.col1	
WHERE
	    [CONDITION]    
	AND [CONDITION]
	AND 
	 (
	     [CONDITION]   true
	   OR
		[CONDITION]   false
	)
	AND 
	 (
	     [CONDITION]   true
	   AND
		[CONDITION]   false
	)
	AND  p.id_entitypage in (  subquery )
LIMIT 0, 20;



SELECT c.id, c.name	
FROM
   criminals as c 	
WHERE
	(
		(  
		   c.is_active = 1 
           AND 
           c.birthdate BETWEEN 'asdfaf' and 'ASDFASFDF'
		)
		AND 
		(
			c.name = "mit"
			OR c.name = "adrian"
			OR c.name = "dodie"
		)
		AND
		c.state = ""
	)
LIMIT 0, 20;







SELECT p.* 
	FROM product_info as p
	
	 WHERE 
	 (   
		  (        p.product_name LIKE  '%block%'
		      AND  p.product_name LIKE  '%code%'  
		  )
		  AND 
		  (
		      CONDITION 
			  OR 
			  CONDITION 
		  )
	 )   
	 GROUP BY p.id_product_info  ORDER BY  rand() 
	LIMIT 0 , 8;




	
CLASS

SET
GET

NEWRECORD  (INSERT)

SAVE      (UPDATE)



During runtime ,  the ID should be usable, after insert.




SELECT name, salary, department
FROM employees
ORDER BY (
    SELECT AVG(salary) 
    FROM employees AS emp 
    WHERE emp.department = employees.department
) DESC
,
(
    SELECT AVG(salary) 
    FROM employees AS emp 
    WHERE emp.department = employees.department
) ASC

;


implode(", ", $arr)


SELECT
FROM
WHERE
GROUP BY 
HAVING  ()  and (   )
LIMIT 