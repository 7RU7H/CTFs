#!/usr/bin/env python

import os
import optparse
import warnings
import logging
import rrddatastore
import forecast
import re
import datetime
import numpy as np

warnings.filterwarnings('ignore')

try:
    import json
except ImportError:
    import simplejson as json


logging.basicConfig(level=logging.DEBUG,
                    format='%(asctime)s %(levelname)-8s %(message)s',
                    datefmt='%a, %d %b %Y %H:%M:%S',
                    filename='/usr/local/nagiosxi/var/components/capacityplanning.log',
                    filemode='a')


def parse_period(period):
    try:
        period = int(period)
    except TypeError:
        period = 1
    except ValueError:
        WEEKS_IN_YEAR = 52
        WEEKS_IN_MONTH = 4

        period = str(period)
        result = re.search(r'.*([0-9]+)(.*)', period)
        if result:
            prefix = int(result.group(1))
            unit = result.group(2).strip().lower()
            if 'year' in unit:
                period = prefix * WEEKS_IN_YEAR
            elif 'month' in unit:
                period = prefix * WEEKS_IN_MONTH
            else:
                period = prefix
        else:
            period = 1
    return period


def parse_values(dtype, parser, values, message):
    if values is not None:
        try:
            return [dtype(x) for x in values.split(',')]
        except Exception as e:
            parser.error(message)
    else:
        return None




def parse_args():
    """Parse the input args of the script. """

    parser = optparse.OptionParser()



    datagroup = optparse.OptionGroup(parser, "Data Selection Options",
        "Use these to specify the host/service/track to report on.")

    datagroup.add_option("-D", "--dir", default="/usr/local/nagios/share/perfdata",
        help="Base directory to search for data files (default: %default).")
    datagroup.add_option("-H", "--host",
        help="Host name to run report for. *Required*")
    datagroup.add_option("-S", "--service",
        help="Service name to run report for. *Required*")
    datagroup.add_option("-T", "--track",
        help="Track name to run report for. *Required*")

    parser.add_option_group(datagroup)



    manipulation = optparse.OptionGroup(parser, "Extrapolation Options",
        "Use these to specify how the data will be extrapolated.")

    manipulation.add_option("-M", "--method", default="Holt-Winters",
        choices=['Holt-Winters', 'Linear Fit', 'Quadratic Fit', 'Cubic Fit'],
        help="Extrapolation method: Holt-Winters, Linear Fit, Quadratic Fit or Cubic Fit (default: %default).")
    manipulation.add_option("-E", "--end", default="now",
        help="End time of observed data to extrapolate from (default: %default).")
    manipulation.add_option("-P", "--period", default=1,
        help="Period of data to use as a basis for future data (default: %default).")
    manipulation.add_option("-s", "--steps", type="int", default=1,
        help="Number of periods to extrapolate out (default: %default).")

    parser.add_option_group(manipulation)



    reporting = optparse.OptionGroup(parser, "Reporting Options",
        "Use these to specify information to be reported.")

    reporting.add_option("--no-highcharts",
        dest="highcharts", action="store_false", default=True,
        help="Suppress outpout of Highcharts JSON data.")

    reporting.add_option("-j", "--json-indent", type="int", metavar="INDENT",
        help="Specify a JSON indent level for human-readable output.")
    reporting.add_option("-k", "--json-sort", action="store_true", default=False,
        help="Sort JSON keys for human-readable output.")
    reporting.add_option("-p", "--json-prettify", type="int", metavar="INDENT",
        help="Prettify JSON output, sorting the keys and using the specified indent level. This is equivalent to specifying: '--json-indent=INDENT --json-sort'.")

    reporting.add_option("-d", "--dates",
        help="Comma-delimited list of unix timestamps to get extrapolated values for. Output a list of date-string / value pairs.")
    reporting.add_option("-t", "--times",
        help="Comma-delimited list of unix timestamps to get extrapolated values for. Output a list of timestamp / value pairs.")

    reporting.add_option("--gt",
        help="Comma-delimited list of values to return times the extrapolation becomes greater than.")
    reporting.add_option("--ge",
        help="Comma-delimited list of values to return times the extrapolation becomes greater than or equal to.")
    reporting.add_option("--lt",
        help="Comma-delimited list of values to return times the extrapolation becomes less than.")
    reporting.add_option("--le",
        help="Comma-delimited list of values to return times the extrapolation becomes less than or equal to.")
    reporting.add_option("--eq",
        help="Comma-delimited list of values to return times the extrapolation becomes equal to.")

    reporting.add_option("-w", "--warn", action="store_true", default=False,
        help="Return the first time the extrapolation exceeds its track's warning value.")
    reporting.add_option("-c", "--crit", action="store_true", default=False,
        help="Return the first time the extrapolation exceeds its track's critical value.")
    reporting.add_option('--lt-threshold', action="store_true", default=False,
        help="Treat the track's warning and critical values as minimum (rather than maximum) acceptable values.")

    parser.add_option_group(reporting)

    # Process the argv/argc command line.
    options, args = parser.parse_args()


    if not os.path.isdir(options.dir + '/.'):
        parser.error("'%s' is not an accessible directory." % options.dir)
    if not options.host:
        parser.error('Host is a required argument.')
    if not options.service:
        parser.error('Service is a required argument.')
    if not options.track:
        parser.error('Track is a required argument.')

    # Extract a sane period value.
    options.period = parse_period(options.period)

    #logging.info("dir: %s, host: %s, service: %s, track: %s, method: %s, steps: %d, period: %d." % (options.dir, options.host, options.service, options.track, options.method, options.steps, options.period))

    if options.json_prettify is not None:
        options.json_indent = options.json_prettify
        options.json_sort = True

    # Convert comma-separated timestamps to lists.
    options.dates = parse_values(int, parser, options.dates,
        "Dates values must be unix timestamps.")
    options.times = parse_values(int, parser, options.times,
        "Times values must be unix timestamps.")

    # Convert comma-separated real (floating point) values to lists.
    options.gt = parse_values(float, parser, options.gt,
        "'Greater than' values must be numbers.")
    options.ge = parse_values(float, parser, options.ge,
        "'Greater than or equal' values must be numbers.")
    options.lt = parse_values(float, parser, options.lt,
        "'Less than' values must be numbers.")
    options.le = parse_values(float, parser, options.le,
        "'Less than or equal' values must be numbers.")
    options.eq = parse_values(float, parser, options.eq,
        "'Equal to' values must be numbers.")


    #logging.info(options)
    return options

