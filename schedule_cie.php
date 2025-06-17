<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book CIE Slots</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="css/responsive.css">
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/styles.css">
</head>

<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light">
    </nav>
    <div class="container mt-5">
        <form action="schedule_cie_process.php" method="POST">
            <div class="mb-3">
                <label for="month" class="form-label">Select Month:</label>
                <select id="month" class="form-select" name="month">
                    <option value="January">January</option>
                    <option value="February">February</option>
                    <option value="March">March</option>
                    <option value="April">April</option>
                    <option value="May">May</option>
                    <option value="June">June</option>
                    <option value="July">July</option>
                    <option value="August">August</option>
                    <option value="September">September</option>
                    <option value="October">October</option>
                    <option value="November">November</option>
                    <option value="December">December</option>
                </select>
            </div>

            <div class="mb-3">
                <label for="departments" class="form-label">Select Departments:</label>
                <div class="d-flex flex-wrap">
                    <?php
                    include 'functions.php';
                    $departments = get_departments();
                    foreach ($departments as $department) {
                        echo "<div class='form-check me-3'>";
                        echo "<input class='form-check-input' type='checkbox' name='departments[]' value='" . $department . "' id='dept_" . $department . "'>";
                        echo "<label class='form-check-label' for='dept_" . $department . "'>" . $department . "</label>";
                        echo "</div>";
                    }
                    ?>
                </div>
            </div>

            <!-- Week 1 -->
            <table class="table table-bordered table-striped mb-5">
                <thead>
                    <tr>
                        <th rowspan="2">Day</th>
                        <th>1</th>
                        <th>2</th>
                        <th>3</th>
                        <th>4</th>
                        <th>5</th>
                        <th>6</th>
                        <th>7</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Morning</td>
                        <td><input type="text" min=0 class="form-control" name="1-morning" placeholder="10" oninput="this.value = this.value.replace(/[^0-9]/, '')"></td>
                        <td><input type="text" min=0 class="form-control" name="2-morning" placeholder="10" oninput="this.value = this.value.replace(/[^0-9]/, '')"></td>
                        <td><input type="text" min=0 class="form-control" name="3-morning" placeholder="10" oninput="this.value = this.value.replace(/[^0-9]/, '')"></td>
                        <td><input type="text" min=0 class="form-control" name="4-morning" placeholder="10" oninput="this.value = this.value.replace(/[^0-9]/, '')"></td>
                        <td><input type="text" min=0 class="form-control" name="5-morning" placeholder="10" oninput="this.value = this.value.replace(/[^0-9]/, '')"></td>
                        <td><input type="text" min=0 class="form-control" name="6-morning" placeholder="10" oninput="this.value = this.value.replace(/[^0-9]/, '')"></td>
                        <td><input type="text" min=0 class="form-control" name="7-morning" placeholder="10" oninput="this.value = this.value.replace(/[^0-9]/, '')"></td>
                    </tr>
                    <tr>
                        <td>Afternoon</td>
                        <td><input type="text" min=0 class="form-control" name="1-afternoon" placeholder="10" oninput="this.value = this.value.replace(/[^0-9]/, '')"></td>
                        <td><input type="text" min=0 class="form-control" name="2-afternoon" placeholder="10" oninput="this.value = this.value.replace(/[^0-9]/, '')"></td>
                        <td><input type="text" min=0 class="form-control" name="3-afternoon" placeholder="10" oninput="this.value = this.value.replace(/[^0-9]/, '')"></td>
                        <td><input type="text" min=0 class="form-control" name="4-afternoon" placeholder="10" oninput="this.value = this.value.replace(/[^0-9]/, '')"></td>
                        <td><input type="text" min=0 class="form-control" name="5-afternoon" placeholder="10" oninput="this.value = this.value.replace(/[^0-9]/, '')"></td>
                        <td><input type="text" min=0 class="form-control" name="6-afternoon" placeholder="10" oninput="this.value = this.value.replace(/[^0-9]/, '')"></td>
                        <td><input type="text" min=0 class="form-control" name="7-afternoon" placeholder="10" oninput="this.value = this.value.replace(/[^0-9]/, '')"></td>
                    </tr>
                </tbody>
                <thead>
                    <tr>
                        <th rowspan="2">Day</th>
                        <th>8</th>
                        <th>9</th>
                        <th>10</th>
                        <th>11</th>
                        <th>12</th>
                        <th>13</th>
                        <th>14</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Morning</td>
                        <td><input type="text" min=0 class="form-control" name="8-morning" placeholder="10" oninput="this.value = this.value.replace(/[^0-9]/, '')"></td>
                        <td><input type="text" min=0 class="form-control" name="9-morning" placeholder="10" oninput="this.value = this.value.replace(/[^0-9]/, '')"></td>
                        <td><input type="text" min=0 class="form-control" name="10-morning" placeholder="10" oninput="this.value = this.value.replace(/[^0-9]/, '')"></td>
                        <td><input type="text" min=0 class="form-control" name="11-morning" placeholder="10" oninput="this.value = this.value.replace(/[^0-9]/, '')"></td>
                        <td><input type="text" min=0 class="form-control" name="12-morning" placeholder="10" oninput="this.value = this.value.replace(/[^0-9]/, '')"></td>
                        <td><input type="text" min=0 class="form-control" name="13-morning" placeholder="10" oninput="this.value = this.value.replace(/[^0-9]/, '')"></td>
                        <td><input type="text" min=0 class="form-control" name="14-morning" placeholder="10" oninput="this.value = this.value.replace(/[^0-9]/, '')"></td>
                    </tr>
                    <tr>
                        <td>Afternoon</td>
                        <td><input type="text" min=0 class="form-control" name="8-afternoon" placeholder="10" oninput="this.value = this.value.replace(/[^0-9]/, '')"></td>
                        <td><input type="text" min=0 class="form-control" name="9-afternoon" placeholder="10" oninput="this.value = this.value.replace(/[^0-9]/, '')"></td>
                        <td><input type="text" min=0 class="form-control" name="10-afternoon" placeholder="10" oninput="this.value = this.value.replace(/[^0-9]/, '')"></td>
                        <td><input type="text" min=0 class="form-control" name="11-afternoon" placeholder="10" oninput="this.value = this.value.replace(/[^0-9]/, '')"></td>
                        <td><input type="text" min=0 class="form-control" name="12-afternoon" placeholder="10" oninput="this.value = this.value.replace(/[^0-9]/, '')"></td>
                        <td><input type="text" min=0 class="form-control" name="13-afternoon" placeholder="10" oninput="this.value = this.value.replace(/[^0-9]/, '')"></td>
                        <td><input type="text" min=0 class="form-control" name="14-afternoon" placeholder="10" oninput="this.value = this.value.replace(/[^0-9]/, '')"></td>
                    </tr>
                </tbody>
                <thead>
                    <tr>
                        <th rowspan="2">Day</th>
                        <th>15</th>
                        <th>16</th>
                        <th>17</th>
                        <th>18</th>
                        <th>19</th>
                        <th>20</th>
                        <th>21</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Morning</td>
                        <td><input type="text" min=0 class="form-control" name="15-morning" placeholder="10" oninput="this.value = this.value.replace(/[^0-9]/, '')"></td>
                        <td><input type="text" min=0 class="form-control" name="16-morning" placeholder="10" oninput="this.value = this.value.replace(/[^0-9]/, '')"></td>
                        <td><input type="text" min=0 class="form-control" name="17-morning" placeholder="10" oninput="this.value = this.value.replace(/[^0-9]/, '')"></td>
                        <td><input type="text" min=0 class="form-control" name="18-morning" placeholder="10" oninput="this.value = this.value.replace(/[^0-9]/, '')"></td>
                        <td><input type="text" min=0 class="form-control" name="19-morning" placeholder="10" oninput="this.value = this.value.replace(/[^0-9]/, '')"></td>
                        <td><input type="text" min=0 class="form-control" name="20-morning" placeholder="10" oninput="this.value = this.value.replace(/[^0-9]/, '')"></td>
                        <td><input type="text" min=0 class="form-control" name="21-morning" placeholder="10" oninput="this.value = this.value.replace(/[^0-9]/, '')"></td>
                    </tr>
                    <tr>
                        <td>Afternoon</td>
                        <td><input type="text" min=0 class="form-control" name="15-afternoon" placeholder="10" oninput="this.value = this.value.replace(/[^0-9]/, '')"></td>
                        <td><input type="text" min=0 class="form-control" name="16-afternoon" placeholder="10" oninput="this.value = this.value.replace(/[^0-9]/, '')"></td>
                        <td><input type="text" min=0 class="form-control" name="17-afternoon" placeholder="10" oninput="this.value = this.value.replace(/[^0-9]/, '')"></td>
                        <td><input type="text" min=0 class="form-control" name="18-afternoon" placeholder="10" oninput="this.value = this.value.replace(/[^0-9]/, '')"></td>
                        <td><input type="text" min=0 class="form-control" name="19-afternoon" placeholder="10" oninput="this.value = this.value.replace(/[^0-9]/, '')"></td>
                        <td><input type="text" min=0 class="form-control" name="20-afternoon" placeholder="10" oninput="this.value = this.value.replace(/[^0-9]/, '')"></td>
                        <td><input type="text" min=0 class="form-control" name="21-afternoon" placeholder="10" oninput="this.value = this.value.replace(/[^0-9]/, '')"></td>
                    </tr>
                </tbody>
                <thead>
                    <tr>
                        <th rowspan="2">Day</th>
                        <th>22</th>
                        <th>23</th>
                        <th>24</th>
                        <th>25</th>
                        <th>26</th>
                        <th>27</th>
                        <th>28</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Morning</td>
                        <td><input type="text" min=0 class="form-control" name="22-morning" placeholder="10" oninput="this.value = this.value.replace(/[^0-9]/, '')"></td>
                        <td><input type="text" min=0 class="form-control" name="23-morning" placeholder="10" oninput="this.value = this.value.replace(/[^0-9]/, '')"></td>
                        <td><input type="text" min=0 class="form-control" name="24-morning" placeholder="10" oninput="this.value = this.value.replace(/[^0-9]/, '')"></td>
                        <td><input type="text" min=0 class="form-control" name="25-morning" placeholder="10" oninput="this.value = this.value.replace(/[^0-9]/, '')"></td>
                        <td><input type="text" min=0 class="form-control" name="26-morning" placeholder="10" oninput="this.value = this.value.replace(/[^0-9]/, '')"></td>
                        <td><input type="text" min=0 class="form-control" name="27-morning" placeholder="10" oninput="this.value = this.value.replace(/[^0-9]/, '')"></td>
                        <td><input type="text" min=0 class="form-control" name="28-morning" placeholder="10" oninput="this.value = this.value.replace(/[^0-9]/, '')"></td>
                    </tr>
                    <tr>
                        <td>Afternoon</td>
                        <td><input type="text" min=0 class="form-control" name="22-afternoon" placeholder="10" oninput="this.value = this.value.replace(/[^0-9]/, '')"></td>
                        <td><input type="text" min=0 class="form-control" name="23-afternoon" placeholder="10" oninput="this.value = this.value.replace(/[^0-9]/, '')"></td>
                        <td><input type="text" min=0 class="form-control" name="24-afternoon" placeholder="10" oninput="this.value = this.value.replace(/[^0-9]/, '')"></td>
                        <td><input type="text" min=0 class="form-control" name="25-afternoon" placeholder="10" oninput="this.value = this.value.replace(/[^0-9]/, '')"></td>
                        <td><input type="text" min=0 class="form-control" name="26-afternoon" placeholder="10" oninput="this.value = this.value.replace(/[^0-9]/, '')"></td>
                        <td><input type="text" min=0 class="form-control" name="27-afternoon" placeholder="10" oninput="this.value = this.value.replace(/[^0-9]/, '')"></td>
                        <td><input type="text" min=0 class="form-control" name="28-afternoon" placeholder="10" oninput="this.value = this.value.replace(/[^0-9]/, '')"></td>
                    </tr>
                </tbody>
                <thead>
                    <tr>
                        <th rowspan="2">Day</th>
                        <th>29</th>
                        <th>30</th>
                        <th>31</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Morning</td>
                        <td><input type="text" min=0 class="form-control" name="29-morning" placeholder="10" oninput="this.value = this.value.replace(/[^0-9]/, '')"></td>
                        <td><input type="text" min=0 class="form-control" name="30-morning" placeholder="10" oninput="this.value = this.value.replace(/[^0-9]/, '')"></td>
                        <td><input type="text" min=0 class="form-control" name="31-morning" placeholder="10" oninput="this.value = this.value.replace(/[^0-9]/, '')"></td>
                    </tr>
                    <tr>
                        <td>Afternoon</td>
                        <td><input type="text" min=0 class="form-control" name="29-afternoon" placeholder="10" oninput="this.value = this.value.replace(/[^0-9]/, '')"></td>
                        <td><input type="text" min=0 class="form-control" name="30-afternoon" placeholder="10" oninput="this.value = this.value.replace(/[^0-9]/, '')"></td>
                        <td><input type="text" min=0 class="form-control" name="31-afternoon" placeholder="10" oninput="this.value = this.value.replace(/[^0-9]/, '')"></td>
                    </tr>
                </tbody>
            </table>

            <button type="submit" class="btn btn-primary">Assign</button>
        </form>
    </div>

    <!-- Footer section -->
    <div class="custom-footer"></div>

    <script src="js/jquery.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/jquery.magnific-popup.min.js"></script>
    <script src="js/jquery.waypoints.js"></script>
    <script src="js/jquery.counterup.min.js"></script>
    <script src="js/jquery.barfiller.js"></script>
    <script src="js/index.js"></script>
</body>

</html>