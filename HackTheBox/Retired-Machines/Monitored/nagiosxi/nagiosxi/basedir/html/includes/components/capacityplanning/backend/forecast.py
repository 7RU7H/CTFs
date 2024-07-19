#!/usr/bin/env python2

from rrddatastore import Dataset
import numpy as np


def linear_fit(dataset, extrap):
    return polynomial_fit(dataset, extrap, 1, ' LinFit')


def quadratic_fit(dataset, extrap):
    return polynomial_fit(dataset, extrap, 2, ' QuadFit')


def cubic_fit(dataset, extrap):
    return polynomial_fit(dataset, extrap, 3, ' CubeFit')


def polynomial_fit(observed, extrap, order, label_suffix):
    """USAGE: poly_fit((Dataset)observed ,(int)extrap, (int)order, (str)label_suffix)

    Performs a polynomial fit of a given order on a dataset. The fitted
    function is used to generate extrap periods of extrapolated data after the
    end time of the observed Dataset.

    Returns:
        (Dataset)projected: The extrapolated data.
        (np.poly1d)function: Callable fitted function of the specified order.
        (float)residuals: Sum of squared residuals for the fit.
    """

    import warnings
    warnings.simplefilter('ignore', np.RankWarning)

    coefficients, residuals, rank, singular_values, rcond = np.polyfit(
        observed.get_x_values(), observed.get_y_values(), order, None, True
    )
    function = np.poly1d(coefficients)

    projected = get_extrap_dataset(observed, extrap)
    projected.set_dataset(function(projected.get_x_values()))
    projected.label += label_suffix

    return projected, function, residuals[0]


def holt_winters(dataset, extrap):
    """USAGE: least_squares((Dataset)dataset, (int)extrap)

    Extrapolate using the Holt-Winters Algorithm

    """
    import warnings
    warnings.simplefilter('ignore', np.RankWarning)


    extrapDataset = get_extrap_dataset(dataset, extrap)

    alpha = .11
    beta = .001
    gamma = .002
    debug = False
    debug_length_adjustments = False
    debug_updates = False
    debug_S = False
    debug_updated_S = False

    ypoints = dataset.get_y_values()
    cycle_length = dataset.steps_in_period
    extrapolate_cycles = extrap

    ylen = ypoints.size

    if debug_length_adjustments:
        print("ylen:", ylen, "cycle_length:", cycle_length)

    if not ylen % cycle_length == 0:
        if debug_length_adjustments:
            print("HW: ylen % cycle_length != 0, appending...")
        ypoints = np.append(ypoints, [np.mean(ypoints)])
        ylen = ypoints.size

    if debug_length_adjustments:
        print("ylen:", ylen)

    if not ylen % cycle_length == 0:
        if debug_length_adjustments:
            print("HW: ylen % cycle_length != 0, truncating...")
        reversed = ypoints[::-1]
        cycles = ylen // cycle_length
        reversed = np.resize(reversed, cycles * cycle_length)
        ypoints = reversed[::-1]
        ylen = ypoints.size

    if debug_length_adjustments:
        print("ylen:", ylen)

    fc = float(cycle_length)
    c = cycle_length
    #~ Get the average of the second cycle of time series data.
    ybar2 = np.mean(ypoints[c:(2 * c)])
    #~ Get the average of the first cycle of of time series data.
    ybar1 = np.mean(ypoints[:c])
    #~ Calcute initial b value from averages
    b_not = (ybar2 - ybar1) / fc
    if debug:
        print("b_not: ", b_not)
    #~ Create a set of t points to go along with the ypoints
    tset = np.arange(1, c + 1)
    tbar = np.mean(tset)
    #~ Use tbar to get initial alpha value
    a_not = ybar1 - b_not * tbar
    if debug:
        print("a_not: ", a_not)

    #~ Construct Trend array
    I = ypoints / (a_not + np.arange(1, ylen + 1) * b_not)

    if debug:
        print("Initial indices: ", I)
    #~ Create empty array for season data
    S = (I[:c] + I[c:(2 * c)]) / 2.0
    S = np.resize(S, ylen + (extrapolate_cycles * c))
    tS = c / np.sum(S[:c])
    S = S * tS
    if debug_S:
        print('S:', S)


    alpha_factors = alpha * ypoints / S[:ylen]
    one_minus_alpha_factors = 1.0 - alpha
    one_minus_beta_factors = 1 - beta

    A_t = a_not
    B_t = b_not
    for i in range(ylen):
        A_tm1 = A_t
        B_tm1 = B_t
        A_t = alpha_factors[i] + one_minus_alpha_factors * (A_tm1 + B_tm1)
        B_t = beta * (A_t - A_tm1) + one_minus_beta_factors * B_tm1
        S[i + c] = gamma * ypoints[i] / A_t + (1.0 - gamma) * S[i]

        if debug_updates:
            print("i:", i, "y[i]:", ypoints[i], "S[i]:", S[i], "Atm1:", A_tm1, "Btm1:",B_tm1, "At:", A_t, "Bt:", B_t, "S[i+c]:", S[i+c])

    if debug_updated_S:
        print('S:', S)

    E = (A_t + B_t * np.arange(1, extrapolate_cycles * c + 1)) * \
        S[ylen:ylen + extrapolate_cycles * c]

    extrapDataset.set_dataset(E)
    extrapDataset.label += ' HoltWinters'

    # Linear fit on the observed data to get some statistics for estimating
    # confidence in our projection.
    xpoints = dataset.get_x_values()
    ypoints = dataset.get_y_values()

    coefficients, residuals, rank, singular_values, rcond = np.polyfit(
        xpoints, ypoints, 1, None, True
    )

    # Now return our dataset, the linear-fit function, and the sum of squared
    # residuals for the original data.
    return extrapDataset, np.poly1d(coefficients), residuals[0]


