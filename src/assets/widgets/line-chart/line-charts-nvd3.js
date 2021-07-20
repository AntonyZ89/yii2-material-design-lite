'use strict';

const COLORS = {
    red: '#f44336',
    lightBlue: '#03a9f4',
    orange: '#ffc107',
    amber: '#ff9800',
    teal: '#00bcd4',
    purple: '#7726d3',
    green: '#00d45a',
    rowBgColor: '#4a4a4a',
};

class LineChart {
    constructor(options) {
        this.options = options;
        this.container = options.container;
        this.maxX = options.maxX;
        this.xStep = options.xStep;
        this.columns = this.options.maxX/2;
        this.color = options.rowBgColor;
        this.margin = options.margin;
        this.data = options.data;
        this.nv = options.nv;
        this.drawStep = this.xStep * options.xDrawStep; // It shows how many points will be drawn in one step
        this.durationResizeAnimation = 500;
    }

    _addSvgContainer() {
        this.svg = this.container.append('div')
            .append('svg');
    }

    _getSvgSizes() {
        let svgWidth = getComputedStyle(this.svg[0][0]).width,
            svgHeight = getComputedStyle(this.svg[0][0]).height;
        this.svgWidth = svgWidth.slice(0, svgWidth.length - 2);
        this.svgHeight = svgHeight.slice(0, svgHeight.length - 2) - this.margin;
    }

    _addAxisLabels() {
        this.container.selectAll('svg .y-axis-label')
            .remove();
        this.container.select('svg')
            .append('text')
            .attr('class', 'y-axis-label')
            .attr('x', -(23 + this.options.yAxis.length*7))
            .attr('y', '12')
            .attr('transform', 'rotate(-90)')
            .text(this.options.yAxis || '');

        this.container.select('svg')
            .append('text')
            .attr('class', 'x-axis-label')
            .text(this.options.xAxis || '');
    }

    _buildBackground() {
        this._addSvgContainer();
        this._getSvgSizes();

        let bars = [];
        for (let i = 0; i < this.columns; i++) {
            bars.push(this.svgHeight);
        }

        this.barsLayout = this.svg.append('g')
            .attr('class', 'bars')
            .attr('transform', `translate(${this.margin}, 0)`)
            .selectAll('rect')
            .data(bars)
            .enter()
            .append('rect');

        this._addAxisLabels();

        this._setBackgroundSizes();
    }

    _setBackgroundSizes() {
        let availableBarWidth = (this.svgWidth - 2 * this.margin) / this.columns,
            barWidth = availableBarWidth / 2;
        this.barsLayout
            .transition().duration(this.durationResizeAnimation)
            .attr('width', barWidth)
            .attr('x', function(d, i) {
                return i * availableBarWidth;
            });
        this.container.select('svg .x-axis-label')
            .transition().duration(this.durationResizeAnimation)
            .attr('x', this.svgWidth - this.margin - 7 - this.options.xAxis.length*7)
            .attr('y', this.svgHeight - (this.svgHeight) / 4 + this.margin + 14);
    }

    drawChart() {
        this._buildBackground();
        this._buildLegend();
        this._buildNvGraph();
    }

    _buildNvGraph() {
        this._tuneNvGraph();

        nv.addGraph(() => {
            this.svg.datum(this.data)
                .transition().duration(0)
                .call(this.lineChart);
            nv.utils.windowResize(this.resizeBackground.bind(this));
            nv.utils.windowResize(this.lineChart.update);
            return this.lineChart;
        });
    }

    _tuneNvGraph() {
        this.lineChart = nv.models.lineChart()
            .margin({top: this.margin, right: this.margin, bottom: 0, left: this.margin})
            .useInteractiveGuideline(true)
            //.yDomain([-1.01, 3])
            .showLegend(false)
            .showYAxis(true)
            .showXAxis(true)
            .pointSize(5);

        this.lineChart.xAxis
            .showMaxMin(false)
            .tickValues([0]);

        this.lineChart.yAxis
            .showMaxMin(false)
            .ticks(10);
    }

    _buildLegend() {
        let legend = this.container.append('div')
            .attr('class', 'legend')
            .selectAll('.legend__item')
            .data(this.data)
            .enter()
            .append('div')
            .attr('class', 'legend__item');

        legend.append('div')
            .attr('class', 'legend__mark pull-left')
            .style('background-color', d => d.color);

        legend.append('div')
            .attr('class', 'legend__text')
            .text(d => d.key);
    }

    resizeBackground() {
        this._getSvgSizes();
        this._setBackgroundSizes();
    }
}
