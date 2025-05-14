/**
 * export-data.js
 * 
 * This file contains the JavaScript code for exporting data to xlsx, csv, and json files.
 */


class ExportData {
    /**
     * Constructor
     * 
     * @param {array} dataArray - Array of data to export
     * @param {string} fileType - Type of file to export (csv, xlsx, json)
     * @param {string} exportType - Type of export (month, day, or table)
     * @param {string} tableName - Name of the table (only for table export type)
     */
    constructor(dataArray, fileType, exportType, tableName = '') {
        this.dataArray = dataArray;
        this.fileType = fileType;
        this.exportType = exportType;
        this.tableName = tableName;

        if (!this.dataArray || dataArray.length === 0) {
            console.error('No data to export');
            return;
        }

        console.log(dataArray);

        if (fileType === 'csv') {
            this.exportCsv();
        } else if (fileType === 'json') {
            this.exportJson();
        }
    }

    /**
     * Export data to CSV format
     */
    exportCsv() {
        let headers, rows;
    
        // Switch case
        switch (this.exportType) {
            case 'month':
                headers = ['Year', 'Month', 'Total Visitors'];
                rows = this.dataArray.map(row => [row[0], row[1], row[2]]);
                break;
            case 'day':
                headers = ['Year', 'Month', 'Day', 'Total Visitors'];
                rows = this.dataArray.map(row => [row[0], row[1], row[2], row[3]]);
                break;
            case 'table':
                // For table data, the first row contains headers
                headers = this.dataArray[0];
                rows = this.dataArray.slice(1);
                break;
            default:
                console.error('Invalid export type');
                return;
        }

        const csvContent = [
            headers,
            ...rows
        ].map(row => row.join(',')).join('\n');

        this.downloadFile(csvContent, 'text/csv', 'csv');
    }

    /**
     * Export data to JSON format
     */
    exportJson() {
        let jsonData;

        switch (this.exportType) {
            case 'month':
                jsonData = this.dataArray.map(row => ({
                    year: row[0],
                    month: row[1],
                    total_visitors: row[2]
                }));
                break;
            case 'day':
                jsonData = this.dataArray.map(row => ({
                    year: row[0],
                    month: row[1],
                    day: row[2],
                    total_visitors: row[3]
                }));
                break;
            case 'table':
                // For table data, convert to array of objects using headers
                const headers = this.dataArray[0];
                jsonData = this.dataArray.slice(1).map(row => {
                    const obj = {};
                    headers.forEach((header, index) => {
                        obj[header] = row[index];
                    });
                    return obj;
                });
                break;
            default:
                console.error('Invalid export type');
                return;
        }

        const jsonContent = JSON.stringify(jsonData, null, 2);
        this.downloadFile(jsonContent, 'application/json', 'json');
    }

    /**
     * Download file
     * @param {string} content - File content
     * @param {string} mimeType - MIME type of the file
     * @param {string} extension - File extension
     */
    downloadFile(content, mimeType, extension) {
        const blob = new Blob([content], { type: `${mimeType};charset=utf-8;` });
        const url = URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;

        // Generate filename based on export type
        let filename;

        if (this.exportType === 'table' && this.tableName) {
            // For table exports, use the table name
            filename = `visitor-analytics-${this.tableName.toLowerCase().replace('_', '-')}-${new Date().toISOString().split('T')[0]}.${extension}`;
        } else {
            // For chart exports, use the export type
            filename = `visitor-analytics-${this.exportType}-${new Date().toISOString().split('T')[0]}.${extension}`;
        }

        a.download = filename;
        a.click();
        URL.revokeObjectURL(url);
    }
}