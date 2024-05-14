<?php
// Include connection file
include_once("connection.php");

// Handle server-side processing
if (isset($_POST['draw'])) {
    // Initialize all variables
    $params = $columns = $totalRecords = $data = array();

    $params = $_REQUEST;

    // Define index of columns
    $columns = array(
        0 => 'id',
        1 => 'employee_name',
        2 => 'employee_salary',
        3 => 'employee_age'
    );

    $where = $sqlTot = $sqlRec = "";

    // Getting total number of records without any search
    $sql = "SELECT * FROM `employee`";
    $sqlTot .= $sql;
    $sqlRec .= $sql;

    // Check if there is a search parameter
    if (!empty($params['search']['value'])) {
        $where .= " WHERE ";
        $where .= " ( employee_name LIKE '%" . $params['search']['value'] . "%' ";
        $where .= " OR employee_salary LIKE '%" . $params['search']['value'] . "%' ";
        $where .= " OR employee_age LIKE '%" . $params['search']['value'] . "%' )";
        // Concatenate search SQL if there's a search parameter
        $sqlTot .= $where;
        $sqlRec .= $where;
    }

    // Adding limit for pagination
    $sqlRec .= " ORDER BY " . $columns[$params['order'][0]['column']] . " " . $params['order'][0]['dir'] . " LIMIT " . $params['start'] . " ," . $params['length'] . " ";

    // Query for total records count
    $queryTot = mysqli_query($conn, $sqlTot) or die("database error:" . mysqli_error($conn));
    $totalRecords = mysqli_num_rows($queryTot);

    // Query for fetching data
    $queryRecords = mysqli_query($conn, $sqlRec) or die("error to fetch employees data");

    // Iterate on results row and create new index array of data
    while ($row = mysqli_fetch_row($queryRecords)) {
        $data[] = $row;
    }

    // Prepare JSON data
    $json_data = array(
        "draw" => intval($params['draw']),
        "recordsTotal" => intval($totalRecords),
        "recordsFiltered" => intval($totalRecords),
        "data" => $data   // total data array
    );

    echo json_encode($json_data);  // Send data as JSON format
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>How to Export the jQuery Datatable data to PDF, Excel, CSV, and Copy</title>
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.21/css/jquery.dataTables.min.css"/>
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/1.6.5/css/buttons.dataTables.min.css"/>

    <script type="text/javascript" src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/buttons/1.6.5/js/dataTables.buttons.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/pdfmake.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/vfs_fonts.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/buttons/1.6.5/js/buttons.html5.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/buttons/1.6.5/js/buttons.print.min.js"></script>
</head>
<body>
    <div class="container" style="padding: 20px;">
        <h1>Data Table with Export features Using PHP server-side</h1>
        <table id="employee_grid" class="display" width="100%" cellspacing="0">
            <thead>
                <tr>
                    <th>Empid</th>
                    <th>Name</th>
                    <th>Salary</th>
                    <th>Age</th>
                </tr>
            </thead>
            <tfoot>
                <tr>
                    <th>Empid</th>
                    <th>Name</th>
                    <th>Salary</th>
                    <th>Age</th>
                </tr>
            </tfoot>
        </table>
    </div>

    <script type="text/javascript">
    $(document).ready(function() {
        $('#employee_grid').DataTable({
            "processing": true,
            "serverSide": true,
            "ajax": {
                "url": "index.php",
                "type": "POST"
            },
            "dom": 'lBfrtip',
            "buttons": [
                {
                    extend: 'collection',
                    text: 'Export',
                    buttons: [
                        'copy',
                        'excel',
                        'csv',
                        'pdf',
                        'print'
                    ]
                }
            ],
            "columns": [
                { "data": 0 },
                { "data": 1 },
                { "data": 2 },
                { "data": 3 }
            ]
        });
    });
    </script>
</body>
</html>
