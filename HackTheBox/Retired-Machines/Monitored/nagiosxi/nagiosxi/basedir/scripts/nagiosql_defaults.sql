/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `tbl_command`
--

DROP TABLE IF EXISTS `tbl_command`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_command` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `command_name` varchar(255) NOT NULL,
  `command_line` text NOT NULL,
  `command_type` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `active` enum('0','1') NOT NULL DEFAULT '1',
  `last_modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `access_rights` varchar(8) DEFAULT NULL,
  `config_id` tinyint(3) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `config_name` (`command_name`,`config_id`)
) ENGINE=MyISAM AUTO_INCREMENT=129 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_command`
--

LOCK TABLES `tbl_command` WRITE;
/*!40000 ALTER TABLE `tbl_command` DISABLE KEYS */;
INSERT INTO `tbl_command` VALUES (1,'notify-host-by-email','/usr/bin/printf \"%b\" \"***** Nagios Monitor XI Alert *****\\n\\nNotification Type: $NOTIFICATIONTYPE$\\nHost: $HOSTNAME$\\nState: $HOSTSTATE$\\nAddress: $HOSTADDRESS$\\nInfo: $HOSTOUTPUT$\\n\\nDate/Time: $LONGDATETIME$\\n\" | /bin/mail -s \"** $NOTIFICATIONTYPE$ Host Alert: $HOSTNAME$ is $HOSTSTATE$ **\" $CONTACTEMAIL$',2,'1','2017-01-06 22:55:59',NULL,1),(2,'notify-service-by-email','/usr/bin/printf \"%b\" \"***** Nagios Monitor XI Alert *****\\n\\nNotification Type: $NOTIFICATIONTYPE$\\n\\nService: $SERVICEDESC$\\nHost: $HOSTALIAS$\\nAddress: $HOSTADDRESS$\\nState: $SERVICESTATE$\\n\\nDate/Time: $LONGDATETIME$\\n\\nAdditional Info:\\n\\n$SERVICEOUTPUT$\" | /bin/mail -s \"** $NOTIFICATIONTYPE$ Service Alert: $HOSTALIAS$/$SERVICEDESC$ is $SERVICESTATE$ **\" $CONTACTEMAIL$',2,'1','2017-01-06 22:55:59',NULL,1),(3,'check-host-alive','$USER1$/check_icmp -H $HOSTADDRESS$ -w 3000.0,80% -c 5000.0,100% -p 5',1,'1','2017-01-06 22:55:59',NULL,1),(4,'check-host-alive-http','$USER1$/check_http -H $HOSTADDRESS$',1,'1','2017-01-06 22:55:59',NULL,1),(5,'check_local_disk','$USER1$/check_disk -w $ARG1$ -c $ARG2$ -p $ARG3$',1,'1','2017-01-06 22:55:59',NULL,1),(6,'check_local_load','$USER1$/check_load -w $ARG1$ -c $ARG2$',1,'1','2017-01-06 22:55:59',NULL,1),(7,'check_local_procs','$USER1$/check_procs -w $ARG1$ -c $ARG2$ -s $ARG3$',1,'1','2017-01-06 22:55:59',NULL,1),(8,'check_local_users','$USER1$/check_users -w $ARG1$ -c $ARG2$',1,'1','2017-01-06 22:55:59',NULL,1),(9,'check_local_swap','$USER1$/check_swap -w $ARG1$ -c $ARG2$',1,'1','2017-01-06 22:55:59',NULL,1),(10,'check_local_mrtgtraf','$USER1$/check_mrtgtraf -F $ARG1$ -a $ARG2$ -w $ARG3$ -c $ARG4$ -e $ARG5$',1,'1','2017-01-06 22:55:59',NULL,1),(11,'check_ftp','$USER1$/check_ftp -H $HOSTADDRESS$ $ARG1$',1,'1','2017-01-06 22:55:59',NULL,1),(12,'check_hpjd','$USER1$/check_hpjd -H $HOSTADDRESS$ $ARG1$',1,'1','2017-01-06 22:55:59',NULL,1),(13,'check_snmp','$USER1$/check_snmp -H $HOSTADDRESS$ $ARG1$',1,'1','2017-01-06 22:55:59',NULL,1),(14,'check_http','$USER1$/check_http -I $HOSTADDRESS$ $ARG1$',1,'1','2017-01-06 22:55:59',NULL,1),(15,'check_ssh','$USER1$/check_ssh $ARG1$ $HOSTADDRESS$',1,'1','2017-01-06 22:55:59',NULL,1),(16,'check_dhcp','$USER1$/check_dhcp $ARG1$',1,'1','2017-01-06 22:55:59',NULL,1),(17,'check_ping','$USER1$/check_ping -H $HOSTADDRESS$ -w $ARG1$ -c $ARG2$ -p 5',1,'1','2017-01-06 22:55:59',NULL,1),(18,'check_icmp','$USER1$/check_ping -H $HOSTADDRESS$ -w $ARG1$ -c $ARG2$',1,'1','2017-01-06 22:55:59',NULL,1),(19,'check_pop','$USER1$/check_pop -H $HOSTADDRESS$ $ARG1$',1,'1','2017-01-06 22:55:59',NULL,1),(20,'check_imap','$USER1$/check_imap -H $HOSTADDRESS$ $ARG1$',1,'1','2017-01-06 22:55:59',NULL,1),(21,'check_smtp','$USER1$/check_smtp -H $HOSTADDRESS$ $ARG1$',1,'1','2017-01-06 22:55:59',NULL,1),(22,'check_tcp','$USER1$/check_tcp -H $HOSTADDRESS$ -p $ARG1$ $ARG2$',1,'1','2017-01-06 22:55:59',NULL,1),(23,'check_udp','$USER1$/check_udp -H $HOSTADDRESS$ -p $ARG1$ $ARG2$',1,'1','2017-01-06 22:55:59',NULL,1),(24,'check_nt','$USER1$/check_nt -H $HOSTADDRESS$ -p $USER7$ -s $USER8$ -v $ARG1$ $ARG2$',1,'1','2017-01-06 22:55:59',NULL,1),(25,'check_nrpe','$USER1$/check_nrpe -H $HOSTADDRESS$ -t 30 -c $ARG1$ $ARG2$',1,'1','2017-01-06 22:55:59',NULL,1),(26,'check_nrpeversion','$USER1$/check_nrpe -H $HOSTADDRESS$',1,'1','2017-01-06 22:55:59',NULL,1),(27,'check_dns','$USER1$/check_dns -H $HOSTNAME$ $ARG1$',1,'1','2017-01-06 22:55:59',NULL,1),(28,'check_dir','$USER1$/check_dir -d $ARG1$ -w $ARG2$ -c $ARG3$ $ARG4$',1,'1','2017-01-06 22:55:59',NULL,1),(29,'check_proc_usage','$USER1$/check_proc_usage -p $ARG1$ $ARG2$',1,'1','2017-01-06 22:55:59',NULL,1),(30,'check_nagios_performance','$USER1$/check_nagios_performance -o $ARG1$ $ARG2$',1,'1','2017-01-06 22:55:59',NULL,1),(31,'check_snmp_int','$USER1$/check_snmp_int.pl -H $HOSTADDRESS$ -C $ARG1$ -2 -n $ARG2$ -f -k -w $ARG3$ -c $ARG4$ $ARG5$',1,'1','2017-01-06 22:55:59',NULL,1),(32,'check_php_snmp_bandwidth','$USER1$/get_snmp.php -H=$HOSTADDRESS$ -C=$ARG1$ -2 -I=$ARG2$ -u -w=$ARG3$ -c=$ARG4$ -d=$ARG5$',1,'1','2017-01-06 22:55:59',NULL,1),(33,'check_dummy','$USER1$/check_dummy $ARG1$ $ARG2$',1,'1','2017-01-06 22:55:59',NULL,1),(34,'check_none','/bin/true',2,'1','2017-01-06 22:55:59',NULL,1),(35,'process-service-perfdata-pnp-normal','/usr/bin/perl /usr/local/nagios/libexec/process_perfdata.pl',2,'1','2017-01-06 22:55:59',NULL,1),(36,'process-host-perfdata-pnp-normal','/usr/bin/perl /usr/local/nagios/libexec/process_perfdata.pl -d HOSTPERFDATA',2,'1','2017-01-06 22:55:59',NULL,1),(37,'process-service-perfdata-file-pnp-bulk','/bin/mv /usr/local/nagios/var/service-perfdata /usr/local/nagios/var/spool/perfdata/service-perfdata.$TIMET$',2,'1','2017-01-06 22:55:59',NULL,1),(38,'process-host-perfdata-file-pnp-bulk','/bin/mv /usr/local/nagios/var/host-perfdata /usr/local/nagios/var/spool/perfdata/host-perfdata.$TIMET$',2,'1','2017-01-06 22:55:59',NULL,1),(39,'process-service-perfdata-file-bulk','/bin/mv /usr/local/nagios/var/service-perfdata /usr/local/nagios/var/spool/xidpe/$TIMET$.perfdata.service',2,'1','2017-01-06 22:55:59',NULL,1),(40,'process-host-perfdata-file-bulk','/bin/mv /usr/local/nagios/var/host-perfdata /usr/local/nagios/var/spool/xidpe/$TIMET$.perfdata.host',2,'1','2017-01-06 22:55:59',NULL,1),(41,'check_bpi','/usr/bin/php $USER1$/check_bpi.php $ARG1$',1,'1','2017-01-06 22:55:59',NULL,1),(42,'check_xi_sla','$USER1$/check_xisla.php $ARG1$',1,'1','2017-01-06 22:55:59',NULL,1),(43,'check_xi_deface','$USER1$/check_http -H $HOSTADDRESS$ -r \'$ARG1$\' -u \'$ARG2$\' $ARG3$',1,'1','2017-01-06 22:56:00',NULL,1),(44,'check_xi_service_dnsquery','$USER1$/check_dns $ARG1$',1,'1','2017-01-06 22:56:00',NULL,1),(45,'check_xi_host_ping','$USER1$/check_icmp -H $HOSTADDRESS$ -w $ARG1$,$ARG2$ -c $ARG3$,$ARG4$ -p 5',1,'1','2017-01-06 22:56:14',NULL,1),(46,'check_xi_service_dns','$USER1$/check_dns -H $HOSTADDRESS$ $ARG1$',1,'1','2017-01-06 22:56:14',NULL,1),(47,'check_xi_domain_v2','$USER1$/check_domain.php -d $ARG1$ $ARG2$ $ARG3$',1,'1','2017-01-06 22:56:00',NULL,1),(48,'check_email_delivery','$USER1$/check_email_delivery $ARG1$',1,'1','2017-01-06 22:56:00',NULL,1),(49,'check_xi_service_ping','$USER1$/check_icmp -H $HOSTADDRESS$ -w $ARG1$,$ARG2$ -c $ARG3$,$ARG4$ -p 5',1,'1','2017-01-06 22:56:14',NULL,1),(50,'check_exchange_rbl','$USER1$/check_bl -H $HOSTADDRESS$ $ARG1$',1,'1','2017-01-06 22:56:01',NULL,1),(51,'check_file_service','$USER1$/folder_watch.pl $ARG1$ $ARG2$ -f',1,'1','2017-01-06 22:56:03',NULL,1),(52,'check_file_size_age','$USER1$/folder_watch.pl $ARG1$ $ARG2$ -f',1,'1','2017-01-06 22:56:03',NULL,1),(53,'check_ftp_fully','$USER1$/check_ftp_fully \"$ARG1$\" \"$ARG2$\" \"$ARG3$\" $HOSTNAME$',1,'1','2017-01-06 22:56:03',NULL,1),(54,'check_xi_service_ftp','$USER1$/check_ftp -H $HOSTADDRESS$ $ARG1$',1,'1','2017-01-06 22:56:14',NULL,1),(55,'check_xi_service_ldap','$USER1$/check_ldap -H $HOSTADDRESS$ $ARG1$',1,'1','2017-01-06 22:56:04',NULL,1),(56,'check_xi_service_snmp_linux_load','$USER1$/check_snmp_load_wizard.pl -H $HOSTADDRESS$ $ARG1$',1,'1','2017-01-06 22:56:04',NULL,1),(57,'check_xi_service_snmp_linux_process','$USER1$/check_snmp_process_wizard.pl -H $HOSTADDRESS$ $ARG1$',1,'1','2017-01-06 22:56:04',NULL,1),(58,'check_xi_service_snmp_linux_storage','$USER1$/check_snmp_storage_wizard.pl -H $HOSTADDRESS$ $ARG1$',1,'1','2017-01-06 22:56:04',NULL,1),(59,'check_mailserver_rbl','$USER1$/check_bl -H $HOSTADDRESS$ $ARG1$',1,'1','2017-01-06 22:56:04',NULL,1),(60,'check_mongodb_database','$USER1$/check_mongodb.py -H $HOSTADDRESS$ -A $ARG1$ -P $ARG2$ -W $ARG3$ -C $ARG4$ -u $ARG5$ -p $ARG6$ -d $ARG7$ -D',1,'1','2017-01-06 22:56:05',NULL,1),(61,'check_mongodb_server','$USER1$/check_mongodb.py -H $HOSTADDRESS$ -A $ARG1$ -P $ARG2$ -W $ARG3$ -C $ARG4$ -u $ARG5$ -p $ARG6$ -D --all-databases',1,'1','2017-01-06 22:56:05',NULL,1),(62,'check_mountpoint','$USER1$/check_mountpoints.sh $ARG1$',1,'1','2017-01-06 22:56:05',NULL,1),(63,'check_xi_mssql_server','$USER1$/check_mssql_server.py -H $HOSTADDRESS$ $ARG1$',1,'1','2017-01-06 22:56:05',NULL,1),(64,'check_xi_mssql_database','$USER1$/check_mssql_database.py -H $HOSTADDRESS$ $ARG1$',1,'1','2017-01-06 22:56:06',NULL,1),(65,'check_xi_mssql_query','$USER1$/check_mssql -H $HOSTADDRESS$ $ARG1$',1,'1','2017-01-06 22:56:06',NULL,1),(66,'check_xi_mysql_health','$USER1$/check_mysql_health $ARG1$',1,'1','2017-01-06 22:56:06',NULL,1),(67,'check_xi_mysql_query','$USER1$/check_mysql_health $ARG1$',1,'1','2017-01-06 22:56:06',NULL,1),(68,'check_xi_service_nagioslogserver','$USER1$/check_nagioslogserver.php $ARG1$',1,'1','2017-01-06 22:56:06',NULL,1),(69,'check_nagiosxi_performance','/usr/bin/php $USER1$/check_nagios_performance.php $ARG1$ $ARG2$ $ARG3$',1,'1','2017-01-06 22:56:07',NULL,1),(70,'check_xi_nagiosxiserver','/usr/bin/php $USER1$/check_nagiosxiserver.php $ARG1$',1,'1','2017-01-06 22:56:07',NULL,1),(71,'check_xi_ncpa','$USER1$/check_ncpa.py -H $HOSTADDRESS$ $ARG1$',1,'1','2017-01-06 22:56:07',NULL,1),(72,'check_xi_nna','$USER1$/check_nna.py -H $HOSTADDRESS$ -K $ARG1$ $ARG2$',1,'1','2017-01-06 22:56:07',NULL,1),(73,'check_xi_oraclequery','/usr/bin/env LD_LIBRARY_PATH=/usr/lib/oracle/11.2/client/lib ORACLE_HOME=/usr/lib/oracle/11.2/client $USER1$/check_oracle_health $ARG1$',1,'1','2017-01-06 22:56:08',NULL,1),(74,'check_xi_oracleserverspace','/usr/bin/env LD_LIBRARY_PATH=/usr/lib/oracle/11.2/client/lib ORACLE_HOME=/usr/lib/oracle/11.2/client $USER1$/check_oracle_health $ARG1$',1,'1','2017-01-06 22:56:08',NULL,1),(75,'check_xi_oracletablespace','/usr/bin/env LD_LIBRARY_PATH=/usr/lib/oracle/11.2/client/lib ORACLE_HOME=/usr/lib/oracle/11.2/client $USER1$/check_oracle_health $ARG1$',1,'1','2017-01-06 22:56:08',NULL,1),(76,'check_xi_check_postgres','',0,'0','2017-01-06 22:56:09',NULL,1),(77,'check_xi_postgres_db','$USER1$/check_postgres.pl $ARG1$',1,'1','2017-01-06 22:56:09',NULL,1),(78,'check_xi_check_postgres_query','',0,'0','2017-01-06 22:56:09',NULL,1),(79,'check_xi_postgres_query','$USER1$/check_postgres.pl $ARG1$',1,'1','2017-01-06 22:56:09',NULL,1),(80,'check_xi_postgres','$USER1$/check_postgres.pl $ARG1$',1,'1','2017-01-06 22:56:09',NULL,1),(81,'check_radius_server_adv','$USER1$/check_radius_adv -r $HOSTADDRESS$ $ARG1$',1,'1','2017-01-06 22:56:09',NULL,1),(82,'check_radius_adv','',0,'0','2017-01-06 22:56:09',NULL,1),(83,'check_xi_by_ssh','$USER1$/check_by_ssh -H $HOSTADDRESS$ $ARG1$ $ARG2$',1,'1','2017-01-06 22:56:10',NULL,1),(84,'check_xi_service_ifoperstatusnag','$USER1$/check_ifoperstatnag $ARG1$ $ARG2$ $HOSTADDRESS$',1,'1','2017-01-06 22:56:10',NULL,1),(85,'check_xi_service_ifoperstatus','$USER1$/check_ifoperstatus -H $HOSTADDRESS$ -C $ARG1$ -k $ARG2$ $ARG3$',1,'1','2017-01-06 22:56:14',NULL,1),(86,'check-host-alive-tftp','tftp $HOSTNAME$ 69',2,'1','2017-01-06 22:56:11',NULL,1),(87,'check_tftp_connect','$USER1$/check_tftp.sh --connect $ARG1$',1,'1','2017-01-06 22:56:11',NULL,1),(88,'check_tftp_get','$USER1$/check_tftp.sh --get $ARG1$ \'$ARG2$\' $ARG3$',1,'1','2017-01-06 22:56:11',NULL,1),(89,'check_esx3_host','$USER1$/check_esx3.pl -H \"$HOSTADDRESS$\" -f \"$ARG1$\" -l \"$ARG2$\" $ARG3$',1,'1','2017-01-06 22:56:11',NULL,1),(90,'check_esx3_guest','$USER1$/check_esx3.pl -H \"$HOSTADDRESS$\" -f \"$ARG1$\" -N \"$ARG2$\" -l \"$ARG3$\" $ARG4$',1,'1','2017-01-06 22:56:11',NULL,1),(91,'check_xi_service_snmp_watchguard','$USER1$/check_snmp_generic.pl -H $HOSTADDRESS$ $ARG1$',1,'1','2017-01-06 22:56:11',NULL,1),(92,'check_em01_temp','$USER1$/check_em01.pl --type=temp --temp=$ARG1$,$ARG2$ $HOSTADDRESS$',1,'1','2017-01-06 22:56:11',NULL,1),(93,'check_em01_humidity','$USER1$/check_em01.pl --type=hum --hum=$ARG1$,$ARG2$ $HOSTADDRESS$',1,'1','2017-01-06 22:56:11',NULL,1),(94,'check_em01_light','$USER1$/check_em01.pl --type=illum --illum=$ARG1$,$ARG2$ $HOSTADDRESS$',1,'1','2017-01-06 22:56:11',NULL,1),(95,'check_em08_temp','$USER1$/check_em08 $HOSTADDRESS$ T $ARG1$ $ARG2$ $ARG3$',1,'1','2017-01-06 22:56:11',NULL,1),(96,'check_em08_humidity','$USER1$/check_em08 $HOSTADDRESS$ H $ARG1$ $ARG2$ $ARG3$',1,'1','2017-01-06 22:56:11',NULL,1),(97,'check_em08_light','$USER1$/check_em08 $HOSTADDRESS$ I $ARG1$ $ARG2$ $ARG3$',1,'1','2017-01-06 22:56:11',NULL,1),(98,'check_em08_rtd','$USER1$/check_em08 $HOSTADDRESS$ R $ARG1$ $ARG2$ $ARG3$',1,'1','2017-01-06 22:56:11',NULL,1),(99,'check_em08_voltage','$USER1$/check_em08 $HOSTADDRESS$ V $ARG1$ $ARG2$ $ARG3$',1,'1','2017-01-06 22:56:11',NULL,1),(100,'check_em08_contacts','$USER1$/check_em08 $HOSTADDRESS$ C',1,'1','2017-01-06 22:56:11',NULL,1),(101,'check_xi_service_snmp_win_load','$USER1$/check_snmp_load.pl -H $HOSTADDRESS$ $ARG1$',1,'1','2017-01-06 22:56:12',NULL,1),(102,'check_xi_service_snmp_win_service','$USER1$/check_snmp_win.pl -H $HOSTADDRESS$ $ARG1$',1,'1','2017-01-06 22:56:12',NULL,1),(103,'check_xi_service_snmp_win_process','$USER1$/check_snmp_process.pl -H $HOSTADDRESS$ $ARG1$',1,'1','2017-01-06 22:56:12',NULL,1),(104,'check_xi_service_snmp_win_storage','$USER1$/check_snmp_storage.pl -H $HOSTADDRESS$ $ARG1$',1,'1','2017-01-06 22:56:12',NULL,1),(105,'check_xi_service_wmiplus_authfile','$USER1$/check_wmi_plus.pl -H $HOSTADDRESS$ -A $ARG1$ -m $ARG2$ $ARG3$',1,'1','2017-01-06 22:56:12',NULL,1),(106,'check_xi_service_wmiplus','$USER1$/check_wmi_plus.pl -H $HOSTADDRESS$ -u $ARG1$ -p $ARG2$ -m $ARG3$ $ARG4$',1,'1','2017-01-06 22:56:12',NULL,1),(107,'check_xi_service_status','sudo /usr/local/nagiosxi/scripts/manage_services.sh status $ARG1$',1,'1','2017-01-06 22:56:14',NULL,1),(108,'xi_host_notification_handler','/usr/bin/php /usr/local/nagiosxi/scripts/handle_nagioscore_notification.php --notification-type=host --contact=\"$CONTACTNAME$\" --contactemail=\"$CONTACTEMAIL$\" --type=$NOTIFICATIONTYPE$ --escalated=\"$NOTIFICATIONISESCALATED$\" --author=\"$NOTIFICATIONAUTHOR$\" --comments=\"$NOTIFICATIONCOMMENT$\" --host=\"$HOSTNAME$\" --hostaddress=\"$HOSTADDRESS$\" --hostalias=\"$HOSTALIAS$\" --hostdisplayname=\"$HOSTDISPLAYNAME$\" --hoststate=$HOSTSTATE$ --hoststateid=$HOSTSTATEID$ --lasthoststate=$LASTHOSTSTATE$ --lasthoststateid=$LASTHOSTSTATEID$ --hoststatetype=$HOSTSTATETYPE$ --currentattempt=$HOSTATTEMPT$ --maxattempts=$MAXHOSTATTEMPTS$ --hosteventid=$HOSTEVENTID$ --hostproblemid=$HOSTPROBLEMID$ --hostoutput=\"$HOSTOUTPUT$\" --longhostoutput=\"$LONGHOSTOUTPUT$\" --datetime=\"$LONGDATETIME$\"',2,'1','2017-01-06 22:56:14',NULL,1),(109,'xi_service_notification_handler','/usr/bin/php /usr/local/nagiosxi/scripts/handle_nagioscore_notification.php --notification-type=service --contact=\"$CONTACTNAME$\" --contactemail=\"$CONTACTEMAIL$\" --type=$NOTIFICATIONTYPE$ --escalated=\"$NOTIFICATIONISESCALATED$\" --author=\"$NOTIFICATIONAUTHOR$\" --comments=\"$NOTIFICATIONCOMMENT$\" --host=\"$HOSTNAME$\" --hostaddress=\"$HOSTADDRESS$\" --hostalias=\"$HOSTALIAS$\" --hostdisplayname=\"$HOSTDISPLAYNAME$\" --service=\"$SERVICEDESC$\" --hoststate=$HOSTSTATE$ --hoststateid=$HOSTSTATEID$ --servicestate=$SERVICESTATE$ --servicestateid=$SERVICESTATEID$ --lastservicestate=$LASTSERVICESTATE$ --lastservicestateid=$LASTSERVICESTATEID$ --servicestatetype=$SERVICESTATETYPE$ --currentattempt=$SERVICEATTEMPT$ --maxattempts=$MAXSERVICEATTEMPTS$ --serviceeventid=$SERVICEEVENTID$ --serviceproblemid=$SERVICEPROBLEMID$ --serviceoutput=\"$SERVICEOUTPUT$\" --longserviceoutput=\"$LONGSERVICEOUTPUT$\" --datetime=\"$LONGDATETIME$\"',2,'1','2017-01-06 22:56:14',NULL,1),(110,'xi_host_event_handler','/usr/bin/php /usr/local/nagiosxi/scripts/handle_nagioscore_event.php --handler-type=host --host=\"$HOSTNAME$\" --hostaddress=\"$HOSTADDRESS$\" --hoststate=$HOSTSTATE$ --hoststateid=$HOSTSTATEID$ --lasthoststate=$LASTHOSTSTATE$ --lasthoststateid=$LASTHOSTSTATEID$ --hoststatetype=$HOSTSTATETYPE$ --currentattempt=$HOSTATTEMPT$ --maxattempts=$MAXHOSTATTEMPTS$ --hosteventid=$HOSTEVENTID$ --hostproblemid=$HOSTPROBLEMID$ --hostoutput=\"$HOSTOUTPUT$\" --longhostoutput=\"$LONGHOSTOUTPUT$\" --hostdowntime=$HOSTDOWNTIME$',2,'1','2017-01-06 22:56:14',NULL,1),(111,'xi_service_event_handler','/usr/bin/php /usr/local/nagiosxi/scripts/handle_nagioscore_event.php --handler-type=service --host=\"$HOSTNAME$\" --service=\"$SERVICEDESC$\" --hostaddress=\"$HOSTADDRESS$\" --hoststate=$HOSTSTATE$ --hoststateid=$HOSTSTATEID$ --hosteventid=$HOSTEVENTID$ --hostproblemid=$HOSTPROBLEMID$ --servicestate=$SERVICESTATE$ --servicestateid=$SERVICESTATEID$ --lastservicestate=$LASTSERVICESTATE$ --lastservicestateid=$LASTSERVICESTATEID$ --servicestatetype=$SERVICESTATETYPE$ --currentattempt=$SERVICEATTEMPT$ --maxattempts=$MAXSERVICEATTEMPTS$ --serviceeventid=$SERVICEEVENTID$ --serviceproblemid=$SERVICEPROBLEMID$ --serviceoutput=\"$SERVICEOUTPUT$\" --longserviceoutput=\"$LONGSERVICEOUTPUT$\" --servicedowntime=$SERVICEDOWNTIME$',2,'1','2017-01-06 22:56:14',NULL,1),(112,'check_xi_host_http','$USER1$/check_http -H $HOSTADDRESS$ $ARG1$',1,'1','2017-01-06 22:56:14',NULL,1),(113,'check_xi_service_none','$USER1$/check_dummy 0 \"Nothing to monitor\"',1,'1','2017-01-06 22:56:14',NULL,1),(114,'check_xi_service_http','$USER1$/check_http -H $HOSTADDRESS$ $ARG1$',1,'1','2017-01-06 22:56:14',NULL,1),(115,'check_xi_service_http_cert','$USER1$/check_http -H $HOSTADDRESS$ -C $ARG1$',1,'1','2017-01-06 22:56:14',NULL,1),(116,'check_xi_service_http_content','$USER1$/check_http -H $HOSTADDRESS$ --onredirect=follow -s \"$ARG1$\"',1,'1','2017-01-06 22:56:14',NULL,1),(117,'check_xi_service_hpjd','$USER1$/check_hpjd -H $HOSTADDRESS$ -C $ARG1$',1,'1','2017-01-06 22:56:14',NULL,1),(118,'check_xi_service_nsclient','$USER1$/check_nt -H $HOSTADDRESS$ -s \"$ARG1$\" -p 12489 -v $ARG2$ $ARG3$ $ARG4$',1,'1','2017-01-06 22:56:14',NULL,1),(119,'check_xi_service_mrtgtraf','$USER1$/check_rrdtraf -f /var/lib/mrtg/$ARG1$ -w $ARG2$ -c $ARG3$ -l $ARG4$',1,'1','2017-01-06 22:56:14',NULL,1),(120,'check_xi_service_webinject','$USER1$/check_webinject.sh $ARG1$',1,'1','2017-01-06 22:56:14',NULL,1),(121,'check_xi_service_imap','$USER1$/check_imap -H $HOSTADDRESS$ $ARG1$',1,'1','2017-01-06 22:56:14',NULL,1),(122,'check_xi_service_pop','$USER1$/check_pop -H $HOSTADDRESS$ $ARG1$',1,'1','2017-01-06 22:56:14',NULL,1),(123,'check_xi_service_smtp','$USER1$/check_smtp -H $HOSTADDRESS$ $ARG1$',1,'1','2017-01-06 22:56:14',NULL,1),(124,'check_xi_service_ssh','$USER1$/check_ssh $ARG1$ $HOSTADDRESS$',1,'1','2017-01-06 22:56:14',NULL,1),(125,'check_xi_service_tcp','$USER1$/check_tcp -H $HOSTADDRESS$ $ARG1$',1,'1','2017-01-06 22:56:14',NULL,1),(126,'check_xi_service_udp','$USER1$/check_udp -H $HOSTADDRESS$ $ARG1$',1,'1','2017-01-06 22:56:14',NULL,1),(127,'check_xi_service_snmp','$USER1$/check_snmp -H $HOSTADDRESS$ $ARG1$',1,'1','2017-01-06 22:56:14',NULL,1),(128,'check_xi_service_rrdtraf','',0,'0','2017-01-06 22:56:14',NULL,1);
/*!40000 ALTER TABLE `tbl_command` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_contact`
--

