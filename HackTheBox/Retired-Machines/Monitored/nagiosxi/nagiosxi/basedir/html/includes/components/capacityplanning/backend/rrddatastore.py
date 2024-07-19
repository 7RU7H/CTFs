#!/usr/bin/env python2

import numpy as np
import sys
from lxml import etree
import rrdtool
import copy
import logging
import operator

class Datastore(object):

    def __init__(self, data_dir, host, service, track):
        """USAGE: Datastore((str)<host>, (str)<service>, (str)<track>)

        Sets a new Datastore's self variables to hold the host and service
        information. Also sets the guessed filenames for the RRD and XML.
        """

        #text = open("/tmp/test.log", "a")
        #text.write("\n")
        #text.write("Start\n")
        #text.write("Track passed: '" + track + "'\n")

        self.host = host
        self.service = service

        self.data_dir = data_dir
        self.rrd, self.xml = Datastore.guess_filename(data_dir, host, service)

        #see if we have a track number or name...
        if track.isdigit():
          #text.write( "track.isdigit\n")
          self.track_number = str( int( track) + 1) # need to add one, the tracks start at 1 in file, 0 elsewhere ? 
          self.track = str(self.get_track_name())
        else:
          #text.write( "not track.isdigit\n")
          self.track = track
          self.track_number = str(self.get_track_number())

        #text.write("Again.\n");
        #text.write("Track Number: '" + str( self.track_number) + "' Track Name: '" + self.track + "' Track passed: '" + track + "'\n")
        #text.write(self.rrd + "\n")
        #text.close()

        self.dataset = None
        self.label = None

    def get_track_number(self):
        """Gets the track number for the given RRD track. """

        root = etree.parse(self.xml)
        for ds in root.iter('DATASOURCE'):
            if ds.find('NAME').text == self.track:
                return ds.find('DS').text

    def get_track_name(self):
        """Gets the track name for the given RRD track. """

        root = etree.parse(self.xml)
        for ds in root.iter('DATASOURCE'):
            #if ds.find('DS').text == str( int(self.track) + 1):
            if ds.find('DS').text == self.track_number:
                return ds.find('NAME').text

    def parse_rrd_file(self, start=1, end='now'):
        """USAGE: parse_rrd_file([(RRD time)<range> (RRD time)<end>])

        Sets information inside of this class about the RRD of choice.
        """

        #~ Record the range for later use
        self.start = start

        #~ Initialize data by unpacking it from rrdtoolfetch
        rrdinfo, dsinfo, rrddata = rrdtool.fetch(
            self.rrd, 'AVERAGE', '-s', 'e-%dw' % start, '-e', end)

        #~ Change the timeseries organization
        organized_data = zip(*rrddata)

        #~ Add each individual dataset to the self.list_of_datasets, also label each
        for dsnumber, dataset in zip(dsinfo, organized_data):
            if self.track_number == dsnumber:
                tmprrddata = rrdinfo + (dsnumber, )
                tmp = Dataset(*tmprrddata)
                tmp.set_dataset(dataset)
                self.dataset = tmp

    def parse_xml_file(self):
        """USAGE: parse_xml_file()

        Parses XML file for the given RRD and assigns information to the RRDs.
        """

        xml = etree.parse(self.xml)
        datasources = xml.findall('DATASOURCE')
        for ds in datasources:
            if ds.find('NAME').text == self.track:
                
                label = ds.find('LABEL')
                if label is not None:
                    self.dataset.label = label.text

                # Units cleanup: catch the double percent for localhost/_HOST_/pl,
                # let the frontend handle unitless tracks as it wants.
                unit = ds.find('UNIT')
                if unit is not None:
                    self.dataset.unit = unit.text
                    if self.dataset.unit == '%%':
                        self.dataset.unit = '%'

                warn = ds.find('WARN')
                if warn is not None:
                    self.dataset.warn = warn.text

                crit = ds.find('CRIT')
                if crit is not None:
                    self.dataset.crit = crit.text

                self.dataset.host = self.host
                self.dataset.service = self.service

    def __str__(self):
        return self.dataset.__repr__()

    @staticmethod
    def guess_filename(data_dir, host, service):
        """USAGE: guess_filename( (str)<data_dir> , (str)<host> , (str)<service> )

        Guesses the filenames for the XML and RRD file given the host 
        and serivce name. Uses Nagios LLC defaults for Nagios XI.
        """

        base = "%s/%s/%s" % (data_dir, host, service)

        rrd = base + '.rrd'
        xml = base + '.xml'

        return rrd, xml


