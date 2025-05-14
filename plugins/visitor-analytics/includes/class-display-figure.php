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

        <div id="visitor-analytics-total-visitors">
            <h2>Total Visitors:  <span><?php echo $total_visitors; ?></span></h2>
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
        <div class="visitor-analytics-total-visitors-by-month">
            <h2>Total Visitors by Month</h2>

            <div class="visitor-analytics-total-visitors-funciton-container">
                <div class="visitor-analytics-total-visitors-range-select">
                    <div class="visitor-analytics-total-visitors-by-year-select"></div>
                </div>

                <button class="visitor-analytics-button export export-chart year-data-export">Export</button>
            </div>


            <canvas id="visitor-analytics-total-visitors-by-month-chart" class="visitor-analytics-total-visitors-chart"></canvas>

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
        <div class="visitor-analytics-total-visitors-by-day">
            <h2>Total Visitors by Day</h2>

            <div class="visitor-analytics-total-visitors-funciton-container">
                <div class="visitor-analytics-total-visitors-range-select">
                    <div class="visitor-analytics-total-visitors-by-year-select"></div>
                    <div class="visitor-analytics-total-visitors-by-month-select"></div>
                </div>

                <button class="visitor-analytics-button export export-chart month-data-export">Export</button>
            </div>
            
            <canvas id="visitor-analytics-total-visitors-by-day-chart" class="visitor-analytics-total-visitors-chart"></canvas>
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
        $results = $this -> database_manager->get_total_visitors_by($column);
        $table_id = 'visitor-analytics-table-' . esc_attr($column);
        $modal_id = 'visitor-analytics-modal-' . esc_attr($column);

        ob_start();
        ?>
        <div id="visitor-analytics-total-visitors-by-<?php echo esc_attr($column); ?>" class="visitor-analytics-table-container">

            <div class="visitor-analytics-table-header">
                <h2><?php echo esc_html($title); ?></h2>

                <!-- Actions -->
                <div class="visitor-analytics-table-actions">

                    <!-- Export -->
                    <button class="visitor-analytics-button export export-table">
                        <span>Export</span>
                    </button>

                    <!-- Modal Trigger -->
                    <button class="visitor-analytics-button modal-trigger">
                        <span>Read More</span>
                    </button>

                </div>
            </div>

            <!-- Table -->
            <table id="<?php echo $table_id; ?>" class="visitor-analytics-total-visitors-by-table">
                <thead>
                    <tr>
                        <th><?php echo esc_html($column_label); ?></th>
                        <th>Total Visitors</th>
                    </tr>
                </thead>

                <tbody>
                <?php 
                $count = 0;
                foreach ($results as $row): 
                    if (!empty($row['group_value'])):
                        $count++;
                        if ($count > 10) break;
                ?>
                    <tr>
                        <td><?php echo esc_html($row['group_value']); ?></td>
                        <td><?php echo esc_html($row['total_visitors']); ?></td>
                    </tr>
                <?php 
                    endif;
                endforeach; 
                ?>
                </tbody>
                
            </table>

            <!-- Modal -->
            <div id="<?php echo esc_attr($modal_id); ?>" class="visitor-analytics-modal">
                <div class="visitor-analytics-modal-content">

                    <!-- Modal Header -->
                    <div class="visitor-analytics-modal-header">
                        <h3><?php echo esc_html($title);?>  - Full Data</h3>
                        <button class="visitor-analytics-modal-close">&times;</button>
                    </div>

                    <!-- Modal Body -->
                    <div class="visitor-analytics-modal-body">
                        <table class="visitor-analytics-total-visitors-by-table">
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
                </div>
            </div>


        </div>
        <?php

        return ob_get_clean();
    }


}