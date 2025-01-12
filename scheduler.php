<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Slots</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="css/responsive.css">
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/styles.css">
    </body>
</head>

<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light">

    </nav>
    <div class="container">

        <h1 class="mt-5">Book Faculty Slots</h1>
        <form id="bookSlotForm">
            <label class="form-label" for="date">Select Date:</label>
            <input class="form-control" type="date" id="date" name="date" required ><br><br>

            <label class="form-label" for="session">Select Session:</label>
            <select class="form-control" id="session" name="session" required>
                <option value="morning">Morning</option>
                <option value="afternoon">Afternoon</option>
            </select><br><br>

            <label class="form-label" for="num">Number of Faculties:</label>
            <input class="form-control" type="number" id="num" name="num" min="1" required><br><br>
                <button type="submit" class="btn btn-primary">Assign Slots</button>
                <p class="btn btn-primary fetch-details mb-0">Fetch</p>
        </form>
    </div>


    <div class="container">

        <!-- <h2>Available Faculties</h2> -->
        <div id="facultyTableContainer">
            <!-- Faculty table will be dynamically generated here -->
        </div>
    </div>



    <script>
        $(document).ready(function () {
            $('#bookSlotForm').on('submit', function (e) {
                e.preventDefault();
                const date = $('#date').val();
                const session = $('#session').val();
                const num = $('#num').val();
                const dt = new Date(date);
                if(dt.getDay() == 0){
                    alert('Please select a weekday');
                    return;
                }
                const curDate = new Date();
                if(dt<curDate){
                    alert('Please select a future date');
                    return;
                }
                $.ajax({
                    url: 'process_slots.php',
                    type: 'POST',
                    data: { date, session, num },
                    success: function (response) {
                        $('#facultyTableContainer').html(response);
                    },
                    error: function (xhr, status, error) {
                        alert('An error occurred: ' + error);
                    }
                });
            });



            $('#bookSlotForm').on('click', '.fetch-details', function (e) {
                // e.preventDefault();
                const date = $('#date').val();
                const session = $('#session').val();
                console.log(date);
                console.log(session);
                $.ajax({
                    type: "POST",
                    url: 'functions.php',
                    data: { functionname: 'renderDuties', date: date, session: session },
                    success: function (response) {
                        // Append the returned HTML to the container
                        $('#facultyTableContainer').empty().append(response);

                    },
                    error: function (xhr, status, error) {
                        console.error("Error:", error);
                        console.error("XHR Response:", xhr.responseText);
                    }
                });
            });




            // Dynamic event delegation for Accept All and Reject
            $('#facultyTableContainer').on('click', '.accept-all', function () {
                const selectedFaculties = [];
                $('.faculty-row').each(function () {
                    selectedFaculties.push($(this).data('fid'));
                });

                const date = $('#date').val();
                const session = $('#session').val();

                $.ajax({
                    url: 'accept_faculties.php',
                    type: 'POST',
                    data: { date, session, selectedFaculties },
                    success: function (response) {
                        alert(response);
                        // location.reload();
                        $('.faculty-table').html(' ');
                    },
                    error: function (xhr, status, error) {
                        alert('An error occurred: ' + error);
                    }
                });
            });

            $('#facultyTableContainer').on('click', '.reject-faculty', function () {
                const rejectedFid = $(this).data('fid');
                const row = $(this).closest('tr');
                row.remove();
                $('#facultyTableContainer').html(' ');
                $.ajax({
                    url: 'fetch_next_faculty.php',
                    type: 'POST',
                    data: { rejectedFid },
                    success: function (response) {
                        $('#facultyTableContainer').html(response);
                    },
                    error: function (xhr, status, error) {
                        alert('An error occurred: ' + error);
                    }
                });
            });


            // Dynamic event delegation for Accept All and Reject for extra faculties
            $('#facultyTableContainer').on('click', '.accept-all-extra', function () {
                const selectedFaculties = [];
                $('.faculty-row-extra').each(function () {
                    selectedFaculties.push($(this).data('fid'));
                });

                const date = $('#date').val();
                const session = $('#session').val();

                $.ajax({
                    url: 'accept_faculties.php',
                    type: 'POST',
                    data: { date, session, selectedFaculties },
                    success: function (response) {
                        alert(response);
                        location.reload();
                        // $('#facultyTableContainer').html(' ');
                    },
                    error: function (xhr, status, error) {
                        alert('An error occurred: ' + error);
                    }
                });
            });

            $('#facultyTableContainer').on('click', '.reject-faculty-extra', function () {
                const rejectedFid = $(this).data('fid');

                $.ajax({
                    type: "POST",
                    url: 'functions.php',
                    data: { functionname: 'renderExtraFacultyTable', arguments: rejectedFid },
                    success: function (response) {
                        // Append the returned HTML to the container
                        $('#facultyTableContainer').append(response);
                    },
                    error: function (xhr, status, error) {
                        console.error("Error:", error);
                        console.error("XHR Response:", xhr.responseText);
                    }
                });
            });
        });
    </script>







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