function create_gauge(container, modifier, configin) {

    var config = {
        'size': $('#' + container).width(),
        'min': 0,
        'max': 100,
        'fontsize': 12,
        'minorTicks': 5,
    }

    //config.yellowZones = configin.yellowZones;
    for (var attrname in configin) {
        config[attrname] = configin[attrname];
    }
    config.label = config.label.split(' ').slice(0, 4).join(' ')
    gauge = new Gauge(container, config, modifier);
    return gauge;
}

function Gauge(placeholderName, configuration, modifier) {
    this.placeholderName = placeholderName;
    this.modifier = modifier

    var self = this; // for internal d3 functions

    this.configure = function (configuration) {
        this.config = configuration;

        // this.config.size = this.config.size * 0.9;

        this.config.radius = this.config.size * 0.90434782608695652173913043478261 / 2;
        this.config.cx = this.config.size / 2;
        this.config.cy = this.config.size / 2;

        this.config.min = undefined != configuration.min ? configuration.min : 0;
        this.config.max = undefined != configuration.max ? configuration.max : 100;
        this.config.range = this.config.max - this.config.min;

        this.config.majorTicks = configuration.majorTicks || 5;
        this.config.minorTicks = configuration.minorTicks || 2;

        this.config.greenColor = configuration.greenColor || "#109618";
        this.config.yellowColor = configuration.yellowColor || "#FF9900";
        this.config.redColor = configuration.redColor || "#DC3912";

        this.config.transitionDuration = configuration.transitionDuration || 500;
    }

    this.render = function () {
        this.body = d3.select("#" + this.placeholderName)
            .append("svg:svg")
            .attr("class", "gauge")
            .attr("width", this.config.size)
            .attr("height", this.config.size);

        this.body.append("svg:circle")
            .attr("cx", this.config.cx)
            .attr("cy", this.config.cy)
            .attr("r", this.config.radius)
            .attr("fill", "#cccccc")
            .attr("stroke", "#333333")
            .attr("stroke-width", "1");

        this.body.append("svg:circle")
            .attr("cx", this.config.cx)
            .attr("cy", this.config.cy)
            .attr("r", 0.88461538461538461538461538461538 * this.config.radius)
            .attr("fill", "#f7f7f7")
            .attr("stroke", "#e0e0e0")
            .attr("stroke-width", "2");

        for (var index in this.config.greenZones) {
            this.drawBand(this.config.greenZones[index].from, this.config.greenZones[index].to, self.config.greenColor);
        }

        for (var index in this.config.yellowZones) {
            this.drawBand(this.config.yellowZones[index].from, this.config.yellowZones[index].to, self.config.yellowColor);
        }

        for (var index in this.config.redZones) {
            this.drawBand(this.config.redZones[index].from, this.config.redZones[index].to, self.config.redColor);
        }

        if (undefined != this.config.label) {
            var fontSize = this.config.radius * 2 / (Math.sqrt(this.config.label.length) / 0.2);
            this.body.append("svg:text")
                .attr("x", this.config.cx)
                .attr("y", this.config.cy - this.config.cy / 4)
                .attr("text-anchor", "middle")
                .text(this.config.label)
                .attr("font-family", "arial")
                .attr("font-size", fontSize)
                .attr("fill", "#333333")
                .attr("stroke", "none")
                .attr("stroke-width", "0");
        }

        var fontSize = Math.round(this.config.size / 16);
        var majorDelta = this.config.range / (this.config.majorTicks - 1);
        for (var major = this.config.min; major <= this.config.max; major += majorDelta) {
            var minorDelta = majorDelta / this.config.minorTicks;
            for (var minor = major + minorDelta; minor < Math.min(major + majorDelta, this.config.max); minor += minorDelta) {
                var point1 = this.valueToPoint(minor, 0.75);
                var point2 = this.valueToPoint(minor, 0.85);

                this.body.append("svg:line")
                    .attr("x1", point1.x)
                    .attr("y1", point1.y)
                    .attr("x2", point2.x)
                    .attr("y2", point2.y)
                    .style("stroke", "#666")
                    .style("stroke-width", "1px");
            }

            var point1 = this.valueToPoint(major, 0.7);
            var point2 = this.valueToPoint(major, 0.85);

            this.body.append("svg:line")
                .attr("x1", point1.x)
                .attr("y1", point1.y)
                .attr("x2", point2.x)
                .attr("y2", point2.y)
                .style("stroke", "#333")
                .style("stroke-width", "2px");

            if (major == this.config.min || major == this.config.max) {
                var point = this.valueToPoint(major, 0.63);

                this.body.append("svg:text")
                    .attr("x", point.x)
                    .attr("y", point.y)
                    .attr("dy", fontSize / 3)
                    .attr("text-anchor", major == this.config.min ? "start" : "end")
                    .text(major)
                    .style("font-family", "arial")
                    .style("font-size", fontSize + "px")
                    .style("fill", "#333")
                    .style("stroke-width", "0px");
            }
        }

        var pointerContainer = this.body.append("svg:g").attr("class", "pointerContainer");

        var midValue = (this.config.min + this.config.max) / 2;

        var pointerPath = this.buildPointerPath(midValue);

        var pointerLine = d3.svg.line()
            .x(function (d) {
                return d.x
            })
            .y(function (d) {
                return d.y
            })
            .interpolate("basis");

        pointerContainer.selectAll("path")
            .data([pointerPath])
            .enter()
            .append("svg:path")
            .attr("d", pointerLine)
            .style("fill", "#dc3912")
            .style("stroke", "#c63310")
            .style("fill-opacity", 0.7)

        pointerContainer.append("svg:circle")
            .attr("cx", this.config.cx)
            .attr("cy", this.config.cy)
            .attr("r", (1 - 0.88461538461538461538461538461538) * this.config.radius)
            .attr("fill", "#4684EE")
            .attr("stroke", "#666666")
            .attr("opacity", 1);

        var fontSize = this.config.radius * 2 / 14;
        pointerContainer.selectAll("text")
            .data([midValue])
            .enter()
            .append("svg:text")
            .attr("x", this.config.cx)
            .attr("y", this.config.size - this.config.cy / 4 - fontSize)
            .attr("dy", fontSize / 4)
            .attr("text-anchor", "middle")
            .attr("font-family", "arial")
            .attr("font-size", fontSize)
            .attr("fill", "#000")
            .attr("stroke", "none")
            .attr("stroke-width", "0");

        this.redraw(this.config.current, 500);
    }

    this.buildPointerPath = function (value) {
        var delta = this.config.range / 13;

        var head = valueToPoint(value, 0.85);
        var head1 = valueToPoint(value - delta, 0.12);
        var head2 = valueToPoint(value + delta, 0.12);

        var tailValue = value - (this.config.range * (1 / (270 / 360)) / 2);
        var tail = valueToPoint(tailValue, 0.28);
        var tail1 = valueToPoint(tailValue - delta, 0.12);
        var tail2 = valueToPoint(tailValue + delta, 0.12);

        return [head, head1, tail2, tail, tail1, head2, head];

        function valueToPoint(value, factor) {
            var point = self.valueToPoint(value, factor);
            point.x -= self.config.cx;
            point.y -= self.config.cy;
            return point;
        }
    }

    this.drawBand = function (start, end, color) {
        if (0 >= end - start) return;

        this.body.append("svg:path")
            .style("fill", color)
            .attr("d", d3.svg.arc()
                .startAngle(this.valueToRadians(start))
                .endAngle(this.valueToRadians(end))
                .innerRadius(0.65 * this.config.radius)
                .outerRadius(0.85 * this.config.radius))
            .attr("transform", function () {
                return "translate(" + self.config.cx + ", " + self.config.cy + ") rotate(270)"
            });
    }

    this.redraw = function (value, transitionDuration) {
        var value;
        var jsonobj = this;

        // performs render if the object hasn't been created yet
        if (!jsonobj.body)
            this.render()

        var jsonkey = this.jsonkey

        if (jsonobj.modifier != undefined) {
            value = modifier * value;
        }

        var pointerContainer = jsonobj.body.select(".pointerContainer");

        pointerContainer.selectAll("text").text(value + this.config['uom']);

        var pointer = pointerContainer.selectAll("path");
        pointer.transition()
            .duration(undefined != transitionDuration ? transitionDuration : jsonobj.config.transitionDuration)
            //.delay(0)
            //.ease("linear")
            //.attr("transform", function(d)
            .attrTween("transform", function () {
                var pointerValue = value;
                if (value > self.config.max) pointerValue = self.config.max + 0.02 * self.config.range;
                else if (value < self.config.min) pointerValue = self.config.min - 0.02 * self.config.range;
                var targetRotation = (self.valueToDegrees(pointerValue) - 90);
                var currentRotation = self._currentRotation || targetRotation;
                self._currentRotation = targetRotation;

                return function (step) {
                    var rotation = currentRotation + (targetRotation - currentRotation) * step;
                    return "translate(" + self.config.cx + ", " + self.config.cy + ") rotate(" + rotation + ")";
                }
            });
    }

    this.valueToDegrees = function (value) {
        // thanks @closealert
        //return value / this.config.range * 270 - 45;
        return value / this.config.range * 270 - (this.config.min / this.config.range * 270 + 45);
    }

    this.valueToRadians = function (value) {
        return this.valueToDegrees(value) * Math.PI / 180;
    }

    this.valueToPoint = function (value, factor) {
        return {
            x: this.config.cx - this.config.radius * factor * Math.cos(this.valueToRadians(value)),
            y: this.config.cy - this.config.radius * factor * Math.sin(this.valueToRadians(value))
        };
    }

    // initialization
    this.configure(configuration);
}

