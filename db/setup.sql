DROP DATABASE IF EXISTS influx;
CREATE DATABASE influx;

GRANT USAGE ON *.* TO 'influx'@'localhost';
DROP USER 'influx'@'localhost';

CREATE USER 'influx'@'localhost' IDENTIFIED BY 'influx';
GRANT SELECT, INSERT, UPDATE, DELETE, LOCK TABLES ON influx.* TO 'influx'@'localhost';

USE influx;
