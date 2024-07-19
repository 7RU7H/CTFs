CREATE DATABASE `nagios` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
CREATE USER 'ndoutils'@'localhost' IDENTIFIED BY 'n@gweb';
GRANT ALL PRIVILEGES ON `nagios`.* TO 'ndoutils'@'localhost' WITH GRANT OPTION;
GRANT PROCESS ON *.* TO 'ndoutils'@'localhost';
