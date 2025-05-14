jQuery(document).ready(function($) {
    'use strict';

    // Initialize admin functionality
    const initAdmin = () => {
        yearAndMonthChart();
        dayChart();
        initTableAction();
    }

    /**
     * Year and month chart
     */
    const yearAndMonthChart = () => {
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
    const dayChart = () => {
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

    /**
     * Initialize table actions (sort, export, modal)
     */
    const initTableAction = () => {
        // Simple click toggle for export dropdown
        $('.visitor-analytics-button.export').on('click', function() {
            const $options = $(this).siblings('.visitor-analytics-export-options');
            $options.toggleClass('show');
        });

        // Export button click for table
        $('.visitor-analytics-export-option').on('click', function() {
            const $btn = $(this);
            const exportType = $btn.data('format');
            const $container = $btn.closest('.visitor-analytics-export-dropdown');
            const $exportBtn = $container.find('.export');
            
            // Hide the dropdown after selection
            $container.find('.visitor-analytics-export-options').removeClass('show');
            
            if ($exportBtn.hasClass('export-table')) {
                const tableId = $container.closest('.visitor-analytics-table-container').attr('id').replace('visitor-analytics-total-visitors-by-', '');
                const table = $(`#${tableId}`);
                const title = table.closest('.visitor-analytics-table-header').find('h2').text();

                // Get all data including modal data
                const modalId = `visitor-analytics-modal-${tableId}`;
                const fullTable = $(`#${modalId}`);
                const rows = fullTable.find('tr').toArray();

                // Convert to CSV/JSON
                let dataArray = [];

                rows.forEach(row => {
                    const rowData = [];
                    $(row).find('th, td').each(function() {
                        rowData.push($(this).text().trim());
                    });
                    dataArray.push(rowData);
                });

                // Export data with table name
                new ExportData(dataArray, exportType, 'table', tableId);
            } else if ($exportBtn.hasClass('export-chart')) {
                const $btn = $exportBtn;
                
                // Handle year chart data export
                if ($exportBtn.hasClass('year-data-export')) {
                    const currentYear = new Date().getFullYear();
                    const figure = new Figure(null, currentYear);
                    
                    try {
                        $btn.prop('disabled', true).text('Exporting...');
                        figure.fetchDataForChart().then(yearData => {
                            // Convert data to CSV/JSON
                            const dataArray = [];

                            if (yearData.success && yearData.data) {
                                yearData.data.forEach(item => {
                                    dataArray.push([item.year, item.month, item.total_visitors]);
                                });
                            }

                            // Export data
                            new ExportData(dataArray, exportType, 'month');
                        });
                    } catch (err) {
                        throw Error(err);
                    } finally {
                        $btn.prop('disabled', false).text('Export');
                    }
                }
                // Handle day chart data export
                else if ($exportBtn.hasClass('month-data-export')) {
                    const currentYear = new Date().getFullYear();
                    const currentMonth = new Date().getMonth() + 1;
                    const figure = new Figure(null, currentYear, currentMonth);
                    
                    try {
                        $btn.prop('disabled', true).text('Exporting...');
                        figure.fetchDataForChart().then(dayData => {
                            // Convert data to CSV/JSON
                            const dataArray = [];

                            if (dayData.success && dayData.data) {
                                dayData.data.forEach(item => {
                                    dataArray.push([item.year, item.month, item.day, item.total_visitors]);
                                });
                            }

                            // Export data
                            new ExportData(dataArray, exportType, 'day');
                        });
                    } catch (err) {
                        throw Error(err);
                    } finally {
                        $btn.prop('disabled', false).text('Export');
                    }
                }
            }
        });

        // Modal functionality
        $('.visitor-analytics-button.modal-trigger').on('click', function() {
            const tableId = $(this).closest('.visitor-analytics-table-container').attr('id').replace('visitor-analytics-total-visitors-by-', '');
            const modalId = `visitor-analytics-modal-${tableId}`;
            $(`#${modalId}`).addClass('active');
        });

        // Close modal when clicking the close button
        $('.visitor-analytics-modal-close').on('click', function() {
            $(this).closest('.visitor-analytics-modal').removeClass('active');
        });

        // Close modal when clicking outside
        $(window).on('click', function(event) {
            if ($(event.target).hasClass('visitor-analytics-modal')) {
                $(event.target).removeClass('active');
            }
        });

        // Close modal when pressing Escape key
        $(document).on('keydown', function(event) {
            if (event.key === 'Escape') {
                $('.visitor-analytics-modal.active').removeClass('active');
            }
        });

        // Export button click for chart
        $('.visitor-analytics-button.export.export-chart').on('click', async function() {
            const $btn = $(this);
        });
    }

    // Call initialization
    $(document).ready(function() {
        initAdmin();
    });

}); 