<?php include_once('../config.php'); ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Task</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js" integrity="sha384-IQsoLXl5PILFhosVNubq5LC7Qb9DXgDA9i+tQ8Zj3iwWAwPtgFTxbJ8NT4GN1R8p" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js" integrity="sha384-cVKIPhGWiC2Al4u+LWgxfKTRIcfu0JTxR+EQDz/bgldoEyl4H0zUF0QKbrJ0EcQF" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.css" />
    <link rel="stylesheet" href="../src/style.css" />
</head>

<body>
    <!--  Form -->
    <div class="col-md-12 container" id="importFrm">

        <div class="alert text-center" id="error"></div>
        <form class="row" id="form" method="post" enctype="multipart/form-data">
            <div class="row">
                <div class="form-group col-md-6">
                    <label for="fileInput col-md-2 " class="visually-hidden">File</label>

                    <input type="file" class="form-control" name="file" id="fileInput" require />
                </div>

                <div class="col-md-6">
                    <input type="submit" class="btn btn-primary mb-3" name="importSubmit" id="importSubmit" value="Import">
                </div>
                <div class="loading" style="display: none;"></div>
            </div>
        </form>
    </div>
    <!-- Chart -->
    <div class="wrapper">
        <canvas id="myChart4"></canvas>
    </div>

    <!-- Data list table -->
    <table class="table table-striped table-bordered display" style="width:100%" id="data">
        <thead class="table-dark">
            <tr>
                <th>#</th>
                <th>Time</th>
                <th>Leads</th>
                <th>Activates</th>
            </tr>
        </thead>
        <tbody id="alldb">

            <?php $check = 0;
            $result = $db->query("SELECT * FROM marketing_data ORDER BY id DESC limit 1241");
            if ($result->num_rows > 0) {
                $i = 0;
                while ($row = $result->fetch_assoc()) {
                    $i++;
                    $check = 1;
            ?>
                    <tr>
                        <td><?php echo $i; ?></td>
                        <td><?php echo $row['time_microseconds']; ?></td>
                        <td><?php echo $row['leads']; ?></td>
                        <td><?php echo $row['activates']; ?></td>
                    </tr>
                <?php
                }
            } else { ?>
                <tr>
                    <td colspan="4">No data found...</td>
                </tr>
            <?php  } ?>
        </tbody>
    </table>

</body>
<script src="https://code.jquery.com/jquery-3.7.0.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.4.0/Chart.min.js"></script>
<script src="../src/custom.js"></script>
<script>
    $(document).ready(function() {
    if ('<?= $check ?>' == 1)
        new DataTable('#data');
    updateChart();
})

</script>
</html>