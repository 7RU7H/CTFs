### Gruntfile Config - Detect (gruntfile-exposure) found on moodle.schooled.htb

----
**Details**: **gruntfile-exposure** matched at moodle.schooled.htb

**Protocol**: HTTP

**Full URL**: http://moodle.schooled.htb/moodle/Gruntfile.js

**Timestamp**: Sun Apr 28 17:02:20 +0100 BST 2024

**Template Information**

| Key | Value |
| --- | --- |
| Name | Gruntfile Config - Detect |
| Authors | sbani |
| Tags | config, exposure |
| Severity | info |
| Description | Gruntfile configuration information was detected. |
| CVSS-Metrics | [CVSS:3.1/AV:N/AC:L/PR:N/UI:N/S:U/C:N/I:N/A:N](https://www.first.org/cvss/calculator/3.1#CVSS:3.1/AV:N/AC:L/PR:N/UI:N/S:U/C:N/I:N/A:N) |
| CWE-ID | [CWE-200](https://cwe.mitre.org/data/definitions/200.html) |
| CVSS-Score | 0.00 |

**Request**
```http
GET /moodle/Gruntfile.js HTTP/1.1
Host: moodle.schooled.htb
User-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_14_5) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/9.1.2 Safari/605.1.15
Connection: close
Accept: */*
Accept-Language: en
Accept-Encoding: gzip


```

**Response**
```http
HTTP/1.1 200 OK
Connection: close
Content-Length: 33641
Accept-Ranges: bytes
Content-Type: application/javascript
Date: Sun, 28 Apr 2024 16:02:20 GMT
Etag: "8369-5a7fb078f1240"
Last-Modified: Sat, 13 Jun 2020 18:04:49 GMT
Server: Apache/2.4.46 (FreeBSD) PHP/7.4.15

// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.
/* jshint node: true, browser: false */
/* eslint-env node */

/**
 * @copyright  2014 Andrew Nicols
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/* eslint-env node */

/**
 * Calculate the cwd, taking into consideration the `root` option (for Windows).
 *
 * @param {Object} grunt
 * @returns {String} The current directory as best we can determine
 */
const getCwd = grunt => {
    const fs = require('fs');
    const path = require('path');

    let cwd = fs.realpathSync(process.env.PWD || process.cwd());

    // Windows users can't run grunt in a subdirectory, so allow them to set
    // the root by passing --root=path/to/dir.
    if (grunt.option('root')) {
        const root = grunt.option('root');
        if (grunt.file.exists(__dirname, root)) {
            cwd = fs.realpathSync(path.join(__dirname, root));
            grunt.log.ok('Setting root to ' + cwd);
        } else {
            grunt.fail.fatal('Setting root to ' + root + ' failed - path does not exist');
        }
    }

    return cwd;
};

/**
 * Register any stylelint tasks.
 *
 * @param {Object} grunt
 * @param {Array} files
 * @param {String} fullRunDir
 */
const registerStyleLintTasks = (grunt, files, fullRunDir) => {
    const getCssConfigForFiles = files => {
        return {
            stylelint: {
                css: {
                    // Use a fully-qualified path.
                    src: files,
                    options: {
                        configOverrides: {
                            rules: {
                                // These rules have to be disabled in .stylelintrc for scss compat.
                                "at-rule-no-unknown": true,
                            }
                        }
                    }
                },
            },
        };
    };

    const getScssConfigForFiles = files => {
        return {
            stylelint: {
                scss: {
                    options: {syntax: 'scss'},
                    src: files,
                },
            },
        };
    };

    let hasCss = true;
    let hasScss = true;

    if (files) {
        // Specific files were passed. Just set them up.
        grunt.config.merge(getCssConfigForFiles(files));
        grunt.config.merge(getScssConfigForFiles(files));
    } else {
        // The stylelint system does not handle the case where there was no file to lint.
        // Check whether there are any files to lint in the current directory.
        const glob = require('glob');

        const scssSrc = [];
        glob.sync(`${fullRunDir}/**/*.scss`).forEach(path => scssSrc.push(path));

        if (scssSrc.length) {
            grunt.config.merge(getScssConfigForFiles(scssSrc));
        } else {
            hasScss = false;
        }

        const cssSrc = [];
        glob.sync(`${fullRunDir}/**/*.css`).forEach(path => cssSrc.push(path));

        if (cssSrc.length) {
            grunt.config.merge(getCssConfigForFiles(cssSrc));
        } else {
            hasCss = false;
        }
    }

    const scssTasks = ['sass'];
    if (hasScss) {
        scssTasks.unshift('stylelint:scss');
    }
    grunt.registerTask('scss', scssTasks);

    const cssTasks = [];
    if (hasCss) {
        cssTasks.push('stylelint:css');
    }
    grunt.registerTask('rawcss', cssTasks);

    grunt.registerTask('css', ['scss', 'rawcss']);
};

/**
 * Grunt configuration.
 *
 * @param {Object} grunt
 */
module.exports = function(grunt) {
    const path = require('path');
    const tasks = {};
    const async = require('async');
    const DOMParser = require('xmldom').DOMParser;
    const xpath = require('xpath');
    const semver = require('semver');
    const watchman = require('fb-watchman');
    const watchmanClient = new watchman.Client();
    const fs = require('fs');
    const ComponentList = require(path.resolve('GruntfileComponents.js'));
    const sass = require('node-sass');

    // Verify the node version is new enough.
    var expected = semver.validRange(grunt.file.readJSON('package.json').engines.node);
    var actual = semver.valid(process.version);
    if (!semver.satisfies(actual, ex.... Truncated ....
```

References: 
- https://gruntjs.com/sample-gruntfile

**CURL command**
```sh
curl -X 'GET' -H 'Accept: */*' -H 'Accept-Language: en' -H 'User-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_14_5) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/9.1.2 Safari/605.1.15' 'http://moodle.schooled.htb/moodle/Gruntfile.js'
```

----

Generated by [Nuclei v3.2.2](https://github.com/projectdiscovery/nuclei)