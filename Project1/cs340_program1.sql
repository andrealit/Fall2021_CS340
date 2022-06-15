-- Program 1
-- Andrea Tongsak
-- CS340 Fall 2021
-- URL: http://web.engr.oregonstate.edu/~tongsaka/cs340/index.php

-- 1. Create the table DEPT_STATS
create table DEPT_STATS (
  Dnumber int(2) not null,
  Emp_count int(11) not null,
  Avg_salary decimal(10,2) not null,
  primary key (Dnumber)
  foreign key (Dnumber) references DEPARTMENT(Dnumber)
)ENGINE = INNODB;

-- 2. Write a procedure called InitDeptStats
create procedure `InitDeptStats` ()
  BEGIN
    -- initialize values in the table use average function
    UPDATE DEPT_STATS
    SET Avg_salary = (SELECT AVG(Salary) FROM EMPLOYEE E WHERE E.Dno = DEPT_STATS.Dnumber);
  END

-- 3. Write triggers for the EMPLOYEE table
CREATE TRIGGER DELETEDeptStats AFTER DELETE ON EMPLOYEE
BEGIN
  -- Emp_count and Avg_salary in DEPT_STATS are changed
  IF OLD.Dno IS NOT NULL THEN
      UPDATE DEPT_STATS
      SET Emp_count = Emp_count - 1
      -- reset the average
      WHERE DEPT_STATS.Dnumber = OLD.Dno;

      UPDATE DEPT_STATS
      SET Avg_salary = (SELECT AVG(Salary) FROM EMPLOYEE E WHERE E.Dno = OLD.Dno)
      WHERE DEPT_STATS.Dnumber = OLD.Dno;
  END IF;
END

CREATE TRIGGER INSERTDeptStats AFTER INSERT ON EMPLOYEE
BEGIN
    -- Emp_count and Avg_salary IN DEPT_STATS are changed
    IF NEW.Dno IS NOT NULL THEN
    	UPDATE DEPT_STATS
        SET Emp_count = Emp_count + 1
        WHERE DEPT_STATS.Dnumber = NEW.Dno;

        UPDATE DEPT_STATS
        SET Avg_salary = (SELECT AVG(Salary) FROM EMPLOYEE E WHERE E.Dno = NEW.Dno)
        WHERE DEPT_STATS.Dnumber = NEW.Dno;
    END IF;
END

CREATE TRIGGER UPDATEDeptStats AFTER UPDATE ON EMPLOYEE
BEGIN
    -- if we edit a user's salary, or Department number, it must be updated
    -- update the old average salary
    IF OLD.Dno IS NOT NULL THEN

        UPDATE DEPT_STATS
        SET Emp_count = Emp_count - 1
        WHERE DEPT_STATS.Dnumber = OLD.Dno;

        UPDATE DEPT_STATS
        SET Avg_salary = (SELECT AVG(Salary) FROM EMPLOYEE E WHERE E.Dno = OLD.Dno)
        WHERE DEPT_STATS.Dnumber = OLD.Dno;

    END IF;

    -- update the new average salary in
    IF (NEW.Dno IS NOT NULL) THEN
        UPDATE DEPT_STATS
        SET Emp_count = Emp_count + 1
        WHERE DEPT_STATS.Dnumber = NEW.Dno;

        UPDATE DEPT_STATS
        SET Avg_salary = (SELECT AVG(Salary) FROM EMPLOYEE E WHERE E.Dno = NEW.Dno)
        WHERE DEPT_STATS.Dnumber = NEW.Dno;
    END IF;
END


-- 4. Trigger called MaxTotalHours
CREATE TRIGGER MaxTotalHours BEFORE INSERT ON WORKS_ON FOR EACH ROW
BEGIN
    DECLARE customMessage varchar(255);
    DECLARE counter decimal;

    SELECT SUM(Hours) INTO counter
        FROM WORKS_ON
        WHERE Essn = New.Essn;

    -- fails if total hours is over 40
    IF ((NEW.Hours + counter) > 40) THEN
        SET customMessage = concat('You entered ', NEW.Hours, '. You currently work ', counter, '. You are over 40 hours.');
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = customMessage;
    END IF;
END


-- 5. Function called PayLevel that returns a level given a Ssn as input
CREATE FUNCTION PayLevel ( Ssn CHAR(9) )
RETURNS VARCHAR(30)
    BEGIN
    declare result varchar(30);

    SELECT case
        WHEN E.Salary > DS.Avg_salary THEN "Above Average"
        WHEN E.Salary < DS.Avg_salary THEN "Below Average"
        ELSE "Average"
     end into result
     FROM EMPLOYEE E, DEPT_STATS DS
     WHERE E.Ssn = Ssn AND E.Dno = DS.Dnumber;

     return result;
END
