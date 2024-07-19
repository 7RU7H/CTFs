#!/usr/bin/env python

import time,sys
from lxml import etree,objectify

class XMLextrap( object ):
    
    def __init__( self , dataset ):
        
        self.dataset    = dataset
        self.unit       = dataset.unit
        self.crit       = dataset.crit
        self.warn       = dataset.warn
        
        self.root       = etree.Element('answers')
        
    def get_value_on_date( self , date ):
        '''Find value of self.dataset on given date.'''
        
        xextrap = self.dataset.get_x_values()
        yextrap = self.dataset.get_y_values()
        
        if date < xextrap[0]:
            return 'Does not occur'
        
        for tvalue,value in zip(xextrap,yextrap):
            if int(date) < tvalue:
                return value
        
        return 'Does not occur'
        
    def get_date_of_warn( self ):
        return self.get_date_of_value( float(self.warn) )
        
    def get_date_of_crit( self ):
        return self.get_date_of_value( float(self.crit) )
        
    def add_date_of_crit( self ):
        self.add_date_to_xml( self.get_date_of_crit() , self.crit , 'Critical Value' )
    
    def add_date_of_warn( self ):
        self.add_date_to_xml( self.get_date_of_warn() , self.warn , 'Warning Value' )
        
    def get_date_of_value( self , value , tolerance = .02 ):
        '''Find date the value exists, with the given tolerance.
        
        Returns a list of dates. If the list is empty, it never happend in the extrap time period.'''
        
        xextrap = self.dataset.get_x_values()
        yextrap = self.dataset.get_y_values()
        
        dates   = []
        
        for tvalue,yvalue in zip(xextrap,yextrap):
            if abs(yvalue - value) < ( tolerance * value ):
                dates.append(tvalue)
            elif yvalue > value:
                dates.append(tvalue)
        
        return dates

            
    def add_date_to_xml( self , date = False , value = False , info='Search Value' ):
        
        if not value:
            value   = self.get_value_on_date( date )
        
        if not date:
            date    = 'Out of Period'
            child       = etree.SubElement( self.root , 'answer' , date=str(date) , info=str(info) )
            child.text  = str( value )
        
        else:
            if not isinstance(date,list):
                child       = etree.SubElement( self.root , 'answer' , date=str(date) , info=str(info) )
                child.text  = str( value )
            else:
                for day in date:
                    child       = etree.SubElement( self.root , 'answer' , date=str(day) , info=str(info) )
                    child.text  = str( value )
        
        
    def add_value_to_xml( self , value , info='Search Value' ):
        
        dates = self.get_date_of_value( value )
        
        if dates:
            for date in dates:
                self.add_date_to_xml( date , value , info )
        else:
            self.add_date_to_xml( False , value , info )
        
    def sort_xml_by_date( self ):
        
        #~ Find all instances of answer
        XMLelements = self.root.findall('answer')
        
        #~ Create a temporary space to store the to-be-sorted list
        scratch = etree.Element('answers')
        
        #~ Sort the to-be-sorted list
        sortElements = sorted( XMLelements , key=lambda x: x.attrib['date'] )
        
        #~ Append the elements of the sorted list to the tmp list
        for element in sortElements:
            scratch.append( element )
        
        #~ Save sorted list as main list
        self.root = scratch
        
        
    def print_xml( self ):
        
        print('<?xml version="1.0" encoding="UTF-8"?>')
        print(etree.tostring( self.root , pretty_print=True ))
