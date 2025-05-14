/**
 * figure.js
 * 
 * This file contains the JavaScript code for the chart.js library.
 * It includes methods for initializing the chart and for updating the chart.
 */

class Figure {

    /**
     * Constructor
     * 
     * @param {HTMLElement} chartElement - The canvas element to display the chart on
     * @param {number} currentYear - Current year to display the chart for
     * @param {number} currentMonth - Current month to display the chart for
     */
    constructor(chartElement = null, currentYear, currentMonth = null) {
        this.chartElement = chartElement;
        this.currentYear = currentYear;
        this.currentMonth = currentMonth;
        this.chart = null; // Store chart instance
        this.monthNames = [
            'January', 'February', 'March', 'April', 'May', 'June',
            'July', 'August', 'September', 'October', 'November', 'December'
        ];

        // Year is required
        if (!this.currentYear) {
            console.error('Year is required');
            return;
        }

        // Only render chart if chartElement is provided
        if (this.chartElement) {
            // Check if month is provided
            if (!this.currentMonth) {
                this.renderYearSelect();
            } else {
                this.renderYearAndMonthSelect();
            }
            this.renderChart();
        }
    }

    /**
     * Render year select for year and month chart
     */
    renderYearSelect() {
        const yearSelectContainer = document.querySelector('.visitor-analytics-total-visitors-by-month .visitor-analytics-total-visitors-by-year-select');

        // Create year select
        const yearSelect = document.createElement('select');

        // Define the range of years
        const startYear = 2025;

        // Generate options for each year in the range, including startYear
        for (let year = startYear; year <= this.currentYear; year++) {
            yearSelect.innerHTML += year === this.currentYear ?
                `<option value="${year}" selected>${year}</option>` :
                `<option value="${year}">${year}</option>`;
        }

        yearSelectContainer.appendChild(yearSelect);

        // Add event listener to year select
        yearSelect.addEventListener('change', () => {
            this.currentYear = parseInt(yearSelect.value);
            this.renderChart(); // Update chart on year change
        });   
    }

    /**
     * Render Year and Month Select
     */
    renderYearAndMonthSelect() {
        const yearSelectContainer = document.querySelector('.visitor-analytics-total-visitors-by-day .visitor-analytics-total-visitors-by-year-select');
        const monthSelectContainer = document.querySelector('.visitor-analytics-total-visitors-by-day .visitor-analytics-total-visitors-by-month-select');

        // Create year select
        const yearSelect = document.createElement('select');

        // Define the range of years
        const startYear = 2025;

        // Generate options for each year in the range
        for (let year = startYear; year <= this.currentYear; year++) {
            yearSelect.innerHTML += year === this.currentYear ?
                `<option value="${year}" selected>${year}</option>` :
                `<option value="${year}">${year}</option>`;
        }

        yearSelectContainer.appendChild(yearSelect);

        // Add event listener to year select
        yearSelect.addEventListener('change', () => {
            this.currentYear = parseInt(yearSelect.value);
            this.renderChart(); // Update chart on year change
        });

        // Create month select
        const monthSelect = document.createElement('select');
        monthSelect.className = 'visitor-analytics-select';
        
        // Generate options for each month
        for (let month = 1; month <= 12; month++) {
            monthSelect.innerHTML += month === this.currentMonth ?
                `<option value="${month}" selected>${this.monthNames[month - 1]}</option>` :
                `<option value="${month}">${this.monthNames[month - 1]}</option>`;
        }

        monthSelectContainer.appendChild(monthSelect);

        // Add event listener to month select
        monthSelect.addEventListener('change', () => {
            this.currentMonth = parseInt(monthSelect.value);
            this.renderChart(); // Update chart on month change
        });
    }

    /**
     * Render chart
     */
    async renderChart() {
        // Only render if chartElement exists
        if (!this.chartElement) {
            return;
        }

        const res = await this.fetchDataForChart();

        if (!res.success || !res.data) {
            console.error('Invalid data received from server');
            return;
        }

        // Destroy existing chart if it exists
        if (this.chart) {
            this.chart.destroy();
        }

        // Get the 2D context from the canvas element
        const ctx = this.chartElement.getContext('2d');

        // Extract labels and data from the response
        const labels = this.currentMonth ? res.data.map(item => item.day) : res.data.map(item => item.month);
        const data = res.data.map(item => item.total_visitors);

        // Calculate max value for better scaling
        const maxDataValue = Math.max(...data);
        const yAxisMax = this.currentMonth ?
            maxDataValue > 100 ? Math.min(maxDataValue + 10, 200) : 100 :
            maxDataValue > 500 ? Math.min(maxDataValue + 50, 1000) : 500;

        // Register the datalabels plugin globally
        Chart.register(ChartDataLabels);

        // Create new chart and store the instance
        this.chart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Total Visitors',
                    data: data,
                    backgroundColor: 'rgba(54, 162, 235, 0.5)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                animation: {
                    duration: 500 // Reduce animation duration for better performance
                },
                plugins: {
                    title: {
                        display: true,
                        text: this.currentMonth ? 
                            `Visitor Analytics for ${this.monthNames[this.currentMonth - 1]} ${this.currentYear}` :
                            `Visitor Analytics for ${this.currentYear}`
                    },
                    datalabels: {
                        display: true,
                        color: '#000',
                        anchor: 'end',
                        align: 'top',
                        offset: 4,
                        formatter: function(value) {
                            return value > 0 ? value : '';
                        },
                        font: {
                            weight: 'bold',
                            size: 11 // Slightly smaller font for better fit
                        },
                        padding: {
                            top: 2
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        max: yAxisMax,
                        title: {
                            display: true,
                            text: 'Number of Visitors'
                        },
                        ticks: {
                            maxTicksLimit: 6 // Limit the number of ticks for better performance
                        }
                    },
                    x: {
                        title: {
                            display: true,
                            text: this.currentMonth ? 'Day' : 'Month'
                        },
                        ticks: {
                            maxRotation: 45, // Rotate labels for better fit
                            minRotation: 45
                        }
                    }
                }
            },
            plugins: [ChartDataLabels]
        });
    }

    /**
     * Fetch data from server
     * @param {number} year - Year to fetch data for
     * @param {number} month - Month to fetch data for. If not provided, data for the year will be fetched
     * @returns {array} Data from server
     */
    async fetchDataForChart(year = this.currentYear, month = this.currentMonth) {
        try {
            const formData = new FormData();

            if (!month) {
                formData.append('action', 'get_total_visitors_by_month');
                formData.append('year', year);
            } else {
                formData.append('action', 'get_total_visitors_by_day');
                formData.append('year', year);
                formData.append('month', month);
            }

            const res = await fetch(visitor_analytics_chart.ajaxurl, {
                method: 'POST',
                body: formData
            });

            if (!res.ok) {
                throw new Error('Failed to fetch year and month data');
            }

            return await res.json();
        } catch (error) {
            console.error('Error fetching data:', error);
        }
    }


}