function load_gauge_hosts()
{
    var url = base_url + "includes/dashlets/gauges/getdata.php";
    $('.host-loader').show();

    // Get all services with data
    $.ajax({
        "url": url,
        data: { 'cmd': 'noperfdata' },
        "success": function (result) {

            $('.host-loader').hide();

            hosts = result;
            var hostslist = "<option selected></option>";
            $(hosts).each(function(k, v) {
                hostslist += "<option value='" + v + "'>" + v + "</option>";
            });
            $('#gauges_form_name').html(hostslist);
        }
    });
}

function getgaugejson()
{
    var url = base_url + "includes/dashlets/gauges/getdata.php";
    var host = $('#gauges_form_name').val();

    if (host == '') {
        $('#gauges_form_services').html("<option selected></option>");
        $('#gauges_form_services').prop('disabled', true);
        $('#empty-services').hide();
        $('#gauges_form_services').show();
        return;
    }

    // Set loading...
    $('.service-loader').show();
    $('#gauges_form_ds').prop('disabled', true);

    // Get all services with data
    $.ajax({
        "url": url,
        data: { 'host': host, 'cmd': 'noperfdata' },
        "success": function (result) {

            $('.service-loader').hide();

            // If services are empty
            if (result.length == 0) {
                $('#gauges_form_services').hide();
                $('#empty-services').show();
                return;
            } else {
                $('#gauges_form_services').show();
                $('#empty-services').hide();
            }

            services = result;
            var servicelist = "<option selected></option>";
            $(services).each(function(k, v) {
                servicelist += "<option value='" + v + "'>" + v + "</option>";
            });
            $('#gauges_form_services').html(servicelist);

            // Remove disabled
            $('#gauges_form_services').prop('disabled', false);
        }
    });
}

function getgaugeservices()
{
    var url = base_url + "includes/dashlets/gauges/getdata.php";
    var host = $('#gauges_form_name').val();
    var service = $("#gauges_form_services").val();

    if (service == '') {
        $('#gauges_form_ds').html("<option selected></option>");
        $('#gauges_form_ds').prop('disabled', true);
        $('#empty-ds').hide();
        $('#gauges_form_ds').show();
        return;
    }

    // Set loading...
    $('.ds-loader').show();

    // Get all services with data
    $.ajax({
        "url": url,
        data: { 'host': host, 'service': service },
        "success": function (result) {

            $('.ds-loader').hide();

            // If services are empty
            if (result.length == 0) {
                $('#gauges_form_ds').hide();
                $('#empty-ds').show();
                return;
            } else {
                $('#gauges_form_ds').show();
                $('#empty-ds').hide();
            }

            datasources = result[host][service];
            var dslist = "<option selected>";
            for (ds in datasources) {
                dslist += "<option value='" + ds + "'>" + ds + "</option>";
            }

            $('#gauges_form_ds').html(dslist);

            // Remove disabled
            $('#gauges_form_ds').prop('disabled', false);
        }
    });
}