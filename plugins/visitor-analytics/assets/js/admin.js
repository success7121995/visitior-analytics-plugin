jQuery(document).ready(function($) {
    'use strict';

    // Initialize admin functionality
    function initAdmin() {
        yearAndMonthChart();
        dayChart();
    }

    // Call initialization
    initAdmin();

    /**
     * Year and month chart
     */
    function yearAndMonthChart() {
        const currentYear = new Date().getFullYear();

        // Get the canvas element for the year and month chart
        const chartElement = document.getElementById('visitor-analytics-total-visitors-by-month-chart');
        
        if (!chartElement) {
            console.error('Canvas element not found');
            return;
        }

        if (!(chartElement instanceof HTMLCanvasElement)) {
            console.error('Element is not a canvas element');
            return;
        }

        new Figure(chartElement, currentYear);
    }

    /**
     * Day chart
     */
    function dayChart() {
        const currentYear = new Date().getFullYear();
        const currentMonth = new Date().getMonth() + 1;

        // Get the canvas element for the day chart
        const chartElement = document.getElementById('visitor-analytics-total-visitors-by-day-chart');

        if (!chartElement) {
            console.error('Canvas element not found');
            return;
        }

        if (!(chartElement instanceof HTMLCanvasElement)) {
            console.error('Element is not a canvas element');
            return;
        }

        new Figure(chartElement, currentYear, currentMonth);
    }

}); 