class Dataset(object):

    def __init__(self, start, end, step, ds):
        """USAGE: RRDDataset((int)<start>, (int)<end>, (int)<step>)

        Initialize general data about the RRD dataset.
        """

        self.start = start
        self.end = end
        self.step = step
        self.ds = ds

        self.label = ''
        self.warn = ''
        self.crit = ''
        self.unit = ''

        self.period = 0

        self.fill_value = None
        self.dataset = None
        self.steps_in_period = None

    def set_dataset(self, data):
        """USAGE: set_dataset((list)<data>)

        Use this as a mutator for the dataset contained by this class. 
        Makes the necessary masked array of the given dataset.
        
        """
        self.dataset = np.ma.masked_invalid([((y is None) and [np.nan] or [y])[0] for y in data])
        self.dataset.fill_value = self.dataset.mean()
        self.fill_value = self.dataset.fill_value

    def get_y_values(self, safe=True):
        """USAGE: get_y_values( [(Boolean)<safe>] )

        Returns the dataset. It defaults to returning a safe dataset.
        However if you want the unfilled dataset, simply pass 
        safe = False.
        
        """
        if safe:
            fill_value = 0
            if self.fill_value is not None:
                fill_value = self.fill_value

            returned_set = self.dataset.filled(fill_value)
            new_set = np.ma.clip(returned_set, np.NINF, np.inf)

            if np.isnan(np.sum(new_set)):
                for i, x in enumerate(new_set):
                    if np.isnan(x):
                        new_set[i] = 0

            return new_set
        else:
            return self.dataset

    def get_integrity(self):
        """Gets the fraction of valid non-masked values in the dataset. """
        masked_count = np.ma.count_masked(self.dataset)
        return 1 - (float(masked_count) / len(self.dataset))


    def get_x_values(self):
        """USAGE: get_x_values( [(int)<start> (int)<end>] )

        Returns a dataset's points' x values.
        """
        return np.ma.arange(self.start, self.end, self.step)

    def get_xy_pairs(self, safe=True):
        """USAGE: get_xy_pairs( [(Boolean)<safe>] )

        Returns a dataset's points as a sequence.
        """
        return zip(self.get_x_values(), self.get_y_values(safe))


    def get_highcharts_dataset(self, name, plot_type='area'):
        return {
            'type': plot_type,
            'name': name,
            'pointStart': int(self.start) * 1000,
            'pointInterval': int(self.step) * 1000,
            'data': self.get_y_values().tolist(),
        }

    def get_mean(self):
        """ Gets a dataset's mean value. """
        m = np.mean(self.get_y_values())
        return ((np.isnan(m)) and [0] or [m])[0]

    def get_max(self):
        """ Gets a dataset's maximum value. """
        m = max(self.get_y_values())
        return ((np.isnan(m)) and [0] or [m])[0]

    def get_min(self):
        """ Gets a dataset's minimum value. """
        m = min(self.get_y_values())
        return ((np.isnan(m)) and [0] or [m])[0]

    def set_period(self, period):
        """USAGE: set_period( [(int)period] )

        Sets a Dataset's period in weeks.
        """
        SECONDS_IN_WEEK = 604800

        self.period = int(period) * SECONDS_IN_WEEK
        self.steps_in_period = int(self.period / self.step)


    def get_value_on_date(self, date):
        """Gets the value on a date, or the first value after the date. """

        x = self.get_x_values()
        y = self.get_y_values()

        if date < x[0]:
            return 'Date happens before extrapolation begins.'
        if date > x[-1]:
            return 'Date happens after extrapolation ends.'
        for i, d in enumerate(x):
            if date <= d:
                return y[i]


    def build_point_value_matches_function(op):
        """Returns a function to get the first point whose y ordinate matches
            a relation to a value.
        """
        def match_function(self, value):
            for x, y in self.get_xy_pairs():
                if op(y, value):
                    return x, y
            return 0, 0
        return match_function

    get_point_gt = build_point_value_matches_function(operator.gt)
    get_point_ge = build_point_value_matches_function(operator.ge)
    get_point_lt = build_point_value_matches_function(operator.lt)
    get_point_le = build_point_value_matches_function(operator.le)
    get_point_eq = build_point_value_matches_function(operator.eq)

    def __str__(self):
        return self.service + ' on ' + self.host