def main():
    o = parse_args()

    datastore = rrddatastore.Datastore(o.dir, o.host, o.service, o.track)
    datastore.parse_rrd_file(o.period * 2, o.end)
    datastore.parse_xml_file()
    observed = datastore.dataset
    observed.set_period(o.period)


    if o.method == 'Holt-Winters':
        extrapolated, f, residues = forecast.holt_winters(observed, o.steps)
    elif o.method == 'Linear Fit':
        extrapolated, f, residues = forecast.linear_fit(observed, o.steps)
    elif o.method == 'Quadratic Fit':
        extrapolated, f, residues = forecast.quadratic_fit(observed, o.steps)
    elif o.method == 'Cubic Fit':
        extrapolated, f, residues = forecast.cubic_fit(observed, o.steps)



    # Our output information starts with the forecast and some statistics.
    info = forecast.make_projections(observed, extrapolated, f, residues)

    info['dmean'] = observed.get_mean()
    info['dmax']  = observed.get_max()
    info['dmin']  = observed.get_min()
    info['emean'] = extrapolated.get_mean()
    info['emax']  = extrapolated.get_max()
    info['emin']  = extrapolated.get_min()
    info['unit']      = observed.unit
    info['integrity'] = observed.get_integrity()

    info['t_start'] = observed.start
    info['t_step'] = observed.step
    info['t_stop'] = extrapolated.end



    # Add Highcharts data to the output if requested.
    if o.highcharts:
        info['highcharts'] = [observed.get_highcharts_dataset('Observed'),
                              extrapolated.get_highcharts_dataset('Predicted')]

        fit_params = (observed, observed.start, extrapolated.end, observed.step)

        fit = forecast.get_fitted_dataset(*(fit_params + (f,)))
        fit.label = 'Fit'
        fit_chart = fit.get_highcharts_dataset(fit.label, 'line')
        fit_chart['color'] = '#005656'
        fit_chart['zIndex'] = 1,

        info['highcharts'] += [fit_chart]



    # Process and add dates and times values to the output if present.
    if o.dates is not None:
        info['dates'] = [{'date': datetime.datetime.fromtimestamp(x).strftime('%c'),
                          'value': extrapolated.get_value_on_date(x)
                         } for x in o.dates]
    if o.times is not None:
        info['times'] = [{'time': x,
                          'value': extrapolated.get_value_on_date(x)
                         } for x in o.times]



    # Finally process any requested comparisons against the extrapolation.
    # We'll use a helper function to build a list of points that match values
    # passed to the list options.
    def process_comparison(method, values, key):
        if values is not None:
            info[key] = [time_value_dict(method, x) for x in values]

    # Another helper function that calls a method with a value to get a point
    # p, and then zips that point into a {'time':p[0], 'value':p[1]} dict.
    def time_value_dict(method, value):
        ret = dict(zip(('time', 'value'), method(value)))

        # This will be json-encoded, so convert numpy types to regular python types
        for key in ret.keys():
            if type(ret[key]) in np.typeDict.values():
                ret[key] = np.asscalar(ret[key])
        return ret

    process_comparison(extrapolated.get_point_gt, o.gt, 'gt')
    process_comparison(extrapolated.get_point_ge, o.ge, 'ge')
    process_comparison(extrapolated.get_point_lt, o.lt, 'lt')
    process_comparison(extrapolated.get_point_le, o.le, 'le')
    process_comparison(extrapolated.get_point_eq, o.eq, 'eq')

    # Our warning and critical values are scalar and not output as arrays.

    threshold_function = extrapolated.get_point_ge
    if o.lt_threshold:
        threshold_function = extrapolated.get_point_le


    if o.warn:
        if extrapolated.warn == None:
            extrapolated.warn = 0
        info['warn'] = time_value_dict(threshold_function, float(extrapolated.warn))
    if o.crit:
        if extrapolated.crit == None:
            extrapolated.crit = 0
        info['crit'] = time_value_dict(threshold_function, float(extrapolated.crit))

    #logging.info("warn: %s, crit: %s" % (extrapolated.warn, extrapolated.crit))

    try:
        info['warn_level'] = float(extrapolated.warn)
        #logging.info("warn: %s" % info['warn_level'])
    except Exception:
        pass

    try:
        info['crit_level'] = float(extrapolated.crit)
        #logging.info("crit: %s" % info['crit_level'])
    except Exception:
        pass

    # ....ahhhnnnd we're finally done.
    try:
        print(json.dumps(info, allow_nan=False, sort_keys=o.json_sort, indent=o.json_indent))
    except ValueError:
        pass

if __name__ == '__main__':
    try:
        main()
    except Exception as e:
        logging.exception(e)
        info = {
            'emax': None,
            'emean': None,
            'dmax': None,
            'dmean': None,
            'integrity': 0,
            'highcharts': [],
            'error': str(e),
        }
        print(json.dumps(info, sort_keys=True))
