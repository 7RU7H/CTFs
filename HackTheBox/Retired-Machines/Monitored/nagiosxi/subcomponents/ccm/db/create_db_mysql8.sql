CREATE DATABASE `nagiosql` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;

CREATE USER 'nagiosql'@'localhost' IDENTIFIED BY 'n@gweb';

GRANT USAGE ON *.* TO 'nagiosql'@'localhost';

GRANT ALL PRIVILEGES ON `nagiosql`.* TO 'nagiosql'@'localhost' WITH GRANT OPTION;

GRANT PROCESS ON *.* TO 'nagiosql'@'localhost';

ALTER USER 'nagiosql'@'localhost' WITH MAX_QUERIES_PER_HOUR 0 MAX_CONNECTIONS_PER_HOUR 0 MAX_UPDATES_PER_HOUR 0 MAX_USER_CONNECTIONS 0;