def get_extrap_dataset(dataset, extrap):
    """USAGE: get_extrap_dataset((Dataset)dataset, (int)extrap)

    Find the set of values that will be the x points of the extrapolated data.

    Returns Dataset object with proper start and end filled in.
    """

    start = dataset.end
    end = start + (extrap * dataset.period)

    #~ Copy over values from old dataset to extrapDataset

    extrapDataset = Dataset(start, end, dataset.step, 'DS Extrap')
    extrapDataset.label = dataset.label
    extrapDataset.unit = dataset.unit
    extrapDataset.warn = dataset.warn
    extrapDataset.crit = dataset.crit
    extrapDataset.host = dataset.host
    extrapDataset.service = dataset.service
  
    return extrapDataset


def get_fitted_dataset(dataset, start, end, step, function):
    """USAGE: get_fitted_dataset((Dataset)dataset, (callable)function)

    Prepare a new Dataset to hold the fitted function data.
    """

    fitted = Dataset(start, end, step, 'DS Fitted')
    fitted.label = dataset.label
    fitted.unit = dataset.unit
    fitted.warn = dataset.warn
    fitted.crit = dataset.crit
    fitted.host = dataset.host
    fitted.service = dataset.service
    fitted.set_dataset(function(fitted.get_x_values()))

    return fitted


def make_projections(observed, projected, function, residue):
    """ . """
    x = projected.get_x_values()
    y = projected.get_y_values()

    xt = x[-1]
    yt = y[-1]

    df = function.deriv()

    ne = float(x.count())
    nd = ne * 2.
    sigma = np.sqrt(residue / nd)

    return {
        'ne': ne,
        'nd': nd,
        'edate':  int(xt),
        'evalue': yt,
        'evalue_min': yt - sigma,
        'evalue_max': yt + sigma,
        'eslope': df(xt),
        'f_of_x_on_date': function(xt),
        'residue': residue,
        'sigma': sigma,
        'adjusted_sigma': sigma / observed.get_integrity(),
    }
