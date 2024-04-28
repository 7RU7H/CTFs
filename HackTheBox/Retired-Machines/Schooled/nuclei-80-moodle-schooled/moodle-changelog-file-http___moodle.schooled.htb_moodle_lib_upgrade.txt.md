### Moodle Changelog File (moodle-changelog-file) found on moodle.schooled.htb

----
**Details**: **moodle-changelog-file** matched at moodle.schooled.htb

**Protocol**: HTTP

**Full URL**: http://moodle.schooled.htb/moodle/lib/upgrade.txt

**Timestamp**: Sun Apr 28 17:02:29 +0100 BST 2024

**Template Information**

| Key | Value |
| --- | --- |
| Name | Moodle Changelog File |
| Authors | oppsec |
| Tags | miscellaneous, misc, moodle |
| Severity | info |
| Description | Moodle has a file which describes API changes in core libraries and APIs, and can be used to discover Moodle version. |

**Request**
```http
GET /moodle/lib/upgrade.txt HTTP/1.1
Host: moodle.schooled.htb
User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36 Edg/122.0.0.0 OS/10.0.22631
Connection: close
Accept: */*
Accept-Language: en
Accept-Encoding: gzip


```

**Response**
```http
HTTP/1.1 200 OK
Connection: close
Content-Length: 105451
Accept-Ranges: bytes
Content-Type: text/plain
Date: Sun, 28 Apr 2024 16:02:29 GMT
Etag: "19beb-5a7fb078f1240"
Last-Modified: Sat, 13 Jun 2020 18:04:49 GMT
Server: Apache/2.4.46 (FreeBSD) PHP/7.4.15

This files describes API changes in core libraries and APIs,
information provided here is intended especially for developers.

=== 3.9 ===
* Following function has been deprecated, please use \core\task\manager::run_from_cli().
    - cron_run_single_task()
* Following class has been deprecated, please use \core\task\manager.
    - \tool_task\run_from_cli
* Following CLI scripts has been deprecated:
  - admin/tool/task/cli/schedule_task.php please use admin/cli/scheduled_task.php
  - admin/tool/task/cli/adhoc_task.php please use admin/cli/adhoc_task.php
* Old Safe Exam Browser quiz access rule (quizaccess_safebrowser) replaced by new Safe Exam Browser access rule (quizaccess_seb).
  Experimental setting enablesafebrowserintegration was deleted.
* New CFPropertyList library has been added to Moodle core in /lib/plist.
* behat_data_generators::the_following_exist() has been removed, please use
  behat_data_generators::the_following_entities_exist() instead. See MDL-67691 for more info.
* admin/tool/task/cli/adhoc_task.php now observers the concurrency limits.
  If you want to get the previous (unlimited) behavior, use the --ignorelimits switch).
* Removed the following deprecated functions:
  - question_add_tops
  - question_is_only_toplevel_category_in_context
* format_float() now accepts a special value (-1) as the $decimalpoints parameter
  which means auto-detecting number of decimal points.
* plagiarism_save_form_elements() has been deprecated. Please use {plugin name}_coursemodule_edit_post_actions() instead.
* plagiarism_get_form_elements_module() has been deprecated. Please use {plugin name}_coursemodule_standard_elements() instead.
* Changed default sessiontimeout to 8 hours to cover most normal working days
* Plugins can now explicitly declare supported and incompatible Moodle versions in version.php
  - $plugin->supported = [37,39];
    supported takes an array of ascending numbers, that correspond to a range of branch numbers of supported versions, inclusive.
    Moodle versions that are outside of this range will produce a message notifying at install time, but will allow for installation.
  - $plugin->incompatible = 36;
    incompatible takes a single int corresponding to the first incompatible branch. Any Moodle versions including and
    above this will be prevented from installing the plugin, and a message will be given when attempting installation.
* Added the <component>_bulk_user_actions() callback which returns a list of custom action_links objects
* Add 'required' admin flag for mod forms allows elements to be toggled between being required or not in admin settings.
  - In mod settings, along with lock, advanced flags, the required flag can now be set with $setting->set_required_flag_options().
    The name of the admin setting must be exactly the same as the mod_form element.
  - Currently supported by:
    - mod_assign
    - mod_quiz
* Added a native MySQL / MariaDB lock implementation
* The database drivers (moodle_database and subclasses) don't need to implement get_columns() anymore.
  They have to implement fetch_columns instead.
* Added function cleanup_after_drop to the database_manager class to take care of all the cleanups that need to be done after a table is dropped.
* The 'xxxx_check_password_policy' callback now only fires if $CFG->passwordpolicy is true
* grade_item::update_final_grade() can now take an optional parameter to set the grade->timemodified. If not present the current time will carry on being used.
* lib/outputrequirementslib::get_jsrev now is public, it can be called from other classes.
* H5P libraries have been moved from /lib/h5p to h5p/h5plib as an h5plib plugintype.
* mdn-polyfills has been renamed to polyfills. The reason there is no polyfill from the MDN is
  because there is no example polyfills on the MDN for this functionality.
* AJAX pages can be called without requiring a session lock if they set READ_ONLY_SESSION to true, eg.
  define('READ_ONLY_SESSION', true); Note - this also requires $CFG->enable_read_only_sessions to be set to true.
* External functions can be called without requiring a session lock if they define 'readonlysession' => true in
  db/services.php. Note - this also requires $CFG->enable_read_only_sessions to be set to true.
* database_manager::check_database_schema() now checks for missing and extra indexes.
* Implement a more direct xsendfile_file() method for an alternative_file_system_class
* A new `dynamic` table interface has been defined, which allows any `flexible_table` to be converted into a table which
  is updatable via ajax calls. See MDL-68495 and `\core_table\dynamic` for further information.
* The core/notification module has been updated to use AMD modals for its confirmation and alert dialogues.
  The confirmation dialogue no longer has a configurable "No" button as per s.... Truncated ....
```


**CURL command**
```sh
curl -X 'GET' -H 'Accept: */*' -H 'Accept-Language: en' -H 'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36 Edg/122.0.0.0 OS/10.0.22631' 'http://moodle.schooled.htb/moodle/lib/upgrade.txt'
```

----

Generated by [Nuclei v3.2.2](https://github.com/projectdiscovery/nuclei)