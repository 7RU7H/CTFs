#!/usr/bin/env python

import rrdtool
import sys
import optparse
try:
    import json
except ImportError:
    import simplejson as json
import lxml.etree
import logging


logging.basicConfig(level=logging.DEBUG,
                    format='%(asctime)s %(levelname)-8s %(message)s',
                    datefmt='%a, %d %b %Y %H:%M:%S',
                    filename='/usr/local/nagiosxi/var/components/capacityplanning_timeframe.log',
                    filemode='a')


# RRD performance data directory. Yeah, yeah, a global...
data_dir = '/usr/local/nagios/share/perfdata'

def parse_args():
    """Make sure they passed the proper amount of args and then
    leave, nothing fancy.

    """
    usage = 'Usage: %prog hostname servicename trackname'
    parser = optparse.OptionParser(usage=usage)

    (options, args) = parser.parse_args()

    if len(args) < 3:
        logging.error('Was given too few arguments. Given: %s' % ' '.join(args))
        raise Exception('Was given too few arguments.')
    if len(args) > 4:
        logging.error('Was given too many arguments. Given: %s' % ' '.join(args))
        raise Exception('Was given too many arguments.')
    if len(args) == 3:
        args.append(None)

    return args


def get_track_number(host, service, track):
    """Gets the integer value of which track the data resides in, given
    a host and service. Consults the XML file associated with the
    datasource.

    """
    XMLFILE = '%s/%s/%s.xml'
    xmlfile = XMLFILE % (data_dir, host, service)

    logging.debug('Reading out of %s', xmlfile)
    root = lxml.etree.parse(xmlfile)

    for ds in root.iter('DATASOURCE'):
        if ds.find('NAME').text == track:
            return ds.find('DS').text
    raise IndexError('Could not find %s in datasource.' % track)

def get_rrd_data(host, service, track_number, date_delta):
    """Returns the data that exists in the given track in an RRD.

    """
    RRDFILE = '%s/%s/%s.rrd'
    rrdfile = RRDFILE % (data_dir, host, service)
    logging.debug('Looking at RRD %s...', rrdfile)
    logging.debug('Using %s as time delta.', date_delta)
    dates, labels, data = rrdtool.fetch(rrdfile, 'AVERAGE', '-s', date_delta)

    count = 0
    for label in labels:
        if label == track_number:
            break
        else:
            count += 1

    return dates, zip(*data)[count]

def get_usable_date_deltas(host, service, track_number):

    acceptable = []
    ACCEPTABLE_NONE_RATE = .55
    for week in [104, 52, 24, 8, 4, 2]:
        date_delta = '-%dw' % week
        dates, data = get_rrd_data(host, service, track_number, date_delta)
        integrity = float(data.count(None)) / len(data)
        logging.debug('None rate for %s/%s was %f' % (host, service, integrity))
        if integrity < ACCEPTABLE_NONE_RATE:
            acceptable.append([week, 1 - integrity])

    return acceptable


def main():

    global data_dir
    host, service, track, data_dir = parse_args()
    logging.debug('Getting timeframe for %s->%s->%s' % (host, service, track))

    track_number = get_track_number(host, service, track)
    acceptable = get_usable_date_deltas(host, service, track_number)

    print(json.dumps({'acceptable': acceptable}))

try:
    main()
except Exception as e:
    print(json.dumps({'error': str(e)}))
    sys.exit(1)