DROP TABLE IF EXISTS `tbl_contact`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_contact` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `contact_name` varchar(255) NOT NULL,
  `alias` varchar(255) NOT NULL,
  `contactgroups` int(10) unsigned NOT NULL DEFAULT '0',
  `contactgroups_tploptions` tinyint(3) unsigned NOT NULL DEFAULT '2',
  `host_notifications_enabled` tinyint(3) unsigned NOT NULL DEFAULT '2',
  `service_notifications_enabled` tinyint(3) unsigned NOT NULL DEFAULT '2',
  `host_notification_period` int(10) unsigned NOT NULL DEFAULT '0',
  `service_notification_period` int(10) unsigned NOT NULL DEFAULT '0',
  `host_notification_options` varchar(20) NOT NULL,
  `service_notification_options` varchar(20) NOT NULL,
  `host_notification_commands` int(10) unsigned NOT NULL DEFAULT '0',
  `host_notification_commands_tploptions` tinyint(3) unsigned NOT NULL DEFAULT '2',
  `service_notification_commands` int(10) unsigned NOT NULL DEFAULT '0',
  `service_notification_commands_tploptions` tinyint(3) unsigned NOT NULL DEFAULT '2',
  `can_submit_commands` tinyint(3) unsigned NOT NULL DEFAULT '2',
  `retain_status_information` tinyint(3) unsigned NOT NULL DEFAULT '2',
  `retain_nonstatus_information` tinyint(3) unsigned NOT NULL DEFAULT '2',
  `email` varchar(255) DEFAULT NULL,
  `pager` varchar(255) DEFAULT NULL,
  `address1` varchar(255) DEFAULT NULL,
  `address2` varchar(255) DEFAULT NULL,
  `address3` varchar(255) DEFAULT NULL,
  `address4` varchar(255) DEFAULT NULL,
  `address5` varchar(255) DEFAULT NULL,
  `address6` varchar(255) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `use_variables` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `use_template` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `use_template_tploptions` tinyint(3) unsigned NOT NULL DEFAULT '2',
  `active` enum('0','1') NOT NULL DEFAULT '1',
  `last_modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `access_rights` varchar(8) DEFAULT NULL,
  `config_id` tinyint(3) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `config_name` (`contact_name`,`config_id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_contact`
--

LOCK TABLES `tbl_contact` WRITE;
/*!40000 ALTER TABLE `tbl_contact` DISABLE KEYS */;
INSERT INTO `tbl_contact` VALUES (1,'nagiosadmin','Nagios Administrator',0,2,1,1,1,1,'d,u,r,f,s','w,u,c,r,f,s',1,2,1,2,2,2,2,'nagios@localhost',NULL,NULL,NULL,NULL,NULL,NULL,NULL,'',0,1,2,'1','2017-01-06 22:56:13',NULL,1);
/*!40000 ALTER TABLE `tbl_contact` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_contactgroup`
--

DROP TABLE IF EXISTS `tbl_contactgroup`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_contactgroup` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `contactgroup_name` varchar(255) NOT NULL,
  `alias` varchar(255) NOT NULL,
  `members` int(10) unsigned NOT NULL DEFAULT '0',
  `contactgroup_members` int(10) unsigned NOT NULL,
  `active` enum('0','1') NOT NULL DEFAULT '1',
  `last_modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `access_rights` varchar(8) DEFAULT NULL,
  `config_id` tinyint(3) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `config_name` (`contactgroup_name`,`config_id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_contactgroup`
--

LOCK TABLES `tbl_contactgroup` WRITE;
/*!40000 ALTER TABLE `tbl_contactgroup` DISABLE KEYS */;
INSERT INTO `tbl_contactgroup` VALUES (1,'admins','Nagios Administrators',1,0,'1','2017-01-06 22:56:12',NULL,1),(2,'xi_contactgroup_all','All Contacts',0,0,'1','2017-01-06 22:56:14',NULL,1);
/*!40000 ALTER TABLE `tbl_contactgroup` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_contacttemplate`
--

DROP TABLE IF EXISTS `tbl_contacttemplate`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_contacttemplate` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `template_name` varchar(255) NOT NULL,
  `alias` varchar(255) NOT NULL,
  `contactgroups` int(10) unsigned NOT NULL DEFAULT '0',
  `contactgroups_tploptions` tinyint(3) unsigned NOT NULL DEFAULT '2',
  `host_notifications_enabled` tinyint(3) unsigned NOT NULL DEFAULT '2',
  `service_notifications_enabled` tinyint(3) unsigned NOT NULL DEFAULT '2',
  `host_notification_period` int(11) NOT NULL DEFAULT '0',
  `service_notification_period` int(11) NOT NULL DEFAULT '0',
  `host_notification_options` varchar(20) NOT NULL,
  `service_notification_options` varchar(20) NOT NULL,
  `host_notification_commands` int(10) unsigned NOT NULL DEFAULT '0',
  `host_notification_commands_tploptions` tinyint(3) unsigned NOT NULL DEFAULT '2',
  `service_notification_commands` int(10) unsigned NOT NULL DEFAULT '0',
  `service_notification_commands_tploptions` tinyint(3) unsigned NOT NULL DEFAULT '2',
  `can_submit_commands` tinyint(3) unsigned NOT NULL DEFAULT '2',
  `retain_status_information` tinyint(3) unsigned NOT NULL DEFAULT '2',
  `retain_nonstatus_information` tinyint(3) unsigned NOT NULL DEFAULT '2',
  `email` varchar(255) DEFAULT NULL,
  `pager` varchar(255) DEFAULT NULL,
  `address1` varchar(255) DEFAULT NULL,
  `address2` varchar(255) DEFAULT NULL,
  `address3` varchar(255) DEFAULT NULL,
  `address4` varchar(255) DEFAULT NULL,
  `address5` varchar(255) DEFAULT NULL,
  `address6` varchar(255) DEFAULT NULL,
  `use_variables` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `use_template` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `use_template_tploptions` tinyint(3) unsigned NOT NULL DEFAULT '2',
  `active` enum('0','1') NOT NULL DEFAULT '1',
  `last_modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `access_rights` varchar(8) DEFAULT NULL,
  `config_id` tinyint(3) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `config_name` (`template_name`,`config_id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_contacttemplate`
--

LOCK TABLES `tbl_contacttemplate` WRITE;
/*!40000 ALTER TABLE `tbl_contacttemplate` DISABLE KEYS */;
INSERT INTO `tbl_contacttemplate` VALUES (1,'xi_contact_generic','',1,2,2,2,7,7,'d,u,r,f,s','w,u,c,r,f,s',1,2,1,2,2,2,2,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,2,'1','2017-01-06 22:56:14',NULL,1),(2,'generic-contact','',0,2,2,2,2,2,'d,u,r,f,s','w,u,c,r,f,s',1,2,1,2,2,2,2,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,2,'1','2017-01-06 22:56:13',NULL,1);
/*!40000 ALTER TABLE `tbl_contacttemplate` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_domain`
--

DROP TABLE IF EXISTS `tbl_domain`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_domain` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `domain` varchar(255) NOT NULL,
  `alias` varchar(255) NOT NULL,
  `server` varchar(255) NOT NULL,
  `method` varchar(255) NOT NULL,
  `user` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `basedir` varchar(255) NOT NULL,
  `hostconfig` varchar(255) NOT NULL,
  `serviceconfig` varchar(255) NOT NULL,
  `backupdir` varchar(255) NOT NULL,
  `hostbackup` varchar(255) NOT NULL,
  `servicebackup` varchar(255) NOT NULL,
  `nagiosbasedir` varchar(255) NOT NULL,
  `importdir` varchar(255) NOT NULL,
  `commandfile` varchar(255) NOT NULL,
  `binaryfile` varchar(255) NOT NULL,
  `pidfile` varchar(255) NOT NULL,
  `version` tinyint(3) unsigned NOT NULL,
  `access_rights` varchar(255) NOT NULL,
  `active` enum('0','1') NOT NULL,
  `nodelete` enum('0','1') NOT NULL DEFAULT '0',
  `last_modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `domain` (`domain`)
) ENGINE=MyISAM AUTO_INCREMENT=12 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_domain`
--

LOCK TABLES `tbl_domain` WRITE;
/*!40000 ALTER TABLE `tbl_domain` DISABLE KEYS */;
INSERT INTO `tbl_domain` VALUES (1,'localhost','Local installation','localhost','1','','','/usr/local/nagios/etc/','/usr/local/nagios/etc/hosts/','/usr/local/nagios/etc/services/','/etc/nagiosql/backup/','/etc/nagiosql/backup/hosts/','/etc/nagiosql/backup/services/','/usr/local/nagios/etc/','/usr/local/nagios/etc/import/','/var/nagios/rw/nagios.cmd','/usr/local/nagios/bin/nagios','/var/nagios/nagios.lock',3,'00000000','1','1','2009-10-08 17:27:46');
/*!40000 ALTER TABLE `tbl_domain` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_host`
--

DROP TABLE IF EXISTS `tbl_host`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_host` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `host_name` varchar(255) NOT NULL,
  `alias` varchar(255) NOT NULL,
  `display_name` varchar(255) DEFAULT '',
  `address` varchar(255) NOT NULL,
  `parents` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `parents_tploptions` tinyint(3) unsigned NOT NULL DEFAULT '2',
  `hostgroups` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `hostgroups_tploptions` tinyint(3) unsigned NOT NULL DEFAULT '2',
  `check_command` text,
  `use_template` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `use_template_tploptions` tinyint(3) unsigned NOT NULL DEFAULT '2',
  `initial_state` varchar(20) DEFAULT '',
  `max_check_attempts` int(11) DEFAULT NULL,
  `check_interval` int(11) DEFAULT NULL,
  `retry_interval` int(11) DEFAULT NULL,
  `active_checks_enabled` tinyint(3) unsigned NOT NULL DEFAULT '2',
  `passive_checks_enabled` tinyint(3) unsigned NOT NULL DEFAULT '2',
  `check_period` int(11) NOT NULL DEFAULT '0',
  `obsess_over_host` tinyint(3) unsigned NOT NULL DEFAULT '2',
  `check_freshness` tinyint(3) unsigned NOT NULL DEFAULT '2',
  `freshness_threshold` int(11) DEFAULT NULL,
  `event_handler` int(11) NOT NULL DEFAULT '0',
  `event_handler_enabled` tinyint(3) unsigned NOT NULL DEFAULT '2',
  `low_flap_threshold` int(11) DEFAULT NULL,
  `high_flap_threshold` int(11) DEFAULT NULL,
  `flap_detection_enabled` tinyint(3) unsigned NOT NULL DEFAULT '2',
  `flap_detection_options` varchar(20) DEFAULT '',
  `process_perf_data` tinyint(3) unsigned NOT NULL DEFAULT '2',
  `retain_status_information` tinyint(3) unsigned NOT NULL DEFAULT '2',
  `retain_nonstatus_information` tinyint(3) unsigned NOT NULL DEFAULT '2',
  `contacts` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `contacts_tploptions` tinyint(3) unsigned NOT NULL DEFAULT '2',
  `contact_groups` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `contact_groups_tploptions` tinyint(3) unsigned NOT NULL DEFAULT '2',
  `notification_interval` int(11) DEFAULT NULL,
  `notification_period` int(11) NOT NULL DEFAULT '0',
  `first_notification_delay` int(11) DEFAULT NULL,
  `notification_options` varchar(20) DEFAULT '',
  `notifications_enabled` tinyint(3) unsigned NOT NULL DEFAULT '2',
  `stalking_options` varchar(20) DEFAULT '',
  `notes` varchar(255) DEFAULT '',
  `notes_url` varchar(255) DEFAULT '',
  `action_url` varchar(255) DEFAULT '',
  `icon_image` varchar(255) DEFAULT '',
  `icon_image_alt` varchar(255) DEFAULT '',
  `vrml_image` varchar(255) DEFAULT '',
  `statusmap_image` varchar(255) DEFAULT '',
  `2d_coords` varchar(255) DEFAULT '',
  `3d_coords` varchar(255) DEFAULT '',
  `use_variables` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `name` varchar(255) NOT NULL,
  `active` enum('0','1') NOT NULL DEFAULT '1',
  `last_modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `access_rights` varchar(8) DEFAULT NULL,
  `config_id` tinyint(3) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `config_name` (`host_name`,`config_id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_host`
--

LOCK TABLES `tbl_host` WRITE;
/*!40000 ALTER TABLE `tbl_host` DISABLE KEYS */;
INSERT INTO `tbl_host` VALUES (1,'localhost','localhost','','127.0.0.1',0,2,0,2,NULL,1,2,'',NULL,NULL,NULL,2,2,0,2,2,NULL,0,2,NULL,NULL,2,'',2,2,2,0,2,0,2,NULL,0,NULL,'',2,'','','','','','','','','','',0,'','1','2017-01-06 22:56:12',NULL,1);
/*!40000 ALTER TABLE `tbl_host` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_hostdependency`
--

DROP TABLE IF EXISTS `tbl_hostdependency`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_hostdependency` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `config_name` varchar(255) NOT NULL,
  `dependent_host_name` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `dependent_hostgroup_name` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `host_name` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `hostgroup_name` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `inherits_parent` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `execution_failure_criteria` varchar(20) DEFAULT '',
  `notification_failure_criteria` varchar(20) DEFAULT '',
  `dependency_period` int(11) NOT NULL DEFAULT '0',
  `active` enum('0','1') NOT NULL DEFAULT '1',
  `last_modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `access_rights` varchar(8) DEFAULT NULL,
  `config_id` tinyint(3) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `config_name` (`config_name`,`config_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_hostdependency`
--

LOCK TABLES `tbl_hostdependency` WRITE;
/*!40000 ALTER TABLE `tbl_hostdependency` DISABLE KEYS */;
/*!40000 ALTER TABLE `tbl_hostdependency` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_hostescalation`
--

DROP TABLE IF EXISTS `tbl_hostescalation`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_hostescalation` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `config_name` varchar(255) NOT NULL,
  `host_name` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `hostgroup_name` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `contacts` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `contact_groups` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `first_notification` int(11) DEFAULT NULL,
  `last_notification` int(11) DEFAULT NULL,
  `notification_interval` int(11) DEFAULT NULL,
  `escalation_period` int(11) NOT NULL DEFAULT '0',
  `escalation_options` varchar(20) DEFAULT '',
  `active` enum('0','1') NOT NULL DEFAULT '1',
  `last_modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `access_rights` varchar(8) DEFAULT NULL,
  `config_id` tinyint(3) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `config_name` (`config_name`,`config_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_hostescalation`
--

LOCK TABLES `tbl_hostescalation` WRITE;
/*!40000 ALTER TABLE `tbl_hostescalation` DISABLE KEYS */;
/*!40000 ALTER TABLE `tbl_hostescalation` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_hostextinfo`
--

DROP TABLE IF EXISTS `tbl_hostextinfo`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_hostextinfo` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `host_name` int(11) DEFAULT NULL,
  `notes` varchar(255) NOT NULL,
  `notes_url` varchar(255) NOT NULL,
  `action_url` varchar(255) NOT NULL,
  `statistik_url` varchar(255) NOT NULL,
  `icon_image` varchar(255) NOT NULL,
  `icon_image_alt` varchar(255) NOT NULL,
  `vrml_image` varchar(255) NOT NULL,
  `statusmap_image` varchar(255) NOT NULL,
  `2d_coords` varchar(255) NOT NULL,
  `3d_coords` varchar(255) NOT NULL,
  `active` enum('0','1') NOT NULL DEFAULT '1',
  `last_modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `access_rights` varchar(8) DEFAULT NULL,
  `config_id` tinyint(3) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `config_name` (`host_name`,`config_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_hostextinfo`
--

LOCK TABLES `tbl_hostextinfo` WRITE;
/*!40000 ALTER TABLE `tbl_hostextinfo` DISABLE KEYS */;
/*!40000 ALTER TABLE `tbl_hostextinfo` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_hostgroup`
--

DROP TABLE IF EXISTS `tbl_hostgroup`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_hostgroup` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `hostgroup_name` varchar(255) NOT NULL,
  `alias` varchar(255) NOT NULL,
  `members` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `hostgroup_members` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `notes` varchar(255) DEFAULT NULL,
  `notes_url` varchar(255) DEFAULT NULL,
  `action_url` varchar(255) DEFAULT NULL,
  `active` enum('0','1') NOT NULL DEFAULT '1',
  `last_modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `access_rights` varchar(8) DEFAULT NULL,
  `config_id` tinyint(3) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `config_name` (`hostgroup_name`,`config_id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_hostgroup`
--

LOCK TABLES `tbl_hostgroup` WRITE;
/*!40000 ALTER TABLE `tbl_hostgroup` DISABLE KEYS */;
INSERT INTO `tbl_hostgroup` VALUES (1,'linux-servers','Linux Servers',1,0,NULL,NULL,NULL,'1','2017-01-06 22:56:12',NULL,1),(2,'windows-servers','',0,0,NULL,NULL,NULL,'0','2017-01-06 22:56:13',NULL,1);
/*!40000 ALTER TABLE `tbl_hostgroup` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_hosttemplate`
--

DROP TABLE IF EXISTS `tbl_hosttemplate`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_hosttemplate` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `template_name` varchar(255) NOT NULL,
  `alias` varchar(255) NOT NULL,
  `parents` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `parents_tploptions` tinyint(3) unsigned NOT NULL DEFAULT '2',
  `hostgroups` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `hostgroups_tploptions` tinyint(3) unsigned NOT NULL DEFAULT '2',
  `check_command` text,
  `use_template` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `use_template_tploptions` tinyint(3) unsigned NOT NULL DEFAULT '2',
  `initial_state` varchar(20) DEFAULT '',
  `max_check_attempts` int(11) DEFAULT NULL,
  `check_interval` int(11) DEFAULT NULL,
  `retry_interval` int(11) DEFAULT NULL,
  `active_checks_enabled` tinyint(3) unsigned NOT NULL DEFAULT '2',
  `passive_checks_enabled` tinyint(3) unsigned NOT NULL DEFAULT '2',
  `check_period` int(11) NOT NULL DEFAULT '0',
  `obsess_over_host` tinyint(3) unsigned NOT NULL DEFAULT '2',
  `check_freshness` tinyint(3) unsigned NOT NULL DEFAULT '2',
  `freshness_threshold` int(11) DEFAULT NULL,
  `event_handler` int(11) NOT NULL DEFAULT '0',
  `event_handler_enabled` tinyint(3) unsigned NOT NULL DEFAULT '2',
  `low_flap_threshold` int(11) DEFAULT NULL,
  `high_flap_threshold` int(11) DEFAULT NULL,
  `flap_detection_enabled` tinyint(3) unsigned NOT NULL DEFAULT '2',
  `flap_detection_options` varchar(20) DEFAULT '',
  `process_perf_data` tinyint(3) unsigned NOT NULL DEFAULT '2',
  `retain_status_information` tinyint(3) unsigned NOT NULL DEFAULT '2',
  `retain_nonstatus_information` tinyint(3) unsigned NOT NULL DEFAULT '2',
  `contacts` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `contacts_tploptions` tinyint(3) unsigned NOT NULL DEFAULT '2',
  `contact_groups` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `contact_groups_tploptions` tinyint(3) unsigned NOT NULL DEFAULT '2',
  `notification_interval` int(11) DEFAULT NULL,
  `notification_period` int(11) NOT NULL DEFAULT '0',
  `first_notification_delay` int(11) DEFAULT NULL,
  `notification_options` varchar(20) DEFAULT '',
  `notifications_enabled` tinyint(3) unsigned NOT NULL DEFAULT '2',
  `stalking_options` varchar(20) DEFAULT '',
  `notes` varchar(255) DEFAULT '',
  `notes_url` varchar(255) DEFAULT '',
  `action_url` varchar(255) DEFAULT '',
  `icon_image` varchar(255) DEFAULT '',
  `icon_image_alt` varchar(255) DEFAULT '',
  `vrml_image` varchar(255) DEFAULT '',
  `statusmap_image` varchar(255) DEFAULT '',
  `2d_coords` varchar(255) DEFAULT '',
  `3d_coords` varchar(255) DEFAULT '',
  `use_variables` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `active` enum('0','1') NOT NULL DEFAULT '1',
  `last_modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `access_rights` varchar(8) DEFAULT NULL,
  `config_id` tinyint(3) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `config_name` (`template_name`,`config_id`)
) ENGINE=MyISAM AUTO_INCREMENT=51 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_hosttemplate`
--

LOCK TABLES `tbl_hosttemplate` WRITE;
/*!40000 ALTER TABLE `tbl_hosttemplate` DISABLE KEYS */;
INSERT INTO `tbl_hosttemplate` VALUES (1,'xiwizard_bpi_host','',0,2,0,2,'33!0!BPI Process',1,2,'',NULL,NULL,NULL,2,2,0,2,2,NULL,0,2,NULL,NULL,2,'',2,2,2,0,2,0,2,NULL,0,NULL,'',2,'','','','','','','','','','',0,'1','2017-01-06 22:55:59',NULL,1),(2,'xiwizard_generic_host','',0,2,0,2,'45!3000.0!80%!5000.0!100%',0,2,'',5,5,1,2,2,7,2,2,NULL,0,1,NULL,NULL,1,'',1,1,1,0,2,0,2,60,7,NULL,'',1,'','','','','','','','','','',0,'1','2017-01-06 22:56:14',NULL,1),(3,'xiwizard_check_deface_host','',0,2,0,2,'4',1,2,'',NULL,NULL,NULL,2,2,0,2,2,NULL,0,2,NULL,NULL,2,'',2,2,2,0,2,0,2,NULL,0,NULL,'',2,'','','','','','','','','','',0,'1','2017-01-06 22:56:00',NULL,1),(4,'xiwizard_dnsquery_host','',0,2,0,2,'45!3000.0!80%!5000.0!100%',1,2,'',NULL,NULL,NULL,2,2,0,2,2,NULL,0,2,NULL,NULL,2,'',2,2,2,0,2,0,2,NULL,0,NULL,'',2,'','','','','server.png','','','','','',0,'1','2017-01-06 22:56:00',NULL,1),(5,'xiwizard_domain_expiration_host_v2','',0,2,0,2,'4',1,2,'',NULL,NULL,NULL,2,2,0,2,2,NULL,0,2,NULL,NULL,2,'',2,2,2,0,2,0,2,NULL,0,NULL,'',2,'','','','','','','','','','',0,'1','2017-01-06 22:56:00',NULL,1),(6,'xiwizard_exchange_host','',0,2,0,2,'45!3000.0!80%!5000.0!100%',1,2,'',NULL,NULL,NULL,2,2,0,2,2,NULL,0,2,NULL,NULL,2,'',2,2,2,0,2,0,2,NULL,0,NULL,'',2,'','','','','','','','','','',0,'1','2017-01-06 22:56:01',NULL,1),(7,'xiwizard_ftpserver_host','',0,2,0,2,'45!3000.0!80%!5000.0!100%',1,2,'',NULL,NULL,NULL,2,2,0,2,2,NULL,0,2,NULL,NULL,2,'',2,2,2,0,2,0,2,NULL,0,NULL,'',2,'','','','','ftpserver.png','','','','','',0,'1','2017-01-06 22:56:03',NULL,1),(8,'xiwizard_ldapserver_host','',0,2,0,2,'45!3000.0!80%!5000.0!100%',1,2,'',NULL,NULL,NULL,2,2,0,2,2,NULL,0,2,NULL,NULL,2,'',2,2,2,0,2,0,2,NULL,0,NULL,'',2,'','','','','ldapserver.png','','','','','',0,'1','2017-01-06 22:56:04',NULL,1),(9,'xiwizard_linuxsnmp_host','',0,2,0,2,'45!3000.0!80%!5000.0!100%',1,2,'',NULL,NULL,NULL,2,2,0,2,2,NULL,0,2,NULL,NULL,2,'',2,2,2,0,2,0,2,NULL,0,NULL,'',2,'','','','','','','','','','',0,'1','2017-01-06 22:56:04',NULL,1),(10,'xiwizard_mailserver_host','',0,2,0,2,'45!3000.0!80%!5000.0!100%',1,2,'',NULL,NULL,NULL,2,2,0,2,2,NULL,0,2,NULL,NULL,2,'',2,2,2,0,2,0,2,NULL,0,NULL,'',2,'','','','','','','','','','',0,'1','2017-01-06 22:56:04',NULL,1),(11,'xiwizard_mongodbdatabase_host','',0,2,0,2,'45!3000.0!80%!5000.0!100%',1,2,'',NULL,NULL,NULL,2,2,0,2,2,NULL,0,2,NULL,NULL,2,'',2,2,2,0,2,0,2,NULL,0,NULL,'',2,'','','','','','','','','','',0,'1','2017-01-06 22:56:05',NULL,1),(12,'xiwizard_mongodbserver_host','',0,2,0,2,'45!3000.0!80%!5000.0!100%',1,2,'',NULL,NULL,NULL,2,2,0,2,2,NULL,0,2,NULL,NULL,2,'',2,2,2,0,2,0,2,NULL,0,NULL,'',2,'','','','','','','','','','',0,'1','2017-01-06 22:56:05',NULL,1),(13,'xiwizard_mssqlserver_host','',0,2,0,2,'45!3000.0!80%!5000.0!100%',1,2,'',NULL,NULL,NULL,2,2,0,2,2,NULL,0,2,NULL,NULL,2,'',2,2,2,0,2,0,2,NULL,0,NULL,'',2,'','','','','','','','','','',0,'1','2017-01-06 22:56:05',NULL,1),(14,'xiwizard_mssqldatabase_host','',0,2,0,2,'45!3000.0!80%!5000.0!100%',1,2,'',NULL,NULL,NULL,2,2,0,2,2,NULL,0,2,NULL,NULL,2,'',2,2,2,0,2,0,2,NULL,0,NULL,'',2,'','','','','','','','','','',0,'1','2017-01-06 22:56:06',NULL,1),(15,'xiwizard_mssqlquery_host','',0,2,0,2,'45!3000.0!80%!5000.0!100%',1,2,'',NULL,NULL,NULL,2,2,0,2,2,NULL,0,2,NULL,NULL,2,'',2,2,2,0,2,0,2,NULL,0,NULL,'',2,'','','','','','','','','','',0,'1','2017-01-06 22:56:06',NULL,1),(16,'xiwizard_mysqlquery_host','',0,2,0,2,'45!3000.0!80%!5000.0!100%',1,2,'',NULL,NULL,NULL,2,2,0,2,2,NULL,0,2,NULL,NULL,2,'',2,2,2,0,2,0,2,NULL,0,NULL,'',2,'','','','','','','','','','',0,'1','2017-01-06 22:56:06',NULL,1),(17,'xiwizard_mysqlserver_host','',0,2,0,2,'45!3000.0!80%!5000.0!100%',1,2,'',NULL,NULL,NULL,2,2,0,2,2,NULL,0,2,NULL,NULL,2,'',2,2,2,0,2,0,2,NULL,0,NULL,'',2,'','','','','','','','','','',0,'1','2017-01-06 22:56:06',NULL,1),(18,'xiwizard_nagioslogserver_host','',0,2,0,2,'45!3000.0!80%!5000.0!100%',1,2,'',NULL,NULL,NULL,2,2,0,2,2,NULL,0,2,NULL,NULL,2,'',2,2,2,0,2,0,2,NULL,0,NULL,'',2,'','','','','nagioslogserver.png','','','','','',0,'1','2017-01-06 22:56:06',NULL,1),(19,'xiwizard_nagiosxiserver_host','',0,2,0,2,'45!3000.0!80%!5000.0!100%',1,2,'',NULL,NULL,NULL,2,2,0,2,2,NULL,0,2,NULL,NULL,2,'',2,2,2,0,2,0,2,NULL,0,NULL,'',2,'','','','','','','','','','',0,'1','2017-01-06 22:56:07',NULL,1),(20,'xiwizard_ncpa_host','',0,2,0,2,'45!3000.0!80%!5000.0!100%',1,2,'',NULL,NULL,NULL,2,2,0,2,2,NULL,0,2,NULL,NULL,2,'',2,2,2,0,2,0,2,NULL,0,NULL,'',2,'','','','','','','','','','',0,'1','2017-01-06 22:56:07',NULL,1),(21,'xiwizard_nna_host','',0,2,0,2,'72',1,2,'',NULL,NULL,NULL,2,2,0,2,2,NULL,0,2,NULL,NULL,2,'',2,2,2,0,2,0,2,NULL,0,NULL,'',2,'','','','','','','','','','',0,'1','2017-01-06 22:56:07',NULL,1),(22,'xiwizard_oraclequery_host','',0,2,0,2,'45!3000.0!80%!5000.0!100%',1,2,'',NULL,NULL,NULL,2,2,0,2,2,NULL,0,2,NULL,NULL,2,'',2,2,2,0,2,0,2,NULL,0,NULL,'',2,'','','','','','','','','','',0,'1','2017-01-06 22:56:08',NULL,1),(23,'xiwizard_oracleserverspace_host','',0,2,0,2,'45!3000.0!80%!5000.0!100%',1,2,'',NULL,NULL,NULL,2,2,0,2,2,NULL,0,2,NULL,NULL,2,'',2,2,2,0,2,0,2,NULL,0,NULL,'',2,'','','','','','','','','','',0,'1','2017-01-06 22:56:08',NULL,1),(24,'xiwizard_oracletablespace_host','',0,2,0,2,'45!3000.0!80%!5000.0!100%',1,2,'',NULL,NULL,NULL,2,2,0,2,2,NULL,0,2,NULL,NULL,2,'',2,2,2,0,2,0,2,NULL,0,NULL,'',2,'','','','','','','','','','',0,'1','2017-01-06 22:56:08',NULL,1),(25,'xiwizard_passive_host','',0,2,0,2,'33!0!\"No data received yet.\"',1,2,'',1,NULL,NULL,0,1,0,2,2,NULL,0,2,NULL,NULL,2,'',2,2,2,0,2,0,2,NULL,0,NULL,'',2,'','','','','','','','','','',0,'1','2017-01-06 22:56:09',NULL,1),(26,'xiwizard_postgresdb_host','',0,2,0,2,'45!3000.0!80%!5000.0!100%',1,2,'',NULL,NULL,NULL,2,2,0,2,2,NULL,0,2,NULL,NULL,2,'',2,2,2,0,2,0,2,NULL,0,NULL,'',2,'','','','','','','','','','',0,'1','2017-01-06 22:56:09',NULL,1),(27,'xiwizard_postgresquery_host','',0,2,0,2,'45!3000.0!80%!5000.0!100%',1,2,'',NULL,NULL,NULL,2,2,0,2,2,NULL,0,2,NULL,NULL,2,'',2,2,2,0,2,0,2,NULL,0,NULL,'',2,'','','','','','','','','','',0,'1','2017-01-06 22:56:09',NULL,1),(28,'xiwizard_postgresserver_host','',0,2,0,2,'45!3000.0!80%!5000.0!100%',1,2,'',NULL,NULL,NULL,2,2,0,2,2,NULL,0,2,NULL,NULL,2,'',2,2,2,0,2,0,2,NULL,0,NULL,'',2,'','','','','','','','','','',0,'1','2017-01-06 22:56:09',NULL,1),(29,'xiwizard_radiusserver_host','',0,2,0,2,'45!3000.0!80%!5000.0!100%',1,2,'',NULL,NULL,NULL,2,2,0,2,2,NULL,0,2,NULL,NULL,2,'',2,2,2,0,2,0,2,NULL,0,NULL,'',2,'','','','','radiusserver.png','','','','','',0,'1','2017-01-06 22:56:09',NULL,1),(30,'xiwizard_snmptrap_host','',0,2,0,2,'33!0!\"Trap host assumed to be UP\"',1,2,'',NULL,NULL,NULL,2,2,0,2,2,NULL,0,2,NULL,NULL,2,'',2,2,2,0,2,0,2,NULL,0,NULL,'',2,'','','','','','','','','','',0,'1','2017-01-06 22:56:10',NULL,1),(31,'xiwizard_tftp_host','',0,2,0,2,'86',1,2,'',NULL,NULL,NULL,2,2,0,2,2,NULL,0,2,NULL,NULL,2,'',2,2,2,0,2,0,2,NULL,0,NULL,'',2,'','','','','','','','','','',0,'1','2017-01-06 22:56:11',NULL,1),(32,'xiwizard_vmware_host','',0,2,0,2,'45!3000.0!80%!5000.0!100%',1,2,'',NULL,NULL,NULL,2,2,0,2,2,NULL,0,2,NULL,NULL,2,'',2,2,2,0,2,0,2,NULL,0,NULL,'',2,'','','','','','','','','','',0,'1','2017-01-06 22:56:11',NULL,1),(33,'xiwizard_vmware_guest','',0,2,0,2,'45!3000.0!80%!5000.0!100%',1,2,'',NULL,NULL,NULL,2,2,0,2,2,NULL,0,2,NULL,NULL,2,'',2,2,2,0,2,0,2,NULL,0,NULL,'',2,'','','','','','','','','','',0,'1','2017-01-06 22:56:11',NULL,1),(34,'xiwizard_watchguard_host','',0,2,0,2,'45!3000.0!80%!5000.0!100%',1,2,'',NULL,NULL,NULL,2,2,0,2,2,NULL,0,2,NULL,NULL,2,'',2,2,2,0,2,0,2,NULL,0,NULL,'',2,'','','','','','','','','','',0,'1','2017-01-06 22:56:11',NULL,1),(35,'xiwizard_websensor_host','',0,2,0,2,'45!3000.0!80%!5000.0!100%',1,2,'',NULL,NULL,NULL,2,2,0,2,2,NULL,0,2,NULL,NULL,2,'',2,2,2,0,2,0,2,NULL,0,NULL,'',2,'','','','','','','','','','',0,'1','2017-01-06 22:56:11',NULL,1),(36,'xiwizard_windowssnmp_host','',0,2,0,2,'45!3000.0!80%!5000.0!100%',1,2,'',NULL,NULL,NULL,2,2,0,2,2,NULL,0,2,NULL,NULL,2,'',2,2,2,0,2,0,2,NULL,0,NULL,'',2,'','','','','','','','','','',0,'1','2017-01-06 22:56:12',NULL,1),(37,'xiwizard_windowswmi_host','',0,2,0,2,'45!3000.0!80%!5000.0!100%',1,2,'',NULL,NULL,NULL,2,2,0,2,2,NULL,0,2,NULL,NULL,2,'',2,2,2,0,2,0,2,NULL,0,NULL,'',2,'','','','','','','','','','',0,'1','2017-01-06 22:56:12',NULL,1),(38,'linux-server','',0,2,0,2,'3',1,2,'',10,5,1,2,2,2,2,2,NULL,0,2,NULL,NULL,2,'',2,2,2,0,2,1,2,120,3,NULL,'d,u,r',2,'','','','','','','','','','',0,'1','2017-01-06 22:56:13',NULL,1),(39,'generic-host','',0,2,0,2,NULL,0,2,'',NULL,NULL,NULL,2,2,0,2,2,NULL,0,1,NULL,NULL,1,'',1,1,1,0,2,0,2,NULL,2,NULL,'',1,'','','','','','','','','','',0,'1','2017-01-06 22:56:13',NULL,1),(40,'windows-server','',0,2,1,2,'3',1,2,'',10,5,1,2,2,2,2,2,NULL,0,2,NULL,NULL,2,'',2,2,2,0,2,1,2,30,2,NULL,'d,r',2,'','','','','','','','','','',0,'1','2017-01-06 22:56:13',NULL,1),(41,'generic-printer','',0,2,0,2,'3',1,2,'',10,5,1,2,2,2,2,2,NULL,0,2,NULL,NULL,2,'',2,2,2,0,2,1,2,30,3,NULL,'d,r',2,'','','','','','','','','','',0,'1','2017-01-06 22:56:13',NULL,1),(42,'generic-switch','',0,2,0,2,'3',1,2,'',10,5,1,2,2,2,2,2,NULL,0,2,NULL,NULL,2,'',2,2,2,0,2,1,2,30,2,NULL,'d,r',2,'','','','','','','','','','',0,'1','2017-01-06 22:56:13',NULL,1),(43,'xiwizard_website_host','',0,2,0,2,'112',1,2,'',NULL,NULL,NULL,2,2,0,2,2,NULL,0,2,NULL,NULL,2,'',2,2,2,0,2,0,2,NULL,0,NULL,'',2,'','','','','','','','','','',0,'1','2017-01-06 22:56:14',NULL,1),(44,'xiwizard_webtransaction_host','',0,2,0,2,'112',1,2,'',NULL,NULL,NULL,2,2,0,2,2,NULL,0,2,NULL,NULL,2,'',2,2,2,0,2,0,2,NULL,0,NULL,'',2,'','','','','','','','','','',0,'1','2017-01-06 22:56:14',NULL,1),(45,'xiwizard_genericnetdevice_host','',0,2,0,2,'45!3000.0!80%!5000.0!100%',1,2,'',NULL,NULL,NULL,2,2,0,2,2,NULL,0,2,NULL,NULL,2,'',2,2,2,0,2,0,2,NULL,0,NULL,'',2,'','','','','','','','','','',0,'1','2017-01-06 22:56:14',NULL,1),(46,'xiwizard_printer_host','',0,2,0,2,'45!3000.0!80%!5000.0!100%',1,2,'',NULL,NULL,NULL,2,2,0,2,2,NULL,0,2,NULL,NULL,2,'',2,2,2,0,2,0,2,NULL,0,NULL,'',2,'','','','','','','','','','',0,'1','2017-01-06 22:56:14',NULL,1),(47,'xiwizard_windowsdesktop_host','',0,2,0,2,'45!3000.0!80%!5000.0!100%',1,2,'',NULL,NULL,NULL,2,2,0,2,2,NULL,0,2,NULL,NULL,2,'',2,2,2,0,2,0,2,NULL,0,NULL,'',2,'','','','','','','','','','',0,'1','2017-01-06 22:56:14',NULL,1),(48,'xiwizard_windowsserver_host','',0,2,0,2,'45!3000.0!80%!5000.0!100%',1,2,'',NULL,NULL,NULL,2,2,0,2,2,NULL,0,2,NULL,NULL,2,'',2,2,2,0,2,0,2,NULL,0,NULL,'',2,'','','','','','','','','','',0,'1','2017-01-06 22:56:14',NULL,1),(49,'xiwizard_switch_host','',0,2,0,2,'45!3000.0!80%!5000.0!100%',1,2,'',NULL,NULL,NULL,2,2,0,2,2,NULL,0,2,NULL,NULL,2,'',2,2,2,0,2,0,2,NULL,0,NULL,'',2,'','','','','','','','','','',0,'1','2017-01-06 22:56:14',NULL,1),(50,'xiwizard_linuxserver_host','',0,2,0,2,'45!3000.0!80%!5000.0!100%',1,2,'',NULL,NULL,NULL,2,2,0,2,2,NULL,0,2,NULL,NULL,2,'',2,2,2,0,2,0,2,NULL,0,NULL,'',2,'','','','','','','','','','',0,'1','2017-01-06 22:56:14',NULL,1);
/*!40000 ALTER TABLE `tbl_hosttemplate` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_info`
--

DROP TABLE IF EXISTS `tbl_info`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_info` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `key1` varchar(200) NOT NULL,
  `key2` varchar(200) NOT NULL,
  `version` varchar(50) NOT NULL,
  `language` varchar(50) NOT NULL,
  `infotext` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `keypair` (`key1`,`key2`,`version`,`language`)
) ENGINE=MyISAM AUTO_INCREMENT=223 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_info`
--

LOCK TABLES `tbl_info` WRITE;
/*!40000 ALTER TABLE `tbl_info` DISABLE KEYS */;
INSERT INTO `tbl_info` VALUES (1,'domain','domain','all','default','Common name of this domain. This field is for internal use only.'),(2,'domain','basedir','all','default','<p>Absolute path to your NagiosQL configuration directory.<br><br>Examples:<br>/etc/nagiosql/ <br>/usr/local/nagiosql/etc/<br><br>Be sure, that your configuration path settings are matching with your nagios.cfg! (cfg_file=<span style=\"color: red;\">/etc/nagiosql</span>/timeperiods.cfg)</p>'),(3,'domain','hostdir','all','default','NagiosQL writes one configuration file for every host. It is useful to store this files inside an own subdirectory below your Nagios configuration path.<br><br>Examples:<br>/etc/nagios/hosts <br>/usr/local/nagios/etc/hosts<br><br>Be sure, that your configuration settings are matching with your nagios.cfg!<br> (cfg_dir=<font color=\"red\">/etc/nagios/hosts</font>)'),(4,'domain','servicedir','all','default','NagiosQL writes services grouped into files identified by the service configuration names. It is useful to store this files inside an own subdirectory below your Nagios configuration path.<br><br>Examples:<br>/etc/nagios/services <br>/usr/local/nagios/etc/services<br><br>Be sure, that your configuration settings are matching with your nagios.cfg!<br> (cfg_dir=<font color=\"red\">/etc/nagios/services</font>)'),(5,'domain','backupdir','all','default','Absolute path to your NagiosQL configuration backup directory.<br><br>Examples:<br>/etc/nagios/backup <br>/usr/local/nagios/etc/backup<br><br>This directory is for internal configuration backups of NagiosQL and is not used by Nagios itself. '),(6,'domain','backuphostdir','all','default','Absolute path to your NagiosQL host configuration backup directory.<br><br>Examples:<br>/etc/nagios/backup/hosts <br>/usr/local/nagios/etc/backup/hosts<br><br>This directory is for internal configuration backups of NagiosQL only and is not used by Nagios itself.'),(7,'domain','backupservicedir','all','default','Absolute path to your NagiosQL service configuration backup directory.<br><br>Examples:<br>/etc/nagios/backup/services <br>/usr/local/nagios/etc/backup/services<br><br>This directory is for internal configuration backups of NagiosQL only and is not used by Nagios itself.'),(8,'domain','commandfile','all','default','Absolute path to your Nagios command file.<br><br>Examples:<br>/var/spool/nagios/nagios.cmd<br>/usr/local/nagios/var/rw/nagios.cmd<br><br>Be sure, that your command file path settings are matching with your nagios.cfg! (command_file=<font color=\"red\">/var/spool/nagios/nagios.cmd</font>)<br>(check_external_commands=1)<br><br>\r\nThis is used to reload Nagios directly from NagiosQL after changing a configuration.'),(9,'common','accesskeys','all','default','<p><strong>Access key/keyholes</strong></p>\r\n<p>NagiosQL uses a very simplified access control mechanism by using up to 8 keys.</p>\r\n<p>To access a secure object (menu, domain), a user must have a key for every defined keyhole.</p>\r\n<p><em>Example:</em></p>\r\n<p>User A has key 1,2,5,7 (can be defined in user management)<br>User B has key 3,5,7,8 (can be defined in user management)</p>\r\n<p>Menu 1 has keyhole 3,5<br>Menu 2 has keyhole 2,5,7<br>Menu 3 has no keyhole<br>Menu 4 has keyhole 4<br><br>User A has access to menu 2 and menu 3 (key 3 for menu 1 and key 4 for menu 4 are missing)<br>User B has access to menu 1 and menu 3 (key 2 for menu 2 and key 4 for menu 4 are missing)</p>\r\n<p>&nbsp;</p>\r\n<p>&nbsp;</p>'),(10,'user','webserverauth','all','default','<p><strong>User - webserver authentification</strong></p>\r\n<p>If your webserver uses authentification and the NagiosQL user name is the same which is actually logged in - the NagiosQL login process will passed. This means, that NagiosQL no longer shows a login page if this user is already logged in by webserver authentification.</p>\r\n<p><span style=\"color: #ff0000;\">This function will be implemented in a future NagiosQL version. Actually, this option is not implemented!</span></p>'),(11,'domain','nagiosbasedir','all','default','<p>Absolute path to your Nagios configuration directory.<br><br>Examples:<br>/etc/nagios/ <br>/usr/local/nagios/etc/</p>\r\n<p>Be sure, that your <span style=\"color: #ff0000;\">nagios.cfg</span> and <span style=\"color: #ff0000;\">cfg.cfg</span> ist located inside this directory. NagiosQL uses this to handle this two files.</p>'),(12,'domain','importdir','all','default','<p>Absolute path to your configuration import directory.<br><br>Examples:<br>/etc/nagiosql/import/ <br>/usr/local/nagios/etc/import/</p>\r\n<p>You can use this directory to store old or example configuration files in it which should be accessable by the importer of NagiosQL.</p>'),(13,'domain','binary','all','default','<p>Absolute path to your Nagios binary file.<br><br>Examples:<br>/usr/bin/nagios<br>/usr/local/nagios/bin/nagios<br><br> This is used to verify your configuration.</p>'),(14,'domain','pidfile','all','default','<p>Absolute path to your Nagios process file.<br><br>Examples:<br>/var/run/nagios/nagios.pid<br>/var/run/nagios/nagios.lock<br><br> This is used to check if nagios is running before sending a reload command to the nagios command file.</p>'),(15,'domain','version','all','default','<p>The nagios version which is running in this domain.</p>\r\n<p>Be sure you select the correct version here - otherweise not all configuration options are available or not supported options are shown.</p>\r\n<p>You can change this with a running configuration - NagiosQL will then upgrade or downgrade your configuration. Don\'t forget to write your complete configuration after a version change!</p>'),(16,'host','hostname','all','default','<p><strong>Host - host name</strong><br><br>This directive is used to define a short name used to identify the host. It is used in host group and service definitions to reference this particular host. Hosts can have multiple services (which are monitored) associated with them. When used properly, the $HOSTNAME$ macro will contain this short name.</p>\r\n<p><em>Parameter name:</em> host_name<br><em>Required:</em> yes</p>'),(17,'host','alias','all','default','<p><strong>Host - alias</strong><br><br>This directive is used to define a longer name or description used to identify the host. It is provided in order to allow you to more easily identify a particular host. When used properly, the $HOSTALIAS$ macro will contain this alias/description.</p>\r\n<p><em>Parameter name:</em> alias<br><em>Required:</em> yes</p>'),(18,'host','address','all','default','<p><strong>Host - address</strong></p>\r\n<p>This directive is used to define the address of the host. Normally, this is an IP address, although it could really be anything you want (so long as it can be used to check the status of the host). You can use a FQDN to identify the host instead of an IP address, but if DNS services are not availble this could cause problems. When used properly, the $HOSTADDRESS$ macro will contain this address.</p>\r\n<p><strong>Note:</strong> If you do not specify an address directive in a host definition, the name of the host will be used as its address. A word of caution about doing this, however - if DNS fails, most of your service checks will fail because the plugins will be unable to resolve the host name.</p>\r\n<p><em>Parameter name:</em> address<br><em>Required:</em> yes</p>'),(19,'host','display_name','all','default','<p><strong>Host - display name</strong></p>\r\n<p>This directive is used to define an alternate name that should be displayed in the web interface for this host. If not specified, this defaults to the value you specify for the <em>host_name</em> directive.</p>\r\n<p><strong>Note:</strong> The current CGIs do not use this option, although future versions of the web interface will.</p>\r\n<p><em>Parameter name:</em> display_name<br><em>Required:</em> no</p>'),(20,'host','parents','all','default','<p><strong>Host - parents</strong></p>\r\n<p>This directive is used to define a comma-delimited list of short names of the \"parent\" hosts for this particular host. Parent hosts are typically routers, switches, firewalls, etc. that lie between the monitoring host and a remote hosts. A router, switch, etc. which is closest to the remote host is considered to be that host\'s \"parent\". Read the \"Determining Status and Reachability of Network Hosts\" document for more information.</p>\r\n<p>If this host is on the same network segment as the host doing the monitoring (without any intermediate routers, etc.) the host is considered to be on the local network and will not have a parent host. Leave this value blank if the host does not have a parent host (i.e. it is on the same segment as the Nagios host). The order in which you specify parent hosts has no effect on how things are monitored.</p>\r\n<p><em>Parameter name:</em> parents<br><em>Required:</em> no</p>'),(21,'host','hostgroups','all','default','<p><strong>Host - hostgroup names</strong></p>\r\n<p>This directive is used to identify the <em>short name(s)</em> of the hostgroup(s) that the host belongs to. Multiple hostgroups should be separated by commas. This directive may be used as an alternative to (or in addition to) using the members directive in hostgroup definitions.<span style=\"color: #ff0000;\"><span style=\"color: #000000;\"> </span></span></p>\r\n<p><span style=\"color: #ff0000;\"><span style=\"color: #000000;\"><strong>NagiosQL:</strong> If a hostgroup is defined here - this host will <span style=\"color: #ff0000;\">not be selected</span> inside the member field of the same hostgroup definition! <br></span></span></p>\r\n<p><em>Parameter name:</em> hostgroups<br><em>Required:</em> no</p>'),(22,'common','tploptions','3','default','<p><strong>Cancelling Inheritance of String Values</strong></p>\r\n<p>In some cases you may not want your host, service, or contact definitions to inherit values of string variables from the templates they reference. If this is the case, you can specify \"<strong>null</strong>\" as the value of the variable that you do not want to inherit.</p>\r\n<p><strong><br>Additive Inheritance of String Values</strong></p>\r\n<p>Nagios gives preference to local variables instead of values inherited from templates. In most cases local variable values override those that are defined in templates. In some cases it makes sense to allow Nagios to use the values of inherited <em>and</em> local variables together.</p>\r\n<p>This \"additive inheritance\" can be accomplished by prepending the local variable value with a plus sign (<strong>+</strong>).  This features is only available for standard (non-custom) variables that contain string values.</p>'),(23,'host','check_command','all','default','<p><strong>Host - check command</strong><br><br>This directive is used to specify the <em>short name</em> of the command that should be used to check if the host is up or down. Typically, this command would try and ping the host to see if it is \"alive\". The command must return a status of OK (0) or Nagios will assume the host is down.</p>\r\n<p>If you leave this argument blank, the host will <em>not</em> be actively checked. Thus, Nagios will likely always assume the host is up (it may show up as being in a \"PENDING\" state in the web interface). This is useful if you are monitoring printers or other devices that are frequently turned off. The maximum amount of time that the notification command can run is controlled by the host_check_timeout option.</p>\r\n<p><em>Parameter name:</em> check_command<br><em>Required:</em> no</p>'),(24,'host','arguments','all','default','<p><strong>Host - arguments</strong></p>\r\n<p>The values defined here will replace the according argument variable behind the selected command. Up to 8 argument variables are supported. Be sure, that you defines a valid value for each required argument variable.</p>'),(25,'host','templateadd','all','default','<p><strong>Host - Templates</strong></p>\r\n<p>You can add one or more host templates to a host configuration. Nagios will add the definitions from each template to a host configuration.</p>\r\n<p>If you add more than one template - the templates from the bottom to the top will be used to overwrite configuration items which are defined inside templates before.</p>\r\n<p>The host configuration itselves will overwrite all values which are defined in templates before and pass all values which are not defined.</p>'),(26,'host','initial_state','3','default','<p><strong>Host - initial state</strong></p>\r\n<p>By default Nagios will assume that all hosts are in UP states when in starts. You can override the initial state for a host by using this directive. Valid options are: <strong><br>o</strong> = UP, <br><strong>d</strong> = DOWN, and <br><strong>u</strong> = UNREACHABLE.</p>\r\n<p><em>Parameter name:</em> initial_state<em><br>Required:</em> no</p>\r\n<p>&nbsp;</p>'),(27,'host','retry_interval','3','default','<p><strong>Host - retry interval</strong></p>\r\n<p>This directive is used to define the number of \"time units\" to wait before scheduling a re-check of the hosts. Hosts are rescheduled at the retry interval when they have changed to a non-UP state. Once the host has been retried <strong>max_check_attempts</strong> times without a change in its status, it will revert to being scheduled at its \"normal\" rate as defined by the <strong>check_interval</strong> value. Unless you\'ve changed the interval_length directive from the default value of 60, this number will mean minutes.  More information on this value can be found in the check scheduling documentation.</p>\r\n<p><em>Parameter name:</em> retry_interval<em><br>Required:</em> no</p>'),(28,'host','max_check_attempts','all','default','<p><strong>Host - max check attempts</strong></p>\r\n<p>This directive is used to define the number of times that Nagios will retry the host check command if it returns any state other than an OK state. Setting this value to 1 will cause Nagios to generate an alert without retrying the host check again. Note: If you do not want to check the status of the host, you must still set this to a minimum value of 1. To bypass the host check, just leave the <em>check_command</em> option blank.</p>\r\n<p><em>Parameter name:</em> max_check_attempts<em><br>Required:</em> yes</p>'),(29,'host','check_interval','all','default','<p><strong>Host - check interval</strong></p>\r\n<p>This directive is used to define the number of \"time units\" between regularly scheduled checks of the host. Unless you\'ve changed the interval_length directive from the default value of 60, this number will mean minutes.  More information on this value can be found in the check scheduling documentation.</p>\r\n<p><em>Parameter name:</em> check_interval<em><br>Required:</em> no</p>'),(30,'host','active_checks_enabled','all','default','<p><strong>Host - active checks enabled<br></strong></p>\r\n<p>This directive is used to determine whether or not active checks (either regularly scheduled or on-demand) of this host are enabled. Values: 0 = disable active host checks, 1 = enable active host checks.</p>\r\n<p><em>Parameter name:</em> active_checks_enabled<br><em>Required:</em> no</p>'),(31,'host','passive_checks_enabled','all','default','<p><strong>Host - passive checks enabled<br> </strong></p>\r\n<p>This directive is used to determine whether or not passive checks are enabled for this host. Values: 0 = disable passive host checks, 1 = enable passive host checks.</p>\r\n<p><em>Parameter name:</em> passive_checks_enabled<br> <em>Required:</em> no</p>'),(32,'host','check_period','all','default','<p><strong>Host - check period<br> </strong></p>\r\n<p>This directive is used to specify the short name of the time period during which active checks of this host can be made.</p>\r\n<p><em>Parameter name:</em> check_period<br> <em>Required:</em> yes</p>'),(33,'host','freshness_threshold','all','default','<p><strong>Host - freshness threshold<br> </strong></p>\r\n<p>This directive is used to specify the freshness threshold (in seconds) for this host. If you set this directive to a value of 0, Nagios will determine a freshness threshold to use automatically.</p>\r\n<p><em>Parameter name:</em> freshness_threshold<br> <em>Required:</em> no</p>'),(34,'host','check_freshness','all','default','<p><strong>Host - check freshness<br> </strong></p>\r\n<p>This directive is used to determine whether or not freshness checks are enabled for this host. Values: 0 = disable freshness checks, 1 = enable freshness checks.</p>\r\n<p><em>Parameter name:</em> check_freshness<br> <em>Required:</em> no</p>'),(35,'host','obsess_over_host','all','default','<p><strong>Host - obsess over host<br> </strong></p>\r\n<p>This directive determines whether or not checks for the host will be \"obsessed\" over using the ochp_command.</p>\r\n<p><em>Parameter name:</em> obsess_over_host<br> <em>Required:</em> no</p>'),(36,'host','event_handler','all','default','<p><strong>Host - event handler<br> </strong></p>\r\n<p>This directive is used to specify the <em>short name</em> of the command that should be run whenever a change in the state of the host is detected (i.e. whenever it goes down or recovers). Read the documentation on event handlers for a more detailed explanation of how to write scripts for handling events. The maximum amount of time that the event handler command can run is controlled by the event_handler_timeout option.</p>\r\n<p><em>Parameter name:</em> event_handler<br> <em>Required:</em> no</p>'),(37,'host','event_handler_enabled','all','default','<p><strong>Host - event handler enabled<br> </strong></p>\r\n<p>This directive is used to determine whether or not the event handler for this host is enabled. Values: 0 = disable host event handler, 1 = enable host event handler.</p>\r\n<p><em>Parameter name:</em> event_handler_enabled<br> <em>Required:</em> no</p>'),(38,'host','low_flap_threshold','all','default','<p><strong>Host - low flap threshold<br> </strong></p>\r\n<p>This directive is used to specify the low state change threshold used in flap detection for this host. If you set this directive to a value of 0, the program-wide value specified by the low_host_flap_threshold directive will be used.</p>\r\n<p><em>Parameter name:</em> low_flap_threshold<br> <em>Required:</em> no</p>'),(39,'host','high_flap_threshold','all','default','<p><strong>Host - high flap threshold<br> </strong></p>\r\n<p>This directive is used to specify the high state change threshold used in flap detection for this host. If you set this directive to a value of 0, the program-wide value specified by the high_host_flap_threshold directive will be used.</p>\r\n<p><em>Parameter name:</em> high_flap_threshold<br> <em>Required:</em> no</p>'),(40,'host','flap_detection_enabled','all','default','<p><strong>Host - flap detection enabled<br> </strong></p>\r\n<p>This directive is used to determine whether or not flap detection is enabled for this host. Values: 0 = disable host flap detection, 1 = enable host flap detection.</p>\r\n<p><em>Parameter name:</em> flap_detection_enabled<br> <em>Required:</em> no</p>'),(41,'host','flap_detection_options','3','default','<p><strong>Host - flap detection options<br> </strong></p>\r\n<p>This directive is used to determine what host states the flap detection logic will use for this host.  Valid options are a combination of one or more of the following: <strong><br>o</strong> = UP states, <br><strong>d</strong> = DOWN states, <br><strong>u</strong> =  UNREACHABLE states.</p>\r\n<p><em>Parameter name:</em> flap_detection_options<br> <em>Required:</em> no</p>'),(42,'host','retain_status_information','all','default','<p><strong>Host - retain status information<br></strong></p>\r\n<p>This directive is used to determine whether or not status-related information about the host is retained across program restarts. This is only useful if you have enabled state retention using the retain_state_information directive.  Value: 0 = disable status information retention, 1 = enable status information retention.</p>\r\n<p><em>Parameter name:</em> retain_status_information<em><br>Required:</em> no</p>'),(43,'host','retain_nonstatus_information','all','default','<p><strong>Host - retain nonstatus information<br></strong></p>\r\n<p>This directive is used to determine whether or not non-status information about the host is retained across program restarts. This is only useful if you have enabled state retention using the retain_state_information directive.  Value: 0 = disable non-status information retention, 1 = enable non-status information retention.</p>\r\n<p><em>Parameter name:</em> retain_nonstatus_information<em><br>Required:</em> no</p>'),(45,'host','contacts','3','default','<p><strong>Host - contacts<br></strong></p>\r\n<p>This is a list of the <em>short names</em> of the contacts that should be notified whenever there are problems (or recoveries) with this host. Multiple contacts should be separated by commas. Useful if you want notifications to go to just a few people and don\'t want to configure contact groups.  You must specify at least one contact or contact group in each host definition.</p>\r\n<p><em>Parameter name:</em> <em>contacs<br>Required:</em> yes (at least one contact <strong>or</strong> contact group)</p>'),(46,'host','contactgroups','all','default','<p><strong>Host - contact groups<br></strong></p>\r\n<p>This is a list of the <em>short names</em> of the contact groups that should be notified whenever there are problems (or recoveries) with this host. Multiple contact groups should be separated by commas. You must specify at least one contact or contact group in each host definition.</p>\r\n<p><em>Parameter name:</em> contact_groups<br><em>Required:</em> yes (at least one contact <strong>or</strong> contact group)</p>'),(47,'host','notification_period','all','default','<p><strong>Host - notification period<br></strong></p>\r\n<p>This directive is used to specify the short name of the time period during which notifications of events for this host can be sent out to contacts. If a host goes down, becomes unreachable, or recoveries during a time which is not covered by the time period, no notifications will be sent out.</p>\r\n<p><em>Parameter name:</em> notification_period<br><em>Required:</em> yes</p>'),(48,'host','notification_options','all','default','<p><strong>Host - notification options<br></strong></p>\r\n<p>This directive is used to determine when notifications for the host should be sent out. Valid options are a combination of one or more of the following: <br><strong>d</strong> = send notifications on a DOWN state, <br><strong>u</strong> = send notifications on an UNREACHABLE state, <strong><br>r</strong> = send notifications on recoveries (OK state), <br><strong>f</strong> = send notifications when the host starts and stops flapping, and <br><strong>s</strong> = send notifications when scheduled downtime starts and ends.  <br>If you do not specify any notification options, Nagios will assume that you want notifications to be sent out for all possible states.</p>\r\n<p>Example: If you specify <strong>d,r</strong> in this field, notifications will only be sent out when the host goes DOWN and when it recovers from a DOWN state.</p>\r\n<p><em>Parameter name:</em> notification_options<br><em>Required:</em> yes</p>'),(51,'host','notification_enabled','all','default','<p><strong>Host - notification enabled<br></strong></p>\r\n<p>This directive is used to determine whether or not notifications for this host are enabled. Values: 0 = disable host notifications, 1 = enable host notifications.</p>\r\n<p><em>Parameter name:</em> notification_enabled<br><em>Required:</em> yes</p>'),(52,'host','stalking_options','all','default','<p><strong>Host - stalking options<br></strong></p>\r\n<p>This directive determines which host states \"stalking\" is enabled for. Valid options are a combination of one or more of the following: <strong><br>o</strong> = stalk on UP states, <br><strong>d</strong> = stalk on DOWN states, and <br><strong>u</strong> = stalk on UNREACHABLE states.</p>\r\n<p><em>Parameter name:</em> stalking_options<br><em>Required:</em> yes</p>'),(53,'host','process_perf_data','all','default','<p><strong>Host - process performance data<br></strong></p>\r\n<p>This directive is used to determine whether or not the processing of performance data is enabled for this host. Values: 0 = disable performance data processing, 1 = enable performance data processing.</p>\r\n<p><em>Parameter name:</em> process_perf_data<em><br>Required:</em> no</p>'),(54,'host','notification_interval','all','default','<p><strong>Host - notification interval<br></strong></p>\r\n<p>This directive is used to define the number of \"time units\" to wait before re-notifying a contact that this service is <em>still</em> down or unreachable.  Unless you\'ve changed the interval_length directive from the default value of 60, this number will mean minutes.  If you set this value to 0, Nagios will <em>not</em> re-notify contacts about problems for this host - only one problem notification will be sent out.</p>\r\n<p><em>Parameter name:</em> notification_interval<br><em>Required:</em> yes</p>'),(55,'host','first_notification_delay','all','default','<p><strong>Host - first notification delay<br></strong></p>\r\n<p>This directive is used to define the number of \"time units\" to wait before sending out the first problem notification when this host enters a non-UP state. Unless you\'ve changed the interval_length directive from the default value of 60, this number will mean minutes. If you set this value to 0, Nagios will start sending out notifications immediately.</p>\r\n<p><em>Parameter name:</em> first_notification_delay<br><em>Required:</em> no</p>'),(56,'host','notes','3','default','<p><strong>Host - notes<br> </strong></p>\r\n<p>This directive is used to define an optional string of notes pertaining to the host. If you specify a note here, you will see the it in the extended information CGI (when you are viewing information about the specified host).</p>\r\n<p><em>Parameter name:</em> notes<br> <em>Required:</em> no</p>'),(57,'host','vrml_image','3','default','<p><strong>Host - vrml image<br> </strong></p>\r\n<p>This variable is used to define the name of a GIF, PNG, or JPG image that should be associated with this host. This image will be used as the texture map for the specified host in the statuswrl CGI.  Unlike the image you use for the <em>icon_image</em> variable, this one should probably <em>not</em> have any transparency.</p>\r\n<p>If it does, the host object will look a bit wierd.  Images for hosts are assumed to be in the <strong>logos/</strong> subdirectory in your HTML images directory (i.e. <em>/usr/local/nagios/share/images/logos</em>).</p>\r\n<p><em>Parameter name:</em> vrml_image<br> <em>Required:</em> no</p>'),(58,'host','notes_url','3','default','<p><strong>Host - notes url<br> </strong></p>\r\n<p>This variable is used to define an optional URL that can be used to provide more information about the host. If you specify an URL, you will see a red folder icon in the CGIs (when you are viewing host information) that links to the URL you specify here. Any valid URL can be used.</p>\r\n<p>If you plan on using relative paths, the base path will the the same as what is used to access the CGIs (i.e. <em>/cgi-bin/nagios/</em>). This can be very useful if you want to make detailed information on the host, emergency contact methods, etc. available to other support staff.</p>\r\n<p><em>Parameter name:</em> notes_url<br> <em>Required:</em> no</p>'),(59,'host','status_image','3','default','<p><strong>Host - statusmap image<br> </strong></p>\r\n<p>This variable is used to define the name of an image that should be associated with this host in the statusmap CGI. You can specify a JPEG, PNG, and GIF image if you want, although I would strongly suggest using a GD2 format image, as other image formats will result in a lot of wasted CPU time when the statusmap image is generated.</p>\r\n<p>GD2 images can be created from PNG images by using the <strong>pngtogd2</strong> utility supplied with Thomas Boutell\'s gd library .  The GD2 images should be created in <em>uncompressed</em> format in order to minimize CPU load when the statusmap CGI is generating the network map image.</p>\r\n<p>The image will look best if it is 40x40 pixels in size. You can leave these option blank if you are not using the statusmap CGI. Images for hosts are assumed to be in the <strong>logos/</strong> subdirectory in your HTML images directory (i.e. <em>/usr/local/nagios/share/images/logos</em>).</p>\r\n<p><em>Parameter name:</em> statusmap_image<br> <em>Required:</em> no</p>'),(60,'host','action_url','3','default','<p><strong>Host - action url<br> </strong></p>\r\n<p>This directive is used to define an optional URL that can be used to provide more actions to be performed on the host. If you specify an URL, you will see a red \"splat\" icon in the CGIs (when you are viewing host information) that links to the URL you specify here. Any valid URL can be used.</p>\r\n<p>If you plan on using relative paths, the base path will the the same as what is used to access the CGIs (i.e. <em>/cgi-bin/nagios/</em>).</p>\r\n<p><em>Parameter name:</em> action_url<br> <em>Required:</em> no</p>'),(61,'host','icon_image','3','default','<p><strong>Host - icon image<br> </strong></p>\r\n<p>This variable is used to define the name of a GIF, PNG, or JPG image that should be associated with this host. This image will be displayed in the various places in the CGIs. The image will look best if it is 40x40 pixels in size. Images for hosts are assumed to be in the <strong>logos/</strong> subdirectory in your HTML images directory (i.e. <em>/usr/local/nagios/share/images/logos</em>).</p>\r\n<p><em>Parameter name:</em> icon_image<br> <em>Required:</em> no</p>'),(62,'host','2d_coords','3','default','<p><strong>Host - 2D coords<br> </strong></p>\r\n<p>This variable is used to define coordinates to use when drawing the host in the statusmap CGI. Coordinates should be given in positive integers, as they correspond to physical pixels in the generated image. The origin for drawing (0,0) is in the upper left hand corner of the image and extends in the positive x direction (to the right) along the top of the image and in the positive y direction (down) along the left hand side of the image.</p>\r\n<p>For reference, the size of the icons drawn is usually about 40x40 pixels (text takes a little extra space). The coordinates you specify here are for the upper left hand corner of the host icon that is drawn. Note: Don\'t worry about what the maximum x and y coordinates that you can use are. The CGI will automatically calculate the maximum dimensions of the image it creates based on the largest x and y coordinates you specify.</p>\r\n<p><em>Parameter name:</em> 2d_coords<br> <em>Required:</em> no</p>'),(63,'host','icon_image_alt_text','3','default','<p><strong>Host - icon image alt<br> </strong></p>\r\n<p>This variable is used to define an optional string that is used in the ALT tag of the image specified by the <em>icon image</em> <em></em> argument.</p>\r\n<p><em>Parameter name:</em> icon_image_alt<br> <em>Required:</em> no</p>'),(64,'host','3d_coords','3','default','<p><strong>Host - 3D coords<br> </strong></p>\r\n<p>This variable is used to define coordinates to use when drawing the host in the statuswrl CGI. Coordinates can be positive or negative real numbers. The origin for drawing is (0.0,0.0,0.0). For reference, the size of the host cubes drawn is 0.5 units on each side (text takes a little more space). The coordinates you specify here are used as the center of the host cube.</p>\r\n<p><em>Parameter name:</em> 3d_coords<br> <em>Required:</em> no</p>'),(65,'common','free_variables_name','all','default','<p><strong>Free variables (custom object variables)<br></strong></p>\r\n<p>NagiosQL supports custom object variables.</p>\r\n<p>There are a few important things that you should note about custom variables:</p>\r\n<ul>\r\n<li>Custom variable names must begin with an underscore (_) to prevent name collision with standard variables </li>\r\n<li>Custom variable names are case-insensitive </li>\r\n<li>Custom variables are inherited from object templates like normal variables </li>\r\n<li>Scripts can reference custom variable values with macros and environment variables </li>\r\n</ul>\r\n<p><em>Examples</em></p>\r\n<p><span style=\"font-family: courier new,courier;\">define host{<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; host_name linuxserver<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; _mac_address    00:06:5B:A6:AD:AA   ; &lt;-- Custom MAC_ADDRESS variable<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; _rack_number   R32     ; &lt;-- Custom RACK_NUMBER variable<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; ...<br>}</span></p>'),(66,'common','free_variables_value','all','default','<p><strong>Free variables (custom object variables)<br></strong></p>\r\n<p>NagiosQL supports custom object variables.</p>\r\n<p>There are a few important things that you should note about custom variables:</p>\r\n<ul>\r\n<li>Custom variable names must begin with an underscore (_) to prevent name collision with standard variables </li>\r\n<li>Custom variable names are case-insensitive </li>\r\n<li>Custom variables are inherited from object templates like normal variables </li>\r\n<li>Scripts can reference custom variable values with macros and environment variables </li>\r\n</ul>\r\n<p><em>Examples</em></p>\r\n<p><span style=\"font-family: courier new,courier;\">define host{<br> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; host_name   linuxserver<br> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; _mac_address   00:06:5B:A6:AD:AA   ; &lt;-- Custom MAC_ADDRESS variable<br> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; _rack_number  R32     ; &lt;-- Custom RACK_NUMBER variable<br> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; ...<br> }</span></p>'),(67,'host','genericname','all','default','<p><strong>Host - generic name</strong></p>\r\n<p>It is possible to use a host definition as a template for other host configurations. If this definition should be used as template, a generic template name must be defined.</p>\r\n<p>We do not recommend to do this - it is more open to define a separate host template than to use this option.</p>\r\n<p><em>Parameter name:</em> name<em><br>Required:</em> no</p>'),(68,'service','config_name','all','default','<p><strong>Service - config name</strong></p>\r\n<p>This directive is used to specify a common config name for a group of service definitions. This is a NagiosQL parameter and it will not be written to the configuration file. Every service definitions with the same configuration name will stored in one file. The configuration name is also the file name of this configuration set.</p>'),(69,'service','hosts','all','default','<p><strong>Service - host name<br> </strong></p>\r\n<p>This directive is used to specify the <em>short name(s)</em> of the host(s) that the service \"runs\" on or is associated with.</p>\r\n<p><em>Parameter name:</em> host_name<br> <em>Required:</em> yes (no, if a hostgroup is defined)</p>'),(70,'service','hostgroups','all','default','<p><strong>Service</strong><strong> - hostgroup name<br> </strong></p>\r\n<p>This directive is used to specify the <em>short name(s)</em> of the hostgroup(s) that the service \"runs\" on or is associated with. The hostgroup_name may be used instead of, or in addition to, the host_name directive.</p>\r\n<p><em>Parameter name:</em> hostgroup_name<br> <em>Required:</em> no (yes, if no host is defined)</p>'),(71,'service','service_description','all','default','<p><strong>Service</strong><strong> - service description<br> </strong></p>\r\n<p>This directive is used to define the description of the service, which may contain spaces, dashes, and colons (semicolons, apostrophes, and quotation marks should be avoided). No two services associated with the same host can have the same description. Services are uniquely identified with their <em>host_name</em> and <em>service_description</em> directives.</p>\r\n<p><em>Parameter name:</em> service_description<br> <em>Required:</em> yes</p>'),(72,'service','service_groups','all','default','<p><strong>Service</strong><strong> - servicegroups<br> </strong></p>\r\n<p>This directive is used to identify the <em>short name(s)</em> of the servicegroup(s) that the service belongs to. Multiple servicegroups should be separated by commas. This directive may be used as an alternative to using the <em>members</em> directive in servicegroup definitions.</p>\r\n<p><span style=\"color: #ff0000;\"><span style=\"color: #000000;\"><strong>NagiosQL:</strong> If a servicegroup is defined here - this service will <span style=\"color: #ff0000;\">not be selected</span> inside the member field of the same servicegroup definition! </span></span></p>\r\n<p><em>Parameter name:</em> servicegroups<br> <em>Required:</em> no</p>'),(73,'service','display_name','all','default','<p><strong>Service</strong><strong> - display name<br> </strong></p>\r\n<p>This directive is used to define an alternate name that should be displayed in the web interface for this service. If not specified, this defaults to the value you specify for the <em>service_description</em> directive.  Note:  The current CGIs do not use this option, although future versions of the web interface will.</p>\r\n<p><em>Parameter name:</em> display_name<br> <em>Required:</em> no</p>'),(74,'service','check_command','all','default','<p><strong>Service</strong><strong> - check command<br> </strong></p>\r\n<p>This directive is used to specify the <em>short name</em> of the command that Nagios will run in order to check the status of the service. The maximum amount of time that the service check command can run is controlled by the service_check_timeout option.</p>\r\n<p><em>Parameter name:</em> check_command<br> <em>Required:</em> yes</p>'),(75,'service','argument','all','default','<p><strong>Service - arguments</strong></p>\r\n<p>The values defined here will replace the according argument variable behind the selected command. Up to 8 argument variables are supported. Be sure, that you defines a valid value for each required argument variable.</p>'),(76,'service','templateadd','all','default','<p><strong>Service - Templates</strong></p>\r\n<p>You can add one or more service templates to a service configuration. Nagios will add the definitions from each template to a service configuration.</p>\r\n<p>If you add more than one template - the templates from the bottom to the top will be used to overwrite configuration items which are defined inside templates before.</p>\r\n<p>The host configuration itselves will overwrite all values which are defined in templates before and pass all values which are not defined.</p>'),(77,'service','initial_state','3','default','<p><strong>Service - initial state<br> </strong></p>\r\n<p>By default Nagios will assume that all services are in OK states when in starts. You can override the initial state for a service by using this directive. Valid options are: <strong><br>o</strong> = OK,<br> <strong>w</strong> = WARNING, <strong><br>u</strong> = UNKNOWN, and <strong><br>c</strong> = CRITICAL.</p>\r\n<p><em>Parameter name:</em> initial_state<br> <em>Required:</em> no</p>'),(78,'service','retry_interval','3','default','<p><strong>Service - retry interval<br> </strong></p>\r\n<p>This directive is used to define the number of \"time units\" to wait before scheduling a re-check of the service. Services are rescheduled at the retry interval when they have changed to a non-OK state. Once the service has been retried <strong>max_check_attempts</strong> times without a change in its status, it will revert to being scheduled at its \"normal\" rate as defined by the <strong>check_interval</strong> value. Unless you\'ve changed the interval_length directive from the default value of 60, this number will mean minutes.  More information on this value can be found in the check scheduling documentation.</p>\r\n<p><em>Parameter name:</em> retry_interval<br> <em>Required:</em> yes</p>'),(79,'service','max_check_attempts','all','default','<p><strong>Service - max check attempts<br> </strong></p>\r\n<p>This directive is used to define the number of times that Nagios will retry the service check command if it returns any state other than an OK state. Setting this value to 1 will cause Nagios to generate an alert without retrying the service check again.</p>\r\n<p><em>Parameter name:</em> max_check_attempts<br> <em>Required:</em> yes</p>'),(80,'service','check_interval','all','default','<p><strong>Service - check interval<br> </strong></p>\r\n<p>This directive is used to define the number of \"time units\" to wait before scheduling the next \"regular\" check of the service. \"Regular\" checks are those that occur when the service is in an OK state or when the service is in a non-OK state, but has already been rechecked <strong>max_check_attempts</strong> number of times.  Unless you\'ve changed the interval_length directive from the default value of 60, this number will mean minutes.  More information on this value can be found in the check scheduling documentation.</p>\r\n<p><em>Parameter name:</em> check_interval<br> <em>Required:</em> yes</p>'),(81,'service','active_checks_enabled','all','default','<p><strong>Service - active checks enabled<br> </strong></p>\r\n<p>This directive is used to determine whether or not active checks of this service are enabled. Values: 0 = disable active service checks, 1 = enable active service checks.</p>\r\n<p><em>Parameter name:</em> active_checks_enabled<br> <em>Required:</em> no</p>'),(82,'service','passive_checks_enabled','all','default','<p><strong>Service - passive checks enabled<br> </strong></p>\r\n<p>This directive is used to determine whether or not passive checks of this service are enabled. Values: 0 = disable passive service checks, 1 = enable passive service checks.</p>\r\n<p><em>Parameter name:</em> passive_checks_enabled<br> <em>Required:</em> no</p>'),(83,'service','parallelize_checks','2','default','<p><strong>Service - </strong><strong>parallelize check</strong></p>\r\n<p>This directive is used to determine whether or not the service check can be parallelized. By default, all service checks are parallelized. Disabling parallel checks of services can result in serious performance problems. More information on service check parallelization can be found in the nagios documentation.</p>\r\n<p>Values: 0 = service check cannot be parallelized (use with caution!), 1 = service check can be parallelized.</p>\r\n<p><em>Parameter name:</em> parallelize_check<br> <em>Required:</em> no</p>'),(84,'service','check_period','all','default','<p><strong>Service - check period<br> </strong></p>\r\n<p>This directive is used to specify the short name of the time period during which active checks of this service can be made.</p>\r\n<p><em>Parameter name:</em> check_period<br> <em>Required:</em> yes</p>'),(85,'service','freshness_threshold','all','default','<p><strong>Service - </strong><strong>freshness threshold</strong></p>\r\n<p>This directive is used to specify the freshness threshold (in seconds) for this service. If you set this directive to a value of 0, Nagios will determine a freshness threshold to use automatically.</p>\r\n<p><em>Parameter name:</em> freshness_threshold<br> <em>Required:</em> no</p>'),(86,'service','check_freshness','all','default','<p><strong>Service - </strong><strong>check freshness</strong></p>\r\n<p>This directive is used to determine whether or not freshness checks are enabled for this service. Values: 0 = disable freshness checks, 1 = enable freshness checks.</p>\r\n<p><em>Parameter name:</em> check_freshness<br> <em>Required:</em> no</p>'),(87,'service','obsess_over_service','all','default','<p><strong>Service - </strong><strong>obsess over service</strong></p>\r\n<p>This directive determines whether or not checks for the service will be \"obsessed\" over using the ocsp_command.</p>\r\n<p><em>Parameter name:</em> obsess_over_service<br> <em>Required:</em> no</p>'),(88,'service','event_handler','all','default','<p><strong>Service - </strong><strong>event handler</strong></p>\r\n<p>This directive is used to specify the <em>short name</em> of the command that should be run whenever a change in the state of the service is detected (i.e. whenever it goes down or recovers). Read the documentation on event handlers for a more detailed explanation of how to write scripts for handling events. The maximum amount of time that the event handler command can run is controlled by the event_handler_timeout option.</p>\r\n<p><em>Parameter name:</em> event_handler<br> <em>Required:</em> no</p>'),(89,'service','event_handler_enabled','all','default','<p><strong>Service - </strong><strong>event handler enabled</strong></p>\r\n<p>This directive is used to determine whether or not the event handler for this service is enabled. Values: 0 = disable service event handler, 1 = enable service event handler.</p>\r\n<p><em>Parameter name:</em> event_handler_enabled<br> <em>Required:</em> no</p>'),(90,'service','low_flap_threshold','all','default','<p><strong>Service - </strong><strong>low flap threshold</strong></p>\r\n<p>This directive is used to specify the low state change threshold used in flap detection for this service. More information on flap detection can be found in the nagios documentation.  If you set this directive to a value of 0, the program-wide value specified by the low_service_flap_threshold  directive will be used.</p>\r\n<p><em>Parameter name:</em> low_flap_threshold<br> <em>Required:</em> no</p>'),(91,'service','high_flap_threshold','all','default','<p><strong>Service - </strong><strong>high flap threshold</strong></p>\r\n<p>This directive is used to specify the high state change threshold used in flap detection for this service. More information on flap detection can be found in the nagios documentation.  If you set this directive to a value of 0, the program-wide value specified by the high_service_flap_threshold directive will be used.</p>\r\n<p><em>Parameter name:</em> high_flap_threshold<br> <em>Required:</em> no</p>'),(92,'service','flap_detection_enabled','all','default','<p><strong>Service - </strong><strong>flap detection enabled</strong></p>\r\n<p>This directive is used to determine whether or not flap detection is enabled for this service. More information on flap detection can be found in the nagios documentation. Values: 0 = disable service flap detection, 1 = enable service flap detection.</p>\r\n<p><em>Parameter name:</em> flap_detection_enabled<br> <em>Required:</em> no</p>'),(93,'service','flap_detection_options','3','default','<p><strong>Service - </strong><strong>flap detection options</strong></p>\r\n<p>This directive is used to determine what service states the flap detection logic will use for this service.  Valid options are a combination of one or more of the following: <strong><br>o</strong> = OK states, <br><strong>w</strong> = WARNING states, <br><strong>c</strong> = CRITICAL states, <br><strong>u</strong> = UNKNOWN states.</p>\r\n<p><em>Parameter name:</em> flap_detection_options<br> <em>Required:</em> no</p>'),(94,'service','retain_status_information','all','default','<p><strong>Service - </strong><strong>retain status information</strong></p>\r\n<p>This directive is used to determine whether or not status-related information about the service is retained across program restarts. This is only useful if you have enabled state retention using the retain_state_information directive.  Value: 0 = disable status information retention, 1 = enable status information retention.</p>\r\n<p><em>Parameter name:</em> retain_status_information<br> <em>Required:</em> no</p>'),(95,'service','retain_nonstatus_information','all','default','<p><strong>Service - </strong><strong>retain nonstatus information</strong></p>\r\n<p>This directive is used to determine whether or not non-status information about the service is retained across program restarts. This is only useful if you have enabled state retention using the retain_state_information directive.  Value: 0 = disable non-status information retention, 1 = enable non-status information retention.</p>\r\n<p><em>Parameter name:</em> retain_nonstatus_information<br> <em>Required:</em> no</p>'),(96,'service','process_perf_data','all','default','<p><strong>Service - </strong><strong>process perf data</strong></p>\r\n<p>This directive is used to determine whether or not the processing of performance data is enabled for this service. Values: 0 = disable performance data processing, 1 = enable performance data processing.</p>\r\n<p><em>Parameter name:</em> process_perf_data<br> <em>Required:</em> no</p>'),(97,'service','is_volatile','all','default','<p><strong>Service</strong><strong> - is volatile<br> </strong></p>\r\n<p>This directive is used to denote whether the service is \"volatile\".  Services are normally <em>not</em> volatile.  More information on volatile service and how they differ from normal services can be found in the nagios documentation.  Value: 0 = service is not volatile, 1 = service is volatile.</p>\r\n<p><em>Parameter name:</em> is_volatile<br> <em>Required:</em>no</p>'),(98,'service','contacts','3','default','<p><strong>Service - </strong><strong>contacts</strong></p>\r\n<p>This is a list of the <em>short names</em> of the contacts that should be notified whenever there are problems (or recoveries) with this service. Multiple contacts should be separated by commas. Useful if you want notifications to go to just a few people and don\'t want to configure contact groups. You must specify at least one contact or contact group in each service definition.</p>\r\n<p><em>Parameter name:</em> contacts<br> <em>Required:</em> yes (no, if a contact group is defined)</p>'),(99,'service','contactgroups','all','default','<p><strong>Service - </strong><strong>contact groups</strong></p>\r\n<p>This is a list of the <em>short names</em> of the contact groups that should be notified whenever there are problems (or recoveries) with this service. Multiple contact groups should be separated by commas. You must specify at least one contact or contact group in each service definition.</p>\r\n<p><em>Parameter name:</em> contact_groups<br> <em>Required:</em> yes (no, if a contact is defined)</p>'),(100,'service','notification_period','all','default','<p><strong>Service - </strong><strong>notification period</strong></p>\r\n<p>This directive is used to specify the short name of the time period during which notifications of events for this service can be sent out to contacts. No service notifications will be sent out during times which is not covered by the time period.</p>\r\n<p><em>Parameter name:</em> notification_period<br> <em>Required:</em> yes</p>'),(101,'service','notification_options','all','default','<p><strong>Service - </strong><strong>notification options</strong></p>\r\n<p>This directive is used to determine when notifications for the service should be sent out. Valid options are a combination of one or more of the following:<br><strong><br>w</strong> = send notifications on a WARNING state, <br><strong>u</strong> = send notifications on an UNKNOWN state, <strong><br>c</strong> = send notifications on a CRITICAL state, <br><strong>r</strong> = send notifications on recoveries (OK state), <strong><br>f</strong> = send notifications when the service starts and stops flapping, and <br><strong>s</strong> = send notifications when scheduled downtime starts and ends.</p>\r\n<p>If you do not specify any notification options, Nagios will assume that you want notifications to be sent out for all possible states.</p>\r\n<p>Example: If you specify <strong>w,r</strong> in this field, notifications will only be sent out when the service goes into a WARNING state and when it recovers from a WARNING state.</p>\r\n<p><em>Parameter name:</em> notification_options<br> <em>Required:</em> no</p>'),(102,'service','notification_interval','all','default','<p><strong>Service - </strong><strong>notification interval</strong></p>\r\n<p>This directive is used to define the number of \"time units\" to wait before re-notifying a contact that this service is <em>still</em> in a non-OK state.  Unless you\'ve changed the interval_length directive from the default value of 60, this number will mean minutes.  If you set this value to 0, Nagios will <em>not</em> re-notify contacts about problems for this service - only one problem notification will be sent out.</p>\r\n<p><em>Parameter name:</em> notification_interval<br> <em>Required:</em> yes</p>'),(103,'service','first_notification_delay','all','default','<p><strong>Service - </strong><strong>first notification delay</strong></p>\r\n<p>This directive is used to define the number of \"time units\" to wait before sending out the first problem notification when this service enters a non-OK state. Unless you\'ve changed the interval_length directive from the default value of 60, this number will mean minutes. If you set this value to 0, Nagios will start sending out notifications immediately.</p>\r\n<p><em>Parameter name:</em> first_notification_delay<br> <em>Required:</em> no</p>'),(104,'service','notification_enabled','all','default','<p><strong>Service - </strong><strong>notifications enabled</strong><strong></strong></p>\r\n<p>This directive is used to determine whether or not notifications for this service are enabled. Values: 0 = disable service notifications, 1 = enable service notifications.</p>\r\n<p><em>Parameter name:</em> notifications_enabled<br> <em>Required:</em> no</p>'),(105,'service','stalking_options','all','default','<p><strong>Service - </strong><strong>stalking options</strong></p>\r\n<p>This directive determines which service states \"stalking\" is enabled for. Valid options are a combination of one or more of the following: <strong><br>o</strong> = stalk on OK states, <br><strong>w</strong> = stalk on WARNING states, <strong><br>u</strong> = stalk on UNKNOWN states, and <strong><br>c</strong> = stalk on CRITICAL states.</p>\r\n<p>More information on state stalking can be found in the nagios documentation.</p>\r\n<p><em>Parameter name:</em> stalking_options<br> <em>Required:</em> no</p>'),(106,'service','notes','3','default','<p><strong>Service - </strong><strong>notes</strong></p>\r\n<p>This directive is used to define an optional string of notes pertaining to the service. If you specify a note here, you will see the it in the extended information CGI (when you are viewing information about the specified service).</p>\r\n<p><em>Parameter name:</em> notes<br> <em>Required:</em> no</p>'),(107,'service','icon_image','3','default','<p><strong>Service - </strong><strong>icon image</strong><strong> </strong></p>\r\n<p>This variable is used to define the name of a GIF, PNG, or JPG image that should be associated with this service. This image will be displayed in the status and extended information CGIs.  The image will look best if it is 40x40 pixels in size.  Images for services are assumed to be in the <strong>logos/</strong> subdirectory in your HTML images directory (i.e. <em>/usr/local/nagios/share/images/logos</em>).</p>\r\n<p><em>Parameter name:</em> icon_image<br> <em>Required:</em> no</p>'),(108,'service','notes_url','3','default','<p><strong>Service - </strong><strong>notes url<br></strong></p>\r\n<p>This directive is used to define an optional URL that can be used to provide more information about the service. If you specify an URL, you will see a red folder icon in the CGIs (when you are viewing service information) that links to the URL you specify here. Any valid URL can be used. If you plan on using relative paths, the base path will the the same as what is used to access the CGIs (i.e. <em>/cgi-bin/nagios/</em>). This can be very useful if you want to make detailed information on the service, emergency contact methods, etc. available to other support staff.</p>\r\n<p><em>Parameter name:</em> notes_url<br> <em>Required:</em> no</p>'),(109,'service','icon_image_alt_text','3','default','<p><strong>Service - </strong><strong>icon image alt</strong><strong> </strong></p>\r\n<p>This variable is used to define an optional string that is used in the ALT tag of the image specified by the <em>&lt;icon_image&gt;</em> argument.  The ALT tag is used in the status, extended information and statusmap CGIs.</p>\r\n<p><em>Parameter name:</em> icon_image_alt<br> <em>Required:</em> no</p>'),(110,'service','action_url','3','default','<p><strong>Service - action</strong><strong> url<br> </strong></p>\r\n<p>This directive is used to define an optional URL that can be used to provide more actions to be performed on the service. If you specify an URL, you will see a red \"splat\" icon in the CGIs (when you are viewing service information) that links to the URL you specify here. Any valid URL can be used. If you plan on using relative paths, the base path will the the same as what is used to access the CGIs (i.e. <em>/cgi-bin/nagios/</em>).</p>\r\n<p><em>Parameter name:</em> action_url<br> <em>Required:</em> no</p>'),(111,'hostgroup','hostgroup_name','all','default','<p><strong>Hostgroup - </strong><strong>hostgroup name</strong></p>\r\n<p>This directive is used to define a short name used to identify the host group.</p>\r\n<p><em>Parameter name:</em> hostgroup_name<br> <em>Required:</em> yes</p>'),(112,'hostgroup','members','all','default','<p><strong>Hostgroup - </strong><strong>members</strong></p>\r\n<p>This is a list of the <em>short names</em> of hosts that should be included in this group. Multiple host names should be separated by commas. This directive may be used as an alternative to (or in addition to) the <em>hostgroups</em> directive in host definitions.</p>\r\n<p><strong>NagiosQL:</strong> If you select a hostgroup inside a host definition using the <em>hostgroups</em> directive in <em>host definition</em>, this host will <span style=\"color: #ff0000;\">not be selected</span> here because these are two different ways to specify a hostgroup!</p>\r\n<p><em>Parameter name:</em> members<br> <em>Required:</em> no</p>'),(113,'hostgroup','description','all','default','<p><strong>Hostgroup - </strong><strong>alias</strong></p>\r\n<p>This directive is used to define is a longer name or description used to identify the host group. It is provided in order to allow you to more easily identify a particular host group.</p>\r\n<p><em>Parameter name:</em> alias<br> <em>Required:</em> yes</p>'),(114,'hostgroup','notes','3','default','<p><strong>Hostgroup - </strong><strong>notes</strong></p>\r\n<p>This directive is used to define an optional string of notes pertaining to the host. If you specify a note here, you will see the it in the extended information CGI (when you are viewing information about the specified host).</p>\r\n<p><em>Parameter name:</em> notes<br> <em>Required:</em> no</p>'),(115,'hostgroup','notes_url','3','default','<p><strong>Hostgroup - </strong><strong>notes url<br></strong></p>\r\n<p>This variable is used to define an optional URL that can be used to provide more information about the host group. If you specify an URL, you will see a red folder icon in the CGIs (when you are viewing hostgroup information) that links to the URL you specify here. Any valid URL can be used. If you plan on using relative paths, the base path will the the same as what is used to access the CGIs (i.e. <em>/cgi-bin/nagios/</em>). This can be very useful if you want to make detailed information on the host group, emergency contact methods, etc. available to other support staff.</p>\r\n<p><em>Parameter name:</em> notes_url<br> <em>Required:</em> no</p>'),(116,'hostgroup','action_url','3','default','<p><strong>Hostgroup - </strong><strong>action url</strong></p>\r\n<p>This directive is used to define an optional URL that can be used to provide more actions to be performed on the host group. If you specify an URL, you will see a red \"splat\" icon in the CGIs (when you are viewing hostgroup information) that links to the URL you specify here. Any valid URL can be used. If you plan on using relative paths, the base path will the the same as what is used to access the CGIs (i.e. <em>/cgi-bin/nagios/</em>).</p>\r\n<p><em>Parameter name:</em> action_url<br> <em>Required:</em> no</p>'),(117,'hostgroup','hostgroup_members','all','default','<p><strong>Hostgroup - </strong><strong>hostgroup members</strong></p>\r\n<p>This optional directive can be used to include hosts from other \"sub\" host groups in this host group. Specify a comma-delimited list of short names of other host groups whose members should be included in this group.</p>\r\n<p><em>Parameter name:</em> hostgroup_members<br> <em>Required:</em> no</p>'),(118,'servicegroup','servicegroup_name','all','default','<p><strong>Servicegroup - </strong><strong>servicegroup name</strong></p>\r\n<p>This directive is used to define a short name used to identify the service group.</p>\r\n<p><em>Parameter name:</em> servicegroup_name<br> <em>Required:</em> yes</p>'),(119,'servicegroup','members','all','default','<p><strong>Servicegroup - </strong><strong>members</strong></p>\r\n<p>This is a list of the <em>descriptions</em> of services (and the names of their corresponding hosts) that should be included in this group. Host and service names should be separated by commas. This directive may be used as an alternative to the <em>servicegroups</em> directive in service definitions.</p>\r\n<p><strong>NagiosQL:</strong> If you select a servicegroup inside a service definition using the <em>servicegroups</em> directive in <em>service definition</em>, this service will <span style=\"color: #ff0000;\">not be selected</span> here because these are two different ways to specify a servicegroup!</p>\r\n<p><em>Parameter name:</em> members<br> <em>Required:</em> no</p>'),(120,'servicegroup','description','all','default','<p><strong>Servicegroup - </strong><strong>alias</strong><strong></strong></p>\r\n<p>This directive is used to define is a longer name or description used to identify the service group. It is provided in order to allow you to more easily identify a particular service group.</p>\r\n<p><em>Parameter name:</em> alias<br> <em>Required:</em> yes</p>'),(121,'servicegroup','notes','3','default','<p><strong>Servicegroup - </strong><strong>notes</strong></p>\r\n<p>This directive is used to define an optional string of notes pertaining to the service group. If you specify a note here, you will see the it in the extended information CGI (when you are viewing information about the specified service group).</p>\r\n<p><em>Parameter name:</em> notes<br> <em>Required:</em> no</p>'),(122,'servicegroup','notes_url','3','default','<p><strong>Servicegroup - </strong><strong>notes url</strong></p>\r\n<p>This directive is used to define an optional URL that can be used to provide more information about the service group. If you specify an URL, you will see a red folder icon in the CGIs (when you are viewing service group information) that links to the URL you specify here. Any valid URL can be used. If you plan on using relative paths, the base path will the the same as what is used to access the CGIs (i.e. <em>/cgi-bin/nagios/</em>). This can be very useful if you want to make detailed information on the service group, emergency contact methods, etc. available to other support staff.</p>\r\n<p><em>Parameter name:</em> notes_url<br> <em>Required:</em> no</p>'),(123,'servicegroup','action_url','3','default','<p><strong>Servicegroup - </strong><strong>action url</strong></p>\r\n<p>This directive is used to define an optional URL that can be used to provide more actions to be performed on the service group. If you specify an URL, you will see a red \"splat\" icon in the CGIs (when you are viewing service group information) that links to the URL you specify here. Any valid URL can be used. If you plan on using relative paths, the base path will the the same as what is used to access the CGIs (i.e. <em>/cgi-bin/nagios/</em>).</p>\r\n<p><em>Parameter name:</em> action_url<br> <em>Required:</em> no</p>'),(124,'servicegroup','servicegroup_members','all','default','<p><strong>Servicegroup - </strong><strong>servicegroup members</strong></p>\r\n<p>This optional directive can be used to include services from other \"sub\" service groups in this service group. Specify a comma-delimited list of short names of other service groups whose members should be included in this group.</p>\r\n<p><em>Parameter name:</em> servicegroup_members<br> <em>Required:</em> yes</p>'),(125,'hosttemplate','template_name','all','default','<p><strong>Hosttemplate - template name</strong></p>\r\n<p>This directive is used to define a short name used to identify the host template.</p>\r\n<p><em>Parameter name:</em> name<br> <em>Required:</em> yes</p>'),(126,'servicetemplate','template_name','all','default','<p><strong>Servicetemplate - template name</strong></p>\r\n<p>This directive is used to define a short name used to identify the service template.</p>\r\n<p><em>Parameter name:</em> name<br> <em>Required:</em> yes</p>'),(127,'contact','contact_name','all','default','<p><strong>Contact - </strong><strong>contact name</strong></p>\r\n<p>This directive is used to define a short name used to identify the contact.  It is referenced in contact group definitions.  Under the right circumstances, the $CONTACTNAME$ macro will contain this value.</p>\r\n<p><em>Parameter name:</em> contact_name<br> <em>Required:</em> yes</p>'),(128,'contact','contactgroups','all','default','<p><strong>Contact - </strong><strong>contactgroups</strong></p>\r\n<p>This directive is used to identify the <em>short name(s)</em> of the contactgroup(s) that the contact belongs to. Multiple contactgroups should be separated by commas. This directive may be used as an alternative to (or in addition to) using the <em>members</em> directive in contactgroup definitions.</p>\r\n<p><span style=\"color: #ff0000;\"><span style=\"color: #000000;\"><strong>NagiosQL:</strong> If a contactgroup is defined here - this contact will <span style=\"color: #ff0000;\">not be selected</span> inside the member field of the same contactgroup definition! </span></span></p>\r\n<p><em>Parameter name:</em> contactgroups<br> <em>Required:</em> no</p>'),(129,'contact','alias','all','default','<p><strong>Contact - </strong><strong>alias</strong></p>\r\n<p>This directive is used to define a longer name or description for the contact. Under the rights circumstances, the $CONTACTALIAS$ macro will contain this value.  If not specified, the <em>contact_name</em> will be used as the alias.</p>\r\n<p><em>Parameter name:</em> alias<br> <em>Required:</em> no (yes in Nagios 2.x)</p>'),(130,'contact','email','all','default','<p><strong>Contact - </strong><strong>email</strong></p>\r\n<p>This directive is used to define an email address for the contact. Depending on how you configure your notification commands, it can be used to send out an alert email to the contact. Under the right circumstances, the $CONTACTEMAIL$ macro will contain this value.</p>\r\n<p>Parameter name: email<br> <em>Required:</em> no</p>'),(131,'contact','pager','all','default','<p><strong>Contact - </strong><strong>pager</strong></p>\r\n<p>This directive is used to define a pager number for the contact. It can also be an email address to a pager gateway (i.e. pagejoe@pagenet.com). Depending on how you configure your notification commands, it can be used to send out an alert page to the contact. Under the right circumstances, the $CONTACTPAGER$ macro will contain this value.</p>\r\n<p>Parameter name: pager<br> <em>Required:</em> no</p>'),(132,'contact','address','all','default','<p><strong>Contact - </strong><strong>address<em>x</em></strong></p>\r\n<p>Address directives are used to define additional \"addresses\" for the contact. These addresses can be anything - cell phone numbers, instant messaging addresses, etc. Depending on how you configure your notification commands, they can be used to send out an alert o the contact. Up to six addresses can be defined using these directives (<em>address1</em> through <em>address6</em>). The $CONTACTADDRESS<em>x</em>$ macro will contain this value.</p>\r\n<p>Parameter name: addressx (x as number from 1 to 6)<br> <em>Required:</em> no</p>'),(133,'contact','host_notifications_enabled','3','default','<p><strong>Contact - </strong><strong>host notifications enabled</strong></p>\r\n<p>This directive is used to determine whether or not the contact will receive notifications about host problems and recoveries. Values: 0 = don\'t send notifications, 1 = send notifications.</p>\r\n<p><em>Parameter name:</em> host_notifications_enabled<br> <em>Required:</em> yes</p>'),(134,'contact','service_notifications_enabled','3','default','<p><strong>Contact - </strong><strong>service notifications enabled</strong></p>\r\n<p>This directive is used to determine whether or not the contact will receive notifications about service problems and recoveries. Values: 0 = don\'t send notifications, 1 = send notifications.</p>\r\n<p><em>Parameter name:</em> service_notifications_enabled<br> <em>Required:</em> yes</p>'),(135,'contact','host_notification_period','all','default','<p><strong>Contact - </strong><strong>host notification period</strong></p>\r\n<p>This directive is used to specify the short name of the time period during which the contact can be notified about host problems or recoveries. You can think of this as an \"on call\" time for host notifications for the contact. Read the documentation on time periods for more information on how this works and potential problems that may result from improper use.</p>\r\n<p><em>Parameter name:</em> host_notification_period<br> <em>Required:</em> yes</p>'),(136,'contact','service_notification_period','all','default','<p><strong>Contact - </strong><strong>service notification period</strong><strong></strong></p>\r\n<p>This directive is used to specify the short name of the time period during which the contact can be notified about service problems or recoveries. You can think of this as an \"on call\" time for service notifications for the contact. Read the documentation on time periods for more information on how this works and potential problems that may result from improper use.</p>\r\n<p><em>Parameter name:</em> service_notification_period<br> <em>Required:</em> yes</p>'),(137,'contact','host_notification_options','2','default','<p><strong>Contact - </strong><strong>host notification options</strong></p>\r\n<p>This directive is used to define the host states for which notifications can be sent out to this contact. Valid options are a combination of one or more of the following: <strong><br>d</strong> = notify on DOWN host states, <br><strong>u</strong> = notify on UNREACHABLE host states, <strong><br>r</strong> = notify on host recoveries (UP states), and <strong><br>f</strong> = notify when the host starts and stops flapping.<br>If you specify <strong>n</strong> (none) as an option, the contact will not receive any type of host notifications.</p>\r\n<p><em>Parameter name:</em> host_notification_options<br> <em>Required:</em> yes</p>'),(138,'contact','host_notification_options','3','default','<p><strong>Contact - </strong><strong>host notification options</strong></p>\r\n<p>This directive is used to define the host states for which notifications can be sent out to this contact. Valid options are a combination of one or more of the following: <br><strong>d</strong> = notify on DOWN host states, <strong><br>u</strong> = notify on UNREACHABLE host states, <strong><br>r</strong> = notify on host recoveries (UP states), <strong><br>f</strong> = notify when the host starts and stops flapping, and <br><strong>s</strong> = send notifications when host or service scheduled downtime starts and ends.<br>If you specify <strong>n</strong> (none) as an option, the contact will not receive any type of host notifications.</p>\r\n<p><em>Parameter name:</em> host_notification_options<br> <em>Required:</em> yes</p>'),(139,'contact','service_notification_options','2','default','<p><strong>Contact - </strong><strong>service notification options</strong></p>\r\n<p>This directive is used to define the service states for which notifications can be sent out to this contact. Valid options are a combination of one or more of the following: <strong><br>w</strong> = notify on WARNING service states, <strong><br>u</strong> = notify on UNKNOWN service states, <strong><br>c</strong> = notify on CRITICAL service states, <br><strong>r</strong> = notify on service recoveries (OK states), and <br><strong>f</strong> = notify when the servuce starts and stops flapping.<br>If you specify <strong>n</strong> (none) as an option, the contact will not receive any type of host notifications.</p>\r\n<p><em>Parameter name:</em> service_notification_options<br> <em>Required:</em> yes</p>'),(140,'contact','service_notification_options','3','default','<p><strong>Contact - </strong><strong>service notification options</strong></p>\r\n<p>This directive is used to define the service states for which notifications can be sent out to this contact. Valid options are a combination of one or more of the following: <strong><br>w</strong> = notify on WARNING service states, <br><strong>u</strong> = notify on UNKNOWN service states, <br><strong>c</strong> = notify on CRITICAL service states, <strong><br>r</strong> = notify on service recoveries (OK states), and <strong><br></strong><strong>f</strong> = notify when the host starts and stops flapping, and <strong><br>s</strong> = send notifications when host or service scheduled downtime starts and ends.  <br>If you specify <strong>n</strong> (none) as an option, the contact will not receive any type of host notifications.</p>\r\n<p><em>Parameter name:</em> service_notification_options<br> <em>Required:</em> yes</p>'),(141,'contact','host_notification_commands','all','default','<p><strong>Contact - </strong><strong>host notification commands</strong></p>\r\n<p>This directive is used to define a list of the <em>short names</em> of the commands used to notify the contact of a <em>host</em> problem or recovery. Multiple notification commands should be separated by commas. All notification commands are executed when the contact needs to be notified. The maximum amount of time that a notification command can run is controlled by the notification_timeout option.</p>\r\n<p><em>Parameter name:</em> host_notification_commands<br> <em>Required:</em> yes</p>'),(142,'contact','service_notification_commands','all','default','<p><strong>Contact - </strong><strong>service notification commands</strong></p>\r\n<p>This directive is used to define a list of the <em>short names</em> of the commands used to notify the contact of a <em>service</em> problem or recovery. Multiple notification commands should be separated by commas. All notification commands are executed when the contact needs to be notified. The maximum amount of time that a notification command can run is controlled by the notification_timeout option.</p>\r\n<p><em>Parameter name:</em> service_notification_commands<br> <em>Required:</em> yes</p>'),(143,'contact','retain_status_information','3','default','<p><strong>Contact - </strong><strong>retain status information</strong></p>\r\n<p>This directive is used to determine whether or not status-related information about the contact is retained across program restarts. This is only useful if you have enabled state retention using the retain_state_information directive.  Value: 0 = disable status information retention, 1 = enable status information retention.</p>\r\n<p>Parameter name: retain_status_information<br> <em>Required:</em> no</p>'),(144,'contact','can_submit_commands','3','default','<p><strong>Contact - </strong><strong>can submit commands</strong></p>\r\n<p>This directive is used to determine whether or not the contact can submit external commands to Nagios from the CGIs. Values: 0 = don\'t allow contact to submit commands, 1 = allow contact to submit commands.</p>\r\n<p>Parameter name: can_submit_commands<br> <em>Required:</em> no</p>'),(145,'contact','retain_nostatus_information','3','default','<p><strong>Contact - </strong><strong>retain nonstatus information</strong></p>\r\n<p>This directive is used to determine whether or not non-status information about the contact is retained across program restarts. This is only useful if you have enabled state retention using the retain_state_information directive.  Value: 0 = disable non-status information retention, 1 = enable non-status information retention.</p>\r\n<p>Parameter name: retain_nonstatus_information<br> <em>Required:</em> no</p>'),(146,'contact','templateadd','all','default','<p><strong>Contact - Templates</strong></p>\r\n<p>You can add one or more contact templates to a contact configuration. Nagios will add the definitions from each template to a contact configuration.</p>\r\n<p>If you add more than one template - the templates from the bottom to the top will be used to overwrite configuration items which are defined inside templates before.</p>\r\n<p>The host configuration itselves will overwrite all values which are defined in templates before and pass all values which are not defined.</p>'),(147,'contact','genericname','all','default','<p><strong>Contact - generic name</strong></p>\r\n<p>It is possible to use a contact definition as a template for other contact configurations. If this definition should be used as template, a generic template name must be defined.</p>\r\n<p>We do not recommend to do this - it is more open to define a separate contact template than use this option.</p>\r\n<p><em>Parameter name:</em> name<em><br>Required:</em> no</p>'),(148,'contactgroup','contactgroup_name','all','default','<p><strong>Contactgroup - contactgroup name</strong></p>\r\n<p>This directive is a short name used to identify the contact group.</p>\r\n<p><em>Parameter name:</em> contactgroup_name<em><br>Required:</em> yes</p>'),(149,'contactgroup','members','all','default','<p><strong>Contactgroup - </strong><strong>members</strong></p>\r\n<p>This directive is used to define a list of the <em>short names</em> of contacts that should be included in this group. Multiple contact names should be separated by commas. This directive may be used as an alternative to (or in addition to) using the <em>contactgroups</em> directive in contact definitions.</p>\r\n<p><strong>NagiosQL:</strong> If you select a contactgroup inside a contact definition using the&nbsp;<em>contactgroups</em> directive in&nbsp;<em>contact definition</em>, this contact will <span style=\"color: #ff0000;\">not be selected</span> here because these are two different ways to specify a contactgroup!</p>\r\n<p><em>Parameter name:</em> members<em><br>Required:</em> yes</p>'),(150,'contactgroup','alias','all','default','<p><strong>Contactgroup - </strong><strong>alias</strong></p>\r\n<p>This directive is used to define a longer name or description used to identify the contact group.</p>\r\n<p><em>Parameter name:</em> alias<em><br>Required:</em> yes</p>'),(151,'contactgroup','contactgroup_members','all','default','<p><strong>Contactgroup - </strong><strong>contactgroup members</strong></p>\r\n<p>This optional directive can be used to include contacts from other \"sub\" contact groups in this contact group. Specify a comma-delimited list of short names of other contact groups whose members should be included in this group.</p>\r\n<p><em>Parameter name:</em> contactgroup_members<em><br>Required:</em> no</p>'),(152,'timeperiod','timeperiod_name','all','default','<p><strong>Timeperiod - </strong><strong>timeperiod name</strong></p>\r\n<p>This directives is the short name used to identify the time period.</p>\r\n<p>Parameter name: timeperiod_name<br> <em>Required:</em> yes</p>'),(153,'timeperiod','exclude','3','default','<p><strong>Timeperiod - </strong><strong>exclude</strong></p>\r\n<p>This directive is used to specify the short names of other timeperiod definitions whose time ranges should be excluded from this timeperiod. Multiple timeperiod names should be separated with a comma.</p>\r\n<p>Parameter name: exclude<br> <em>Required:</em> yes</p>'),(154,'timeperiod','alias','all','default','<p><strong>Timeperiod - </strong><strong>alias</strong></p>\r\n<p>This directive is a longer name or description used to identify the time period.</p>\r\n<p>Parameter name: alias<br> <em>Required:</em> yes</p>'),(155,'timeperiod','templatename','3','default','<p><strong>Timeperiod - </strong><strong>template name</strong></p>\r\n<p>Not yet implemented.</p>\r\n<p>Parameter name: name<br> <em>Required:</em> no</p>'),(156,'timeperiod','weekday','2','default','<p><strong>Timeperiod - </strong><strong>time definition<br></strong></p>\r\n<p>The <em>sunday</em> through <em>saturday</em> directives are comma-delimited lists of time ranges that are \"valid\" times for a particular day of the week. Notice that there are seven different days for which you can define time ranges (Sunday through Saturday).</p>\r\n<p>Parameter name: [weekday] [exception]<br> <em>Required:</em> no</p>'),(157,'timeperiod','timerange','2','default','<p><strong>Timeperiod - </strong><strong>time range<br></strong></p>\r\n<p>Each time range is in the form of <strong>HH:MM-HH:MM</strong>, where hours are specified on a 24 hour clock.  For example, <strong>00:15-24:00</strong> means 12:15am in the morning for this day until 12:20am midnight (a 23 hour, 45 minute total time range). If you wish to exclude an entire day from the timeperiod, simply do not include it in the timeperiod definition.</p>\r\n<p>Parameter name: [weekday] [exception]<br> <em>Required:</em> no</p>'),(158,'timeperiod','weekday','3','default','<p><strong>Timeperiod - </strong><strong>time definition<br></strong></p>\r\n<p>The weekday directives (\"<em>sunday</em>\" through \"<em>saturday</em>\")are comma-delimited lists of time ranges that are \"valid\" times for a particular day of the week. Notice that there are seven different days for which you can define time ranges (Sunday through Saturday).&nbsp;</p>\r\n<p>You can also specify several different types of exceptions to the standard rotating weekday schedule. Exceptions can take a number of different forms including single days of a specific or generic month, single weekdays in a month, or single calendar dates. You can also specify a range of days/dates and even specify skip intervals to obtain functionality described by \"every 3 days between these dates\". Rather than list all the possible formats for exception strings, Weekdays and different types of exceptions all have different levels of precedence, so its important to understand how they can affect each other. More information on this can be found in the documentation on timeperiods.</p>\r\n<p>Parameter name: [weekday] [exception]<br> <em>Required:</em> no</p>'),(159,'timeperiod','timerange','3','default','<p><strong>Timeperiod - </strong><strong>time range<br></strong></p>\r\n<p>Each time range is in the form of <strong>HH:MM-HH:MM</strong>, where hours are specified on a 24 hour clock.  For example, <strong>00:15-24:00</strong> means 12:15am in the morning for this day until 12:00am midnight (a 23 hour, 45 minute total time range). If you wish to exclude an entire day from the timeperiod, simply do not include it in the timeperiod definition.</p>\r\n<p>Parameter name: [weekday] [exception]<br> <em>Required:</em> no</p>'),(160,'contacttemplate','template_name','all','default','<p><strong>Contacttemplate - template name</strong></p>\r\n<p>This directive is used to define a short name used to identify the contact template.</p>\r\n<p><em>Parameter name:</em> name<br> <em>Required:</em> yes</p>'),(161,'command','command_name','all','default','<p><strong>Command - </strong><strong>command name</strong></p>\r\n<p>This directive is the short name used to identify the command.  It is referenced in contact, host, and service definitions (in notification, check, and event handler directives), among other places.</p>\r\n<p><em>Parameter name:</em> command_name<br> <em>Required:</em> yes</p>'),(162,'command','command_line','all','default','<p><strong>Command - </strong><strong>command line</strong></p>\r\n<p>This directive is used to define what is actually executed by Nagios when the command is used for service or host checks, notifications, or event handlers. Before the command line is executed, all valid macros are replaced with their respective values. See the documentation on macros for determining when you can use different macros. Note that the command line is <em>not</em> surrounded in quotes. Also, if you want to pass a dollar sign ($) on the command line, you have to escape it with another dollar sign.</p>\r\n<p><strong>NOTE</strong>: You may not include a <strong>semicolon</strong> (;) in the <em>command_line</em> directive, because everything after it will be ignored as a config file comment. You can work around this limitation by setting one of the <strong>$USER$</strong> macros in your resource file to a semicolon and then referencing the appropriate $USER$ macro in the <em>command_line</em> directive in place of the semicolon.</p>\r\n<p>If you want to pass arguments to commands during runtime, you can use <strong>$ARGn$</strong> macros in the <em>command_line</em> directive of the command definition and then separate individual arguments from the command name (and from each other) using bang (!) characters in the object definition directive (host check command, service event handler command, etc) that references the command. More information on how arguments in command definitions are processed during runtime can be found in the documentation on macros.</p>\r\n<p><em>Parameter name:</em> command_line<br> <em>Required:</em> yes</p>'),(163,'command','command_type','all','default','<p><strong>Command - </strong><strong>command type</strong></p>\r\n<p>This directive is used to differ checks and misc commands. Its a NagiosQL definition only.</p>\r\n<p>Commands tagged as \"check command\" will be displayed in services and hosts as check command.</p>\r\n<p>Commands tagged as \"misc command\" will be displayed in contacts, services and hosts as event command.</p>\r\n<p>Not classified commands will be displayed everywhere.</p>\r\n<p>This definition is only used to reduce the amount of commands shown in the selection fields and to have a more clear view.</p>'),(164,'hostdependency','dependent_host','all','default','<p><strong>Hostdependency - </strong><strong>dependent host name</strong></p>\r\n<p>This directive is used to identify the <em>short name(s)</em> of the <em>dependent</em> host(s).  Multiple hosts should be separated by commas</p>\r\n<p><em>Parameter name:</em> dependent_host_name<br> <em>Required:</em> yes (no, if a dependent hostgroup is defined)</p>'),(165,'hostdependency','dependent_hostgroups','all','default','<p><strong>Hostdependency - </strong><strong>dependent hostgroup name</strong></p>\r\n<p>This directive is used to identify the <em>short name(s)</em> of the <em>dependent</em>hostgroup(s). Multiple hostgroups should be separated by commas. The dependent_hostgroup_name may be used instead of, or in addition to, the dependent_host_name directive.</p>\r\n<p><em>Parameter name:</em> dependent_hostgroup_name<br> <em>Required:</em> no (yes, if no dependent host is defined)</p>'),(166,'hostdependency','host','all','default','<p><strong>Hostdependency - </strong><strong>host name</strong></p>\r\n<p>This directive is used to identify the <em>short name(s)</em> of the host(s) <em>that is being depended upon</em> (also referred to as the master host).  Multiple hosts should be separated by commas.</p>\r\n<p><em>Parameter name:</em> host_name<br> <em>Required:</em> yes (no, if&nbsp; a hostgroup is defined)</p>'),(167,'hostdependency','hostgroup','all','default','<p><strong>Hostdependency - </strong><strong>hostgroup name</strong></p>\r\n<p>This directive is used to identify the <em>short name(s)</em> of the host(s) <em>that is being depended upon</em> (also referred to as the master host).  Multiple hosts should be separated by commas.</p>\r\n<p><em>Parameter name:</em> hostgroup_name<br> <em>Required:</em> no (yes, if a no host is defined)</p>'),(168,'hostdependency','config_name','all','default','<p><strong>Hostdependency - config name</strong></p>\r\n<p>This directive is used to specify a common config name for a hostdependency configration. This is a NagiosQL parameter and it will not be written to the configuration file.</p>'),(169,'hostdependency','inherit_parents','all','default','<p><strong>Hostdependency - </strong><strong>inherits parent</strong></p>\r\n<p>This directive indicates whether or not the dependency inherits dependencies of the host <em>that is being depended upon</em> (also referred to as the master host). In other words, if the master host is dependent upon other hosts and any one of those dependencies fail, this dependency will also fail.</p>\r\n<p><em>Parameter name:</em> inherits_parent<br> <em>Required:</em> no</p>'),(170,'hostdependency','dependency_period','3','default','<p><strong>Hostdependency - </strong><strong>dependency_period</strong></p>\r\n<p>This directive is used to specify the short name of the time period during which this dependency is valid. If this directive is not specified, the dependency is considered to be valid during all times.</p>\r\n<p><em>Parameter name:</em> dependency_period<br> <em>Required:</em> no</p>'),(171,'hostdependency','execution_failure_criteria','all','default','<p><strong>Hostdependency - </strong><strong>execution failure criteria</strong></p>\r\n<p>This directive is used to specify the criteria that determine when the dependent host should <em>not</em> be actively checked.  If the <em>master</em> host is in one of the failure states we specify, the <em>dependent</em> host will not be actively checked. Valid options are a combination of one or more of the following (multiple options are separated with commas): <br><strong>o</strong> = fail on an UP state, <br><strong>d</strong> = fail on a DOWN state, <br><strong>u</strong> = fail on an UNREACHABLE state, and <strong><br>p</strong> = fail on a pending state (e.g. the host has not yet been checked).</p>\r\n<p>If you specify <strong>n</strong> (none) as an option, the execution dependency will never fail and the dependent host will always be actively checked (if other conditions allow for it to be).</p>\r\n<p>Example: If you specify <strong>u,d</strong> in this field, the <em>dependent</em> host will not be actively checked if the <em>master</em> host is in either an UNREACHABLE or DOWN state.</p>\r\n<p><em>Parameter name:</em> execution_failure_criteria<br> <em>Required:</em> no</p>'),(172,'hostdependency','notification_failure_criteria','all','default','<p><strong>Hostdependency - </strong><strong>notification failure criteria</strong></p>\r\n<p>This directive is used to define the criteria that determine when notifications for the dependent host should <em>not</em> be sent out.  If the <em>master</em> host is in one of the failure states we specify, notifications for the <em>dependent</em> host will not be sent to contacts.  Valid options are a combination of one or more of the following: <br><strong>o</strong> = fail on an UP state, <br><strong>d</strong> = fail on a DOWN state, <br><strong>u</strong> = fail on an UNREACHABLE state, and <br><strong>p</strong> = fail on a pending state (e.g. the host has not yet been checked).</p>\r\n<p>If you specify <strong>n</strong> (none) as an option, the notification dependency will never fail and notifications for the dependent host will always be sent out.</p>\r\n<p>Example: If you specify <strong>d</strong> in this field, the notifications for the <em>dependent</em> host will not be sent out if the <em>master</em> host is in a DOWN state.</p>\r\n<p><em>Parameter name:</em> notification_failure_criteria<br> <em>Required:</em> no</p>'),(173,'hostescalation','host','all','default','<p><strong>Hostescalation - </strong><strong>host name</strong></p>\r\n<p>This directive is used to identify the <em>short name</em> of the host that the escalation should apply to.</p>\r\n<p><em>Parameter name:</em> host_name<br> <em>Required:</em> yes (no, if a hostgroup name is defined</p>'),(174,'hostescalation','hostgroup','all','default','<p><strong>Hostescalation - </strong><strong>hostgroup name</strong><strong></strong></p>\r\n<p>This directive is used to identify the <em>short name(s)</em> of the hostgroup(s) that the escalation should apply to. Multiple hostgroups should be separated by commas. If this is used, the escalation will apply to all hosts that are members of the specified hostgroup(s).</p>\r\n<p><em>Parameter name:</em> hostgroup_name<br> <em>Required:</em> no (yes, if no host ist defined)</p>'),(175,'hostescalation','contact','all','default','<p><strong>Hostescalation - </strong><strong>contacts</strong><strong></strong></p>\r\n<p>This is a list of the <em>short names</em> of the contacts that should be notified whenever there are problems (or recoveries) with this host. Multiple contacts should be separated by commas. Useful if you want notifications to go to just a few people and don\'t want to configure contact groups.  You must specify at least one contact or contact group in each host escalation definition.</p>\r\n<p><em>Parameter name:</em> contacts<br> <em>Required:</em> yes (no, if a contactgroup is defined)</p>'),(176,'hostescalation','contactgroup','all','default','<p><strong>Hostescalation - </strong><strong>contact groups</strong></p>\r\n<p>This directive is used to identify the <em>short name</em> of the contact group that should be notified when the host notification is escalated. Multiple contact groups should be separated by commas. You must specify at least one contact or contact group in each host escalation definition.</p>\r\n<p><em>Parameter name:</em> contact_groups<br> <em>Required:</em> yes (no, if a contact is defined)</p>'),(177,'hostescalation','config_name','all','default','<p><strong>Hostescalation - config name</strong></p>\r\n<p>This directive is used to specify a common config name for a hostescalation configration. This is a NagiosQL parameter and it will not be written to the configuration file.</p>'),(178,'hostescalation','escalation_period','all','default','<p><strong>Hostescalation - </strong><strong>escalation period</strong></p>\r\n<p>This directive is used to specify the short name of the time period during which this escalation is valid. If this directive is not specified, the escalation is considered to be valid during all times.</p>\r\n<p><em>Parameter name:</em> escalation_period<br> <em>Required:</em> no</p>'),(179,'hostescalation','escalation_options','all','default','<p><strong>Hostescalation - </strong><strong>escalation options</strong><strong></strong></p>\r\n<p>This directive is used to define the criteria that determine when this host escalation is used. The escalation is used only if the host is in one of the states specified in this directive. If this directive is not specified in a host escalation, the escalation is considered to be valid during all host states. Valid options are a combination of one or more of the following: <br><strong>r</strong> = escalate on an UP (recovery) state, <br><strong>d</strong> = escalate on a DOWN state, and <strong><br>u</strong> = escalate on an UNREACHABLE state.</p>\r\n<p>Example: If you specify <strong>d</strong> in this field, the escalation will only be used if the host is in a DOWN state.</p>\r\n<p><em>Parameter name:</em> escalation_options<br> <em>Required:</em> no</p>'),(180,'hostescalation','first_notification','all','default','<p><strong>Hostescalation - </strong><strong>first notification</strong><strong></strong></p>\r\n<p>This directive is a number that identifies the <em>first</em> notification for which this escalation is effective. For instance, if you set this value to 3, this escalation will only be used if the host is down or unreachable long enough for a third notification to go out.</p>\r\n<p><em>Parameter name:</em> first_notification<br> <em>Required:</em> yes</p>'),(181,'hostescalation','last_notification','all','default','<p><strong>Hostescalation - </strong><strong>last notification</strong></p>\r\n<p>This directive is a number that identifies the <em>last</em> notification for which this escalation is effective. For instance, if you set this value to 5, this escalation will not be used if more than five notifications are sent out for the host. Setting this value to 0 means to keep using this escalation entry forever (no matter how many notifications go out).</p>\r\n<p><em>Parameter name:</em> last_notification<br> <em>Required:</em> yes</p>'),(182,'hostescalation','notification_interval','all','default','<p><strong>Hostescalation - </strong><strong>notification interval</strong></p>\r\n<p>This directive is used to determine the interval at which notifications should be made while this escalation is valid. If you specify a value of 0 for the interval, Nagios will send the first notification when this escalation definition is valid, but will then prevent any more problem notifications from being sent out for the host. Notifications are sent out again until the host recovers.</p>\r\n<p>This is useful if you want to stop having notifications sent out after a certain amount of time. Note: If multiple escalation entries for a host overlap for one or more notification ranges, the smallest notification interval from all escalation entries is used.</p>\r\n<p><em>Parameter name:</em> notification_interval<br> <em>Required:</em> yes</p>'),(183,'hostextinfo','host_name','all','default','<p><strong>Hostextinfo - </strong><strong>host name</strong></p>\r\n<p>This variable is used to identify the <em>short name</em> of the host which the data is associated with.</p>\r\n<p><em>Parameter name:</em> host_name<br> <em>Required:</em> yes</p>'),(184,'hostextinfo','icon_image','all','default','<p><strong>Hostextinfo - </strong><strong>icon image</strong></p>\r\n<p>This variable is used to define the name of a GIF, PNG, or JPG image that should be associated with this host. This image will be displayed in the status and extended information CGIs.  The image will look best if it is 40x40 pixels in size.</p>\r\n<p>Images for hosts are assumed to be in the <strong>logos/</strong> subdirectory in your HTML images directory (i.e. <em>/usr/local/nagios/share/images/logos</em>).</p>\r\n<p><em>Parameter name:</em> icon_image<br> <em>Required:</em> no</p>'),(185,'hostextinfo','notes','all','default','<p><strong>Hostextinfo - </strong><strong>notes</strong><strong></strong></p>\r\n<p>This directive is used to define an optional string of notes pertaining to the host. If you specify a note here, you will see the it in the extended information CGI (when you are viewing information about the specified host).</p>\r\n<p><em>Parameter name:</em> notes<br> <em>Required:</em> no</p>'),(186,'hostextinfo','icon_image_alt_text','all','default','<p><strong>Hostextinfo - </strong><strong>icon image alt</strong><strong></strong></p>\r\n<p>This variable is used to define an optional string that is used in the ALT tag of the image specified by the <em>&lt;icon_image&gt;</em> argument.  The ALT tag is used in the status, extended information and statusmap CGIs.</p>\r\n<p><em>Parameter name:</em> icon_image_alt<br> <em>Required:</em> no</p>'),(187,'hostextinfo','notes_url','all','default','<p><strong>Hostextinfo - </strong><strong>notes url</strong></p>\r\n<p>This variable is used to define an optional URL that can be used to provide more information about the host. If you specify an URL, you will see a link that says \"Extra Host Notes\" in the extended information CGI (when you are viewing information about the specified host). Any valid URL can be used. If you plan on using relative paths, the base path will the the same as what is used to access the CGIs (i.e. <em>/cgi-bin/nagios/</em>). This can be very useful if you want to make detailed information on the host, emergency contact methods, etc. available to other support staff.</p>\r\n<p><em>Parameter name:</em> notes_url<br> <em>Required:</em> no</p>'),(188,'hostextinfo','vrml_image','all','default','<p><strong>Hostextinfo - </strong><strong>vrml image</strong><strong></strong></p>\r\n<p>This variable is used to define the name of a GIF, PNG, or JPG image that should be associated with this host. This image will be used as the texture map for the specified host in the <a href=\"http://nagios.sourceforge.net/docs/3_0/cgis.html#statuswrl_cgi\">statuswrl</a> CGI.  Unlike the image you use for the <em>icon_image</em> variable, this one should probably <em>not</em> have any transparency.  If it does, the host object will look a bit wierd.</p>\r\n<p>Images for hosts are assumed to be in the <strong>logos/</strong> subdirectory in your HTML images directory (i.e. <em>/usr/local/nagios/share/images/logos</em>).</p>\r\n<p><em>Parameter name:</em> vrml_image<br> <em>Required:</em> no</p>'),(189,'hostextinfo','action_url','all','default','<p><strong>Hostextinfo - </strong><strong>action url</strong></p>\r\n<p>This directive is used to define an optional URL that can be used to provide more actions to be performed on the host. If you specify an URL, you will see a link that says \"Extra Host Actions\" in the extended information CGI (when you are viewing information about the specified host). Any valid URL can be used. If you plan on using relative paths, the base path will the the same as what is used to access the CGIs (i.e. <em>/cgi-bin/nagios/</em>).</p>\r\n<p><em>Parameter name:</em> action_url<br> <em>Required:</em> no</p>'),(190,'hostextinfo','status_image','all','default','<p><strong>Hostextinfo - </strong><strong>statusmap image</strong></p>\r\n<p>This variable is used to define the name of an image that should be associated with this host in the statusmap CGI. You can specify a JPEG, PNG, and GIF image if you want, although I would strongly suggest using a GD2 format image, as other image formats will result in a lot of wasted CPU time when the statusmap image is generated.</p>\r\n<p>GD2 images can be created from PNG images by using the <strong>pngtogd2</strong> utility supplied with Thomas Boutell\'s gd library.  The GD2 images should be created in <em>uncompressed</em> format in order to minimize CPU load when the statusmap CGI is generating the network map image.</p>\r\n<p>The image will look best if it is 40x40 pixels in size. You can leave these option blank if you are not using the statusmap CGI. Images for hosts are assumed to be in the <strong>logos/</strong> subdirectory in your HTML images directory (i.e. <em>/usr/local/nagios/share/images/logos</em>).</p>\r\n<p><em>Parameter name:</em> statusmap_image<br> <em>Required:</em> no</p>'),(191,'hostextinfo','2d_coords','all','default','<p><strong>Hostextinfo - </strong><strong>2d coords</strong></p>\r\n<p>This variable is used to define coordinates to use when drawing the host in the statusmap CGI. Coordinates should be given in positive integers, as they correspond to physical pixels in the generated image. The origin for drawing (0,0) is in the upper left hand corner of the image and extends in the positive x direction (to the right) along the top of the image and in the positive y direction (down) along the left hand side of the image. For reference, the size of the icons drawn is usually about 40x40 pixels (text takes a little extra space). The coordinates you specify here are for the upper left hand corner of the host icon that is drawn.</p>\r\n<p>Note: Don\'t worry about what the maximum x and y coordinates that you can use are. The CGI will automatically calculate the maximum dimensions of the image it creates based on the largest x and y coordinates you specify.</p>\r\n<p><em>Parameter name:</em> 2d_coords<br> <em>Required:</em> no</p>'),(192,'hostextinfo','3d_coords','all','default','<p><strong>Hostextinfo - </strong><strong>3d coords</strong></p>\r\n<p>This variable is used to define coordinates to use when drawing the host in the statuswrl CGI. Coordinates can be positive or negative real numbers. The origin for drawing is (0.0,0.0,0.0). For reference, the size of the host cubes drawn is 0.5 units on each side (text takes a little more space). The coordinates you specify here are used as the center of the host cube.</p>\r\n<p><em>Parameter name:</em> 3d_coords<br> <em>Required:</em> no</p>'),(193,'serviceescalation','host','all','default','<p><strong>Serviceescalation - </strong><strong>host name</strong><strong></strong></p>\r\n<p>This directive is used to identify the <em>short name(s)</em> of the host(s) that the service escalation should apply to or is associated with.</p>\r\n<p><em>Parameter name:</em> host_name<br> <em>Required:</em> yes (no, if a hostgroup name is defined)</p>'),(194,'serviceescalation','hostgroup','all','default','<p><strong>Serviceescalation - </strong><strong>hostgroup name</strong></p>\r\n<p>This directive is used to specify the <em>short name(s)</em> of the hostgroup(s) that the service escalation should apply to or is associated with. Multiple hostgroups should be separated by commas. The hostgroup_name may be used instead of, or in addition to, the host_name directive.</p>\r\n<p><em>Parameter name:</em> hostgroup_name<br> <em>Required:</em> yes (no, if a host name is defined)</p>'),(195,'serviceescalation','contact','all','default','<p><strong>Serviceescalation - </strong><strong>contacts</strong></p>\r\n<p>This is a list of the <em>short names</em> of the contacts that should be notified whenever there are problems (or recoveries) with this service. Multiple contacts should be separated by commas. Useful if you want notifications to go to just a few people and don\'t want to configure contact groups.  You must specify at least one contact or contact group in each service escalation definition.</p>\r\n<p><em>Parameter name:</em> contacts<br> <em>Required:</em> yes (no, if a contact group is defined)</p>'),(196,'serviceescalation','contactgroup','all','default','<p><strong>Serviceescalation - </strong><strong>contact groups</strong></p>\r\n<p>This directive is used to identify the <em>short name</em> of the contact group that should be notified when the service notification is escalated. Multiple contact groups should be separated by commas. You must specify at least one contact or contact group in each service escalation definition.</p>\r\n<p><em>Parameter name:</em> contact_groups<br> <em>Required:</em> yes (no, if a contact is defined)</p>'),(197,'serviceescalation','config_name','all','default','<p><strong>Serviceescalation - config name</strong></p>\r\n<p>This directive is used to specify a common config name for a serviceescalation configration. This is a NagiosQL parameter and it will not be written to the configuration file.</p>'),(198,'serviceescalation','service','all','default','<p><strong>Serviceescalation - </strong><strong>service description</strong><strong></strong></p>\r\n<p>This directive is used to identify the <em>description</em> of the service the escalation should apply to.</p>\r\n<p><em>Parameter name:</em> service_description<br> <em>Required:</em> yes</p>'),(199,'serviceescalation','first_notification','all','default','<p><strong>Serviceescalation - </strong><strong>first notification</strong></p>\r\n<p>This directive is a number that identifies the <em>first</em> notification for which this escalation is effective. For instance, if you set this value to 3, this escalation will only be used if the service is in a non-OK state long enough for a third notification to go out.</p>\r\n<p><em>Parameter name:</em> first_notification<br> <em>Required:</em> yes</p>'),(200,'serviceescalation','last_notification','all','default','<p><strong>Serviceescalation - </strong><strong>last notification</strong></p>\r\n<p>This directive is a number that identifies the <em>last</em> notification for which this escalation is effective. For instance, if you set this value to 5, this escalation will not be used if more than five notifications are sent out for the service. Setting this value to 0 means to keep using this escalation entry forever (no matter how many notifications go out).</p>\r\n<p><em>Parameter name:</em> last_notification<br> <em>Required:</em> yes</p>'),(201,'serviceescalation','notification_interval','all','default','<p><strong>Serviceescalation - </strong><strong>notification interval</strong></p>\r\n<p>This directive is used to determine the interval at which notifications should be made while this escalation is valid. If you specify a value of 0 for the interval, Nagios will send the first notification when this escalation definition is valid, but will then prevent any more problem notifications from being sent out for the host. Notifications are sent out again until the host recovers.</p>\r\n<p>This is useful if you want to stop having notifications sent out after a certain amount of time. Note: If multiple escalation entries for a host overlap for one or more notification ranges, the smallest notification interval from all escalation entries is used.</p>\r\n<p><em>Parameter name:</em> notification_interval<br> <em>Required:</em> yes</p>'),(202,'serviceescalation','escalation_period','all','default','<p><strong>Serviceescalation - </strong><strong>escalation period</strong></p>\r\n<p>This directive is used to specify the short name of the time period during which this escalation is valid. If this directive is not specified, the escalation is considered to be valid during all times.</p>\r\n<p><em>Parameter name:</em> escalation_period<br> <em>Required:</em> no</p>'),(203,'serviceescalation','escalation_options','all','default','<p><strong>Serviceescalation - </strong><strong>escalation options</strong></p>\r\n<p>This directive is used to define the criteria that determine when this service escalation is used. The escalation is used only if the service is in one of the states specified in this directive. If this directive is not specified in a service escalation, the escalation is considered to be valid during all service states. Valid options are a combination of one or more of the following: <strong><br>r</strong> = escalate on an OK (recovery) state, <br><strong>w</strong> = escalate on a WARNING state, <strong><br>u</strong> = escalate on an UNKNOWN state, and <br><strong>c</strong> = escalate on a CRITICAL state.</p>\r\n<p>Example: If you specify <strong>w</strong> in this field, the escalation will only be used if the service is in a WARNING state.</p>\r\n<p><em>Parameter name:</em> escalation_options<br> <em>Required:</em> no</p>'),(204,'servicedependency','dependent_host','all','default','<p><strong>Servicedependency - </strong><strong>dependent host</strong></p>\r\n<p>This directive is used to identify the <em>short name(s)</em> of the host(s) that the <em>dependent</em> service \"runs\" on or is associated with. Multiple hosts should be separated by commas. Leaving this directive blank can be used to create \"same host\" dependencies.</p>\r\n<p><em>Parameter name:</em> dependent_host<br> <em>Required:</em> yes (no, if a dependent hostgroup is defined)</p>'),(205,'servicedependency','host','all','default','<p><strong>Servicedependency -</strong><strong> </strong><strong>host name</strong></p>\r\n<p>This directive is used to identify the <em>short name(s)</em> of the host(s) that the service <em>that is being depended upon</em> (also referred to as the master service) \"runs\" on or is associated with.  Multiple hosts should be separated by commas.</p>\r\n<p><em>Parameter name:</em> host_name<br> <em>Required:</em> yes (no, if a hostgroup is defined)</p>'),(206,'servicedependency','dependent_hostgroup','all','default','<p><strong>Servicedependency - </strong><strong>dependent hostgroup</strong></p>\r\n<p>This directive is used to specify the <em>short name(s)</em> of the hostgroup(s) that the <em>dependent</em> service \"runs\" on or is associated with. Multiple hostgroups should be separated by commas. The dependent_hostgroup may be used instead of, or in addition to, the dependent_host directive.</p>\r\n<p><em>Parameter name:</em> dependent_hostgroup<br> <em>Required:</em> yes (no, if a dependent host is defined)</p>'),(207,'servicedependency','hostgroup','all','default','<p><strong>Servicedependency -</strong><strong> </strong><strong>hostgroup name</strong></p>\r\n<p>This directive is used to identify the <em>short name(s)</em> of the hostgroup(s) that the service <em>that is being depended upon</em> (also referred to as the master service) \"runs\" on or is associated with. Multiple hostgroups should be separated by commas. The hostgroup_name may be used instead of, or in addition to, the host_name directive.</p>\r\n<p><em>Parameter name:</em> hostgroup_name<br> <em>Required:</em> yes (no, if a host is defined)</p>'),(208,'servicedependency','dependent_services','all','default','<p><strong>Servicedependency -</strong><strong> dependent service description</strong><strong></strong></p>\r\n<p>This directive is used to identify the <em>description</em> of the <em>dependent</em> service.</p>\r\n<p><em>Parameter name:</em> dependent_service_description<br> <em>Required:</em> yes</p>'),(209,'servicedependency','services','all','default','<p><strong>Servicedependency -</strong><strong> </strong><strong>service description</strong><strong></strong></p>\r\n<p>This directive is used to identify the <em>description</em> of the service <em>that is being depended upon</em> (also referred to as the master service).</p>\r\n<p><em>Parameter name:</em> service_description<br> <em>Required:</em> yes</p>'),(210,'servicedependency','config_name','all','default','<p><strong>Servicedependency - config name</strong></p>\r\n<p>This directive is used to specify a common config name for a servicedependency configration. This is a NagiosQL parameter and it will not be written to the configuration file.</p>'),(211,'servicedependency','inherit_parents','all','default','<p><strong>Servicedependency -</strong><strong> </strong><strong>inherits parent</strong></p>\r\n<p>This directive indicates whether or not the dependency inherits dependencies of the service <em>that is being depended upon</em> (also referred to as the master service). In other words, if the master service is dependent upon other services and any one of those dependencies fail, this dependency will also fail.</p>\r\n<p><em>Parameter name:</em> inherits_parent<br> <em>Required:</em> no</p>'),(212,'servicedependency','dependency_period','all','default','<p><strong>Servicedependency -</strong><strong> </strong><strong>dependency period</strong><strong></strong></p>\r\n<p>This directive is used to specify the short name of the time period during which this dependency is valid. If this directive is not specified, the dependency is considered to be valid during all times.</p>\r\n<p><em>Parameter name:</em> dependency_period<br> <em>Required:</em> no</p>'),(213,'servicedependency','execution_failure_criteria','all','default','<p><strong>Servicedependency -</strong><strong> </strong><strong>execution failure criteria</strong><strong></strong></p>\r\n<p>This directive is used to specify the criteria that determine when the dependent service should <em>not</em> be actively checked.  If the <em>master</em> service is in one of the failure states we specify, the <em>dependent</em> service will not be actively checked. Valid options are a combination of one or more of the following (multiple options are separated with commas): <br><strong>o</strong> = fail on an OK state, <br><strong>w</strong> = fail on a WARNING state, <strong><br>u</strong> = fail on an UNKNOWN state, <br><strong>c</strong> = fail on a CRITICAL state, and <br><strong>p</strong> = fail on a pending state (e.g. the service has not yet been checked).  <br>If you specify <strong>n</strong> (none) as an option, the execution dependency will never fail and checks of the dependent service will always be actively checked (if other conditions allow for it to be).</p>\r\n<p>Example: If you specify <strong>o,c,u</strong> in this field, the <em>dependent</em> service will not be actively checked if the <em>master</em> service is in either an OK, a CRITICAL, or an UNKNOWN state.</p>\r\n<p><em>Parameter name:</em> execution_failure_criteria<br> <em>Required:</em> no</p>'),(214,'servicedependency','notification_failure_criteria','all','default','<p><strong>Servicedependency -</strong><strong> </strong><strong>notification failure criteria</strong><strong></strong></p>\r\n<p>This directive is used to define the criteria that determine when notifications for the dependent service should <em>not</em> be sent out.  If the <em>master</em> service is in one of the failure states we specify, notifications for the <em>dependent</em> service will not be sent to contacts.  Valid options are a combination of one or more of the following: <strong><br>o</strong> = fail on an OK state, <br><strong>w</strong> = fail on a WARNING state, <strong><br>u</strong> = fail on an UNKNOWN state, <br><strong>c</strong> = fail on a CRITICAL state, and <br><strong>p</strong> = fail on a pending state (e.g. the service has not yet been checked).  <br>If you specify <strong>n</strong> (none) as an option, the notification dependency will never fail and notifications for the dependent service will always be sent out.</p>\r\n<p>Example: If you specify <strong>w</strong> in this field, the notifications for the <em>dependent</em> service will not be sent out if the <em>master</em> service is in a WARNING state.</p>\r\n<p><em>Parameter name:</em> notification_failure_criteria<br> <em>Required:</em> no</p>'),(216,'serviceextinfo','host_name','all','default','<p><strong>Serviceextinfo -</strong><strong> </strong><strong>host name</strong></p>\r\n<p>This directive is used to identify the <em>short name</em> of the host that the service is associated with.</p>\r\n<p><em>Parameter name:</em> host_name<br> <em>Required:</em> yes</p>'),(217,'serviceextinfo','icon_image','all','default','<p><strong>Serviceextinfo -</strong><strong> </strong><strong>icon image</strong></p>\r\n<p>This variable is used to define the name of a GIF, PNG, or JPG image that should be associated with this host. This image will be displayed in the status and extended information CGIs.</p>\r\n<p>The image will look best if it is 40x40 pixels in size.  Images for hosts are assumed to be in the <strong>logos/</strong> subdirectory in your HTML images directory (i.e. <em>/usr/local/nagios/share/images/logos</em>).</p>\r\n<p><em>Parameter name:</em> icon_image<br> <em>Required:</em> no</p>'),(218,'serviceextinfo','service_description','all','default','<p><strong>Serviceextinfo -</strong><strong> </strong><strong>service description</strong></p>\r\n<p>This directive is description of the service which the data is associated with.</p>\r\n<p><em>Parameter name:</em> service_description<br> <em>Required:</em> yes</p>'),(219,'serviceextinfo','notes','all','default','<p><strong>Serviceextinfo -</strong><strong> </strong><strong>notes</strong></p>\r\n<p>This directive is used to define an optional string of notes pertaining to the service. If you specify a note here, you will see the it in the extended information CGI (when you are viewing information about the specified service).</p>\r\n<p><em>Parameter name:</em> notes<br> <em>Required:</em> no</p>'),(220,'serviceextinfo','action_url','all','default','<p><strong>Serviceextinfo -</strong><strong> </strong><strong>action url</strong></p>\r\n<p>This directive is used to define an optional URL that can be used to provide more actions to be performed on the service. If you specify an URL, you will see a link that says \"Extra Service Actions\" in the extended information CGI (when you are viewing information about the specified service). Any valid URL can be used. If you plan on using relative paths, the base path will the the same as what is used to access the CGIs (i.e. <em>/cgi-bin/nagios/</em>).</p>\r\n<p><em>Parameter name:</em> action_url<br> <em>Required:</em> no</p>'),(221,'serviceextinfo','notes_url','all','default','<p><strong>Serviceextinfo -</strong><strong> </strong><strong>notes url</strong></p>\r\n<p>This directive is used to define an optional URL that can be used to provide more information about the service. If you specify an URL, you will see a link that says \"Extra Service Notes\" in the extended information CGI (when you are viewing information about the specified service). Any valid URL can be used.</p>\r\n<p>If you plan on using relative paths, the base path will the the same as what is used to access the CGIs (i.e. <em>/cgi-bin/nagios/</em>). This can be very useful if you want to make detailed information on the service, emergency contact methods, etc. available to other support staff.</p>\r\n<p><em>Parameter name:</em> notes_url<br> <em>Required:</em> no</p>'),(222,'serviceextinfo','icon_image_alt','all','default','<p><strong>Serviceextinfo -</strong><strong> </strong><strong>icon image alt</strong><strong></strong></p>\r\n<p>This variable is used to define an optional string that is used in the ALT tag of the image specified by the <em>&lt;icon_image&gt;</em> argument.  The ALT tag is used in the status, extended information and statusmap CGIs.</p>\r\n<p><em>Parameter name:</em> icon_image_alt<br> <em>Required:</em> no</p>');
/*!40000 ALTER TABLE `tbl_info` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_lnkContactToCommandHost`
--

DROP TABLE IF EXISTS `tbl_lnkContactToCommandHost`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_lnkContactToCommandHost` (
  `idMaster` int(11) NOT NULL,
  `idSlave` int(11) NOT NULL,
  PRIMARY KEY (`idMaster`,`idSlave`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_lnkContactToCommandHost`
--

LOCK TABLES `tbl_lnkContactToCommandHost` WRITE;
/*!40000 ALTER TABLE `tbl_lnkContactToCommandHost` DISABLE KEYS */;
INSERT INTO `tbl_lnkContactToCommandHost` VALUES (1,108);
/*!40000 ALTER TABLE `tbl_lnkContactToCommandHost` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_lnkContactToCommandService`
--

DROP TABLE IF EXISTS `tbl_lnkContactToCommandService`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_lnkContactToCommandService` (
  `idMaster` int(11) NOT NULL,
  `idSlave` int(11) NOT NULL,
  PRIMARY KEY (`idMaster`,`idSlave`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_lnkContactToCommandService`
--

LOCK TABLES `tbl_lnkContactToCommandService` WRITE;
/*!40000 ALTER TABLE `tbl_lnkContactToCommandService` DISABLE KEYS */;
INSERT INTO `tbl_lnkContactToCommandService` VALUES (1,109);
/*!40000 ALTER TABLE `tbl_lnkContactToCommandService` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_lnkContactToContactgroup`
--

DROP TABLE IF EXISTS `tbl_lnkContactToContactgroup`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_lnkContactToContactgroup` (
  `idMaster` int(11) NOT NULL,
  `idSlave` int(11) NOT NULL,
  PRIMARY KEY (`idMaster`,`idSlave`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_lnkContactToContactgroup`
--

LOCK TABLES `tbl_lnkContactToContactgroup` WRITE;
/*!40000 ALTER TABLE `tbl_lnkContactToContactgroup` DISABLE KEYS */;
/*!40000 ALTER TABLE `tbl_lnkContactToContactgroup` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_lnkContactToContacttemplate`
--

DROP TABLE IF EXISTS `tbl_lnkContactToContacttemplate`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_lnkContactToContacttemplate` (
  `idMaster` int(11) NOT NULL,
  `idSlave` int(11) NOT NULL,
  `idSort` int(11) NOT NULL,
  `idTable` tinyint(4) NOT NULL,
  PRIMARY KEY (`idMaster`,`idSlave`,`idTable`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_lnkContactToContacttemplate`
--

LOCK TABLES `tbl_lnkContactToContacttemplate` WRITE;
/*!40000 ALTER TABLE `tbl_lnkContactToContacttemplate` DISABLE KEYS */;
INSERT INTO `tbl_lnkContactToContacttemplate` VALUES (1,1,1,1);
/*!40000 ALTER TABLE `tbl_lnkContactToContacttemplate` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_lnkContactToVariabledefinition`
--

DROP TABLE IF EXISTS `tbl_lnkContactToVariabledefinition`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_lnkContactToVariabledefinition` (
  `idMaster` int(11) NOT NULL,
  `idSlave` int(11) NOT NULL,
  PRIMARY KEY (`idMaster`,`idSlave`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_lnkContactToVariabledefinition`
--

LOCK TABLES `tbl_lnkContactToVariabledefinition` WRITE;
/*!40000 ALTER TABLE `tbl_lnkContactToVariabledefinition` DISABLE KEYS */;
/*!40000 ALTER TABLE `tbl_lnkContactToVariabledefinition` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_lnkContactgroupToContact`
--

DROP TABLE IF EXISTS `tbl_lnkContactgroupToContact`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_lnkContactgroupToContact` (
  `idMaster` int(11) NOT NULL,
  `idSlave` int(11) NOT NULL,
  PRIMARY KEY (`idMaster`,`idSlave`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_lnkContactgroupToContact`
--

LOCK TABLES `tbl_lnkContactgroupToContact` WRITE;
/*!40000 ALTER TABLE `tbl_lnkContactgroupToContact` DISABLE KEYS */;
INSERT INTO `tbl_lnkContactgroupToContact` VALUES (1,1);
/*!40000 ALTER TABLE `tbl_lnkContactgroupToContact` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_lnkContactgroupToContactgroup`
--

DROP TABLE IF EXISTS `tbl_lnkContactgroupToContactgroup`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_lnkContactgroupToContactgroup` (
  `idMaster` int(11) NOT NULL,
  `idSlave` int(11) NOT NULL,
  PRIMARY KEY (`idMaster`,`idSlave`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_lnkContactgroupToContactgroup`
--

LOCK TABLES `tbl_lnkContactgroupToContactgroup` WRITE;
/*!40000 ALTER TABLE `tbl_lnkContactgroupToContactgroup` DISABLE KEYS */;
/*!40000 ALTER TABLE `tbl_lnkContactgroupToContactgroup` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_lnkContacttemplateToCommandHost`
--

DROP TABLE IF EXISTS `tbl_lnkContacttemplateToCommandHost`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_lnkContacttemplateToCommandHost` (
  `idMaster` int(11) NOT NULL,
  `idSlave` int(11) NOT NULL,
  PRIMARY KEY (`idMaster`,`idSlave`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_lnkContacttemplateToCommandHost`
--

LOCK TABLES `tbl_lnkContacttemplateToCommandHost` WRITE;
/*!40000 ALTER TABLE `tbl_lnkContacttemplateToCommandHost` DISABLE KEYS */;
INSERT INTO `tbl_lnkContacttemplateToCommandHost` VALUES (1,108),(2,1);
/*!40000 ALTER TABLE `tbl_lnkContacttemplateToCommandHost` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_lnkContacttemplateToCommandService`
--

DROP TABLE IF EXISTS `tbl_lnkContacttemplateToCommandService`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_lnkContacttemplateToCommandService` (
  `idMaster` int(11) NOT NULL,
  `idSlave` int(11) NOT NULL,
  PRIMARY KEY (`idMaster`,`idSlave`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_lnkContacttemplateToCommandService`
--

LOCK TABLES `tbl_lnkContacttemplateToCommandService` WRITE;
/*!40000 ALTER TABLE `tbl_lnkContacttemplateToCommandService` DISABLE KEYS */;
INSERT INTO `tbl_lnkContacttemplateToCommandService` VALUES (1,109),(2,2);
/*!40000 ALTER TABLE `tbl_lnkContacttemplateToCommandService` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_lnkContacttemplateToContactgroup`
--

DROP TABLE IF EXISTS `tbl_lnkContacttemplateToContactgroup`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_lnkContacttemplateToContactgroup` (
  `idMaster` int(11) NOT NULL,
  `idSlave` int(11) NOT NULL,
  PRIMARY KEY (`idMaster`,`idSlave`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_lnkContacttemplateToContactgroup`
--

LOCK TABLES `tbl_lnkContacttemplateToContactgroup` WRITE;
/*!40000 ALTER TABLE `tbl_lnkContacttemplateToContactgroup` DISABLE KEYS */;
INSERT INTO `tbl_lnkContacttemplateToContactgroup` VALUES (1,2);
/*!40000 ALTER TABLE `tbl_lnkContacttemplateToContactgroup` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_lnkContacttemplateToContacttemplate`
--

DROP TABLE IF EXISTS `tbl_lnkContacttemplateToContacttemplate`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_lnkContacttemplateToContacttemplate` (
  `idMaster` int(11) NOT NULL,
  `idSlave` int(11) NOT NULL,
  `idSort` int(11) NOT NULL,
  `idTable` tinyint(4) NOT NULL,
  PRIMARY KEY (`idMaster`,`idSlave`,`idTable`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_lnkContacttemplateToContacttemplate`
--

LOCK TABLES `tbl_lnkContacttemplateToContacttemplate` WRITE;
/*!40000 ALTER TABLE `tbl_lnkContacttemplateToContacttemplate` DISABLE KEYS */;
/*!40000 ALTER TABLE `tbl_lnkContacttemplateToContacttemplate` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_lnkContacttemplateToVariabledefinition`
--

DROP TABLE IF EXISTS `tbl_lnkContacttemplateToVariabledefinition`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_lnkContacttemplateToVariabledefinition` (
  `idMaster` int(11) NOT NULL,
  `idSlave` int(11) NOT NULL,
  PRIMARY KEY (`idMaster`,`idSlave`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_lnkContacttemplateToVariabledefinition`
--

LOCK TABLES `tbl_lnkContacttemplateToVariabledefinition` WRITE;
/*!40000 ALTER TABLE `tbl_lnkContacttemplateToVariabledefinition` DISABLE KEYS */;
/*!40000 ALTER TABLE `tbl_lnkContacttemplateToVariabledefinition` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_lnkHostToContact`
--

DROP TABLE IF EXISTS `tbl_lnkHostToContact`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_lnkHostToContact` (
  `idMaster` int(11) NOT NULL,
  `idSlave` int(11) NOT NULL,
  PRIMARY KEY (`idMaster`,`idSlave`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_lnkHostToContact`
--

LOCK TABLES `tbl_lnkHostToContact` WRITE;
/*!40000 ALTER TABLE `tbl_lnkHostToContact` DISABLE KEYS */;
/*!40000 ALTER TABLE `tbl_lnkHostToContact` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_lnkHostToContactgroup`
--

DROP TABLE IF EXISTS `tbl_lnkHostToContactgroup`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_lnkHostToContactgroup` (
  `idMaster` int(11) NOT NULL,
  `idSlave` int(11) NOT NULL,
  PRIMARY KEY (`idMaster`,`idSlave`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_lnkHostToContactgroup`
--

LOCK TABLES `tbl_lnkHostToContactgroup` WRITE;
/*!40000 ALTER TABLE `tbl_lnkHostToContactgroup` DISABLE KEYS */;
/*!40000 ALTER TABLE `tbl_lnkHostToContactgroup` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_lnkHostToHost`
--

DROP TABLE IF EXISTS `tbl_lnkHostToHost`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_lnkHostToHost` (
  `idMaster` int(11) NOT NULL,
  `idSlave` int(11) NOT NULL,
  PRIMARY KEY (`idMaster`,`idSlave`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_lnkHostToHost`
--

LOCK TABLES `tbl_lnkHostToHost` WRITE;
/*!40000 ALTER TABLE `tbl_lnkHostToHost` DISABLE KEYS */;
/*!40000 ALTER TABLE `tbl_lnkHostToHost` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_lnkHostToHostgroup`
--

DROP TABLE IF EXISTS `tbl_lnkHostToHostgroup`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_lnkHostToHostgroup` (
  `idMaster` int(11) NOT NULL,
  `idSlave` int(11) NOT NULL,
  PRIMARY KEY (`idMaster`,`idSlave`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_lnkHostToHostgroup`
--

LOCK TABLES `tbl_lnkHostToHostgroup` WRITE;
/*!40000 ALTER TABLE `tbl_lnkHostToHostgroup` DISABLE KEYS */;
/*!40000 ALTER TABLE `tbl_lnkHostToHostgroup` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_lnkHostToHosttemplate`
--

DROP TABLE IF EXISTS `tbl_lnkHostToHosttemplate`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_lnkHostToHosttemplate` (
  `idMaster` int(11) NOT NULL,
  `idSlave` int(11) NOT NULL,
  `idSort` int(11) NOT NULL,
  `idTable` tinyint(4) NOT NULL,
  PRIMARY KEY (`idMaster`,`idSlave`,`idTable`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_lnkHostToHosttemplate`
--

LOCK TABLES `tbl_lnkHostToHosttemplate` WRITE;
/*!40000 ALTER TABLE `tbl_lnkHostToHosttemplate` DISABLE KEYS */;
INSERT INTO `tbl_lnkHostToHosttemplate` VALUES (1,38,1,1);
/*!40000 ALTER TABLE `tbl_lnkHostToHosttemplate` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_lnkHostToVariabledefinition`
--

DROP TABLE IF EXISTS `tbl_lnkHostToVariabledefinition`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_lnkHostToVariabledefinition` (
  `idMaster` int(11) NOT NULL,
  `idSlave` int(11) NOT NULL,
  PRIMARY KEY (`idMaster`,`idSlave`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_lnkHostToVariabledefinition`
--

LOCK TABLES `tbl_lnkHostToVariabledefinition` WRITE;
/*!40000 ALTER TABLE `tbl_lnkHostToVariabledefinition` DISABLE KEYS */;
/*!40000 ALTER TABLE `tbl_lnkHostToVariabledefinition` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_lnkHostdependencyToHost_DH`
--

DROP TABLE IF EXISTS `tbl_lnkHostdependencyToHost_DH`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_lnkHostdependencyToHost_DH` (
  `idMaster` int(11) NOT NULL,
  `idSlave` int(11) NOT NULL,
  PRIMARY KEY (`idMaster`,`idSlave`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_lnkHostdependencyToHost_DH`
--

LOCK TABLES `tbl_lnkHostdependencyToHost_DH` WRITE;
/*!40000 ALTER TABLE `tbl_lnkHostdependencyToHost_DH` DISABLE KEYS */;
/*!40000 ALTER TABLE `tbl_lnkHostdependencyToHost_DH` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_lnkHostdependencyToHost_H`
--

DROP TABLE IF EXISTS `tbl_lnkHostdependencyToHost_H`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_lnkHostdependencyToHost_H` (
  `idMaster` int(11) NOT NULL,
  `idSlave` int(11) NOT NULL,
  PRIMARY KEY (`idMaster`,`idSlave`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_lnkHostdependencyToHost_H`
--

LOCK TABLES `tbl_lnkHostdependencyToHost_H` WRITE;
/*!40000 ALTER TABLE `tbl_lnkHostdependencyToHost_H` DISABLE KEYS */;
/*!40000 ALTER TABLE `tbl_lnkHostdependencyToHost_H` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_lnkHostdependencyToHostgroup_DH`
--

DROP TABLE IF EXISTS `tbl_lnkHostdependencyToHostgroup_DH`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_lnkHostdependencyToHostgroup_DH` (
  `idMaster` int(11) NOT NULL,
  `idSlave` int(11) NOT NULL,
  PRIMARY KEY (`idMaster`,`idSlave`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_lnkHostdependencyToHostgroup_DH`
--

LOCK TABLES `tbl_lnkHostdependencyToHostgroup_DH` WRITE;
/*!40000 ALTER TABLE `tbl_lnkHostdependencyToHostgroup_DH` DISABLE KEYS */;
/*!40000 ALTER TABLE `tbl_lnkHostdependencyToHostgroup_DH` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_lnkHostdependencyToHostgroup_H`
--

DROP TABLE IF EXISTS `tbl_lnkHostdependencyToHostgroup_H`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_lnkHostdependencyToHostgroup_H` (
  `idMaster` int(11) NOT NULL,
  `idSlave` int(11) NOT NULL,
  PRIMARY KEY (`idMaster`,`idSlave`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_lnkHostdependencyToHostgroup_H`
--

LOCK TABLES `tbl_lnkHostdependencyToHostgroup_H` WRITE;
/*!40000 ALTER TABLE `tbl_lnkHostdependencyToHostgroup_H` DISABLE KEYS */;
/*!40000 ALTER TABLE `tbl_lnkHostdependencyToHostgroup_H` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_lnkHostescalationToContact`
--

DROP TABLE IF EXISTS `tbl_lnkHostescalationToContact`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_lnkHostescalationToContact` (
  `idMaster` int(11) NOT NULL,
  `idSlave` int(11) NOT NULL,
  PRIMARY KEY (`idMaster`,`idSlave`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_lnkHostescalationToContact`
--

LOCK TABLES `tbl_lnkHostescalationToContact` WRITE;
/*!40000 ALTER TABLE `tbl_lnkHostescalationToContact` DISABLE KEYS */;
/*!40000 ALTER TABLE `tbl_lnkHostescalationToContact` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_lnkHostescalationToContactgroup`
--

DROP TABLE IF EXISTS `tbl_lnkHostescalationToContactgroup`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_lnkHostescalationToContactgroup` (
  `idMaster` int(11) NOT NULL,
  `idSlave` int(11) NOT NULL,
  PRIMARY KEY (`idMaster`,`idSlave`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_lnkHostescalationToContactgroup`
--

LOCK TABLES `tbl_lnkHostescalationToContactgroup` WRITE;
/*!40000 ALTER TABLE `tbl_lnkHostescalationToContactgroup` DISABLE KEYS */;
/*!40000 ALTER TABLE `tbl_lnkHostescalationToContactgroup` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_lnkHostescalationToHost`
--

DROP TABLE IF EXISTS `tbl_lnkHostescalationToHost`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_lnkHostescalationToHost` (
  `idMaster` int(11) NOT NULL,
  `idSlave` int(11) NOT NULL,
  `exclude` tinyint(1) NOT NULL,
  PRIMARY KEY (`idMaster`,`idSlave`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_lnkHostescalationToHost`
--

LOCK TABLES `tbl_lnkHostescalationToHost` WRITE;
/*!40000 ALTER TABLE `tbl_lnkHostescalationToHost` DISABLE KEYS */;
/*!40000 ALTER TABLE `tbl_lnkHostescalationToHost` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_lnkHostescalationToHostgroup`
--

DROP TABLE IF EXISTS `tbl_lnkHostescalationToHostgroup`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_lnkHostescalationToHostgroup` (
  `idMaster` int(11) NOT NULL,
  `idSlave` int(11) NOT NULL,
  `exclude` tinyint(1) NOT NULL,
  PRIMARY KEY (`idMaster`,`idSlave`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_lnkHostescalationToHostgroup`
--

LOCK TABLES `tbl_lnkHostescalationToHostgroup` WRITE;
/*!40000 ALTER TABLE `tbl_lnkHostescalationToHostgroup` DISABLE KEYS */;
/*!40000 ALTER TABLE `tbl_lnkHostescalationToHostgroup` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_lnkHostgroupToHost`
--

DROP TABLE IF EXISTS `tbl_lnkHostgroupToHost`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_lnkHostgroupToHost` (
  `idMaster` int(11) NOT NULL,
  `idSlave` int(11) NOT NULL,
  `exclude` tinyint(1) NOT NULL,
  PRIMARY KEY (`idMaster`,`idSlave`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_lnkHostgroupToHost`
--

LOCK TABLES `tbl_lnkHostgroupToHost` WRITE;
/*!40000 ALTER TABLE `tbl_lnkHostgroupToHost` DISABLE KEYS */;
INSERT INTO `tbl_lnkHostgroupToHost` VALUES (1,1,0);
/*!40000 ALTER TABLE `tbl_lnkHostgroupToHost` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_lnkHostgroupToHostgroup`
--

DROP TABLE IF EXISTS `tbl_lnkHostgroupToHostgroup`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_lnkHostgroupToHostgroup` (
  `idMaster` int(11) NOT NULL,
  `idSlave` int(11) NOT NULL,
  `exclude` tinyint(1) NOT NULL,
  PRIMARY KEY (`idMaster`,`idSlave`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_lnkHostgroupToHostgroup`
--

LOCK TABLES `tbl_lnkHostgroupToHostgroup` WRITE;
/*!40000 ALTER TABLE `tbl_lnkHostgroupToHostgroup` DISABLE KEYS */;
/*!40000 ALTER TABLE `tbl_lnkHostgroupToHostgroup` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_lnkHosttemplateToContact`
--

DROP TABLE IF EXISTS `tbl_lnkHosttemplateToContact`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_lnkHosttemplateToContact` (
  `idMaster` int(11) NOT NULL,
  `idSlave` int(11) NOT NULL,
  PRIMARY KEY (`idMaster`,`idSlave`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_lnkHosttemplateToContact`
--

LOCK TABLES `tbl_lnkHosttemplateToContact` WRITE;
/*!40000 ALTER TABLE `tbl_lnkHosttemplateToContact` DISABLE KEYS */;
/*!40000 ALTER TABLE `tbl_lnkHosttemplateToContact` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_lnkHosttemplateToContactgroup`
--

DROP TABLE IF EXISTS `tbl_lnkHosttemplateToContactgroup`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_lnkHosttemplateToContactgroup` (
  `idMaster` int(11) NOT NULL,
  `idSlave` int(11) NOT NULL,
  PRIMARY KEY (`idMaster`,`idSlave`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_lnkHosttemplateToContactgroup`
--

LOCK TABLES `tbl_lnkHosttemplateToContactgroup` WRITE;
/*!40000 ALTER TABLE `tbl_lnkHosttemplateToContactgroup` DISABLE KEYS */;
INSERT INTO `tbl_lnkHosttemplateToContactgroup` VALUES (38,1),(40,1),(41,1),(42,1);
/*!40000 ALTER TABLE `tbl_lnkHosttemplateToContactgroup` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_lnkHosttemplateToHost`
--

DROP TABLE IF EXISTS `tbl_lnkHosttemplateToHost`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_lnkHosttemplateToHost` (
  `idMaster` int(11) NOT NULL,
  `idSlave` int(11) NOT NULL,
  PRIMARY KEY (`idMaster`,`idSlave`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_lnkHosttemplateToHost`
--

LOCK TABLES `tbl_lnkHosttemplateToHost` WRITE;
/*!40000 ALTER TABLE `tbl_lnkHosttemplateToHost` DISABLE KEYS */;
/*!40000 ALTER TABLE `tbl_lnkHosttemplateToHost` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_lnkHosttemplateToHostgroup`
--

DROP TABLE IF EXISTS `tbl_lnkHosttemplateToHostgroup`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_lnkHosttemplateToHostgroup` (
  `idMaster` int(11) NOT NULL,
  `idSlave` int(11) NOT NULL,
  PRIMARY KEY (`idMaster`,`idSlave`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_lnkHosttemplateToHostgroup`
--

LOCK TABLES `tbl_lnkHosttemplateToHostgroup` WRITE;
/*!40000 ALTER TABLE `tbl_lnkHosttemplateToHostgroup` DISABLE KEYS */;
INSERT INTO `tbl_lnkHosttemplateToHostgroup` VALUES (40,2);
/*!40000 ALTER TABLE `tbl_lnkHosttemplateToHostgroup` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_lnkHosttemplateToHosttemplate`
--

DROP TABLE IF EXISTS `tbl_lnkHosttemplateToHosttemplate`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_lnkHosttemplateToHosttemplate` (
  `idMaster` int(11) NOT NULL,
  `idSlave` int(11) NOT NULL,
  `idSort` int(11) NOT NULL,
  `idTable` tinyint(4) NOT NULL,
  PRIMARY KEY (`idMaster`,`idSlave`,`idTable`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_lnkHosttemplateToHosttemplate`
--

LOCK TABLES `tbl_lnkHosttemplateToHosttemplate` WRITE;
/*!40000 ALTER TABLE `tbl_lnkHosttemplateToHosttemplate` DISABLE KEYS */;
INSERT INTO `tbl_lnkHosttemplateToHosttemplate` VALUES (1,2,1,1),(3,2,1,1),(4,2,1,1),(5,2,1,1),(6,2,1,1),(7,2,1,1),(8,2,1,1),(9,2,1,1),(10,2,1,1),(11,2,1,1),(12,2,1,1),(13,2,1,1),(14,2,1,1),(15,2,1,1),(16,2,1,1),(17,2,1,1),(18,2,1,1),(19,2,1,1),(20,2,1,1),(21,2,1,1),(22,2,1,1),(23,2,1,1),(24,2,1,1),(25,2,1,1),(26,2,1,1),(27,2,1,1),(28,2,1,1),(29,2,1,1),(30,2,1,1),(31,2,1,1),(32,2,1,1),(33,2,1,1),(34,2,1,1),(35,2,1,1),(36,2,1,1),(37,2,1,1),(38,39,1,1),(40,39,1,1),(41,39,1,1),(42,39,1,1),(43,2,1,1),(44,2,1,1),(45,2,1,1),(46,2,1,1),(47,2,1,1),(48,2,1,1),(49,2,1,1),(50,2,1,1);
/*!40000 ALTER TABLE `tbl_lnkHosttemplateToHosttemplate` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_lnkHosttemplateToVariabledefinition`
--

DROP TABLE IF EXISTS `tbl_lnkHosttemplateToVariabledefinition`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_lnkHosttemplateToVariabledefinition` (
  `idMaster` int(11) NOT NULL,
  `idSlave` int(11) NOT NULL,
  PRIMARY KEY (`idMaster`,`idSlave`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_lnkHosttemplateToVariabledefinition`
--

LOCK TABLES `tbl_lnkHosttemplateToVariabledefinition` WRITE;
/*!40000 ALTER TABLE `tbl_lnkHosttemplateToVariabledefinition` DISABLE KEYS */;
/*!40000 ALTER TABLE `tbl_lnkHosttemplateToVariabledefinition` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_lnkServiceToContact`
--

DROP TABLE IF EXISTS `tbl_lnkServiceToContact`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_lnkServiceToContact` (
  `idMaster` int(11) NOT NULL,
  `idSlave` int(11) NOT NULL,
  PRIMARY KEY (`idMaster`,`idSlave`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_lnkServiceToContact`
--

LOCK TABLES `tbl_lnkServiceToContact` WRITE;
/*!40000 ALTER TABLE `tbl_lnkServiceToContact` DISABLE KEYS */;
/*!40000 ALTER TABLE `tbl_lnkServiceToContact` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_lnkServiceToContactgroup`
--

DROP TABLE IF EXISTS `tbl_lnkServiceToContactgroup`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_lnkServiceToContactgroup` (
  `idMaster` int(11) NOT NULL,
  `idSlave` int(11) NOT NULL,
  PRIMARY KEY (`idMaster`,`idSlave`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_lnkServiceToContactgroup`
--

LOCK TABLES `tbl_lnkServiceToContactgroup` WRITE;
/*!40000 ALTER TABLE `tbl_lnkServiceToContactgroup` DISABLE KEYS */;
/*!40000 ALTER TABLE `tbl_lnkServiceToContactgroup` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_lnkServiceToHost`
--

DROP TABLE IF EXISTS `tbl_lnkServiceToHost`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_lnkServiceToHost` (
  `idMaster` int(11) NOT NULL,
  `idSlave` int(11) NOT NULL,
  `exclude` tinyint(1) NOT NULL,
  PRIMARY KEY (`idMaster`,`idSlave`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_lnkServiceToHost`
--

LOCK TABLES `tbl_lnkServiceToHost` WRITE;
/*!40000 ALTER TABLE `tbl_lnkServiceToHost` DISABLE KEYS */;
INSERT INTO `tbl_lnkServiceToHost` VALUES (1,1,0),(2,1,0),(3,1,0),(4,1,0),(5,1,0),(6,1,0),(7,1,0),(8,1,0),(9,1,0),(10,1,0),(11,1,0),(12,1,0),(13,1,0),(14,1,0);
/*!40000 ALTER TABLE `tbl_lnkServiceToHost` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_lnkServiceToHostgroup`
--

DROP TABLE IF EXISTS `tbl_lnkServiceToHostgroup`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_lnkServiceToHostgroup` (
  `idMaster` int(11) NOT NULL,
  `idSlave` int(11) NOT NULL,
  `exclude` tinyint(1) NOT NULL,
  PRIMARY KEY (`idMaster`,`idSlave`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_lnkServiceToHostgroup`
--

LOCK TABLES `tbl_lnkServiceToHostgroup` WRITE;
/*!40000 ALTER TABLE `tbl_lnkServiceToHostgroup` DISABLE KEYS */;
/*!40000 ALTER TABLE `tbl_lnkServiceToHostgroup` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_lnkServiceToServicegroup`
--

DROP TABLE IF EXISTS `tbl_lnkServiceToServicegroup`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_lnkServiceToServicegroup` (
  `idMaster` int(11) NOT NULL,
  `idSlave` int(11) NOT NULL,
  PRIMARY KEY (`idMaster`,`idSlave`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_lnkServiceToServicegroup`
--

LOCK TABLES `tbl_lnkServiceToServicegroup` WRITE;
/*!40000 ALTER TABLE `tbl_lnkServiceToServicegroup` DISABLE KEYS */;
/*!40000 ALTER TABLE `tbl_lnkServiceToServicegroup` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_lnkServiceToServicetemplate`
--

DROP TABLE IF EXISTS `tbl_lnkServiceToServicetemplate`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_lnkServiceToServicetemplate` (
  `idMaster` int(11) NOT NULL,
  `idSlave` int(11) NOT NULL,
  `idSort` int(11) NOT NULL,
  `idTable` tinyint(4) NOT NULL,
  PRIMARY KEY (`idMaster`,`idSlave`,`idTable`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_lnkServiceToServicetemplate`
--

LOCK TABLES `tbl_lnkServiceToServicetemplate` WRITE;
/*!40000 ALTER TABLE `tbl_lnkServiceToServicetemplate` DISABLE KEYS */;
INSERT INTO `tbl_lnkServiceToServicetemplate` VALUES (1,57,1,1),(2,57,1,1),(3,57,1,1),(4,57,1,1),(5,57,1,1),(6,57,1,1),(7,57,1,1),(8,57,1,1),(9,57,1,1),(10,57,1,1),(11,57,1,1),(12,57,1,1),(13,57,1,1),(14,57,1,1);
/*!40000 ALTER TABLE `tbl_lnkServiceToServicetemplate` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_lnkServiceToVariabledefinition`
--

DROP TABLE IF EXISTS `tbl_lnkServiceToVariabledefinition`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_lnkServiceToVariabledefinition` (
  `idMaster` int(11) NOT NULL,
  `idSlave` int(11) NOT NULL,
  PRIMARY KEY (`idMaster`,`idSlave`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_lnkServiceToVariabledefinition`
--

LOCK TABLES `tbl_lnkServiceToVariabledefinition` WRITE;
/*!40000 ALTER TABLE `tbl_lnkServiceToVariabledefinition` DISABLE KEYS */;
/*!40000 ALTER TABLE `tbl_lnkServiceToVariabledefinition` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_lnkServicedependencyToHost_DH`
--

DROP TABLE IF EXISTS `tbl_lnkServicedependencyToHost_DH`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_lnkServicedependencyToHost_DH` (
  `idMaster` int(11) NOT NULL,
  `idSlave` int(11) NOT NULL,
  PRIMARY KEY (`idMaster`,`idSlave`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_lnkServicedependencyToHost_DH`
--

LOCK TABLES `tbl_lnkServicedependencyToHost_DH` WRITE;
/*!40000 ALTER TABLE `tbl_lnkServicedependencyToHost_DH` DISABLE KEYS */;
/*!40000 ALTER TABLE `tbl_lnkServicedependencyToHost_DH` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_lnkServicedependencyToHost_H`
--

DROP TABLE IF EXISTS `tbl_lnkServicedependencyToHost_H`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_lnkServicedependencyToHost_H` (
  `idMaster` int(11) NOT NULL,
  `idSlave` int(11) NOT NULL,
  PRIMARY KEY (`idMaster`,`idSlave`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_lnkServicedependencyToHost_H`
--

LOCK TABLES `tbl_lnkServicedependencyToHost_H` WRITE;
/*!40000 ALTER TABLE `tbl_lnkServicedependencyToHost_H` DISABLE KEYS */;
/*!40000 ALTER TABLE `tbl_lnkServicedependencyToHost_H` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_lnkServicedependencyToHostgroup_DH`
--

DROP TABLE IF EXISTS `tbl_lnkServicedependencyToHostgroup_DH`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_lnkServicedependencyToHostgroup_DH` (
  `idMaster` int(11) NOT NULL,
  `idSlave` int(11) NOT NULL,
  PRIMARY KEY (`idMaster`,`idSlave`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_lnkServicedependencyToHostgroup_DH`
--

LOCK TABLES `tbl_lnkServicedependencyToHostgroup_DH` WRITE;
/*!40000 ALTER TABLE `tbl_lnkServicedependencyToHostgroup_DH` DISABLE KEYS */;
/*!40000 ALTER TABLE `tbl_lnkServicedependencyToHostgroup_DH` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_lnkServicedependencyToHostgroup_H`
--

DROP TABLE IF EXISTS `tbl_lnkServicedependencyToHostgroup_H`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_lnkServicedependencyToHostgroup_H` (
  `idMaster` int(11) NOT NULL,
  `idSlave` int(11) NOT NULL,
  PRIMARY KEY (`idMaster`,`idSlave`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_lnkServicedependencyToHostgroup_H`
--

LOCK TABLES `tbl_lnkServicedependencyToHostgroup_H` WRITE;
/*!40000 ALTER TABLE `tbl_lnkServicedependencyToHostgroup_H` DISABLE KEYS */;
/*!40000 ALTER TABLE `tbl_lnkServicedependencyToHostgroup_H` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_lnkServicedependencyToService_DS`
--

DROP TABLE IF EXISTS `tbl_lnkServicedependencyToService_DS`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_lnkServicedependencyToService_DS` (
  `idMaster` int(11) NOT NULL,
  `idSlave` int(11) NOT NULL,
  PRIMARY KEY (`idMaster`,`idSlave`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_lnkServicedependencyToService_DS`
--

LOCK TABLES `tbl_lnkServicedependencyToService_DS` WRITE;
/*!40000 ALTER TABLE `tbl_lnkServicedependencyToService_DS` DISABLE KEYS */;
/*!40000 ALTER TABLE `tbl_lnkServicedependencyToService_DS` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_lnkServicedependencyToService_S`
--

DROP TABLE IF EXISTS `tbl_lnkServicedependencyToService_S`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_lnkServicedependencyToService_S` (
  `idMaster` int(11) NOT NULL,
  `idSlave` int(11) NOT NULL,
  PRIMARY KEY (`idMaster`,`idSlave`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_lnkServicedependencyToService_S`
--

LOCK TABLES `tbl_lnkServicedependencyToService_S` WRITE;
/*!40000 ALTER TABLE `tbl_lnkServicedependencyToService_S` DISABLE KEYS */;
/*!40000 ALTER TABLE `tbl_lnkServicedependencyToService_S` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_lnkServiceescalationToContact`
--

DROP TABLE IF EXISTS `tbl_lnkServiceescalationToContact`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_lnkServiceescalationToContact` (
  `idMaster` int(11) NOT NULL,
  `idSlave` int(11) NOT NULL,
  PRIMARY KEY (`idMaster`,`idSlave`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_lnkServiceescalationToContact`
--

LOCK TABLES `tbl_lnkServiceescalationToContact` WRITE;
/*!40000 ALTER TABLE `tbl_lnkServiceescalationToContact` DISABLE KEYS */;
/*!40000 ALTER TABLE `tbl_lnkServiceescalationToContact` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_lnkServiceescalationToContactgroup`
--

DROP TABLE IF EXISTS `tbl_lnkServiceescalationToContactgroup`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_lnkServiceescalationToContactgroup` (
  `idMaster` int(11) NOT NULL,
  `idSlave` int(11) NOT NULL,
  PRIMARY KEY (`idMaster`,`idSlave`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_lnkServiceescalationToContactgroup`
--

LOCK TABLES `tbl_lnkServiceescalationToContactgroup` WRITE;
/*!40000 ALTER TABLE `tbl_lnkServiceescalationToContactgroup` DISABLE KEYS */;
/*!40000 ALTER TABLE `tbl_lnkServiceescalationToContactgroup` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_lnkServiceescalationToHost`
--

DROP TABLE IF EXISTS `tbl_lnkServiceescalationToHost`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_lnkServiceescalationToHost` (
  `idMaster` int(11) NOT NULL,
  `idSlave` int(11) NOT NULL,
  `exclude` tinyint(1) NOT NULL,
  PRIMARY KEY (`idMaster`,`idSlave`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_lnkServiceescalationToHost`
--

LOCK TABLES `tbl_lnkServiceescalationToHost` WRITE;
/*!40000 ALTER TABLE `tbl_lnkServiceescalationToHost` DISABLE KEYS */;
/*!40000 ALTER TABLE `tbl_lnkServiceescalationToHost` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_lnkServiceescalationToHostgroup`
--

DROP TABLE IF EXISTS `tbl_lnkServiceescalationToHostgroup`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_lnkServiceescalationToHostgroup` (
  `idMaster` int(11) NOT NULL,
  `idSlave` int(11) NOT NULL,
  `exclude` tinyint(1) NOT NULL,
  PRIMARY KEY (`idMaster`,`idSlave`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_lnkServiceescalationToHostgroup`
--

LOCK TABLES `tbl_lnkServiceescalationToHostgroup` WRITE;
/*!40000 ALTER TABLE `tbl_lnkServiceescalationToHostgroup` DISABLE KEYS */;
/*!40000 ALTER TABLE `tbl_lnkServiceescalationToHostgroup` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_lnkServiceescalationToService`
--

DROP TABLE IF EXISTS `tbl_lnkServiceescalationToService`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_lnkServiceescalationToService` (
  `idMaster` int(11) NOT NULL,
  `idSlave` int(11) NOT NULL,
  PRIMARY KEY (`idMaster`,`idSlave`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_lnkServiceescalationToService`
--

LOCK TABLES `tbl_lnkServiceescalationToService` WRITE;
/*!40000 ALTER TABLE `tbl_lnkServiceescalationToService` DISABLE KEYS */;
/*!40000 ALTER TABLE `tbl_lnkServiceescalationToService` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_lnkServicegroupToService`
--

DROP TABLE IF EXISTS `tbl_lnkServicegroupToService`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_lnkServicegroupToService` (
  `idMaster` int(11) NOT NULL,
  `idSlaveH` int(11) NOT NULL,
  `idSlaveHG` int(11) NOT NULL,
  `idSlaveS` int(11) NOT NULL,
  PRIMARY KEY (`idMaster`,`idSlaveH`,`idSlaveHG`,`idSlaveS`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_lnkServicegroupToService`
--

LOCK TABLES `tbl_lnkServicegroupToService` WRITE;
/*!40000 ALTER TABLE `tbl_lnkServicegroupToService` DISABLE KEYS */;
/*!40000 ALTER TABLE `tbl_lnkServicegroupToService` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_lnkServicegroupToServicegroup`
--

DROP TABLE IF EXISTS `tbl_lnkServicegroupToServicegroup`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_lnkServicegroupToServicegroup` (
  `idMaster` int(11) NOT NULL,
  `idSlave` int(11) NOT NULL,
  PRIMARY KEY (`idMaster`,`idSlave`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_lnkServicegroupToServicegroup`
--

LOCK TABLES `tbl_lnkServicegroupToServicegroup` WRITE;
/*!40000 ALTER TABLE `tbl_lnkServicegroupToServicegroup` DISABLE KEYS */;
/*!40000 ALTER TABLE `tbl_lnkServicegroupToServicegroup` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_lnkServicetemplateToContact`
--

DROP TABLE IF EXISTS `tbl_lnkServicetemplateToContact`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_lnkServicetemplateToContact` (
  `idMaster` int(11) NOT NULL,
  `idSlave` int(11) NOT NULL,
  PRIMARY KEY (`idMaster`,`idSlave`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_lnkServicetemplateToContact`
--

LOCK TABLES `tbl_lnkServicetemplateToContact` WRITE;
/*!40000 ALTER TABLE `tbl_lnkServicetemplateToContact` DISABLE KEYS */;
/*!40000 ALTER TABLE `tbl_lnkServicetemplateToContact` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_lnkServicetemplateToContactgroup`
--

DROP TABLE IF EXISTS `tbl_lnkServicetemplateToContactgroup`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_lnkServicetemplateToContactgroup` (
  `idMaster` int(11) NOT NULL,
  `idSlave` int(11) NOT NULL,
  PRIMARY KEY (`idMaster`,`idSlave`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_lnkServicetemplateToContactgroup`
--

LOCK TABLES `tbl_lnkServicetemplateToContactgroup` WRITE;
/*!40000 ALTER TABLE `tbl_lnkServicetemplateToContactgroup` DISABLE KEYS */;
INSERT INTO `tbl_lnkServicetemplateToContactgroup` VALUES (58,1);
/*!40000 ALTER TABLE `tbl_lnkServicetemplateToContactgroup` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_lnkServicetemplateToHost`
--

DROP TABLE IF EXISTS `tbl_lnkServicetemplateToHost`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_lnkServicetemplateToHost` (
  `idMaster` int(11) NOT NULL,
  `idSlave` int(11) NOT NULL,
  `exclude` tinyint(1) NOT NULL,
  PRIMARY KEY (`idMaster`,`idSlave`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_lnkServicetemplateToHost`
--

LOCK TABLES `tbl_lnkServicetemplateToHost` WRITE;
/*!40000 ALTER TABLE `tbl_lnkServicetemplateToHost` DISABLE KEYS */;
/*!40000 ALTER TABLE `tbl_lnkServicetemplateToHost` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_lnkServicetemplateToHostgroup`
--

DROP TABLE IF EXISTS `tbl_lnkServicetemplateToHostgroup`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_lnkServicetemplateToHostgroup` (
  `idMaster` int(11) NOT NULL,
  `idSlave` int(11) NOT NULL,
  `exclude` tinyint(1) NOT NULL,
  PRIMARY KEY (`idMaster`,`idSlave`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_lnkServicetemplateToHostgroup`
--

LOCK TABLES `tbl_lnkServicetemplateToHostgroup` WRITE;
/*!40000 ALTER TABLE `tbl_lnkServicetemplateToHostgroup` DISABLE KEYS */;
/*!40000 ALTER TABLE `tbl_lnkServicetemplateToHostgroup` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_lnkServicetemplateToServicegroup`
--

DROP TABLE IF EXISTS `tbl_lnkServicetemplateToServicegroup`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_lnkServicetemplateToServicegroup` (
  `idMaster` int(11) NOT NULL,
  `idSlave` int(11) NOT NULL,
  PRIMARY KEY (`idMaster`,`idSlave`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_lnkServicetemplateToServicegroup`
--

LOCK TABLES `tbl_lnkServicetemplateToServicegroup` WRITE;
/*!40000 ALTER TABLE `tbl_lnkServicetemplateToServicegroup` DISABLE KEYS */;
/*!40000 ALTER TABLE `tbl_lnkServicetemplateToServicegroup` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_lnkServicetemplateToServicetemplate`
--

DROP TABLE IF EXISTS `tbl_lnkServicetemplateToServicetemplate`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_lnkServicetemplateToServicetemplate` (
  `idMaster` int(11) NOT NULL,
  `idSlave` int(11) NOT NULL,
  `idSort` int(11) NOT NULL,
  `idTable` tinyint(4) NOT NULL,
  PRIMARY KEY (`idMaster`,`idSlave`,`idTable`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_lnkServicetemplateToServicetemplate`
--

LOCK TABLES `tbl_lnkServicetemplateToServicetemplate` WRITE;
/*!40000 ALTER TABLE `tbl_lnkServicetemplateToServicetemplate` DISABLE KEYS */;
INSERT INTO `tbl_lnkServicetemplateToServicetemplate` VALUES (1,2,1,1),(3,2,1,1),(4,2,1,1),(5,2,1,1),(6,2,1,1),(7,2,1,1),(8,2,1,1),(9,2,1,1),(10,2,1,1),(11,2,1,1),(12,2,1,1),(13,2,1,1),(14,2,1,1),(15,2,1,1),(16,2,1,1),(17,2,1,1),(18,2,1,1),(19,2,1,1),(20,2,1,1),(21,2,1,1),(22,2,1,1),(23,2,1,1),(24,2,1,1),(25,2,1,1),(26,2,1,1),(27,2,1,1),(28,2,1,1),(29,2,1,1),(30,2,1,1),(31,32,1,1),(33,34,1,1),(35,2,1,1),(36,2,1,1),(37,2,1,1),(38,2,1,1),(39,2,1,1),(40,2,1,1),(41,2,1,1),(42,2,1,1),(43,2,1,1),(44,2,1,1),(45,2,1,1),(46,2,1,1),(47,2,1,1),(48,2,1,1),(49,2,1,1),(50,2,1,1),(51,2,1,1),(52,2,1,1),(53,2,1,1),(54,2,1,1),(55,2,1,1),(56,2,1,1),(57,58,1,1),(32,2,1,1),(59,2,1,1),(60,2,1,1),(34,2,1,1),(61,2,1,1),(62,2,1,1),(63,2,1,1),(64,2,1,1),(65,2,1,1),(66,2,1,1),(67,2,1,1),(68,2,1,1),(69,2,1,1),(70,2,1,1),(71,2,1,1),(72,2,1,1),(73,2,1,1),(74,2,1,1),(75,2,1,1),(76,2,1,1),(77,2,1,1),(78,2,1,1),(79,2,1,1),(80,2,1,1),(81,2,1,1),(82,2,1,1),(83,2,1,1);
/*!40000 ALTER TABLE `tbl_lnkServicetemplateToServicetemplate` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_lnkServicetemplateToVariabledefinition`
--

DROP TABLE IF EXISTS `tbl_lnkServicetemplateToVariabledefinition`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_lnkServicetemplateToVariabledefinition` (
  `idMaster` int(11) NOT NULL,
  `idSlave` int(11) NOT NULL,
  PRIMARY KEY (`idMaster`,`idSlave`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_lnkServicetemplateToVariabledefinition`
--

LOCK TABLES `tbl_lnkServicetemplateToVariabledefinition` WRITE;
/*!40000 ALTER TABLE `tbl_lnkServicetemplateToVariabledefinition` DISABLE KEYS */;
/*!40000 ALTER TABLE `tbl_lnkServicetemplateToVariabledefinition` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_lnkTimeperiodToTimeperiod`
--

DROP TABLE IF EXISTS `tbl_lnkTimeperiodToTimeperiod`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_lnkTimeperiodToTimeperiod` (
  `idMaster` int(11) NOT NULL,
  `idSlave` int(11) NOT NULL,
  PRIMARY KEY (`idMaster`,`idSlave`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_lnkTimeperiodToTimeperiod`
--

LOCK TABLES `tbl_lnkTimeperiodToTimeperiod` WRITE;
/*!40000 ALTER TABLE `tbl_lnkTimeperiodToTimeperiod` DISABLE KEYS */;
/*!40000 ALTER TABLE `tbl_lnkTimeperiodToTimeperiod` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_logbook`
--

DROP TABLE IF EXISTS `tbl_logbook`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_logbook` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `user` varchar(255) NOT NULL,
  `ipadress` varchar(255) NOT NULL,
  `domain` varchar(255) NOT NULL,
  `entry` tinytext CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=60 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_logbook`
--

LOCK TABLES `tbl_logbook` WRITE;
/*!40000 ALTER TABLE `tbl_logbook` DISABLE KEYS */;
INSERT INTO `tbl_logbook` VALUES (1,'2009-10-07 02:53:37','nagiosadmin','192.168.1.4','','Session timeout reached - Seconds: 1254884017 - User: nagiosadmin'),(2,'2017-01-06 22:55:59','nagiosxi','::1','localhost','File imported - File [overwrite flag]: /usr/local/nagios/etc/import/commands.cfg [1]'),(3,'2017-01-06 22:55:59','nagiosxi','::1','localhost','File imported - File [overwrite flag]: /usr/local/nagios/etc/import/configwizard-bpiwizard.cfg [1]'),(4,'2017-01-06 22:55:59','nagiosxi','::1','localhost','File imported - File [overwrite flag]: /usr/local/nagios/etc/import/configwizard-check_sla.cfg [1]'),(5,'2017-01-06 22:56:00','nagiosxi','::1','localhost','File imported - File [overwrite flag]: /usr/local/nagios/etc/import/configwizard-check_xi_deface.cfg [1]'),(6,'2017-01-06 22:56:00','nagiosxi','::1','localhost','File imported - File [overwrite flag]: /usr/local/nagios/etc/import/configwizard-dnsquery.cfg [1]'),(7,'2017-01-06 22:56:00','nagiosxi','::1','localhost','File imported - File [overwrite flag]: /usr/local/nagios/etc/import/configwizard-domain_expiration.cfg [1]'),(8,'2017-01-06 22:56:00','nagiosxi','::1','localhost','File imported - File [overwrite flag]: /usr/local/nagios/etc/import/configwizard-email-delivery.cfg [1]'),(9,'2017-01-06 22:56:01','nagiosxi','::1','localhost','File imported - File [overwrite flag]: /usr/local/nagios/etc/import/configwizard-exchange.cfg [1]'),(10,'2017-01-06 22:56:03','nagiosxi','::1','localhost','File imported - File [overwrite flag]: /usr/local/nagios/etc/import/configwizard-folder_watch.cfg [1]'),(11,'2017-01-06 22:56:03','nagiosxi','::1','localhost','File imported - File [overwrite flag]: /usr/local/nagios/etc/import/configwizard-ftpserver.cfg [1]'),(12,'2017-01-06 22:56:04','nagiosxi','::1','localhost','File imported - File [overwrite flag]: /usr/local/nagios/etc/import/configwizard-ldapserver.cfg [1]'),(13,'2017-01-06 22:56:04','nagiosxi','::1','localhost','File imported - File [overwrite flag]: /usr/local/nagios/etc/import/configwizard-linux-server.cfg [1]'),(14,'2017-01-06 22:56:04','nagiosxi','::1','localhost','File imported - File [overwrite flag]: /usr/local/nagios/etc/import/configwizard-linuxsnmp.cfg [1]'),(15,'2017-01-06 22:56:04','nagiosxi','::1','localhost','File imported - File [overwrite flag]: /usr/local/nagios/etc/import/configwizard-macosx.cfg [1]'),(16,'2017-01-06 22:56:04','nagiosxi','::1','localhost','File imported - File [overwrite flag]: /usr/local/nagios/etc/import/configwizard-mailserver.cfg [1]'),(17,'2017-01-06 22:56:05','nagiosxi','::1','localhost','File imported - File [overwrite flag]: /usr/local/nagios/etc/import/configwizard-mongodb_database.cfg [1]'),(18,'2017-01-06 22:56:05','nagiosxi','::1','localhost','File imported - File [overwrite flag]: /usr/local/nagios/etc/import/configwizard-mongodb_server.cfg [1]'),(19,'2017-01-06 22:56:05','nagiosxi','::1','localhost','File imported - File [overwrite flag]: /usr/local/nagios/etc/import/configwizard-mountpoint.cfg [1]'),(20,'2017-01-06 22:56:05','nagiosxi','::1','localhost','File imported - File [overwrite flag]: /usr/local/nagios/etc/import/configwizard-mssql_server.cfg [1]'),(21,'2017-01-06 22:56:06','nagiosxi','::1','localhost','File imported - File [overwrite flag]: /usr/local/nagios/etc/import/configwizard-mssqldatabase.cfg [1]'),(22,'2017-01-06 22:56:06','nagiosxi','::1','localhost','File imported - File [overwrite flag]: /usr/local/nagios/etc/import/configwizard-mssqlquery.cfg [1]'),(23,'2017-01-06 22:56:06','nagiosxi','::1','localhost','File imported - File [overwrite flag]: /usr/local/nagios/etc/import/configwizard-mysqlquery.cfg [1]'),(24,'2017-01-06 22:56:06','nagiosxi','::1','localhost','File imported - File [overwrite flag]: /usr/local/nagios/etc/import/configwizard-mysqlserver.cfg [1]'),(25,'2017-01-06 22:56:06','nagiosxi','::1','localhost','File imported - File [overwrite flag]: /usr/local/nagios/etc/import/configwizard-nagioslogserver.cfg [1]'),(26,'2017-01-06 22:56:07','nagiosxi','::1','localhost','File imported - File [overwrite flag]: /usr/local/nagios/etc/import/configwizard-nagiostats.cfg [1]'),(27,'2017-01-06 22:56:07','nagiosxi','::1','localhost','File imported - File [overwrite flag]: /usr/local/nagios/etc/import/configwizard-nagiosxiserver.cfg [1]'),(28,'2017-01-06 22:56:07','nagiosxi','::1','localhost','File imported - File [overwrite flag]: /usr/local/nagios/etc/import/configwizard-ncpa_agent.cfg [1]'),(29,'2017-01-06 22:56:07','nagiosxi','::1','localhost','File imported - File [overwrite flag]: /usr/local/nagios/etc/import/configwizard-nna.cfg [1]'),(30,'2017-01-06 22:56:08','nagiosxi','::1','localhost','File imported - File [overwrite flag]: /usr/local/nagios/etc/import/configwizard-oraclequery.cfg [1]'),(31,'2017-01-06 22:56:08','nagiosxi','::1','localhost','File imported - File [overwrite flag]: /usr/local/nagios/etc/import/configwizard-oracleserverspace.cfg [1]'),(32,'2017-01-06 22:56:08','nagiosxi','::1','localhost','File imported - File [overwrite flag]: /usr/local/nagios/etc/import/configwizard-oracletablespace.cfg [1]'),(33,'2017-01-06 22:56:08','nagiosxi','::1','localhost','File imported - File [overwrite flag]: /usr/local/nagios/etc/import/configwizard-passivecheck.cfg [1]'),(34,'2017-01-06 22:56:09','nagiosxi','::1','localhost','File imported - File [overwrite flag]: /usr/local/nagios/etc/import/configwizard-passiveobject.cfg [1]'),(35,'2017-01-06 22:56:09','nagiosxi','::1','localhost','File imported - File [overwrite flag]: /usr/local/nagios/etc/import/configwizard-postgresdb.cfg [1]'),(36,'2017-01-06 22:56:09','nagiosxi','::1','localhost','File imported - File [overwrite flag]: /usr/local/nagios/etc/import/configwizard-postgresquery.cfg [1]'),(37,'2017-01-06 22:56:09','nagiosxi','::1','localhost','File imported - File [overwrite flag]: /usr/local/nagios/etc/import/configwizard-postgresserver.cfg [1]'),(38,'2017-01-06 22:56:09','nagiosxi','::1','localhost','File imported - File [overwrite flag]: /usr/local/nagios/etc/import/configwizard-radiusserver.cfg [1]'),(39,'2017-01-06 22:56:10','nagiosxi','::1','localhost','File imported - File [overwrite flag]: /usr/local/nagios/etc/import/configwizard-snmptrap.cfg [1]'),(40,'2017-01-06 22:56:10','nagiosxi','::1','localhost','File imported - File [overwrite flag]: /usr/local/nagios/etc/import/configwizard-solaris.cfg [1]'),(41,'2017-01-06 22:56:10','nagiosxi','::1','localhost','File imported - File [overwrite flag]: /usr/local/nagios/etc/import/configwizard-sshproxy.cfg [1]'),(42,'2017-01-06 22:56:10','nagiosxi','::1','localhost','File imported - File [overwrite flag]: /usr/local/nagios/etc/import/configwizard-switch.cfg [1]'),(43,'2017-01-06 22:56:11','nagiosxi','::1','localhost','File imported - File [overwrite flag]: /usr/local/nagios/etc/import/configwizard-tftp.cfg [1]'),(44,'2017-01-06 22:56:11','nagiosxi','::1','localhost','File imported - File [overwrite flag]: /usr/local/nagios/etc/import/configwizard-vmware.cfg [1]'),(45,'2017-01-06 22:56:11','nagiosxi','::1','localhost','File imported - File [overwrite flag]: /usr/local/nagios/etc/import/configwizard-watchguard.cfg [1]'),(46,'2017-01-06 22:56:11','nagiosxi','::1','localhost','File imported - File [overwrite flag]: /usr/local/nagios/etc/import/configwizard-websensor.cfg [1]'),(47,'2017-01-06 22:56:12','nagiosxi','::1','localhost','File imported - File [overwrite flag]: /usr/local/nagios/etc/import/configwizard-windowseventlog.cfg [1]'),(48,'2017-01-06 22:56:12','nagiosxi','::1','localhost','File imported - File [overwrite flag]: /usr/local/nagios/etc/import/configwizard-windowssnmp.cfg [1]'),(49,'2017-01-06 22:56:12','nagiosxi','::1','localhost','File imported - File [overwrite flag]: /usr/local/nagios/etc/import/configwizard-windowswmi.cfg [1]'),(50,'2017-01-06 22:56:12','nagiosxi','::1','localhost','File imported - File [overwrite flag]: /usr/local/nagios/etc/import/contacts.cfg [1]'),(51,'2017-01-06 22:56:12','nagiosxi','::1','localhost','File imported - File [overwrite flag]: /usr/local/nagios/etc/import/localhost.cfg [1]'),(52,'2017-01-06 22:56:13','nagiosxi','::1','localhost','File imported - File [overwrite flag]: /usr/local/nagios/etc/import/nagiosadmin.cfg [1]'),(53,'2017-01-06 22:56:13','nagiosxi','::1','localhost','File imported - File [overwrite flag]: /usr/local/nagios/etc/import/templates.cfg [1]'),(54,'2017-01-06 22:56:13','nagiosxi','::1','localhost','File imported - File [overwrite flag]: /usr/local/nagios/etc/import/timeperiods.cfg [1]'),(55,'2017-01-06 22:56:13','nagiosxi','::1','localhost','File imported - File [overwrite flag]: /usr/local/nagios/etc/import/xi_timeperiod_24x7.cfg [1]'),(56,'2017-01-06 22:56:14','nagiosxi','::1','localhost','File imported - File [overwrite flag]: /usr/local/nagios/etc/import/xicommands.cfg [1]'),(57,'2017-01-06 22:56:14','nagiosxi','::1','localhost','File imported - File [overwrite flag]: /usr/local/nagios/etc/import/xiobjects.cfg [1]'),(58,'2017-01-06 22:56:14','nagiosxi','::1','localhost','File imported - File [overwrite flag]: /usr/local/nagios/etc/import/xitemplates.cfg [1]'),(59,'2017-01-06 22:56:14','nagiosxi','::1','localhost','File imported - File [overwrite flag]: /usr/local/nagios/etc/import/xiwzardtemplates.cfg [1]');
/*!40000 ALTER TABLE `tbl_logbook` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_mainmenu`
--

DROP TABLE IF EXISTS `tbl_mainmenu`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_mainmenu` (
  `id` tinyint(4) NOT NULL AUTO_INCREMENT,
  `order_id` tinyint(4) NOT NULL DEFAULT '0',
  `menu_id` tinyint(4) NOT NULL DEFAULT '0',
  `item` varchar(20) NOT NULL DEFAULT '',
  `link` varchar(50) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_mainmenu`
--

LOCK TABLES `tbl_mainmenu` WRITE;
/*!40000 ALTER TABLE `tbl_mainmenu` DISABLE KEYS */;
INSERT INTO `tbl_mainmenu` VALUES (1,1,2,'NagiosQL Home','admin.php'),(2,2,2,'Monitoring','admin/monitoring.php'),(3,3,2,'Alerting','admin/alarming.php'),(4,4,2,'Commands','admin/commands.php'),(5,5,2,'Advanced','admin/specials.php'),(6,6,2,'Tools','admin/tools.php'),(7,7,2,'Administration','admin/administration.php');
/*!40000 ALTER TABLE `tbl_mainmenu` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_service`
--

DROP TABLE IF EXISTS `tbl_service`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_service` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `config_name` varchar(255) NOT NULL,
  `host_name` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `host_name_tploptions` tinyint(3) unsigned NOT NULL DEFAULT '2',
  `hostgroup_name` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `hostgroup_name_tploptions` tinyint(3) unsigned NOT NULL DEFAULT '2',
  `service_description` varchar(255) NOT NULL,
  `display_name` varchar(255) NOT NULL,
  `servicegroups` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `servicegroups_tploptions` tinyint(3) unsigned NOT NULL DEFAULT '2',
  `use_template` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `use_template_tploptions` tinyint(3) unsigned NOT NULL DEFAULT '2',
  `check_command` text NOT NULL,
  `is_volatile` tinyint(3) unsigned NOT NULL DEFAULT '2',
  `initial_state` varchar(20) NOT NULL,
  `max_check_attempts` int(11) DEFAULT NULL,
  `check_interval` int(11) DEFAULT NULL,
  `retry_interval` int(11) DEFAULT NULL,
  `active_checks_enabled` tinyint(3) unsigned NOT NULL DEFAULT '2',
  `passive_checks_enabled` tinyint(3) unsigned NOT NULL DEFAULT '2',
  `check_period` int(11) NOT NULL DEFAULT '0',
  `parallelize_check` tinyint(3) unsigned NOT NULL DEFAULT '2',
  `obsess_over_service` tinyint(3) unsigned NOT NULL DEFAULT '2',
  `check_freshness` tinyint(3) unsigned NOT NULL DEFAULT '2',
  `freshness_threshold` int(11) DEFAULT NULL,
  `event_handler` int(11) NOT NULL DEFAULT '0',
  `event_handler_enabled` tinyint(3) unsigned NOT NULL DEFAULT '2',
  `low_flap_threshold` int(11) DEFAULT NULL,
  `high_flap_threshold` int(11) DEFAULT NULL,
  `flap_detection_enabled` tinyint(3) unsigned NOT NULL DEFAULT '2',
  `flap_detection_options` varchar(20) NOT NULL,
  `process_perf_data` tinyint(3) unsigned NOT NULL DEFAULT '2',
  `retain_status_information` tinyint(3) unsigned NOT NULL DEFAULT '2',
  `retain_nonstatus_information` tinyint(3) unsigned NOT NULL DEFAULT '2',
  `notification_interval` int(11) DEFAULT NULL,
  `first_notification_delay` int(11) DEFAULT NULL,
  `notification_period` int(11) NOT NULL DEFAULT '0',
  `notification_options` varchar(20) NOT NULL,
  `notifications_enabled` tinyint(3) unsigned NOT NULL DEFAULT '2',
  `contacts` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `contacts_tploptions` tinyint(3) unsigned NOT NULL DEFAULT '2',
  `contact_groups` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `contact_groups_tploptions` tinyint(3) unsigned NOT NULL DEFAULT '2',
  `stalking_options` varchar(20) NOT NULL DEFAULT '',
  `notes` varchar(255) NOT NULL,
  `notes_url` varchar(255) NOT NULL,
  `action_url` varchar(255) NOT NULL,
  `icon_image` varchar(255) NOT NULL,
  `icon_image_alt` varchar(255) NOT NULL,
  `use_variables` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `name` varchar(255) NOT NULL DEFAULT '',
  `active` enum('0','1') NOT NULL DEFAULT '1',
  `last_modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `access_rights` varchar(8) DEFAULT NULL,
  `config_id` tinyint(3) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=15 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_service`
--

LOCK TABLES `tbl_service` WRITE;
/*!40000 ALTER TABLE `tbl_service` DISABLE KEYS */;
INSERT INTO `tbl_service` VALUES (1,'localhost',1,2,0,2,'PING','',0,2,1,2,'17!100.0,20%!500.0,60%',2,'',NULL,NULL,NULL,2,2,0,2,2,2,NULL,0,2,NULL,NULL,2,'',2,2,2,NULL,NULL,0,'',2,0,2,0,2,'','','','','','',0,'','1','2017-01-06 22:56:12',NULL,1),(2,'localhost',1,2,0,2,'Root Partition','',0,2,1,2,'5!20%!10%!/',2,'',NULL,NULL,NULL,2,2,0,2,2,2,NULL,0,2,NULL,NULL,2,'',2,2,2,NULL,NULL,0,'',2,0,2,0,2,'','','','','','',0,'','1','2017-01-06 22:56:12',NULL,1),(3,'localhost',1,2,0,2,'Current Users','',0,2,1,2,'8!20!50',2,'',NULL,NULL,NULL,2,2,0,2,2,2,NULL,0,2,NULL,NULL,2,'',2,2,2,NULL,NULL,0,'',2,0,2,0,2,'','','','','','',0,'','1','2017-01-06 22:56:12',NULL,1),(4,'localhost',1,2,0,2,'Total Processes','',0,2,1,2,'7!400!500!RSZDT',2,'',NULL,NULL,NULL,2,2,0,2,2,2,NULL,0,2,NULL,NULL,2,'',2,2,2,NULL,NULL,0,'',2,0,2,0,2,'','','','','','',0,'','1','2017-01-06 22:56:12',NULL,1),(5,'localhost',1,2,0,2,'Current Load','',0,2,1,2,'6!5.0,4.0,3.0!10.0,6.0,4.0',2,'',NULL,NULL,NULL,2,2,0,2,2,2,NULL,0,2,NULL,NULL,2,'',2,2,2,NULL,NULL,0,'',2,0,2,0,2,'','','','','','',0,'','1','2017-01-06 22:56:12',NULL,1),(6,'localhost',1,2,0,2,'Swap Usage','',0,2,1,2,'9!20!10',2,'',NULL,NULL,NULL,2,2,0,2,2,2,NULL,0,2,NULL,NULL,2,'',2,2,2,NULL,NULL,0,'',2,0,2,0,2,'','','','','','',0,'','1','2017-01-06 22:56:12',NULL,1),(7,'localhost',1,2,0,2,'SSH','',0,2,1,2,'15',2,'',NULL,NULL,NULL,2,2,0,2,2,2,NULL,0,2,NULL,NULL,2,'',2,2,2,NULL,NULL,0,'',2,0,2,0,2,'','','','','','',0,'','1','2017-01-06 22:56:12',NULL,1),(8,'localhost',1,2,0,2,'HTTP','',0,2,1,2,'14',2,'',NULL,NULL,NULL,2,2,0,2,2,2,NULL,0,2,NULL,NULL,2,'',2,2,2,NULL,NULL,0,'',2,0,2,0,2,'','','','','','',0,'','1','2017-01-06 22:56:12',NULL,1),(9,'localhost',1,2,0,2,'Service Status - httpd','',0,2,1,2,'107!httpd!!!!!!',2,'',NULL,NULL,NULL,2,2,0,2,2,2,NULL,0,2,NULL,NULL,2,'',2,2,2,NULL,NULL,0,'',2,0,2,0,2,'','','','','','',0,'','1','2017-01-06 22:56:12',NULL,1),(10,'localhost',1,2,0,2,'Service Status - mysqld','',0,2,1,2,'107!mysqld!!!!!!',2,'',NULL,NULL,NULL,2,2,0,2,2,2,NULL,0,2,NULL,NULL,2,'',2,2,2,NULL,NULL,0,'',2,0,2,0,2,'','','','','','',0,'','1','2017-01-06 22:56:12',NULL,1),(11,'localhost',1,2,0,2,'Service Status - crond','',0,2,1,2,'107!crond!!!!!!',2,'',NULL,NULL,NULL,2,2,0,2,2,2,NULL,0,2,NULL,NULL,2,'',2,2,2,NULL,NULL,0,'',2,0,2,0,2,'','','','','','',0,'','1','2017-01-06 22:56:12',NULL,1),(12,'localhost',1,2,0,2,'Service Status - ntpd','',0,2,1,2,'107!ntpd!!!!!!',2,'',NULL,NULL,NULL,2,2,0,2,2,2,NULL,0,2,NULL,NULL,2,'',2,2,2,NULL,NULL,0,'',2,0,2,0,2,'','','','','','',0,'','1','2017-01-06 22:56:12',NULL,1),(13,'localhost',1,2,0,2,'Service Status - npcd','',0,2,1,2,'107!npcd!!!!!!',2,'',NULL,NULL,NULL,2,2,0,2,2,2,NULL,0,2,NULL,NULL,2,'',2,2,2,NULL,NULL,0,'',2,0,2,0,2,'','','','','','',0,'','1','2017-01-06 22:56:12',NULL,1);
/*!40000 ALTER TABLE `tbl_service` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_servicedependency`
--

DROP TABLE IF EXISTS `tbl_servicedependency`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_servicedependency` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `config_name` varchar(255) NOT NULL,
  `dependent_host_name` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `dependent_hostgroup_name` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `dependent_service_description` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `host_name` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `hostgroup_name` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `service_description` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `inherits_parent` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `execution_failure_criteria` varchar(20) DEFAULT '',
  `notification_failure_criteria` varchar(20) DEFAULT '',
  `dependency_period` int(11) NOT NULL DEFAULT '0',
  `active` enum('0','1') NOT NULL DEFAULT '1',
  `last_modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `access_rights` varchar(8) DEFAULT NULL,
  `config_id` tinyint(3) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `config_name` (`config_name`,`config_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_servicedependency`
--

LOCK TABLES `tbl_servicedependency` WRITE;
/*!40000 ALTER TABLE `tbl_servicedependency` DISABLE KEYS */;
/*!40000 ALTER TABLE `tbl_servicedependency` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_serviceescalation`
--

DROP TABLE IF EXISTS `tbl_serviceescalation`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_serviceescalation` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `config_name` varchar(255) NOT NULL,
  `host_name` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `hostgroup_name` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `service_description` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `contacts` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `contact_groups` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `first_notification` int(11) DEFAULT NULL,
  `last_notification` int(11) DEFAULT NULL,
  `notification_interval` int(11) DEFAULT NULL,
  `escalation_period` int(11) NOT NULL DEFAULT '0',
  `escalation_options` varchar(20) DEFAULT '',
  `active` enum('0','1') NOT NULL DEFAULT '1',
  `last_modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `access_rights` varchar(8) DEFAULT NULL,
  `config_id` tinyint(3) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `config_name` (`config_name`,`config_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_serviceescalation`
--

LOCK TABLES `tbl_serviceescalation` WRITE;
/*!40000 ALTER TABLE `tbl_serviceescalation` DISABLE KEYS */;
/*!40000 ALTER TABLE `tbl_serviceescalation` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_serviceextinfo`
--

DROP TABLE IF EXISTS `tbl_serviceextinfo`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_serviceextinfo` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `host_name` int(11) DEFAULT NULL,
  `service_description` int(11) NOT NULL,
  `notes` varchar(255) NOT NULL,
  `notes_url` varchar(255) NOT NULL,
  `action_url` varchar(255) NOT NULL,
  `statistic_url` varchar(255) NOT NULL,
  `icon_image` varchar(255) NOT NULL,
  `icon_image_alt` varchar(255) NOT NULL,
  `active` enum('0','1') NOT NULL DEFAULT '1',
  `last_modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `access_rights` varchar(8) DEFAULT NULL,
  `config_id` tinyint(3) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `config_name` (`host_name`,`service_description`,`config_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_serviceextinfo`
--

LOCK TABLES `tbl_serviceextinfo` WRITE;
/*!40000 ALTER TABLE `tbl_serviceextinfo` DISABLE KEYS */;
/*!40000 ALTER TABLE `tbl_serviceextinfo` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_servicegroup`
--

DROP TABLE IF EXISTS `tbl_servicegroup`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_servicegroup` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `servicegroup_name` varchar(255) NOT NULL,
  `alias` varchar(255) NOT NULL,
  `members` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `servicegroup_members` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `notes` varchar(255) DEFAULT NULL,
  `notes_url` varchar(255) DEFAULT NULL,
  `action_url` varchar(255) DEFAULT NULL,
  `active` enum('0','1') NOT NULL DEFAULT '1',
  `last_modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `access_rights` varchar(8) DEFAULT NULL,
  `config_id` tinyint(3) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `config_name` (`servicegroup_name`,`config_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_servicegroup`
--

LOCK TABLES `tbl_servicegroup` WRITE;
/*!40000 ALTER TABLE `tbl_servicegroup` DISABLE KEYS */;
/*!40000 ALTER TABLE `tbl_servicegroup` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_servicetemplate`
--

DROP TABLE IF EXISTS `tbl_servicetemplate`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_servicetemplate` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `template_name` varchar(255) NOT NULL,
  `host_name` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `host_name_tploptions` tinyint(3) unsigned NOT NULL DEFAULT '2',
  `hostgroup_name` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `hostgroup_name_tploptions` tinyint(3) unsigned NOT NULL DEFAULT '2',
  `service_description` varchar(255) NOT NULL,
  `display_name` varchar(255) NOT NULL,
  `servicegroups` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `servicegroups_tploptions` tinyint(3) unsigned NOT NULL DEFAULT '2',
  `use_template` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `use_template_tploptions` tinyint(3) unsigned NOT NULL DEFAULT '2',
  `check_command` text NOT NULL,
  `is_volatile` tinyint(3) unsigned NOT NULL DEFAULT '2',
  `initial_state` varchar(20) NOT NULL,
  `max_check_attempts` int(11) DEFAULT NULL,
  `check_interval` int(11) DEFAULT NULL,
  `retry_interval` int(11) DEFAULT NULL,
  `active_checks_enabled` tinyint(3) unsigned NOT NULL DEFAULT '2',
  `passive_checks_enabled` tinyint(3) unsigned NOT NULL DEFAULT '2',
  `check_period` int(11) NOT NULL DEFAULT '0',
  `parallelize_check` tinyint(3) unsigned NOT NULL DEFAULT '2',
  `obsess_over_service` tinyint(3) unsigned NOT NULL DEFAULT '2',
  `check_freshness` tinyint(3) unsigned NOT NULL DEFAULT '2',
  `freshness_threshold` int(11) DEFAULT NULL,
  `event_handler` int(11) NOT NULL DEFAULT '0',
  `event_handler_enabled` tinyint(3) unsigned NOT NULL DEFAULT '2',
  `low_flap_threshold` int(11) DEFAULT NULL,
  `high_flap_threshold` int(11) DEFAULT NULL,
  `flap_detection_enabled` tinyint(3) unsigned NOT NULL DEFAULT '2',
  `flap_detection_options` varchar(20) NOT NULL,
  `process_perf_data` tinyint(3) unsigned NOT NULL DEFAULT '2',
  `retain_status_information` tinyint(3) unsigned NOT NULL DEFAULT '2',
  `retain_nonstatus_information` tinyint(3) unsigned NOT NULL DEFAULT '2',
  `notification_interval` int(11) DEFAULT NULL,
  `first_notification_delay` int(11) DEFAULT NULL,
  `notification_period` int(11) NOT NULL DEFAULT '0',
  `notification_options` varchar(20) NOT NULL,
  `notifications_enabled` tinyint(3) unsigned NOT NULL DEFAULT '2',
  `contacts` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `contacts_tploptions` tinyint(3) unsigned NOT NULL DEFAULT '2',
  `contact_groups` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `contact_groups_tploptions` tinyint(3) unsigned NOT NULL DEFAULT '2',
  `stalking_options` varchar(20) NOT NULL DEFAULT '',
  `notes` varchar(255) NOT NULL,
  `notes_url` varchar(255) NOT NULL,
  `action_url` varchar(255) NOT NULL,
  `icon_image` varchar(255) NOT NULL,
  `icon_image_alt` varchar(255) NOT NULL,
  `use_variables` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `active` enum('0','1') NOT NULL DEFAULT '1',
  `last_modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `access_rights` varchar(8) DEFAULT NULL,
  `config_id` tinyint(3) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `config_name` (`template_name`,`config_id`)
) ENGINE=MyISAM AUTO_INCREMENT=84 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_servicetemplate`
--

LOCK TABLES `tbl_servicetemplate` WRITE;
/*!40000 ALTER TABLE `tbl_servicetemplate` DISABLE KEYS */;
INSERT INTO `tbl_servicetemplate` VALUES (1,'xiwizard_bpi_service',0,2,0,2,'','',0,2,1,2,'',2,'',NULL,NULL,NULL,2,2,0,2,2,2,NULL,0,2,NULL,NULL,2,'',2,2,2,NULL,NULL,0,'',2,0,2,0,2,'','','','','','',0,'1','2017-01-06 22:55:59',NULL,1),(2,'xiwizard_generic_service',0,2,0,2,'','',0,2,0,2,'113',0,'',5,5,1,1,1,7,1,1,0,NULL,0,1,NULL,NULL,1,'',1,1,1,60,NULL,7,'',1,0,2,0,2,'','','','','','',0,'1','2017-01-06 22:56:14',NULL,1),(3,'xiwizard_check_sla',0,2,0,2,'','',0,2,1,2,'42',2,'',NULL,NULL,NULL,2,2,0,2,2,2,NULL,0,2,NULL,NULL,2,'',2,2,2,NULL,NULL,0,'',2,0,2,0,2,'','','','','','',0,'1','2017-01-06 22:55:59',NULL,1),(4,'xiwizard_check_deface_service',0,2,0,2,'','',0,2,1,2,'43',2,'',NULL,60,NULL,2,2,0,2,2,2,NULL,0,2,NULL,NULL,2,'',2,2,2,NULL,NULL,0,'',2,0,2,0,2,'','','','','','',0,'1','2017-01-06 22:56:00',NULL,1),(5,'xiwizard_dnsquery_service',0,2,0,2,'DNS Lookup','',0,2,1,2,'46',2,'',NULL,NULL,NULL,2,2,0,2,2,2,NULL,0,2,NULL,NULL,2,'',2,2,2,NULL,NULL,0,'',2,0,2,0,2,'','','','','','',0,'1','2017-01-06 22:56:00',NULL,1),(6,'xiwizard_domain_expiration_service_v2',0,2,0,2,'','',0,2,1,2,'47',2,'',NULL,1440,NULL,2,2,0,2,2,2,NULL,0,2,NULL,NULL,2,'',2,2,2,NULL,NULL,0,'',2,0,2,0,2,'','','','','','',0,'1','2017-01-06 22:56:00',NULL,1),(7,'xiwizard_exchange_ping_service',0,2,0,2,'','',0,2,1,2,'49!3000.0!80%!5000.0!100%',2,'',NULL,NULL,NULL,2,2,0,2,2,2,NULL,0,2,NULL,NULL,2,'',2,2,2,NULL,NULL,0,'',2,0,2,0,2,'','','','','','',0,'1','2017-01-06 22:56:01',NULL,1),(8,'xiwizard_exchange_service',0,2,0,2,'','',0,2,1,2,'',2,'',NULL,NULL,NULL,2,2,0,2,2,2,NULL,0,2,NULL,NULL,2,'',2,2,2,NULL,NULL,0,'',2,0,2,0,2,'','','','','','',0,'1','2017-01-06 22:56:01',NULL,1),(9,'xiwizard_check_file_service',0,2,0,2,'','',0,2,1,2,'51',2,'',NULL,NULL,NULL,2,2,0,2,2,2,NULL,0,2,NULL,NULL,2,'',2,2,2,NULL,NULL,0,'',2,0,2,0,2,'','','','','','',0,'1','2017-01-06 22:56:03',NULL,1),(10,'xiwizard_check_file_sa_service',0,2,0,2,'','',0,2,1,2,'52',2,'',NULL,NULL,NULL,2,2,0,2,2,2,NULL,0,2,NULL,NULL,2,'',2,2,2,NULL,NULL,0,'',2,0,2,0,2,'','','','','','',0,'1','2017-01-06 22:56:03',NULL,1),(11,'xiwizard_ftpserver_transfer_service',0,2,0,2,'FTP Transfer','',0,2,1,2,'53',2,'',NULL,NULL,NULL,2,2,0,2,2,2,NULL,0,2,NULL,NULL,2,'',2,2,2,NULL,NULL,0,'',2,0,2,0,2,'','','','','ftpserver.png','',0,'1','2017-01-06 22:56:03',NULL,1),(12,'xiwizard_ftpserver_server_service',0,2,0,2,'FTP Server','',0,2,1,2,'54',2,'',NULL,NULL,NULL,2,2,0,2,2,2,NULL,0,2,NULL,NULL,2,'',2,2,2,NULL,NULL,0,'',2,0,2,0,2,'','','','','','',0,'1','2017-01-06 22:56:03',NULL,1),(13,'xiwizard_ldapserver_ldap_service',0,2,0,2,'LDAP','',0,2,1,2,'55',2,'',NULL,NULL,NULL,2,2,0,2,2,2,NULL,0,2,NULL,NULL,2,'',2,2,2,NULL,NULL,0,'',2,0,2,0,2,'','','','','ldapserver.png','',0,'1','2017-01-06 22:56:04',NULL,1),(14,'xiwizard_linuxsnmp_load',0,2,0,2,'','',0,2,1,2,'56',2,'',NULL,NULL,NULL,2,2,0,2,2,2,NULL,0,2,NULL,NULL,2,'',2,2,2,NULL,NULL,0,'',2,0,2,0,2,'','','','','','',0,'1','2017-01-06 22:56:04',NULL,1),(15,'xiwizard_linuxsnmp_process',0,2,0,2,'','',0,2,1,2,'57',2,'',NULL,NULL,NULL,2,2,0,2,2,2,NULL,0,2,NULL,NULL,2,'',2,2,2,NULL,NULL,0,'',2,0,2,0,2,'','','','','','',0,'1','2017-01-06 22:56:04',NULL,1),(16,'xiwizard_linuxsnmp_storage',0,2,0,2,'','',0,2,1,2,'58',2,'',NULL,NULL,NULL,2,2,0,2,2,2,NULL,0,2,NULL,NULL,2,'',2,2,2,NULL,NULL,0,'',2,0,2,0,2,'','','','','','',0,'1','2017-01-06 22:56:04',NULL,1),(17,'xiwizard_mailserver_ping_service',0,2,0,2,'','',0,2,1,2,'49!3000.0!80%!5000.0!100%',2,'',NULL,NULL,NULL,2,2,0,2,2,2,NULL,0,2,NULL,NULL,2,'',2,2,2,NULL,NULL,0,'',2,0,2,0,2,'','','','','','',0,'1','2017-01-06 22:56:04',NULL,1),(18,'xiwizard_mailserver_service',0,2,0,2,'','',0,2,1,2,'',2,'',NULL,NULL,NULL,2,2,0,2,2,2,NULL,0,2,NULL,NULL,2,'',2,2,2,NULL,NULL,0,'',2,0,2,0,2,'','','','','','',0,'1','2017-01-06 22:56:04',NULL,1),(19,'xiwizard_mongodbdatabase_service',0,2,0,2,'','',0,2,1,2,'60',2,'',NULL,NULL,NULL,2,2,0,2,2,2,NULL,0,2,NULL,NULL,2,'',2,2,2,NULL,NULL,0,'',2,0,2,0,2,'','','','','','',0,'1','2017-01-06 22:56:05',NULL,1),(20,'xiwizard_mongodbserver_service',0,2,0,2,'','',0,2,1,2,'61',2,'',NULL,NULL,NULL,2,2,0,2,2,2,NULL,0,2,NULL,NULL,2,'',2,2,2,NULL,NULL,0,'',2,0,2,0,2,'','','','','','',0,'1','2017-01-06 22:56:05',NULL,1),(21,'xiwizard_mountpoint_check',0,2,0,2,'','',0,2,1,2,'62',2,'',NULL,NULL,NULL,2,2,0,2,2,2,NULL,0,2,NULL,NULL,2,'',2,2,2,NULL,NULL,0,'',2,0,2,0,2,'','','','','','',0,'1','2017-01-06 22:56:05',NULL,1),(22,'xiwizard_mountpoint_check_table',0,2,0,2,'','',0,2,1,2,'62',2,'',NULL,NULL,NULL,2,2,0,2,2,2,NULL,0,2,NULL,NULL,2,'',2,2,2,NULL,NULL,0,'',2,0,2,0,2,'','','','','','',0,'1','2017-01-06 22:56:05',NULL,1),(23,'xiwizard_mssqlserver_service',0,2,0,2,'','',0,2,1,2,'63',2,'',NULL,NULL,NULL,2,2,0,2,2,2,NULL,0,2,NULL,NULL,2,'',2,2,2,NULL,NULL,0,'',2,0,2,0,2,'','','','','','',0,'1','2017-01-06 22:56:05',NULL,1),(24,'xiwizard_mssqldatabase_service',0,2,0,2,'','',0,2,1,2,'64',2,'',NULL,NULL,NULL,2,2,0,2,2,2,NULL,0,2,NULL,NULL,2,'',2,2,2,NULL,NULL,0,'',2,0,2,0,2,'','','','','','',0,'1','2017-01-06 22:56:06',NULL,1),(25,'xiwizard_mssqlquery_service',0,2,0,2,'','',0,2,1,2,'65',2,'',NULL,NULL,NULL,2,2,0,2,2,2,NULL,0,2,NULL,NULL,2,'',2,2,2,NULL,NULL,0,'',2,0,2,0,2,'','','','','','',0,'1','2017-01-06 22:56:06',NULL,1),(26,'xiwizard_mysqlquery_service',0,2,0,2,'','',0,2,1,2,'66',2,'',NULL,NULL,NULL,2,2,0,2,2,2,NULL,0,2,NULL,NULL,2,'',2,2,2,NULL,NULL,0,'',2,0,2,0,2,'','','','','','',0,'1','2017-01-06 22:56:06',NULL,1),(27,'xiwizard_mysqlserver_service',0,2,0,2,'','',0,2,1,2,'66',2,'',NULL,NULL,NULL,2,2,0,2,2,2,NULL,0,2,NULL,NULL,2,'',2,2,2,NULL,NULL,0,'',2,0,2,0,2,'','','','','','',0,'1','2017-01-06 22:56:06',NULL,1),(28,'xiwizard_nagioslogserver_service',0,2,0,2,'Nagios Log Server Query','',0,2,1,2,'',2,'',NULL,NULL,NULL,2,2,0,2,2,2,NULL,0,2,NULL,NULL,2,'',2,2,2,NULL,NULL,0,'',2,0,2,0,2,'','','','','','',0,'1','2017-01-06 22:56:06',NULL,1),(29,'xiwizard_nagiostats_service',0,2,0,2,'','',0,2,1,2,'69',2,'',NULL,NULL,NULL,2,2,0,2,2,2,NULL,0,2,NULL,NULL,2,'',2,2,2,NULL,NULL,0,'',2,0,2,0,2,'','','','','','',0,'1','2017-01-06 22:56:07',NULL,1),(30,'xiwizard_nagiosxiserver_service',0,2,0,2,'','',0,2,1,2,'70',2,'',NULL,NULL,NULL,2,2,0,2,2,2,NULL,0,2,NULL,NULL,2,'',2,2,2,NULL,NULL,0,'',2,0,2,0,2,'','','','','','',0,'1','2017-01-06 22:56:07',NULL,1),(31,'xiwizard_nagiosxiserver_http_service',0,2,0,2,'','',0,2,1,2,'',2,'',NULL,NULL,NULL,2,2,0,2,2,2,NULL,0,2,NULL,NULL,2,'',2,2,2,NULL,NULL,0,'',2,0,2,0,2,'','','','','','',0,'1','2017-01-06 22:56:07',NULL,1),(32,'xiwizard_website_http_service',0,2,0,2,'','',0,2,1,2,'114',2,'',NULL,NULL,NULL,2,2,0,2,2,2,NULL,0,2,NULL,NULL,2,'',2,2,2,NULL,NULL,0,'',2,0,2,0,2,'','','','','','',0,'1','2017-01-06 22:56:14',NULL,1),(33,'xiwizard_nagiosxiserver_ping_service',0,2,0,2,'','',0,2,1,2,'',2,'',NULL,NULL,NULL,2,2,0,2,2,2,NULL,0,2,NULL,NULL,2,'',2,2,2,NULL,NULL,0,'',2,0,2,0,2,'','','','','','',0,'1','2017-01-06 22:56:07',NULL,1),(34,'xiwizard_website_ping_service',0,2,0,2,'','',0,2,1,2,'49!3000.0!80%!5000.0!100%',2,'',NULL,NULL,NULL,2,2,0,2,2,2,NULL,0,2,NULL,NULL,2,'',2,2,2,NULL,NULL,0,'',2,0,2,0,2,'','','','','','',0,'1','2017-01-06 22:56:14',NULL,1),(35,'xiwizard_ncpa_service',0,2,0,2,'','',0,2,1,2,'71',2,'',NULL,NULL,NULL,2,2,0,2,2,2,NULL,0,2,NULL,NULL,2,'',2,2,2,NULL,NULL,0,'',2,0,2,0,2,'','','','','','',0,'1','2017-01-06 22:56:07',NULL,1),(36,'xiwizard_nna_service',0,2,0,2,'','',0,2,1,2,'72',2,'',NULL,NULL,NULL,2,2,0,2,2,2,NULL,0,2,NULL,NULL,2,'',2,2,2,NULL,NULL,0,'',2,0,2,0,2,'','','','','','',0,'1','2017-01-06 22:56:07',NULL,1),(37,'xiwizard_oraclequery_service',0,2,0,2,'','',0,2,1,2,'73',2,'',NULL,NULL,NULL,2,2,0,2,2,2,NULL,0,2,NULL,NULL,2,'',2,2,2,NULL,NULL,0,'',2,0,2,0,2,'','','','','','',0,'1','2017-01-06 22:56:08',NULL,1),(38,'xiwizard_oracleserverspace_service',0,2,0,2,'','',0,2,1,2,'74',2,'',NULL,NULL,NULL,2,2,0,2,2,2,NULL,0,2,NULL,NULL,2,'',2,2,2,NULL,NULL,0,'',2,0,2,0,2,'','','','','','',0,'1','2017-01-06 22:56:08',NULL,1),(39,'xiwizard_oracletablespace_service',0,2,0,2,'','',0,2,1,2,'75',2,'',NULL,NULL,NULL,2,2,0,2,2,2,NULL,0,2,NULL,NULL,2,'',2,2,2,NULL,NULL,0,'',2,0,2,0,2,'','','','','','',0,'1','2017-01-06 22:56:08',NULL,1),(40,'xiwizard_passive_service',0,2,0,2,'Passive Service','',0,2,1,2,'33!0!\"No data received yet.\"',0,'o',1,NULL,NULL,0,1,0,2,2,2,NULL,0,2,NULL,NULL,0,'',2,2,2,NULL,NULL,0,'',2,0,2,0,2,'o,w,u,c','','','','','',0,'1','2017-01-06 22:56:09',NULL,1),(41,'xiwizard_postgresdb_service',0,2,0,2,'','',0,2,1,2,'76',2,'',NULL,NULL,NULL,2,2,0,2,2,2,NULL,0,2,NULL,NULL,2,'',2,2,2,NULL,NULL,0,'',2,0,2,0,2,'','','','','','',0,'1','2017-01-06 22:56:09',NULL,1),(42,'xiwizard_postgresquery_service',0,2,0,2,'','',0,2,1,2,'78',2,'',NULL,NULL,NULL,2,2,0,2,2,2,NULL,0,2,NULL,NULL,2,'',2,2,2,NULL,NULL,0,'',2,0,2,0,2,'','','','','','',0,'1','2017-01-06 22:56:09',NULL,1),(43,'xiwizard_postgresserver_service',0,2,0,2,'','',0,2,1,2,'76',2,'',NULL,NULL,NULL,2,2,0,2,2,2,NULL,0,2,NULL,NULL,2,'',2,2,2,NULL,NULL,0,'',2,0,2,0,2,'','','','','','',0,'1','2017-01-06 22:56:09',NULL,1),(44,'xiwizard_radiusserver_radius_service',0,2,0,2,'Radius Server','',0,2,1,2,'82',2,'',NULL,NULL,NULL,2,2,0,2,2,2,NULL,0,2,NULL,NULL,2,'',2,2,2,NULL,NULL,0,'',2,0,2,0,2,'','','','','radiusserver.png','',0,'1','2017-01-06 22:56:09',NULL,1),(45,'xiwizard_snmptrap_service',0,2,0,2,'SNMP Traps','',0,2,1,2,'33!0!\"TRAP RESET\"',1,'o',1,NULL,NULL,0,1,0,2,2,2,NULL,0,2,NULL,NULL,0,'',2,2,2,NULL,NULL,0,'',2,0,2,0,2,'o,w,u,c','','','','snmptrap.png','',0,'1','2017-01-06 22:56:10',NULL,1),(46,'xiwizard_tftp_service_connect',0,2,0,2,'','',0,2,1,2,'87',2,'',NULL,NULL,NULL,2,2,0,2,2,2,NULL,0,2,NULL,NULL,2,'',2,2,2,NULL,NULL,0,'',2,0,2,0,2,'','','','','','',0,'1','2017-01-06 22:56:11',NULL,1),(47,'xiwizard_tftp_service_get',0,2,0,2,'','',0,2,1,2,'88',2,'',NULL,NULL,NULL,2,2,0,2,2,2,NULL,0,2,NULL,NULL,2,'',2,2,2,NULL,NULL,0,'',2,0,2,0,2,'','','','','','',0,'1','2017-01-06 22:56:11',NULL,1),(48,'xiwizard_watchguard_service',0,2,0,2,'','',0,2,1,2,'91',2,'',NULL,NULL,NULL,2,2,0,2,2,2,NULL,0,2,NULL,NULL,2,'',2,2,2,NULL,NULL,0,'',2,0,2,0,2,'','','','','','',0,'1','2017-01-06 22:56:11',NULL,1),(49,'xiwizard_websensor_ping_service',0,2,0,2,'','',0,2,1,2,'49!3000.0!80%!5000.0!100%',2,'',NULL,NULL,NULL,2,2,0,2,2,2,NULL,0,2,NULL,NULL,2,'',2,2,2,NULL,NULL,0,'',2,0,2,0,2,'','','','','','',0,'1','2017-01-06 22:56:11',NULL,1),(50,'xiwizard_websensor_service',0,2,0,2,'','',0,2,1,2,'92',2,'',NULL,NULL,NULL,2,2,0,2,2,2,NULL,0,2,NULL,NULL,2,'',2,2,2,NULL,NULL,0,'',2,0,2,0,2,'','','','','','',0,'1','2017-01-06 22:56:11',NULL,1),(51,'xiwizard_windowseventlog_service',0,2,0,2,'Event Log Service','',0,2,1,2,'33!0!\"No data received yet.\"',1,'o',1,NULL,NULL,0,1,0,2,2,2,NULL,0,2,NULL,NULL,0,'',2,2,2,NULL,NULL,0,'',2,0,2,0,2,'o,w,u,c','','','','','',0,'1','2017-01-06 22:56:12',NULL,1),(52,'xiwizard_windowssnmp_load',0,2,0,2,'','',0,2,1,2,'101',2,'',NULL,NULL,NULL,2,2,0,2,2,2,NULL,0,2,NULL,NULL,2,'',2,2,2,NULL,NULL,0,'',2,0,2,0,2,'','','','','','',0,'1','2017-01-06 22:56:12',NULL,1),(53,'xiwizard_windowssnmp_service',0,2,0,2,'','',0,2,1,2,'102',2,'',NULL,NULL,NULL,2,2,0,2,2,2,NULL,0,2,NULL,NULL,2,'',2,2,2,NULL,NULL,0,'',2,0,2,0,2,'','','','','','',0,'1','2017-01-06 22:56:12',NULL,1),(54,'xiwizard_windowssnmp_process',0,2,0,2,'','',0,2,1,2,'103',2,'',NULL,NULL,NULL,2,2,0,2,2,2,NULL,0,2,NULL,NULL,2,'',2,2,2,NULL,NULL,0,'',2,0,2,0,2,'','','','','','',0,'1','2017-01-06 22:56:12',NULL,1),(55,'xiwizard_windowssnmp_storage',0,2,0,2,'','',0,2,1,2,'104',2,'',NULL,NULL,NULL,2,2,0,2,2,2,NULL,0,2,NULL,NULL,2,'',2,2,2,NULL,NULL,0,'',2,0,2,0,2,'','','','','','',0,'1','2017-01-06 22:56:12',NULL,1),(56,'xiwizard_windowswmi_service',0,2,0,2,'','',0,2,1,2,'106',2,'',NULL,NULL,NULL,2,2,0,2,2,2,NULL,0,2,NULL,NULL,2,'',2,2,2,NULL,NULL,0,'',2,0,2,0,2,'','','','','','',0,'1','2017-01-06 22:56:12',NULL,1),(57,'local-service',0,2,0,2,'','',0,2,1,2,'',2,'',4,5,1,2,2,0,2,2,2,NULL,0,2,NULL,NULL,2,'',2,2,2,NULL,NULL,0,'',2,0,2,0,2,'','','','','','',0,'1','2017-01-06 22:56:13',NULL,1),(58,'generic-service',0,2,0,2,'','',0,2,0,2,'',0,'',3,10,2,1,1,2,1,1,0,NULL,0,1,NULL,NULL,1,'',1,1,1,60,NULL,2,'w,u,c,r',1,0,2,1,2,'','','','','','',0,'1','2017-01-06 22:56:13',NULL,1),(59,'xiwizard_website_http_content_service',0,2,0,2,'','',0,2,1,2,'116!\"Content\"',2,'',NULL,NULL,NULL,2,2,0,2,2,2,NULL,0,2,NULL,NULL,2,'',2,2,2,NULL,NULL,0,'',2,0,2,0,2,'','','','','','',0,'1','2017-01-06 22:56:14',NULL,1),(60,'xiwizard_website_http_cert_service',0,2,0,2,'','',0,2,1,2,'115!30',2,'',NULL,NULL,NULL,2,2,0,2,2,2,NULL,0,2,NULL,NULL,2,'',2,2,2,NULL,NULL,0,'',2,0,2,0,2,'','','','','','',0,'1','2017-01-06 22:56:14',NULL,1),(61,'xiwizard_website_dns_service',0,2,0,2,'','',0,2,1,2,'46',2,'',NULL,NULL,NULL,2,2,0,2,2,2,NULL,0,2,NULL,NULL,2,'',2,2,2,NULL,NULL,0,'',2,0,2,0,2,'','','','','','',0,'1','2017-01-06 22:56:14',NULL,1),(62,'xiwizard_website_dnsip_service',0,2,0,2,'','',0,2,1,2,'46',2,'',NULL,NULL,NULL,2,2,0,2,2,2,NULL,0,2,NULL,NULL,2,'',2,2,2,NULL,NULL,0,'',2,0,2,0,2,'','','','','','',0,'1','2017-01-06 22:56:14',NULL,1),(63,'xiwizard_genericnetdevice_ping_service',0,2,0,2,'','',0,2,1,2,'49!3000.0!80%!5000.0!100%',2,'',NULL,NULL,NULL,2,2,0,2,2,2,NULL,0,2,NULL,NULL,2,'',2,2,2,NULL,NULL,0,'',2,0,2,0,2,'','','','','','',0,'1','2017-01-06 22:56:14',NULL,1),(64,'xiwizard_printer_ping_service',0,2,0,2,'','',0,2,1,2,'49!3000.0!80%!5000.0!100%',2,'',NULL,NULL,NULL,2,2,0,2,2,2,NULL,0,2,NULL,NULL,2,'',2,2,2,NULL,NULL,0,'',2,0,2,0,2,'','','','','','',0,'1','2017-01-06 22:56:14',NULL,1),(65,'xiwizard_printer_hpjd_service',0,2,0,2,'','',0,2,1,2,'117!public',2,'',NULL,NULL,NULL,2,2,0,2,2,2,NULL,0,2,NULL,NULL,2,'',2,2,2,NULL,NULL,0,'',2,0,2,0,2,'','','','','','',0,'1','2017-01-06 22:56:14',NULL,1),(66,'xiwizard_windowsdesktop_ping_service',0,2,0,2,'','',0,2,1,2,'49!3000.0!80%!5000.0!100%',2,'',NULL,NULL,NULL,2,2,0,2,2,2,NULL,0,2,NULL,NULL,2,'',2,2,2,NULL,NULL,0,'',2,0,2,0,2,'','','','','','',0,'1','2017-01-06 22:56:14',NULL,1),(67,'xiwizard_windowsdesktop_nsclient_service',0,2,0,2,'','',0,2,1,2,'118!password!CLIENTVERSION',2,'',NULL,NULL,NULL,2,2,0,2,2,2,NULL,0,2,NULL,NULL,2,'',2,2,2,NULL,NULL,0,'',2,0,2,0,2,'','','','','','',0,'1','2017-01-06 22:56:14',NULL,1),(68,'xiwizard_windowsserver_ping_service',0,2,0,2,'','',0,2,1,2,'49!3000.0!80%!5000.0!100%',2,'',NULL,NULL,NULL,2,2,0,2,2,2,NULL,0,2,NULL,NULL,2,'',2,2,2,NULL,NULL,0,'',2,0,2,0,2,'','','','','','',0,'1','2017-01-06 22:56:14',NULL,1),(69,'xiwizard_windowsserver_nsclient_service',0,2,0,2,'','',0,2,1,2,'118!password!CLIENTVERSION',2,'',NULL,NULL,NULL,2,2,0,2,2,2,NULL,0,2,NULL,NULL,2,'',2,2,2,NULL,NULL,0,'',2,0,2,0,2,'','','','','','',0,'1','2017-01-06 22:56:14',NULL,1),(70,'xiwizard_switch_ping_service',0,2,0,2,'','',0,2,1,2,'49!3000.0!80%!5000.0!100%',2,'',NULL,NULL,NULL,2,2,0,2,2,2,NULL,0,2,NULL,NULL,2,'',2,2,2,NULL,NULL,0,'',2,0,2,0,2,'','','','','','',0,'1','2017-01-06 22:56:14',NULL,1),(71,'xiwizard_switch_port_status_service',0,2,0,2,'','',0,2,1,2,'85!public!1',2,'',NULL,NULL,NULL,2,2,0,2,2,2,NULL,0,2,NULL,NULL,2,'',2,2,2,NULL,NULL,0,'',2,0,2,0,2,'','','','','','',0,'1','2017-01-06 22:56:14',NULL,1),(72,'xiwizard_switch_port_bandwidth_service',0,2,0,2,'','',0,2,1,2,'128!127.0.0.1!1!80,90!85,90!K',2,'',NULL,NULL,NULL,2,2,0,2,2,2,NULL,0,2,NULL,NULL,2,'',2,2,2,NULL,NULL,0,'',2,0,2,0,2,'','','','','','',0,'1','2017-01-06 22:56:14',NULL,1),(73,'xiwizard_webtransaction_webinject_service',0,2,0,2,'','',0,2,1,2,'120!none',2,'',NULL,NULL,NULL,2,2,0,2,2,2,NULL,0,2,NULL,NULL,2,'',2,2,2,NULL,NULL,0,'',2,0,2,0,2,'','','','','whirl.png','',0,'1','2017-01-06 22:56:14',NULL,1),(74,'xiwizard_ftp_service',0,2,0,2,'','',0,2,1,2,'54',2,'',NULL,NULL,NULL,2,2,0,2,2,2,NULL,0,2,NULL,NULL,2,'',2,2,2,NULL,NULL,0,'',2,0,2,0,2,'','','','','','',0,'1','2017-01-06 22:56:14',NULL,1),(75,'xiwizard_imap_service',0,2,0,2,'','',0,2,1,2,'121',2,'',NULL,NULL,NULL,2,2,0,2,2,2,NULL,0,2,NULL,NULL,2,'',2,2,2,NULL,NULL,0,'',2,0,2,0,2,'','','','','','',0,'1','2017-01-06 22:56:14',NULL,1),(76,'xiwizard_pop_service',0,2,0,2,'','',0,2,1,2,'122',2,'',NULL,NULL,NULL,2,2,0,2,2,2,NULL,0,2,NULL,NULL,2,'',2,2,2,NULL,NULL,0,'',2,0,2,0,2,'','','','','','',0,'1','2017-01-06 22:56:14',NULL,1),(77,'xiwizard_smtp_service',0,2,0,2,'','',0,2,1,2,'123',2,'',NULL,NULL,NULL,2,2,0,2,2,2,NULL,0,2,NULL,NULL,2,'',2,2,2,NULL,NULL,0,'',2,0,2,0,2,'','','','','','',0,'1','2017-01-06 22:56:14',NULL,1),(78,'xiwizard_ssh_service',0,2,0,2,'','',0,2,1,2,'124',2,'',NULL,NULL,NULL,2,2,0,2,2,2,NULL,0,2,NULL,NULL,2,'',2,2,2,NULL,NULL,0,'',2,0,2,0,2,'','','','','','',0,'1','2017-01-06 22:56:14',NULL,1),(79,'xiwizard_tcp_service',0,2,0,2,'','',0,2,1,2,'125',2,'',NULL,NULL,NULL,2,2,0,2,2,2,NULL,0,2,NULL,NULL,2,'',2,2,2,NULL,NULL,0,'',2,0,2,0,2,'','','','','','',0,'1','2017-01-06 22:56:14',NULL,1),(80,'xiwizard_udp_service',0,2,0,2,'','',0,2,1,2,'126',2,'',NULL,NULL,NULL,2,2,0,2,2,2,NULL,0,2,NULL,NULL,2,'',2,2,2,NULL,NULL,0,'',2,0,2,0,2,'','','','','','',0,'1','2017-01-06 22:56:14',NULL,1),(81,'xiwizard_snmp_service',0,2,0,2,'','',0,2,1,2,'127',2,'',NULL,NULL,NULL,2,2,0,2,2,2,NULL,0,2,NULL,NULL,2,'',2,2,2,NULL,NULL,0,'',2,0,2,0,2,'','','','','','',0,'1','2017-01-06 22:56:14',NULL,1),(82,'xiwizard_linuxserver_ping_service',0,2,0,2,'','',0,2,1,2,'49!3000.0!80%!5000.0!100%',2,'',NULL,NULL,NULL,2,2,0,2,2,2,NULL,0,2,NULL,NULL,2,'',2,2,2,NULL,NULL,0,'',2,0,2,0,2,'','','','','','',0,'1','2017-01-06 22:56:14',NULL,1),(83,'xiwizard_nrpe_service',0,2,0,2,'','',0,2,1,2,'25',2,'',NULL,NULL,NULL,2,2,0,2,2,2,NULL,0,2,NULL,NULL,2,'',2,2,2,NULL,NULL,0,'',2,0,2,0,2,'','','','','','',0,'1','2017-01-06 22:56:14',NULL,1);
/*!40000 ALTER TABLE `tbl_servicetemplate` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_session`
--

DROP TABLE IF EXISTS `tbl_session`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_session` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `session_id` varchar(120) NOT NULL,
  `ip` varchar(64) NOT NULL,
  `type` varchar(255) NOT NULL,
  `obj_id` int(10) unsigned NOT NULL,
  `started` varchar(20) NOT NULL,
  `last_updated` varchar(20) NOT NULL,
  `active` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_session`
--

LOCK TABLES `tbl_session` WRITE;
/*!40000 ALTER TABLE `tbl_session` DISABLE KEYS */;
/*!40000 ALTER TABLE `tbl_session` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_session_locks`
--

DROP TABLE IF EXISTS `tbl_session_locks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_session_locks` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `sid` int(10) unsigned NOT NULL,
  `type` varchar(255) NOT NULL,
  `obj_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_session_locks`
--

LOCK TABLES `tbl_session_locks` WRITE;
/*!40000 ALTER TABLE `tbl_session_locks` DISABLE KEYS */;
/*!40000 ALTER TABLE `tbl_session_locks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_settings`
--

DROP TABLE IF EXISTS `tbl_settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_settings` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `category` varchar(20) NOT NULL,
  `name` varchar(30) NOT NULL,
  `value` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=MyISAM AUTO_INCREMENT=13 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_settings`
--

LOCK TABLES `tbl_settings` WRITE;
/*!40000 ALTER TABLE `tbl_settings` DISABLE KEYS */;
INSERT INTO `tbl_settings` VALUES (1,'db','version','3.0.3'),(2,'path','root','/nagiosql/'),(3,'path','physical','/var/www/html/nagiosql/'),(4,'path','protocol','http'),(5,'path','tempdir','/tmp'),(6,'data','locale','en_GB'),(7,'data','encoding','utf-8'),(8,'security','logofftime','3600'),(9,'security','wsauth','0'),(10,'common','pagelines','15'),(11,'common','seldisable','1'),(12,'db','magic_quotes','0');
/*!40000 ALTER TABLE `tbl_settings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_submenu`
--

DROP TABLE IF EXISTS `tbl_submenu`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_submenu` (
  `id` tinyint(4) NOT NULL AUTO_INCREMENT,
  `id_main` tinyint(4) NOT NULL DEFAULT '0',
  `order_id` tinyint(4) NOT NULL DEFAULT '0',
  `item` varchar(20) NOT NULL DEFAULT '',
  `link` varchar(50) NOT NULL DEFAULT '',
  `access_rights` varchar(8) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=31 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_submenu`
--

LOCK TABLES `tbl_submenu` WRITE;
/*!40000 ALTER TABLE `tbl_submenu` DISABLE KEYS */;
INSERT INTO `tbl_submenu` VALUES (1,2,1,'Hosts','admin/hosts.php','00000000'),(2,3,3,'Time Periods','admin/timeperiods.php','00000000'),(26,2,5,'Host Templates','admin/hosttemplates.php','00000000'),(4,4,1,'Command Definitions','admin/checkcommands.php','00000000'),(5,3,1,'Contacts','admin/contacts.php','00000000'),(6,3,2,'Contact Groups','admin/contactgroups.php','00000000'),(7,2,2,'Services','admin/services.php','00000000'),(8,2,3,'Host Groups','admin/hostgroups.php','00000000'),(9,2,4,'Service Groups','admin/servicegroups.php','00000000'),(10,5,4,'Service Dependencies','admin/servicedependencies.php','00000000'),(11,5,5,'Service Escalations','admin/serviceescalations.php','00000000'),(12,5,1,'Host Dependencies','admin/hostdependencies.php','00000000'),(13,5,2,'Host Escalations','admin/hostescalations.php','00000000'),(14,5,3,'Extended Host Info','admin/hostextinfo.php','00000000'),(15,5,6,'Extended Service Inf','admin/serviceextinfo.php','00000000'),(16,6,1,'Import Core Config','admin/import.php','00000000'),(17,6,2,'Delete Backup Files','admin/delbackup.php','00000000'),(18,7,2,'NagiosQL User Admin','admin/user.php','00000000'),(19,6,5,'Nagios Core Control','admin/verify.php','00000000'),(20,7,1,'NagiosQL Password','admin/password.php','00000000'),(21,7,5,'Logbook','admin/logbook.php','00000000'),(22,6,3,'Nagios Core Main Con','admin/nagioscfg.php','00000000'),(23,6,4,'Nagios Core UI Confi','admin/cgicfg.php','00000000'),(24,7,3,'Menu Access','admin/menuaccess.php','00000000'),(25,7,4,'Domains','admin/domain.php','00000000'),(27,2,6,'Service Templates','admin/servicetemplates.php','00000000'),(28,3,4,'Contact Templates','admin/contacttemplates.php','00000000'),(29,7,6,'Settings','admin/settings.php','00000000'),(30,7,7,'Help Editor','admin/helpedit.php','00000000');
/*!40000 ALTER TABLE `tbl_submenu` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_timedefinition`
--

DROP TABLE IF EXISTS `tbl_timedefinition`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_timedefinition` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `tipId` int(10) unsigned NOT NULL,
  `definition` varchar(255) NOT NULL,
  `range` text NOT NULL,
  `last_modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=47 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_timedefinition`
--

LOCK TABLES `tbl_timedefinition` WRITE;
/*!40000 ALTER TABLE `tbl_timedefinition` DISABLE KEYS */;
INSERT INTO `tbl_timedefinition` VALUES (37,1,'tuesday','00:00-24:00','2017-01-06 22:56:13'),(36,1,'monday','00:00-24:00','2017-01-06 22:56:13'),(35,1,'wednesday','00:00-24:00','2017-01-06 22:56:13'),(34,1,'thursday','00:00-24:00','2017-01-06 22:56:13'),(33,1,'saturday','00:00-24:00','2017-01-06 22:56:13'),(8,2,'sunday','00:00-24:00','2017-01-06 22:56:13'),(9,2,'monday','00:00-24:00','2017-01-06 22:56:13'),(10,2,'tuesday','00:00-24:00','2017-01-06 22:56:13'),(11,2,'wednesday','00:00-24:00','2017-01-06 22:56:13'),(12,2,'thursday','00:00-24:00','2017-01-06 22:56:13'),(13,2,'friday','00:00-24:00','2017-01-06 22:56:13'),(14,2,'saturday','00:00-24:00','2017-01-06 22:56:13'),(15,3,'monday','09:00-17:00','2017-01-06 22:56:13'),(16,3,'tuesday','09:00-17:00','2017-01-06 22:56:13'),(17,3,'wednesday','09:00-17:00','2017-01-06 22:56:13'),(18,3,'thursday','09:00-17:00','2017-01-06 22:56:13'),(19,3,'friday','09:00-17:00','2017-01-06 22:56:13'),(20,5,'january 1','00:00-00:00','2017-01-06 22:56:13'),(21,5,'monday 1 september','00:00-00:00','2017-01-06 22:56:13'),(22,5,'july 4','00:00-00:00','2017-01-06 22:56:13'),(23,5,'thursday -1 november','00:00-00:00','2017-01-06 22:56:13'),(24,5,'december 25','00:00-00:00','2017-01-06 22:56:13'),(25,6,'use','us-holidays','2017-01-06 22:56:13'),(26,6,'sunday','00:00-24:00','2017-01-06 22:56:13'),(27,6,'monday','00:00-24:00','2017-01-06 22:56:13'),(28,6,'tuesday','00:00-24:00','2017-01-06 22:56:13'),(29,6,'wednesday','00:00-24:00','2017-01-06 22:56:13'),(30,6,'thursday','00:00-24:00','2017-01-06 22:56:13'),(31,6,'friday','00:00-24:00','2017-01-06 22:56:13'),(32,6,'saturday','00:00-24:00','2017-01-06 22:56:13'),(38,1,'sunday','00:00-24:00','2017-01-06 22:56:13'),(39,1,'friday','00:00-24:00','2017-01-06 22:56:13'),(40,7,'sunday','00:00-24:00','2017-01-06 22:56:13'),(41,7,'monday','00:00-24:00','2017-01-06 22:56:13'),(42,7,'tuesday','00:00-24:00','2017-01-06 22:56:13'),(43,7,'wednesday','00:00-24:00','2017-01-06 22:56:13'),(44,7,'thursday','00:00-24:00','2017-01-06 22:56:13'),(45,7,'friday','00:00-24:00','2017-01-06 22:56:13'),(46,7,'saturday','00:00-24:00','2017-01-06 22:56:13');
/*!40000 ALTER TABLE `tbl_timedefinition` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_timeperiod`
--

DROP TABLE IF EXISTS `tbl_timeperiod`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_timeperiod` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `timeperiod_name` varchar(255) NOT NULL DEFAULT '',
  `alias` varchar(255) NOT NULL DEFAULT '',
  `exclude` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `name` varchar(255) NOT NULL,
  `active` enum('0','1') NOT NULL DEFAULT '1',
  `last_modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `access_rights` varchar(8) DEFAULT NULL,
  `config_id` tinyint(3) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `timeperiod_name` (`timeperiod_name`,`config_id`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8 PACK_KEYS=0;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_timeperiod`
--

LOCK TABLES `tbl_timeperiod` WRITE;
/*!40000 ALTER TABLE `tbl_timeperiod` DISABLE KEYS */;
INSERT INTO `tbl_timeperiod` VALUES (1,'nagiosadmin_notification_times','Notification Times for nagiosadmin',0,'','1','2017-01-06 22:56:13',NULL,1),(2,'24x7','24 Hours A Day, 7 Days A Week',0,'','1','2017-01-06 22:56:13',NULL,1),(3,'workhours','Normal Work Hours',0,'','1','2017-01-06 22:56:13',NULL,1),(4,'none','No Time Is A Good Time',0,'','1','2017-01-06 22:56:13',NULL,1),(5,'us-holidays','U.S. Holidays',0,'us-holidays','1','2017-01-06 22:56:13',NULL,1),(6,'24x7_sans_holidays','24x7 Sans Holidays',0,'','1','2017-01-06 22:56:13',NULL,1),(7,'xi_timeperiod_24x7','24x7',0,'xi_timeperiod_24x7','1','2017-01-06 22:56:13',NULL,1);
/*!40000 ALTER TABLE `tbl_timeperiod` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_user`
--

DROP TABLE IF EXISTS `tbl_user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_user` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(255) NOT NULL,
  `alias` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `access_rights` varchar(8) DEFAULT NULL,
  `wsauth` enum('0','1') NOT NULL DEFAULT '0',
  `active` enum('0','1') NOT NULL DEFAULT '0',
  `nodelete` enum('0','1') NOT NULL DEFAULT '0',
  `last_login` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `last_modified` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `locale` varchar(6) DEFAULT 'en_EN',
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_user`
--

LOCK TABLES `tbl_user` WRITE;
/*!40000 ALTER TABLE `tbl_user` DISABLE KEYS */;
INSERT INTO `tbl_user` VALUES (1,'nagiosadmin','Administrator','40be4e59b9a2a2b5dffb918c0e86b3d7','11111111','0','1','1','2009-10-22 18:53:49','2009-10-22 18:53:49','en_EN'),(4,'nagiosxi','Nagios XI Subsystem Account','63083d139baf364a4391dae71377e9c1','11111111','0','1','0','2017-01-06 22:56:15','2009-10-22 18:55:08','en_EN');
/*!40000 ALTER TABLE `tbl_user` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_variabledefinition`
--

DROP TABLE IF EXISTS `tbl_variabledefinition`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_variabledefinition` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `value` varchar(255) NOT NULL,
  `last_modified` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_variabledefinition`
--

LOCK TABLES `tbl_variabledefinition` WRITE;
/*!40000 ALTER TABLE `tbl_variabledefinition` DISABLE KEYS */;
/*!40000 ALTER TABLE `tbl_variabledefinition` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2017-01-06 16:57:53
