<?php
/**
 * Display figure class
 * 
 * @package WP Visitor Analytics
 */

class Display_Figure {
    private $database_manager;

    public function __construct() {
        $this -> database_manager = new Database_Manager();
    }

    /**
     * Display total visitors
     */
    public function display_total_visitors() {
        $total_visitors = $this -> database_manager -> get_total_visitors();

        ob_start();
        ?>

        <div class="visitor-analytics-total-visitors">
            <p>Total Visitors: <?php echo $total_visitors; ?></p>
        </div>

        <?php

        return ob_get_clean();
    }

    /**
     * Display total visitors by a specific column in a table
     * 
     * @param string $column The database column to group by (e.g., 'ip', 'country_name', 'browser', 'device')
     * @param string $title The title displayed above the table
     * @param string $column_label The table header label for the grouped value
     * @return string Rendered HTML table
     */
    public function display_total_visitors_by($column, $title, $column_label) {
        $results = $this->database_manager->get_total_visitors_by($column);

        ob_start();
        ?>
        <div id="visitor-analytics-total-visitors-by-<?php echo esc_attr($column); ?>">
            <h2><?php echo esc_html($title); ?></h2>
            <table>
                <thead>
                    <tr>
                        <th><?php echo esc_html($column_label); ?></th>
                        <th>Total Visitors</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($results as $row): ?>
                        <?php if (!empty($row['group_value'])): ?>
                            <tr>
                                <td><?php echo esc_html($row['group_value']); ?></td>
                                <td><?php echo esc_html($row['total_visitors']); ?></td>
                            </tr>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php

        return ob_get_clean();
    }

    /**
     * Display total visitors by month
     * @return string HTML output of visitors by month
     */
    public function display_total_visitors_by_month() {
        ob_start();
        ?>
        <div class="visitor-analytics-total-visitors-by-month" style="width: 700px; height: 400px;">
            <h2>Total Visitors by Month</h2>
            <div class="visitor-analytics-total-visitors-by-year-select"></div>
            <canvas id="visitor-analytics-total-visitors-by-month-chart"</canvas>
        </div>
        <?php

        return ob_get_clean();
    }

    /**
     * Display total visitors by day
     * @return string HTML output of visitors by day
     */
    public function display_total_visitors_by_day() {
        ob_start();
        ?>
        <div class="visitor-analytics-total-visitors-by-day" style="width: 700px; height: 400px; margin-bottom: 20px;">
            <h2>Total Visitors by Day</h2>
            <div class="visitor-analytics-total-visitors-by-year-select"></div>
            <div class="visitor-analytics-total-visitors-by-month-select"></div>
            <canvas id="visitor-analytics-total-visitors-by-day-chart"></canvas>
        </div>
        <?php

        return ob_get_clean();
    }
}